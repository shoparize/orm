<?php

namespace Benzine\ORM\Components;

use Benzine\Exceptions\BenzineException;
use Benzine\ORM\Connection\Database;
use Benzine\ORM\Laminator;
use Gone\Inflection\Inflect;
use Laminas\Db\Metadata\Object\ColumnObject;
use Laminas\Db\Metadata\Object\ConstraintObject;

class Model extends Entity
{
    protected ?string $classPrefix = null;
    protected string $namespace;
    protected Database $database;
    protected string $table;
    /** @var Column[] */
    protected array $columns = [];
    protected array $constraints = [];
    protected array $relatedObjects = [];
    protected array $primaryKeys = [];
    protected $autoIncrements;

    /**
     * @return self
     */
    public static function Factory(Laminator $Laminator)
    {
        return parent::Factory($Laminator);
    }

    /**
     * @param Model[]            $models
     * @param ConstraintObject[] $zendConstraints
     */
    public function computeConstraints(array $models, array $keyMap, array $zendConstraints): self
    {
        //echo "Computing the constraints of {$this->getClassName()}\n";
        foreach ($zendConstraints as $zendConstraint) {
            if ('FOREIGN KEY' == $zendConstraint->getType()) {
                //\Kint::dump($this->getTable(), $this->getClassPrefix(), $zendConstraint->getTableName());
                $keyMapIdLocal = $zendConstraint->getSchemaName().'::'.$zendConstraint->getTableName();
                $keyMapIdRemote = $zendConstraint->getReferencedTableSchema().'::'.$zendConstraint->getReferencedTableName();
                $localRelatedModel = $models[$keyMap[$keyMapIdLocal]];
                $remoteRelatedModel = $models[$keyMap[$keyMapIdRemote]];
                //\Kint::dump(array_keys($models), $zendConstraint, $relatedModel);exit;

                /*printf(
                    " > Related > We're generating a \"%s\", which is related to link from \"%s\" to \"%s\".\n",
                    $this->getClassName(),
                    $localRelatedModel->getClassName(),
                    $remoteRelatedModel->getClassName(),
                );*/

                $newRelatedObject = RelatedModel::Factory($this->getLaminator())
                    ->setDatabase($this->getDatabase())
                    ->setLocalRelatedModel($localRelatedModel)
                    ->setRemoteRelatedModel($remoteRelatedModel)
                    ->setSchema($zendConstraint->getReferencedTableSchema())
                    ->setLocalTable($zendConstraint->getTableName())
                    ->setRemoteTable($zendConstraint->getReferencedTableName())
                    ->setBindings(
                        $this->getDatabase()->getName(),
                        $this->sanitiseColumnName($zendConstraint->getColumns()[0]),
                        $this->getLaminator()->schemaName2databaseName($zendConstraint->getReferencedTableSchema()),
                        $this->sanitiseColumnName($zendConstraint->getReferencedColumns()[0])
                    )
                ;
                $this->relatedObjects[] = $newRelatedObject;
            }
            if ('PRIMARY KEY' == $zendConstraint->getType()) {
                $this->setPrimaryKeys($zendConstraint->getColumns());
            }
            if ('UNIQUE' == $zendConstraint->getType()) {
                if ('PermissionGroup' == $this->getClassName()) {
                    foreach ($this->columns as $column) {
                        foreach ($zendConstraint->getColumns() as $affectedColumn) {
                            if ($column->getPropertyName() == $affectedColumn) {
                                $column->setIsUnique(true);
                            }
                        }
                    }
                }
            }
        }

        // Sort related objects into their column objects also
        if (count($this->relatedObjects) > 0) {
            foreach ($this->relatedObjects as $relatedObject) {
                /** @var RelatedModel $relatedObject */
                $localBoundVariable = $this->transStudly2Camel->transform($relatedObject->getLocalBoundColumn());
                //echo "In {$this->getClassName()} column {$localBoundVariable} has a related object called {$relatedObject->getLocalClass()}::{$relatedObject->getRemoteClass()}\n";
                $this->columns[$localBoundVariable]
                    ->addRelatedObject($relatedObject)
                ;
            }
        }

        // Calculate autoincrement fields
        $autoIncrements = $this->getLaminator()->getAutoincrementColumns($this->getDatabase(), $this->getTable());
        $this->setAutoIncrements($autoIncrements);

        // Return a decked-out model
        return $this;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function setDatabase(Database $database)
    {
        $this->database = $database;

        return $this;
    }

    public function getClassName(): string
    {
        if ($this->getClassPrefix()) {
            return
                $this->getClassPrefix().
                $this->transStudly2Studly->transform($this->getTableSanitised());
        }

        return
            $this->transStudly2Studly->transform($this->getTableSanitised());
    }

    /**
     * Get the table name, sanitised by removing any prefixes as per Laminator.yml.
     *
     * @return string
     */
    public function getTableSanitised()
    {
        return $this->getLaminator()->sanitiseTableName($this->getTable(), $this->getDatabase());
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return Model
     */
    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param Model[] $models
     */
    public function scanForRemoteRelations(array &$models): void
    {
        //echo "Scan: {$this->getClassName()}\n";
        foreach ($this->getColumns() as $column) {
            //echo " > {$column->getField()}:\n";
            if (count($column->getRelatedObjects()) > 0) {
                foreach ($column->getRelatedObjects() as $relatedObject) {
                    $remoteObject = clone $relatedObject;

                    /*printf(
                        " > Remote  > We're generating a \"%s\" which is remote to a \"%s\". class prefix is \"%s\"\n",
                        $this->getClassName(),
                        $remoteObject->getRemoteRelatedModel()->getClassName(),
                        $remoteObject->getRemoteClassPrefix()
                    );*/

                    if (!isset($models[$remoteObject->getRemoteClass()])) {
                        \Kint::dump(array_keys($models));
                    }
                    $models[$remoteObject->getRemoteClass()]
                        ->getColumn($remoteObject->getRemoteBoundColumn())
                        ->addRemoteObject($remoteObject)
                    ;
                }
            }
        }
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumn($name): Column
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new BenzineException("Cannot find a Column called {$name} in ".implode(', ', array_keys($this->getColumns())));
    }

    public function hasColumn(string $columName): bool
    {
        if (isset($this->columns[$columName])) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return Model
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @param \Zend\Db\Metadata\Object\ColumnObject[] $columns
     *
     * @return $this
     */
    public function computeColumns(array $columns)
    {
        $autoIncrementColumns = $this->getLaminator()->getAutoincrementColumns($this->getDatabase(), $this->getTable());

        foreach ($columns as $column) {
            /** @var ColumnObject $column */
            $typeFragments = explode(' ', $column->getDataType());
            $dbColumnName = $column->getName();
            $codeColumnName = $this->sanitiseColumnName($column->getName());

            $oColumn = Column::Factory($this->getLaminator())
                ->setModel($this)
                ->setField($codeColumnName)
                ->setDbField($dbColumnName)
                ->setDbType(reset($typeFragments))
                ->setMaxDecimalPlaces($column->getNumericScale())
                ->setIsUnsigned($column->getNumericUnsigned() ?? true)
                ->setDefaultValue($column->getColumnDefault())
            ;

            // Decide on the permitted values
            switch ($this->getDatabase()->getAdapter()->getDriver()->getDatabasePlatformName()) {
                case 'Mysql':
                    $oColumn->setPermittedValues($column->getErrata('permitted_values'));

                    break;
                case 'Postgresql':
                    if ('USER-DEFINED' == $column->getDataType()) {
                        $enumName = explode('::', $column->getColumnDefault(), 2)[1];
                        $permittedValues = [];
                        foreach ($this->getDatabase()->getAdaptor()->query("SELECT unnest(enum_range(NULL::{$enumName})) AS option")->execute() as $aiColumn) {
                            $permittedValues[] = $aiColumn['option'];
                        }
                        $oColumn->setPermittedValues($permittedValues);
                    }

                    break;
                default:
                    throw new BenzineException("Cannot get permitted values for field {$oColumn->getField()} for platform {$this->getDatabase()->getAdapter()->getDriver()->getDatabasePlatformName()}");
            }

            // If this column is in the AutoIncrement list, mark it as such.
            if (in_array($oColumn->getField(), $autoIncrementColumns, true)) {
                $oColumn->setIsAutoIncrement(true);
            }

            // Calculate Max Length for field.
            switch ($column->getDataType()) {
                case 'bigint': // mysql & postgres
                    $oColumn->setMaxFieldLength(9223372036854775807);

                    break;
                case 'int': // mysql
                case 'integer': // postgres
                case 'serial': // postgres
                    $oColumn->setMaxFieldLength(2147483647);

                    break;
                case 'mediumint': // mysql
                    $oColumn->setMaxFieldLength(8388607);

                    break;
                case 'smallint': // mysql & postgres
                    $oColumn->setMaxFieldLength(32767);

                    break;
                case 'tinyint': // mysql
                    $oColumn->setMaxFieldLength(127);

                    break;
                default:
                    $oColumn->setMaxLength($column->getCharacterMaximumLength());
            }

            $this->columns[$oColumn->getPropertyName()] = $oColumn;
        }

        return $this;
    }

    public function getRenderDataset(): array
    {
        return [
            'namespace' => $this->getNamespace(),
            'database' => $this->getDatabase()->getName(),
            'table' => $this->getTable(),
            'app_name' => $this->getLaminator()->getBenzineConfig()->getAppName(),
            //'app_container' => $this->getLaminator()->getBenzineConfig()->getAppContainer(),
            'class_name' => $this->getClassName(),
            'variable_name' => $this->transStudly2Camel->transform($this->getClassName()),
            'name' => $this->getClassName(),
            'object_name_plural' => Inflect::pluralize($this->getClassName()),
            'object_name_singular' => $this->getClassName(),
            'controller_route' => $this->transCamel2Snake->transform(Inflect::pluralize($this->getClassName())),
            'namespace_model' => "{$this->getNamespace()}\\Models\\{$this->getClassName()}Model",
            'columns' => $this->columns,
            'related_objects' => $this->getRelatedObjects(),
            'related_objects_shared' => $this->getRelatedObjectsSharedAssets(),
            'remote_objects' => $this->getRemoteObjects(),
            'primary_keys' => $this->getPrimaryKeys(),
            'primary_parameters' => $this->getPrimaryParameters(),
            'autoincrement_keys' => $this->getAutoIncrements(),
            // @todo: work out why there are two.
            'autoincrement_parameters' => $this->getAutoIncrements(),
        ];
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return RelatedModel[]
     */
    public function getRelatedObjects(): array
    {
        return $this->relatedObjects;
    }

    public function setRelatedObjects(array $relatedObjects): self
    {
        $this->relatedObjects = $relatedObjects;

        return $this;
    }

    public function getRelatedObjectsSharedAssets(): array
    {
        $sharedAssets = [];
        foreach ($this->getRelatedObjects() as $relatedObject) {
            $sharedAssets[$relatedObject->getRemoteClass()] = $relatedObject;
        }
        //if(count($this->getRelatedObjects())) {
        //    \Kint::dump($this->getRelatedObjects(), $sharedAssets);
        //    exit;
        //}
        return $sharedAssets;
    }

    /**
     * @return RelatedModel[]
     */
    public function getRemoteObjects(): array
    {
        $remoteObjects = [];
        foreach ($this->getColumns() as $column) {
            if (count($column->getRemoteObjects()) > 0) {
                foreach ($column->getRemoteObjects() as $remoteObject) {
                    $remoteObjects[] = $remoteObject;
                }
            }
        }

        return $remoteObjects;
    }

    public function getPrimaryKeys(): array
    {
        $primaryKeys = [];
        foreach ($this->primaryKeys as $primaryKey) {
            foreach ($this->getColumns() as $column) {
                if ($column->getDbField() == $primaryKey) {
                    $primaryKeys[$column->getFieldSanitised()] = $column->getDbField();
                }
            }
        }

        return $primaryKeys;
    }

    public function setPrimaryKeys(array $primaryKeys): self
    {
        $this->primaryKeys = $primaryKeys;

        return $this;
    }

    public function getPrimaryParameters(): array
    {
        $parameters = [];
        foreach ($this->getPrimaryKeys() as $primaryKey) {
            foreach ($this->getColumns() as $column) {
                if ($primaryKey == $column->getField()) {
                    $parameters[] = $column->getPropertyFunction();
                }
            }
        }

        return $parameters;
    }

    public function getAutoIncrements()
    {
        $autoincrementKeys = [];
        foreach ($this->autoIncrements as $autoincrementKey) {
            foreach ($this->getColumns() as $column) {
                if ($column->getDbField() == $autoincrementKey) {
                    $autoincrementKeys[$column->getFieldSanitised()] = $column->getDbField();
                }
            }
        }

        return $autoincrementKeys;
    }

    public function setAutoIncrements($autoIncrements): self
    {
        $this->autoIncrements = $autoIncrements;

        return $this;
    }

    public function getClassPrefix(): ?string
    {
        return $this->classPrefix;
    }

    /**
     * When set to null, this class has no prefix.
     */
    public function setClassPrefix(?string $classPrefix): Model
    {
        $this->classPrefix = $classPrefix;

        return $this;
    }

    private function sanitiseColumnName(string $columnName): string
    {
        $database = $this->getDatabase()->getName();

        if (Laminator::BenzineConfig()->has("benzine/databases/{$database}/column_options/_/pre-replace")) {
            $replacements = Laminator::BenzineConfig()->getArray("benzine/databases/{$database}/column_options/_/pre-replace");
            foreach ($replacements as $before => $after) {
                //echo "  > Replacing {$before} with {$after} in {$tableName}\n";
                $columnName = str_replace($before, $after, $columnName);
            }
        }
        if (Laminator::BenzineConfig()->has("benzine/databases/{$database}/column_options/_/transform")) {
            $transform = Laminator::BenzineConfig()->get("benzine/databases/{$database}/column_options/_/transform");
            $columnName = $this->getLaminator()->{$transform}->transform($columnName);
        }
        if (Laminator::BenzineConfig()->has("benzine/databases/{$database}/column_options/_/replace")) {
            $replacements = Laminator::BenzineConfig()->getArray("benzine/databases/{$database}/column_options/_/replace");
            foreach ($replacements as $before => $after) {
                //echo "  > Replacing {$before} with {$after} in {$tableName}\n";
                $columnName = str_replace($before, $after, $columnName);
            }
        }

        return $columnName;
    }
}
