<?php

namespace Benzine\ORM\Abstracts;

use Benzine\ORM\TabularData;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql;
use Laminas\Db\Sql\Select;

abstract class Service
{
    abstract public function getNewModelInstance(): Model;

    abstract public function getTermPlural(): string;

    abstract public function getTermSingular(): string;

    abstract public function getNewTableGatewayInstance(): TableGateway;

    /**
     * @param null|array|\Closure[]      $wheres
     * @param null|Sql\Expression|string $order
     *
     * @return Model[]
     */
    public function getAll(
        int $limit = null,
        int $offset = null,
        array $wheres = null,
        $order = null,
        string $orderDirection = null
    ) {
        /** @var TableGateway $tableGateway */
        $tableGateway = $this->getNewTableGatewayInstance();
        list($matches, $count) = $tableGateway->fetchAll(
            $limit,
            $offset,
            $wheres,
            $order,
            null !== $orderDirection ? $orderDirection : Sql\Select::ORDER_ASCENDING
        );
        $return = [];

        if ($matches instanceof ResultSet) {
            foreach ($matches as $match) {
                $return[] = $match;
            }
        }

        return $return;
    }

    /**
     * @param null|string           $distinctColumn
     * @param null|array|\Closure[] $wheres
     *
     * @return Model[]
     */
    public function getDistinct(
        string $distinctColumn,
        array $wheres = null
    ) {
        /** @var TableGateway $tableGateway */
        $tableGateway = $this->getNewTableGatewayInstance();
        list($matches, $count) = $tableGateway->fetchDistinct(
            $distinctColumn,
            $wheres
        );

        $return = [];
        if ($matches instanceof ResultSet) {
            foreach ($matches as $match) {
                $return[] = $match;
            }
        }

        return $return;
    }

    /**
     * @param null|array|\Closure[] $wheres
     *
     * @return int
     */
    public function countAll(
        array $wheres = null
    ) {
        /** @var TableGateway $tableGateway */
        $tableGateway = $this->getNewTableGatewayInstance();

        return $tableGateway->getCount($wheres);
    }

    /**
     * @return Benzine\ORM\Abstracts\Model[]
     */
    public function search(Sql\Where $where, int $limit = null, int $offset = null): \Generator
    {
        $tableGateway = $this->getNewTableGatewayInstance();

        $select = $tableGateway->getSql()->select();
        $select->where($where);
        if ($limit) {
            $select->limit($limit);
        }
        if ($offset) {
            $select->offset($offset);
        }
        $matches = $tableGateway->selectWith($select);
        if ($matches instanceof ResultSet) {
            foreach ($matches as $match) {
                yield $match;
            }
        }
    }

    public function getTabularData(): TabularData\Table
    {
        return new TabularData\Table($this);
    }

    abstract public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Model;

    abstract public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?array;

    abstract public function countByField(string $field, $value): int;

    abstract public function getRandom(): ?Model;
}
