<?php

namespace Benzine\ORM\Abstracts;

use Benzine\ORM\Interfaces\ModelInterface;
use Camel\CaseTransformer;
use Camel\Format;

abstract class Model implements ModelInterface
{
    protected array $_primary_keys = [];
    protected array $_autoincrement_keys = [];

    protected array $_original;

    public function __construct(array $data = [])
    {
        if ($data) {
            $this->exchangeArray($data);
        }
        $this->__setUp();
    }

    /**
     * Overrideable __setUp function that will allow you to hijack
     * it and create any related objects that need to be recreated.
     */
    public function __setUp(): void
    {
    }

    public function __wakeup()
    {
        $this->__setUp();
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $array = [];

        $transformer = new CaseTransformer(new Format\StudlyCaps(), new Format\StudlyCaps());

        foreach ($this->getListOfProperties() as $property) {
            $getFunction = "get{$property}";
            $currentValue = $this->{$getFunction}();
            $array[$transformer->transform($property)] = $currentValue;
        }

        return array_merge($array);
    }

    /**
     * @return array
     */
    public function __toRawArray()
    {
        $array = [];

        $transformer = new CaseTransformer(new Format\StudlyCaps(), new Format\StudlyCaps());

        foreach ($this->getListOfProperties() as $dbField => $property) {
            $currentValue = $this->{$property};
            $array[$dbField] = $currentValue;
        }

        return $array;
    }

    public function __toPublicArray(): array
    {
        $publicArray = [];
        foreach ($this->getListOfProperties() as $property) {
            $publicArray[ucfirst($property)] = $this->{$property};
        }

        return $publicArray;
    }

    public function __fromPublicArray(array $publicArray): self
    {
        foreach ($this->getListOfProperties() as $property) {
            $this->{$property} = $publicArray[ucfirst($property)];
        }

        return $this;
    }

    public function __serialize(): array
    {
        return $this->__toPublicArray();
    }

    public function __unserialize(array $data): void
    {
        $this->__fromPublicArray($data);
    }

    public function __pre_save()
    {
        // Stub function to be overridden.
    }

    public function __post_save()
    {
        // Stub function to be overridden.
    }

    public static function factory(array $data = [])
    {
        $class = get_called_class();

        return new $class($data);
    }

    public function getPrimaryKeys(): array
    {
        $primaryKeyValues = [];
        foreach ($this->_primary_keys as $internalName => $dbName) {
            $getFunction = "get{$internalName}";
            $primaryKeyValues[$internalName] = $this->{$getFunction}();
        }

        return $primaryKeyValues;
    }

    public function getPrimaryKeys_dbColumns(): array
    {
        $primaryKeyValues = [];
        foreach ($this->_primary_keys as $internalName => $dbName) {
            $getFunction = "get{$internalName}";
            $primaryKeyValues[$dbName] = $this->{$getFunction}();
        }

        return $primaryKeyValues;
    }

    /**
     * Return autoincrement key values in an associative array.
     *
     * @return array
     */
    public function getAutoIncrementKeys()
    {
        $autoIncrementKeyValues = [];
        foreach ($this->_autoincrement_keys as $autoincrement_key => $autoincrement_db_column) {
            $getFunction = "get{$autoincrement_key}";
            $autoIncrementKeyValues[$autoincrement_key] = $this->{$getFunction}();
        }

        return $autoIncrementKeyValues;
    }

    /**
     * Returns true if the primary key isn't null.
     *
     * @return bool
     */
    public function hasPrimaryKey()
    {
        foreach ($this->getPrimaryKeys() as $primaryKey) {
            if (null != $primaryKey) {
                return true;
            }
        }

        return false;
    }

    public function getListOfProperties(): array
    {
        // @todo make this into an interface entry
        throw new \Exception('getListOfProperties in Abstract Model should never be used.');
    }

    /**
     * Returns whether or not the data has been modified inside this model.
     */
    public function hasDirtyProperties(): bool
    {
        return count($this->getListOfDirtyProperties()) > 0;
    }

    /**
     * Returns an array of dirty properties.
     */
    public function getListOfDirtyProperties(): array
    {
        $transformer = new CaseTransformer(new Format\CamelCase(), new Format\StudlyCaps());
        $dirtyProperties = [];
        foreach ($this->getListOfProperties() as $property) {
            $originalProperty = $transformer->transform($property);
            //echo "Writing into \$this->{$originalProperty}: getListOfDirtyProperties\n";
            if (!isset($this->_original[$originalProperty]) || $this->{$property} != $this->_original[$originalProperty]) {
                $dirtyProperties[$property] = [
                    'before' => isset($this->_original[$originalProperty]) ? $this->_original[$originalProperty] : null,
                    'after' => $this->{$property},
                ];
            }
        }

        return $dirtyProperties;
    }

    public function isDirty(): bool
    {
        $clean = true;
        foreach ($this->_original as $key => $originalValue) {
            foreach ($this->getListOfProperties() as $existingKey) {
                if (strtolower($key) == strtolower($existingKey)) {
                    if ($this->{$existingKey} != $originalValue) {
                        $clean = false;
                    }
                }
            }
        }

        return !$clean;
    }

    protected function getProtectedMethods(): array
    {
        return ['getPrimaryKeys', 'getProtectedMethods', 'getDIContainer'];
    }
}
