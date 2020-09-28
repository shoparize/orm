<?php

namespace Benzine\ORM\Abstracts;

use Benzine\Controllers\Filters\FilterCondition;
use Benzine\Exceptions\BenzineException;
use Benzine\Exceptions\DbRuntimeException;
use Benzine\ORM\Interfaces\ModelInterface;
use Benzine\ORM\LaminatorSql;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Predicate\PredicateInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;

abstract class AbstractTableGateway extends TableGateway
{
    protected string $model;
    protected $table;

    public function __construct($table, AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $this->adapter = $adapter;
        $this->table = $table;

        if (!$sql) {
            $sql = new LaminatorSql($this->adapter, $this->table);
        }
        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }

    public function __set($property, $value): void
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        }
    }

    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            throw new BenzineException(sprintf('No such property %s on class %s', $property, get_called_class()));
        }

        return $this->{$property};
    }

    /**
     * @return null|array|\ArrayObject
     */
    public function save(AbstractModel $model)
    {
        // @todo check $model->isDirty() to quick-reject a save operation on a non-dirty record
        $model->__pre_save();

        $pk = $model->getPrimaryKeys_dbColumns();

        $pkIsBlank = true;
        foreach ($pk as $key => $value) {
            if (!is_null($value)) {
                $pkIsBlank = false;
            }
        }

        try {
            /** @var AbstractModel $oldModel */
            $oldModel = $this->select($pk)->current();
            if ($pkIsBlank || !$oldModel) {
                $pk = $this->saveInsert($model);
            } else {
                $this->saveUpdate($model, $oldModel);
            }

            $updatedModel = $this->getByPrimaryKey($pk);

            // Update the primary key fields on the existant $model object, because we may still be referencing this.
            // While it feels a bit yucky to magically mutate the model object, it is expected behaviour.
            foreach ($model->getPrimaryKeys() as $key => $value) {
                $setter = "set{$key}";
                $getter = "get{$key}";
                $model->{$setter}($updatedModel->{$getter}());
            }

            $model->__post_save();

            return $updatedModel;
        } catch (InvalidQueryException $iqe) {
            throw new InvalidQueryException(
                'While trying to call '.get_class().'->save(): ... '.
                $iqe->getMessage()."\n\n".
                substr(var_export($model, true), 0, 1024)."\n\n",
                $iqe->getCode(),
                $iqe
            );
        }
    }

    /**
     * @return null|int
     */
    public function saveInsert(AbstractModel $model)
    {
        switch ($this->getSql()->getAdapter()->getDriver()->getDatabasePlatformName()) {
            case 'Postgresql':
                $data = $model->__toRawArray();
                foreach ($this->getAutoIncrementKeys() as $autoIncrementKey) {
                    unset($data[$autoIncrementKey]);
                }

                break;
            default:
                $data = $model->__toRawArray();
        }
        $this->insert($data);

        if ($model->hasPrimaryKey()) {
            return $model->getPrimaryKeys_dbColumns();
        }

        $pk = [];

        switch ($this->getSql()->getAdapter()->getDriver()->getDatabasePlatformName()) {
            case 'Postgresql':
                foreach ($model->getPrimaryKeys_dbColumns() as $primaryKey => $dontCare) {
                    $sequenceId = sprintf(
                        '"%s_%s_seq"',
                        $this->getTable(),
                        $primaryKey
                    );

                    $pk[$primaryKey] = $this
                        ->getSql()
                        ->getAdapter()
                        ->getDriver()
                        ->getConnection()
                        ->getResource()
                        ->lastInsertId($sequenceId)
                    ;
                }

                break;
            default:
                foreach ($model->getPrimaryKeys_dbColumns() as $primaryKey => $dontCare) {
                    $pk[$primaryKey] = $this->getLastInsertValue();
                }
        }

        return $pk;
    }

    /**
     * @return int
     */
    public function saveUpdate(AbstractModel $model, AbstractModel $oldModel)
    {
        return $this->update(
            $model->__toRawArray(),
            $model->getPrimaryKeys_dbColumns(),
            $oldModel->__toRawArray()
        );
    }

    /**
     * @param array $data
     * @param null  $id
     *
     * @return int
     */
    public function insert($data, &$id = null)
    {
        return parent::insert($data);
    }

    /**
     * @param array               $data
     * @param null                $where
     * @param AbstractModel|array $oldData
     *
     * @return int
     */
    public function update($data, $where = null, $oldData = [])
    {
        $data = array_filter($data);
        //!\Kint::dump($data, $oldData, $where);exit;
        return parent::update($data, $where);
    }

    /**
     * This method is only supposed to be used by getListAction.
     *
     * @param null|int               $limit     Number to limit to
     * @param null|int               $offset    Offset of limit statement. Is ignored if limit not set.
     * @param null|array             $wheres    array of conditions to filter by
     * @param null|Expression|string $order     Column to order on
     * @param null|string            $direction Direction to order on (SELECT::ORDER_ASCENDING|SELECT::ORDER_DESCENDING)
     *
     * @return array [ResultSet,int] Returns an array of resultSet,total_found_rows
     */
    public function fetchAll(
        int $limit = null,
        int $offset = null,
        array $wheres = null,
        $order = null,
        string $direction = Select::ORDER_ASCENDING
    ) {
        /** @var Select $select */
        $select = $this->getSql()->select();

        if (null !== $limit && is_numeric($limit)) {
            $select->limit(intval($limit));
            if (null !== $offset && is_numeric($offset)) {
                $select->offset($offset);
            }
        }
        //\Kint::dump($limit, $offset, $wheres, $order, $direction);
        if (null != $wheres) {
            foreach ($wheres as $conditional) {
                if ($conditional instanceof \Closure) {
                    $select->where($conditional);
                } else {
                    $spec = function (Where $where) use ($conditional): void {
                        switch ($conditional['condition']) {
                            case FilterCondition::CONDITION_EQUAL:
                                $where->equalTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_NOT_EQUAL:
                                $where->notEqualTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_GREATER_THAN:
                                $where->greaterThan($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_GREATER_THAN_OR_EQUAL:
                                $where->greaterThanOrEqualTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LESS_THAN:
                                $where->lessThan($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LESS_THAN_OR_EQUAL:
                                $where->lessThanOrEqualTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LIKE:
                                $where->like($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_NOT_LIKE:
                                $where->notLike($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_IN:
                                $where->in($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_NOT_IN:
                                $where->notIn($conditional['column'], $conditional['value']);

                                break;
                            default:
                                throw new DbRuntimeException("Cannot work out what conditional '{$conditional['condition']}'' is supposed to do in Zend... Probably unimplemented?");
                        }
                    };
                    $select->where($spec);
                }
            }
        }

        if (null !== $order) {
            if ($order instanceof Expression) {
                $select->order($order);
            } else {
                $select->order("{$order} {$direction}");
            }
        }

        $resultSet = $this->selectWith($select);

        $quantifierSelect = $select
            ->reset(Select::LIMIT)
            ->reset(Select::COLUMNS)
            ->reset(Select::OFFSET)
            ->reset(Select::ORDER)
            ->reset(Select::COMBINE)
            ->columns(['total' => new Expression('COUNT(*)')])
        ;

        // execute the select and extract the total
        $row = $this->getSql()
            ->prepareStatementForSqlObject($quantifierSelect)
            ->execute()
            ->current()
        ;
        $total = (int) $row['total'];

        return [$resultSet, $total];
    }

    /**
     * This method is only supposed to be used by getListAction.
     *
     * @param string $distinctColumn column to be distinct on
     * @param array  $wheres         array of conditions to filter by
     *
     * @return array [ResultSet,int] Returns an array of resultSet,total_found_rows
     */
    public function fetchDistinct(
        string $distinctColumn,
        array $wheres = null
    ) {
        /** @var Select $select */
        $select = $this->getSql()->select();
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->columns([$distinctColumn]);

        //\Kint::dump($distinctColumn, $wheres);
        if (null != $wheres) {
            foreach ($wheres as $conditional) {
                if ($conditional instanceof \Closure) {
                    $select->where($conditional);
                } else {
                    $spec = function (Where $where) use ($conditional): void {
                        switch ($conditional['condition']) {
                            case FilterCondition::CONDITION_EQUAL:
                                $where->equalTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_GREATER_THAN:
                                $where->greaterThan($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_GREATER_THAN_OR_EQUAL:
                                $where->greaterThanOrEqualTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LESS_THAN:
                                $where->lessThan($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LESS_THAN_OR_EQUAL:
                                $where->lessThanOrEqualTo($conditional['column'], $conditional['value']);

                                break;
                            case FilterCondition::CONDITION_LIKE:
                                $where->like($conditional['column'], $conditional['value']);

                                break;
                            default:
                                throw new DbRuntimeException("Cannot work out what conditional {$conditional['condition']} is supposed to do in Zend... Probably unimplemented?");
                        }
                    };
                    $select->where($spec);
                }
            }
        }

        $resultSet = $this->selectWith($select);

        $quantifierSelect = $select
            ->reset(Select::LIMIT)
            ->reset(Select::COLUMNS)
            ->reset(Select::OFFSET)
            ->reset(Select::ORDER)
            ->reset(Select::COMBINE)
            ->columns(['total' => new Expression('COUNT(*)')])
        ;

        // execute the select and extract the total
        $row = $this->getSql()
            ->prepareStatementForSqlObject($quantifierSelect)
            ->execute()
            ->current()
        ;
        $total = (int) $row['total'];

        return [$resultSet, $total];
    }

    /**
     * @return null|ModelInterface
     */
    public function fetchRandom()
    {
        $resultSet = $this->select(function (Select $select): void {
            switch ($this->adapter->getDriver()->getDatabasePlatformName()) {
                case 'Mysql':
                    $select->order(new Expression('RAND()'));

                    break;
                case 'Postgresql':
                    $select->order(new Expression('RANDOM()'));

                    break;
                default:
                    throw new BenzineException("Can't fetchRandom for a {$this->adapter->getDriver()->getDatabasePlatformName()} type database!");
            }
            $select->limit(1);
        });

        if (0 == count($resultSet)) {
            return null;
        }

        return $resultSet->current();
    }

    /**
     * @param array|Select $where
     * @param array|string $order
     * @param int          $offset
     *
     * @return null|AbstractModel|array|\ArrayObject
     */
    public function fetchRow($where = null, $order = null, $offset = null)
    {
        if ($where instanceof Select) {
            $resultSet = $this->selectWith($where);
        } else {
            $resultSet = $this->select(function (Select $select) use ($where, $order, $offset): void {
                if (!is_null($where)) {
                    $select->where($where);
                }
                if (!is_null($order)) {
                    $select->order($order);
                }
                if (!is_null($offset)) {
                    $select->offset($offset);
                }
                $select->limit(1);
            });
        }

        return (count($resultSet) > 0) ? $resultSet->current() : null;
    }

    public function getCount($wheres = []): int
    {
        $select = $this->getSql()->select();
        $select->columns(['total' => new Expression('IFNULL(COUNT(*),0)')]);

        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $where) {
                $select->where($where);
            }
        }

        $row = $this->getSql()
            ->prepareStatementForSqlObject($select)
            ->execute()
            ->current()
        ;

        return !is_null($row) ? $row['total'] : 0;
    }

    /**
     * @param PredicateInterface[]|Where[] $wheres
     */
    public function getCountUnique(string $field, $wheres = []): int
    {
        $select = $this->getSql()->select();
        $select->columns(['total' => new Expression('DISTINCT '.$field)]);
        if (count($wheres) > 0) {
            foreach ($wheres as $where) {
                $select->where($where);
            }
        }

        $row = $this->getSql()
            ->prepareStatementForSqlObject($select)
            ->execute()
            ->current()
        ;

        return !is_null($row) ? $row['total'] : 0;
    }

    public function getPrimaryKeys(): array
    {
        /** @var AbstractModel $oModel */
        $oModel = $this->getNewMockModelInstance();

        return array_keys($oModel->getPrimaryKeys());
    }

    public function getAutoIncrementKeys(): array
    {
        /** @var AbstractModel $oModel */
        $oModel = $this->getNewMockModelInstance();

        return array_keys($oModel->getAutoIncrementKeys());
    }

    /**
     * Returns an array of all primary keys on the table keyed by the column.
     */
    public function getHighestPrimaryKey(): array
    {
        $highestPrimaryKeys = [];
        foreach ($this->getPrimaryKeys() as $primaryKey) {
            $Select = $this->getSql()->select();
            $Select->columns(['max' => new Expression("MAX({$primaryKey})")]);
            $row = $this->getSql()
                ->prepareStatementForSqlObject($Select)
                ->execute()
                ->current()
            ;

            $highestPrimaryKey = !is_null($row) ? $row['max'] : 0;
            $highestPrimaryKeys[$primaryKey] = $highestPrimaryKey;
        }

        return $highestPrimaryKeys;
    }

    /**
     * Returns an array of all autoincrement keys on the table keyed by the column.
     */
    public function getHighestAutoincrementKey(): array
    {
        $highestAutoIncrementKeys = [];
        foreach ($this->getPrimaryKeys() as $autoIncrementKey) {
            $Select = $this->getSql()->select();
            $Select->columns(['max' => new Expression("MAX({$autoIncrementKey})")]);
            $row = $this->getSql()
                ->prepareStatementForSqlObject($Select)
                ->execute()
                ->current()
            ;

            $highestAutoIncrementKey = !is_null($row) ? $row['max'] : 0;
            $highestAutoIncrementKeys[$autoIncrementKey] = $highestAutoIncrementKey;
        }

        return $highestAutoIncrementKeys;
    }

    /**
     * @param $id
     *
     * @return null|AbstractModel
     */
    public function getById($id)
    {
        return $this->getByField('id', $id);
    }

    /**
     * @param $field
     * @param $value
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|array|\ArrayObject
     */
    public function getByField($field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING)
    {
        $select = $this->sql->select();

        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }

        $select->where([$field => $value]);
        if ($orderBy) {
            if ($orderBy instanceof Expression) {
                $select->order($orderBy);
            } else {
                $select->order("{$orderBy} {$orderDirection}");
            }
        }
        $select->limit(1);

        $resultSet = $this->selectWith($select);

        $row = $resultSet->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|array|\ArrayObject
     */
    public function getManyByField(string $field, $value, int $limit = null, string $orderBy = null, string $orderDirection = Select::ORDER_ASCENDING): ?array
    {
        $select = $this->sql->select();

        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }

        $select->where([$field => $value]);
        if ($orderBy) {
            if ($orderBy instanceof Expression) {
                $select->order($orderBy);
            } else {
                $select->order("{$orderBy} {$orderDirection}");
            }
        }

        if ($limit) {
            $select->limit($limit);
        }

        $resultSet = $this->selectWith($select);

        $results = [];
        if (0 == $resultSet->count()) {
            return null;
        }
        for ($i = 0; $i < $resultSet->count(); ++$i) {
            $row = $resultSet->current();
            $results[] = $row;
            $resultSet->next();
        }

        return $results;
    }

    public function countByField(string $field, $value): int
    {
        $select = $this->sql->select();
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }
        $select->where([$field => $value]);
        $select->columns([
            new Expression('COUNT(*) as count'),
        ]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $data = $result->current();

        return $data['count'];
    }

    /**
     * @return null|array|\ArrayObject
     */
    public function getByPrimaryKey(array $primaryKeys)
    {
        //\Kint::dump($primaryKeys);
        $row = $this->select($primaryKeys)->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Get single matching object.
     *
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param null                                                     $orderBy
     * @param string                                                   $orderDirection
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING)
    {
        $select = $this->sql->select();
        $select->where($keyValue);
        if ($orderBy) {
            if ($orderBy instanceof Expression) {
                $select->order($orderBy);
            } else {
                $select->order("{$orderBy} {$orderDirection}");
            }
        }
        $select->limit(1);

        $resultSet = $this->selectWith($select);

        $row = $resultSet->current();
        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Get many matching objects.
     *
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param null                                                     $orderBy
     * @param string                                                   $orderDirection
     * @param int                                                      $limit
     *
     * @return null|array|\ArrayObject
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null): ?array
    {
        $select = $this->sql->select();
        $select->where($keyValue);
        if ($orderBy) {
            if ($orderBy instanceof Expression) {
                $select->order($orderBy);
            } else {
                $select->order("{$orderBy} {$orderDirection}");
            }
        }
        if ($limit) {
            $select->limit($limit);
        }
        $resultSet = $this->selectWith($select);

        $results = [];
        if (0 == $resultSet->count()) {
            return null;
        }
        for ($i = 0; $i < $resultSet->count(); ++$i) {
            /** @var AbstractModel $row */
            $row = $resultSet->current();
            if ($row->hasPrimaryKey()) {
                $id = implode('-', $row->getPrimaryKeys());
                $results[$id] = $row;
            } else {
                $results[] = $row;
            }
            $resultSet->next();
        }

        return $results;
    }

    public function getNewModelInstance(array $data = []): AbstractModel
    {
        $model = $this->model;

        return new $model($data);
    }

    /**
     * @return AbstractModel[]
     */
    public function getBySelect(Select $select): array
    {
        $resultSet = $this->executeSelect($select);
        $return = [];
        foreach ($resultSet as $result) {
            $return[] = $result;
        }

        return $return;
    }

    /**
     * @return AbstractModel[]
     */
    public function getBySelectRaw(Select $select): array
    {
        $resultSet = $this->executeSelect($select);
        $return = [];
        while ($result = $resultSet->getDataSource()->current()) {
            $return[] = $result;
            $resultSet->getDataSource()->next();
        }

        return $return;
    }

    abstract public function getNewMockModelInstance(): ModelInterface;

    protected function getModelName(): string
    {
        $modelName = explode('\\', $this->model);
        $modelName = end($modelName);

        return str_replace('Model', '', $modelName);
    }
}
