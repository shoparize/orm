<?php
namespace Example\BlogApp\Services\Base;

use ⌬\Controllers\Abstracts\Service as AbstractService;
use Gone\AppCore\Interfaces\ServiceInterface as ServiceInterface;
use \Example\BlogApp\TableGateways;
use \Example\BlogApp\Models;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

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
 * extends this, or modify the Zenderator Template!     *
 ********************************************************/
// @todo: Make all Services implement a ServicesInterface. - MB
abstract class BaseCommentsService
    extends AbstractService
    implements ServiceInterface
{

    // Related Objects Table Gateways
    /** @var TableGateways\UsersTableGateway */
    protected $usersTableGateway;

    // Remote Constraints Table Gateways

    // Self Table Gateway
    /** @var TableGateways\CommentsTableGateway */
    protected $commentsTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway
     * @param TableGateways\CommentsTableGateway $commentsTableGateway
     */
    public function __construct(
        TableGateways\UsersTableGateway $usersTableGateway,
        TableGateways\CommentsTableGateway $commentsTableGateway
    )
    {
        $this->usersTableGateway = $usersTableGateway;
        $this->commentsTableGateway = $commentsTableGateway;
    }

    public function getNewTableGatewayInstance() : TableGateways\CommentsTableGateway
    {
        return $this->commentsTableGateway;
    }
    
    public function getNewModelInstance($dataExchange = []) : Models\CommentsModel
    {
        return $this->commentsTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|\Closure[]|null $wheres
     * @param string|Expression|null $order
     * @param string|null $orderDirection
     *
     * @return Models\CommentsModel[]
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
     * @return Models\CommentsModel|null
     */
    public function getById(int $id) : ?Models\CommentsModel
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->getById($id);
    }

    /**
     * @param string $field
     * @param $value
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\CommentsModel|null
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\CommentsModel
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->getByField($field, $value, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @return Models\CommentsModel[]|null
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?array
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @return int
     */
    public function countByField(string $field, $value) : int
    {
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->countByField($field, $value);
    }

    /**
     * @return Models\CommentsModel
     */
    public function getRandom() : ?Models\CommentsModel
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->fetchRandom();
    }


    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @return Models\CommentsModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING) : ?Models\CommentsModel
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     * @return Models\CommentsModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null) : ?array
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     * @return array|\ArrayObject|null
     */
    public function createFromArray($dataExchange)
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        $comments = $this->getNewModelInstance($dataExchange);
        return $commentsTable->save($comments);
    }

    /**
     * @param int $id
     * @return int
     */
    public function deleteByID($id) : int
    {
        /** @var TableGateways\CommentsTableGateway $commentsTable */
        $commentsTable = $this->getNewTableGatewayInstance();
        return $commentsTable->delete(['id' => $id]);
    }

    public function getTermPlural() : string
    {
        return 'Comments';
    }

    public function getTermSingular() : string
    {
        return 'Comments';
    }

    /**
     * @returns Models\CommentsModel
     */
    public function getMockObject()
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
