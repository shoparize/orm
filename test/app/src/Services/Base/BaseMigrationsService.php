<?php

namespace Benzine\ORM\Tests\Services\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Where;
use Benzine\ORM\Abstracts\Service as AbstractService;
use Benzine\ORM\Interfaces\ServiceInterface as ServiceInterface;

/**            ___                         __
 *            / _ \___ ____  ___ ____ ____/ /
 *           / // / _ `/ _ \/ _ `/ -_) __/_/
 *          /____/\_,_/_//_/\_, /\__/_/ (_)
 *                         /___/.
 *
 * Anything in this file is prone to being overwritten!
 *
 * This file was programmatically generated. To modify
 * this classes behaviours, do so in the class that
 * extends this, or modify the Laminator Template!
 */
abstract class BaseMigrationsService extends AbstractService implements ServiceInterface
{
    // Related Objects Table Gateways

    // Remote Constraints Table Gateways

    // Self Table Gateway
    protected TableGateways\MigrationsTableGateway $migrationsTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\MigrationsTableGateway $migrationsTableGateway
     */
    public function __construct(
        TableGateways\MigrationsTableGateway $migrationsTableGateway
    ) {
        $this->migrationsTableGateway = $migrationsTableGateway;
    }

    public function getNewTableGatewayInstance(): TableGateways\MigrationsTableGateway
    {
        return $this->migrationsTableGateway;
    }

    public function getNewModelInstance($dataExchange = []): Models\MigrationsModel
    {
        return $this->migrationsTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param null|int               $limit
     * @param null|int               $offset
     * @param null|array|\Closure[]  $wheres
     * @param null|Expression|string $order
     * @param null|string            $orderDirection
     *
     * @return Models\MigrationsModel[]
     */
    public function getAll(
        int $limit = null,
        int $offset = null,
        array $wheres = null,
        $order = null,
        string $orderDirection = null
    ) {
        return parent::getAll(
            $limit,
            $offset,
            $wheres,
            $order,
            $orderDirection
        );
    }


    /**
     * @param string $field
     * @param $value
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|Models\MigrationsModel
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\MigrationsModel
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->getByField($field, $value, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|Models\MigrationsModel[]
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?array
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     *
     * @return int
     */
    public function countByField(string $field, $value): int
    {
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->countByField($field, $value);
    }

    /**
     * @return Models\MigrationsModel
     */
    public function getRandom(): ?Models\MigrationsModel
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->fetchRandom();
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\MigrationsModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\MigrationsModel
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     *
     * @return Models\MigrationsModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null): ?array
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     *
     * @return Models\MigrationsModel
     */
    public function createFromArray($dataExchange): Models\MigrationsModel
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();
        $migrations = $this->getNewModelInstance($dataExchange);

        return $migrationsTable->save($migrations);
    }


    /**
     * @param string $field
     * @param mixed value
     *
     * @return int
     */
    public function deleteByField(string $field, $value): int
    {
        /** @var TableGateways\MigrationsTableGateway $migrationsTable */
        $migrationsTable = $this->getNewTableGatewayInstance();

        return $migrationsTable->delete([$field => $value]);
    }

    public function getTermPlural(): string
    {
        return 'Migrations';
    }

    public function getTermSingular(): string
    {
        return 'Migrations';
    }

    /**
     * Get a version of this object pre-populated with nonsense.
     *
     * @returns Models\MigrationsModel
     */
    public function getMockObject(): Models\MigrationsModel
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
