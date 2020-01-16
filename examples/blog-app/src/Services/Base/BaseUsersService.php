<?php
namespace Example\BlogApp\Services\Base;

use ⌬\Controllers\Abstracts\Service as AbstractService;
use ⌬\Database\Interfaces\ServiceInterface as ServiceInterface;
use \Example\BlogApp\TableGateways;
use \Example\BlogApp\Models;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;

/********************************************************
 *             ___                         __           *
 *            / _ \___ ____  ___ ____ ____/ /           *
 *           / // / _ `/ _ \/ _ `/ -_) __/_/            *
 *          /____/\_,_/_//_/\_, /\__/_/ (_)             *
 *                         /___/                        *
 *                                                      *
 * Anything in this file is prone to being overwritten! *
 *                                                      *
 * This file was programatically generated. To modify   *
 * this classes behaviours, do so in the class that     *
 * extends this, or modify the Laminator Template!     *
 ********************************************************/
// @todo: Make all Services implement a ServicesInterface. - MB
abstract class BaseUsersService
    extends AbstractService
    implements ServiceInterface
{

    // Related Objects Table Gateways

    // Remote Constraints Table Gateways

    // Self Table Gateway
    /** @var TableGateways\UsersTableGateway */
    protected $usersTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway
     */
    public function __construct(
        TableGateways\UsersTableGateway $usersTableGateway
    )
    {
        $this->usersTableGateway = $usersTableGateway;
    }

    public function getNewTableGatewayInstance() : TableGateways\UsersTableGateway
    {
        return $this->usersTableGateway;
    }
    
    public function getNewModelInstance($dataExchange = []) : Models\UsersModel
    {
        return $this->usersTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|\Closure[]|null $wheres
     * @param string|Expression|null $order
     * @param string|null $orderDirection
     *
     * @return Models\UsersModel[]
     */
    public function getAll(
        int $limit = null,
        int $offset = null,
        array $wheres = null,
        $order = null,
        string $orderDirection = null
    )
    {
        return parent::getAll(
            $limit,
            $offset,
            $wheres,
            $order,
            $orderDirection
        );
    }

    /**
     * @param int $id
     * @return Models\UsersModel|null
     */
    public function getById(int $id) : ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->getById($id);
    }

    /**
     * @param string $field
     * @param $value
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\UsersModel|null
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\UsersModel
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
     * @return Models\UsersModel[]|null
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?array
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @return int
     */
    public function countByField(string $field, $value) : int
    {
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->countByField($field, $value);
    }

    /**
     * @return Models\UsersModel
     */
    public function getRandom() : ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->fetchRandom();
    }


    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @return Models\UsersModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     * @return Models\UsersModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null) : ?array
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     * @return array|\ArrayObject|null
     */
    public function createFromArray($dataExchange)
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        $users = $this->getNewModelInstance($dataExchange);
        return $usersTable->save($users);
    }

    /**
     * @param int $id
     * @return int
     */
    public function deleteByID($id) : int
    {
        /** @var TableGateways\UsersTableGateway $usersTable */
        $usersTable = $this->getNewTableGatewayInstance();
        return $usersTable->delete(['id' => $id]);
    }

    public function getTermPlural() : string
    {
        return 'Users';
    }

    public function getTermSingular() : string
    {
        return 'Users';
    }

    /**
     * @returns Models\UsersModel
     */
    public function getMockObject()
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
