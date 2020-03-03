<?php

namespace ⌬\Database\Components;

use Gone\Inflection\Inflect;
use Laminas\Db\Adapter\Adapter as DbAdaptor;
use Laminas\Db\Metadata\Object\ConstraintObject;
use ⌬\Database\Laminator;

class Model extends Entity
{
    /** @var DbAdaptor */
    protected $dbAdaptor;

    protected ?string $classPrefix = null;
    protected string $namespace;
    protected string $database;
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

    public function getDbAdaptor(): DbAdaptor
    {
        return $this->dbAdaptor;
    }

    public function setDbAdaptor(DbAdaptor $dbAdaptor): self
    {
        $this->dbAdaptor = $dbAdaptor;

        return $this;
    }

    public function setAdaptor(DbAdaptor $dbAdaptor): self
    {
        $this->dbAdaptor = $dbAdaptor;

        return $this;
    }

    /**
     * @param Model[]            $models
     * @param array              $keyMap
     * @param ConstraintObject[] $zendConstraints
     */
    public function computeConstraints(array $models, array $keyMap, array $zendConstraints): self
    {
        //echo "Computing the constraints of {$this->getClassName()}\n";
        foreach ($zendConstraints as $zendConstraint) {
            if ('FOREIGN KEY' == $zendConstraint->getType()) {
                //\Kint::dump($this->getTable(), $this->getClassPrefix(), $zendConstraint->getTableName());
                $keyMapId = $zendConstraint->getReferencedTableSchema().'::'.$zendConstraint->getReferencedTableName();
                $relatedModel = $models[$keyMap[$keyMapId]];
                //\Kint::dump(array_keys($models), $zendConstraint, $relatedModel);exit;

                $newRelatedObject = RelatedModel::Factory($this->getLaminator())
                    ->setClassPrefix($relatedModel->getClassPrefix())
                    ->setSchema($zendConstraint->getReferencedTableSchema())
                    ->setLocalTable($zendConstraint->getTableName())
                    ->setRemoteTable($zendConstraint->getReferencedTableName())
                    ->setBindings(
                        $this->getDatabase(),
                        $this->sanitiseColumnName($zendConstraint->getColumns()[0]),
                        Laminator::schemaName2databaseName($zendConstraint->getReferencedTableSchema()),
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
        $autoIncrements = Laminator::getAutoincrementColumns($this->getAdaptor(), $this->getTable());
        $this->setAutoIncrements($autoIncrements);

        // Return a decked-out model
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return Model
     */
    public function setDatabase(string $database)
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
        return $this->getLaminator()->sanitiseTableName($this->getTable(), $this->database);
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
     * @return DbAdaptor
     */
    public function getAdaptor()
    {
        return $this->dbAdaptor;
    }

    /**
     * @param Model[] $models
     */
    public function scanForRemoteRelations(array &$models)
    {
        //echo "Scan: {$this->getClassName()}\n";
        foreach ($this->getColumns() as $column) {
            //echo " > {$column->getField()}:\n";
            if (count($column->getRelatedObjects()) > 0) {
                foreach ($column->getRelatedObjects() as $relatedObject) {
                    //echo "Processing Related Objects for {$this->getClassName()}'s {$column->getField()}\n\n";
                    //echo "  > r: {$relatedObject->getRemoteClass()} :: {$relatedObject->getRemoteBoundColumn()}\n";
                    //echo "  > l: {$relatedObject->getLocalClass()} :: {$relatedObject->getLocalBoundColumn()}\n";
                    //echo "\n";
                    // @var Model $remoteModel
                    if (!isset($models[$relatedObject->getRemoteClass()])) {
                        \Kint::dump(array_keys($models));
                    }
                    $models[$relatedObject->getRemoteClass()]
                        ->getColumn($relatedObject->getRemoteBoundColumn())
                        ->addRemoteObject($relatedObject)
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
        die("Cannot find a Column called {$name} in ".implode(', ', array_keys($this->getColumns())));
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
     * @return array
     *
     * @todo verify this actually works.
     */
    public function computeAutoIncrementColumns()
    {
        $sql = "SHOW columns FROM `{$this->getTable()}` WHERE extra LIKE '%auto_increment%'";
        $query = $this->getAdaptor()->query($sql);
        $columns = [];

        foreach ($query->execute() as $aiColumn) {
            $columns[] = $aiColumn['Field'];
        }

        return $columns;
    }

    /**
     * @param \Zend\Db\Metadata\Object\ColumnObject[] $columns
     *
     * @return $this
     */
    public function computeColumns(array $columns)
    {
        $autoIncrementColumns = Laminator::getAutoincrementColumns($this->dbAdaptor, $this->getTable());

        foreach ($columns as $column) {
            $typeFragments = explode(' ', $column->getDataType());
            $dbColumnName = $column->getName();
            $codeColumnName = $this->sanitiseColumnName($column->getName());

            $oColumn = Column::Factory($this->getLaminator())
                ->setModel($this)
                ->setField($codeColumnName)
                ->setDbField($dbColumnName)
                ->setDbType(reset($typeFragments))
                ->setPermittedValues($column->getErrata('permitted_values'))
                ->setMaxDecimalPlaces($column->getNumericScale())
                ->setIsUnsigned($column->getNumericUnsigned())
                ->setDefaultValue($column->getColumnDefault())
            ;

            // If this column is in the AutoIncrement list, mark it as such.
            if (in_array($oColumn->getField(), $autoIncrementColumns, true)) {
                $oColumn->setIsAutoIncrement(true);
            }

            // Calculate Max Length for field.
            if (in_array($column->getDataType(), ['int', 'bigint', 'mediumint', 'smallint', 'tinyint'], true)) {
                $oColumn->setMaxLength($column->getNumericPrecision());
            } else {
                $oColumn->setMaxLength($column->getCharacterMaximumLength());
            }

            switch ($column->getDataType()) {
                case 'bigint':
                    $oColumn->setMaxFieldLength(9223372036854775807);

                    break;
                case 'int':
                    $oColumn->setMaxFieldLength(2147483647);

                    break;
                case 'mediumint':
                    $oColumn->setMaxFieldLength(8388607);

                    break;
                case 'smallint':
                    $oColumn->setMaxFieldLength(32767);

                    break;
                case 'tinyint':
                    $oColumn->setMaxFieldLength(127);

                    break;
            }

            $this->columns[$oColumn->getPropertyName()] = $oColumn;
        }

        return $this;
    }

    public function getRenderDataset(): array
    {
        return [
            'namespace' => $this->getNamespace(),
            'database' => $this->getDatabase(),
            'table' => $this->getTable(),
            'app_name' => $this->getLaminator()->getBenzineConfig()->getAppName(),
            'app_container' => $this->getLaminator()->getBenzineConfig()->getAppContainer(),
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

    /**
     * @param string $namespace
     */
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

    /**
     * @return mixed
     */
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

    /**
     * @param mixed $autoIncrements
     */
    public function setAutoIncrements($autoIncrements): self
    {
        $this->autoIncrements = $autoIncrements;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getClassPrefix(): ?string
    {
        return $this->classPrefix;
    }

    /**
     * When set to null, this class has no prefix.
     *
     * @param null|string $classPrefix
     *
     * @return Model
     */
    public function setClassPrefix(?string $classPrefix): Model
    {
        $this->classPrefix = $classPrefix;

        return $this;
    }

    private function sanitiseColumnName(string $columnName): string
    {
        $database = $this->getDatabase();

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
