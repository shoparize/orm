<?php

namespace ⌬\Database\Components;

use ⌬\Database\Exception\DBTypeNotTranslatedException;
use ⌬\Database\Laminator;

class Column extends Entity
{
    /** @var Model */
    protected $model;

    protected $field;
    protected $dbField;
    protected $dbType;
    protected $phpType;
    protected $maxLength;
    protected $isUnsigned = false;
    protected $maxFieldLength;
    protected $maxDecimalPlaces;
    protected $permittedValues;
    protected $defaultValue;
    protected $defaultValueIsLiteral = false;
    protected $isAutoIncrement = false;
    protected $isUnique = false;
    /** @var RelatedModel[] */
    protected $relatedObjects = [];
    /** @var RelatedModel[] */
    protected $remoteObjects = [];

    /**
     * @return self
     */
    public static function Factory(Laminator $Laminator)
    {
        return parent::Factory($Laminator);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return Column
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function isUnsigned(): bool
    {
        return $this->isUnsigned;
    }

    public function setIsUnsigned(bool $isUnsigned): Column
    {
        $this->isUnsigned = $isUnsigned;

        return $this;
    }

    public function isAutoIncrement(): bool
    {
        return $this->isAutoIncrement;
    }

    public function setIsAutoIncrement(bool $isAutoIncrement): Column
    {
        $this->isAutoIncrement = $isAutoIncrement;

        return $this;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    public function setIsUnique(bool $isUnique): Column
    {
        $this->isUnique = $isUnique;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhpType()
    {
        return $this->phpType;
    }

    /**
     * @param mixed $phpType
     *
     * @return Column
     */
    public function setPhpType($phpType)
    {
        $this->phpType = $phpType;

        return $this;
    }

    public function getPropertyName()
    {
        return $this->transField2Property->transform($this->getField());
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getFieldSanitised(): string
    {
        return preg_replace('/[^A-Za-z0-9_]/', '', $this->getField());
    }

    /**
     * @param mixed $field
     *
     * @return Column
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbField()
    {
        return $this->dbField;
    }

    /**
     * @param mixed $dbField
     *
     * @return Column
     */
    public function setDbField($dbField)
    {
        $this->dbField = $dbField;

        return $this;
    }

    public function getPropertyFunction()
    {
        return $this->transCamel2Studly->transform($this->getFieldSanitised());
    }

    /**
     * @return mixed
     */
    public function getMaxDecimalPlaces()
    {
        return $this->maxDecimalPlaces;
    }

    /**
     * @param mixed $maxDecimalPlaces
     *
     * @return Column
     */
    public function setMaxDecimalPlaces($maxDecimalPlaces)
    {
        $this->maxDecimalPlaces = $maxDecimalPlaces;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return Column
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        if ('NULL' == $defaultValue) {
            $this->defaultValue = null;
            $this->setDefaultValueIsLiteral(true);
        } elseif (is_numeric($defaultValue)) {
            $this->setDefaultValueIsLiteral(true);
        } elseif (false !== stripos($defaultValue, '()')) {
            $this->setDefaultValueIsLiteral(false);
        } else {
            $this->setDefaultValueIsLiteral(true);
        }
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultValueIsLiteral(): bool
    {
        return $this->defaultValueIsLiteral;
    }

    /**
     * @param bool $defaultValueIsLiteral
     *
     * @return Column
     */
    public function setDefaultValueIsLiteral(bool $defaultValueIsLiteral): Column
    {
        $this->defaultValueIsLiteral = $defaultValueIsLiteral;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param mixed $maxLength
     *
     * @return Column
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxFieldLength()
    {
        return $this->maxFieldLength;
    }

    /**
     * @param mixed $maxFieldLength
     *
     * @return Column
     */
    public function setMaxFieldLength($maxFieldLength)
    {
        $this->maxFieldLength = $maxFieldLength;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * @param mixed $dbType
     *
     * @throws DBTypeNotTranslatedException
     *
     * @return Column
     */
    public function setDbType($dbType)
    {
        $this->dbType = $dbType;
        switch ($this->getDbType()) {
            case 'float':
            case 'decimal':
            case 'double':
                $this->setPhpType('float');

                break;
            case 'bit':
            case 'int':
            case 'bigint':
            case 'mediumint':
            case 'tinyint':
            case 'smallint':
                $this->setPhpType('int');

                break;
            case 'char':
            case 'varchar':
            case 'tinyblob':
            case 'smallblob':
            case 'blob':
            case 'longblob':
            case 'tinytext':
            case 'smalltext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'json':
                $this->setPhpType('string');

                break;
            case 'enum':
                $this->setPhpType('string');

                break;
            case 'datetime':
            case 'timestamp':
                $this->setPhpType('string');

                break;
            default:
                throw new DBTypeNotTranslatedException("Type not translated: {$this->getDbType()}");
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermittedValues()
    {
        return $this->permittedValues;
    }

    /**
     * @param mixed $permittedValues
     *
     * @return Column
     */
    public function setPermittedValues($permittedValues)
    {
        $this->permittedValues = $permittedValues;

        return $this;
    }

    /**
     * @return $this
     */
    public function addRelatedObject(RelatedModel $relatedModel)
    {
        $this->relatedObjects[] = $relatedModel;

        return $this;
    }

    /**
     * @return $this
     */
    public function addRemoteObject(RelatedModel $relatedModel)
    {
        $this->remoteObjects[] = $relatedModel;

        return $this;
    }

    public function hasRelatedObjects(): bool
    {
        return count($this->relatedObjects) > 0;
    }

    public function hasRemoteObjects(): bool
    {
        return count($this->remoteObjects) > 0;
    }

    /**
     * @return RelatedModel[]
     */
    public function getRelatedObjects(): array
    {
        return $this->relatedObjects;
    }

    /**
     * @return RelatedModel[]
     */
    public function getRemoteObjects(): array
    {
        return $this->remoteObjects;
    }
}
