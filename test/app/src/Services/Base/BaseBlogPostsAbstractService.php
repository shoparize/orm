<?php

namespace Benzine\ORM\Tests\Services\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Where;
use Benzine\ORM\Abstracts\AbstractService as AbstractService;
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
abstract class BaseBlogPostsAbstractService extends AbstractService implements ServiceInterface
{
    // Related Objects Table Gateways
    protected TableGateways\UsersTableGateway $usersTableGateway;

    // Remote Constraints Table Gateways

    // Self Table Gateway
    protected TableGateways\BlogPostsTableGateway $blogPostsTableGateway;

    /**
     * Constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway
     * @param TableGateways\BlogPostsTableGateway $blogPostsTableGateway
     */
    public function __construct(
        TableGateways\UsersTableGateway $usersTableGateway,
        TableGateways\BlogPostsTableGateway $blogPostsTableGateway
    ) {
        $this->usersTableGateway = $usersTableGateway;
        $this->blogPostsTableGateway = $blogPostsTableGateway;
    }

    public function getNewTableGatewayInstance(): TableGateways\BlogPostsTableGateway
    {
        return $this->blogPostsTableGateway;
    }

    public function getNewModelInstance($dataExchange = []): Models\BlogPostsModel
    {
        return $this->blogPostsTableGateway->getNewModelInstance($dataExchange);
    }

    /**
     * @param null|int               $limit
     * @param null|int               $offset
     * @param null|array|\Closure[]  $wheres
     * @param null|Expression|string $order
     * @param null|string            $orderDirection
     *
     * @return Models\BlogPostsModel[]
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
     * @return null|Models\BlogPostsModel
     */
    public function getByField(string $field, $value, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\BlogPostsModel
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->getByField($field, $value, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     * @param $limit int
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return null|Models\BlogPostsModel[]
     */
    public function getManyByField(string $field, $value, int $limit = null, $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?array
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->getManyByField($field, $value, $limit, $orderBy, $orderDirection);
    }

    /**
     * @param string $field
     * @param $value
     *
     * @return int
     */
    public function countByField(string $field, $value): int
    {
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->countByField($field, $value);
    }

    /**
     * @return Models\BlogPostsModel
     */
    public function getRandom(): ?Models\BlogPostsModel
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->fetchRandom();
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     *
     * @return Models\BlogPostsModel
     */
    public function getMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING): ?Models\BlogPostsModel
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->getMatching($keyValue, $orderBy, $orderDirection);
    }

    /**
     * @param array|\Closure|Predicate\PredicateInterface|string|Where $keyValue
     * @param $orderBy string Field to sort by
     * @param $orderDirection string Direction to sort (Select::ORDER_ASCENDING || Select::ORDER_DESCENDING)
     * @param $limit int Limit the number of matches returned
     *
     * @return Models\BlogPostsModel[]
     */
    public function getManyMatching($keyValue = [], $orderBy = null, $orderDirection = Select::ORDER_ASCENDING, int $limit = null): ?array
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->getManyMatching($keyValue, $orderBy, $orderDirection, $limit);
    }

    /**
     * @param $dataExchange
     *
     * @return Models\BlogPostsModel
     */
    public function createFromArray($dataExchange): Models\BlogPostsModel
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();
        $blogPosts = $this->getNewModelInstance($dataExchange);

        return $blogPostsTable->save($blogPosts);
    }


    /**
     * @param string $field
     * @param mixed value
     *
     * @return int
     */
    public function deleteByField(string $field, $value): int
    {
        /** @var TableGateways\BlogPostsTableGateway $blogPostsTable */
        $blogPostsTable = $this->getNewTableGatewayInstance();

        return $blogPostsTable->delete([$field => $value]);
    }

    public function getTermPlural(): string
    {
        return 'BlogPosts';
    }

    public function getTermSingular(): string
    {
        return 'BlogPosts';
    }

    /**
     * Get a version of this object pre-populated with nonsense.
     *
     * @returns Models\BlogPostsModel
     */
    public function getMockObject(): Models\BlogPostsModel
    {
        return $this->getNewTableGatewayInstance()->getNewMockModelInstance();
    }
}
