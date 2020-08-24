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
abstract class BaseUsersService extends AbstractService implements ServiceInterface
{
    // Related Objects Table Gateways

    // Remote Constraints Table Gateways

    // Self Table Gateway
    protected TableGateways\UsersTableGateway $usersTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway
     */
    public function __construct(
        TableGateways\UsersTableGateway $usersTableGateway
    ) {
        $this->usersTableGateway = $usersTableGateway;
    }

    public function getNewTableGatewayInstance(): TableGateways\UsersTableGateway
    {
        return $this->usersTableGateway;
    }

    public function getNewModelInstance($dataExchange = []): Models\UsersModel
    {
        return $this->usersTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param null|int               $limit
     * @param null|int               $offset
     * @param null|array|\Closure[]  $wheres
     * @param null|Expression|string $order
     * @param null|string            $orderDirection
     *
     * @return Models\UsersModel[]
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
     * @return null|Models\UsersModel
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->getByField($field, $value, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|Models\UsersModel[]
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?array
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     *
     * @return int
     */
    public function countByField(string $field, $value): int
    {
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->countByField($field, $value);
    }

    /**
     * @return Models\UsersModel
     */
    public function getRandom(): ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->fetchRandom();
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\UsersModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     *
     * @return Models\UsersModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null): ?array
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     *
     * @return Models\UsersModel
     */
    public function createFromArray($dataExchange): Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        $users = $this->getNewModelInstance($dataExchange);

        return $usersTable->save($users);
    }


    /**
     * @param string $field
     * @param mixed value
     *
     * @return int
     */
    public function deleteByField(string $field, $value): int
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();

        return $usersTable->delete([$field => $value]);
    }

    public function getTermPlural(): string
    {
        return 'Users';
    }

    public function getTermSingular(): string
    {
        return 'Users';
    }

    /**
     * Get a version of this object pre-populated with nonsense.
     *
     * @returns Models\UsersModel
     */
    public function getMockObject(): Models\UsersModel
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
