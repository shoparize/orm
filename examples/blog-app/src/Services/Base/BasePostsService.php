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
abstract class BasePostsService
    extends AbstractService
    implements ServiceInterface
{

    // Related Objects Table Gateways
    /** @var TableGateways\UsersTableGateway */
    protected $usersTableGateway;

    // Remote Constraints Table Gateways

    // Self Table Gateway
    /** @var TableGateways\PostsTableGateway */
    protected $postsTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway
     * @param TableGateways\PostsTableGateway $postsTableGateway
     */
    public function __construct(
        TableGateways\UsersTableGateway $usersTableGateway,
        TableGateways\PostsTableGateway $postsTableGateway
    )
    {
        $this->usersTableGateway = $usersTableGateway;
        $this->postsTableGateway = $postsTableGateway;
    }

    public function getNewTableGatewayInstance() : TableGateways\PostsTableGateway
    {
        return $this->postsTableGateway;
    }
    
    public function getNewModelInstance($dataExchange = []) : Models\PostsModel
    {
        return $this->postsTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|\Closure[]|null $wheres
     * @param string|Expression|null $order
     * @param string|null $orderDirection
     *
     * @return Models\PostsModel[]
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
     * @return Models\PostsModel|null
     */
    public function getById(int $id) : ?Models\PostsModel
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->getById($id);
    }

    /**
     * @param string $field
     * @param $value
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\PostsModel|null
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\PostsModel
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->getByField($field, $value, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @return Models\PostsModel[]|null
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?array
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @return int
     */
    public function countByField(string $field, $value) : int
    {
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->countByField($field, $value);
    }

    /**
     * @return Models\PostsModel
     */
    public function getRandom() : ?Models\PostsModel
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->fetchRandom();
    }


    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @return Models\PostsModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\PostsModel
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     * @return Models\PostsModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null) : ?array
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     * @return array|\ArrayObject|null
     */
    public function createFromArray($dataExchange)
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        $posts = $this->getNewModelInstance($dataExchange);
        return $postsTable->save($posts);
    }

    /**
     * @param int $id
     * @return int
     */
    public function deleteByID($id) : int
    {
        /** @var TableGateways\PostsTableGateway $postsTable */
        $postsTable = $this->getNewTableGatewayInstance();
        return $postsTable->delete(['id' => $id]);
    }

    public function getTermPlural() : string
    {
        return 'Posts';
    }

    public function getTermSingular() : string
    {
        return 'Posts';
    }

    /**
     * @returns Models\PostsModel
     */
    public function getMockObject()
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
