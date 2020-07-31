<?php

namespace Benzine\ORM\Abstracts;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql;

abstract class Service
{
    abstract public function getNewModelInstance();

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
    public function search(Sql\Where $where): array
    {
        $tableGateway = $this->getNewTableGatewayInstance();
        $matches = $tableGateway->select($where);
        $return = [];
        if ($matches instanceof ResultSet) {
            foreach ($matches as $match) {
                $return[] = $match;
            }
        }

        return $return;
    }
}
