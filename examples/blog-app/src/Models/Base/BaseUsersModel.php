<?php
namespace Example\BlogApp\Models\Base;
use \⌬\Config\⌬\⌬ as App;
use \Example\BlogApp\Example\BlogApp;
use \Gone\AppCore\Exceptions;
use \Gone\AppCore\Abstracts\Model as AbstractModel;
use \Gone\AppCore\Interfaces\ModelInterface as ModelInterface;
use \Example\BlogApp\Services;
use \Example\BlogApp\Models;
use \Example\BlogApp\TableGateways;
use \Example\BlogApp\Models\UsersModel;

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
abstract class BaseUsersModel
    extends AbstractModel
    implements ModelInterface
{

    // Declare what fields are available on this object
    const FIELD_ID = 'id';
    const FIELD_DISPLAYNAME = 'displayName';
    const FIELD_USERNAME = 'userName';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';

    const TYPE_ID = 'int';
    const TYPE_DISPLAYNAME = 'varchar';
    const TYPE_USERNAME = 'varchar';
    const TYPE_EMAIL = 'varchar';
    const TYPE_PASSWORD = 'varchar';

    // Constant arrays defined by ENUMs

    // Constants defined by ENUMs

    protected $_primary_keys = ['id'];

    protected $_autoincrement_keys = ['id'];

    protected $id;
    protected $displayName;
    protected $userName;
    protected $email;
    protected $password;

    /**
     * @param array $data An array of a UsersModel's properties.
     * @return UsersModel
     */
    static public function factory(array $data = [])
    {
        return parent::factory($data);
    }

    /**
     * @return array
     */
    public function getPropertyMeta()
    {

        $properties = [
            self::FIELD_ID => [
                    'type' => self::TYPE_ID,
                    'length' => 10,
                ],
            self::FIELD_DISPLAYNAME => [
                    'type' => self::TYPE_DISPLAYNAME,
                    'length' => 45,
                ],
            self::FIELD_USERNAME => [
                    'type' => self::TYPE_USERNAME,
                    'length' => 45,
                ],
            self::FIELD_EMAIL => [
                    'type' => self::TYPE_EMAIL,
                    'length' => 320,
                ],
            self::FIELD_PASSWORD => [
                    'type' => self::TYPE_PASSWORD,
                    'length' => 200,
                ],
        ];
        return $properties;
    }

    /**
     * @return int
     */
    public function getId() : ?int    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UsersModel
     */
    public function setId(int $id = null) : UsersModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName() : ?string    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return UsersModel
     */
    public function setDisplayName(string $displayName = null) : UsersModel
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserName() : ?string    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return UsersModel
     */
    public function setUserName(string $userName = null) : UsersModel
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() : ?string    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UsersModel
     */
    public function setEmail(string $email = null) : UsersModel
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() : ?string    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UsersModel
     */
    public function setPassword(string $password = null) : UsersModel
    {
        $this->password = $password;
        return $this;
    }


    /*****************************************************
     * "Referenced To" Remote Constraint Object Fetchers *
     *****************************************************/
    /*****************************************************
     * "Referenced By" Remote Constraint Object Fetchers *
     *****************************************************/

    /**
     * Fetch a singular Post that references this UsersModel.
     *
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\PostsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return null|Models\PostsModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function fetchPostObject(
        $orderBy = null,
        $orderDirection='ASC'
    ) : ?Models\PostsModel {
        /** @var $postsService Services\PostsService */
        $postsService = App::Container()->get(Services\PostsService::class);
        return $postsService->getByField('authorId', $this->getId(), $orderBy, $orderDirection);
    }

    /**
     * Fetch all matching Post that reference this UsersModel.
     *
     * @param $limit int Number to fetch maximum
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\PostsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return Models\PostsModel[]
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function fetchPostObjects(
        int $limit = null,
        string $orderBy = null,
        string $orderDirection='ASC'
    ) : ?array {
        /** @var $postsService Services\PostsService */
        $postsService = App::Container()->get(Services\PostsService::class);
        return $postsService->getManyByField('authorId', $this->getId(), $limit, $orderBy, $orderDirection);
    }

    /**
     * Count the number of matching Post that reference this UsersModel.
     *
     * @return int Number of objects found.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function countPostObjects() : int {
        /** @var $postsService Services\PostsService */
        $postsService = App::Container()->get(Services\PostsService::class);
        return $postsService->countByField('authorId', $this->getId());
    }

    /**
     * Fetch a singular Comment that references this UsersModel.
     *
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\CommentsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return null|Models\CommentsModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function fetchCommentObject(
        $orderBy = null,
        $orderDirection='ASC'
    ) : ?Models\CommentsModel {
        /** @var $commentsService Services\CommentsService */
        $commentsService = App::Container()->get(Services\CommentsService::class);
        return $commentsService->getByField('authorId', $this->getId(), $orderBy, $orderDirection);
    }

    /**
     * Fetch all matching Comment that reference this UsersModel.
     *
     * @param $limit int Number to fetch maximum
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\CommentsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return Models\CommentsModel[]
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function fetchCommentObjects(
        int $limit = null,
        string $orderBy = null,
        string $orderDirection='ASC'
    ) : ?array {
        /** @var $commentsService Services\CommentsService */
        $commentsService = App::Container()->get(Services\CommentsService::class);
        return $commentsService->getManyByField('authorId', $this->getId(), $limit, $orderBy, $orderDirection);
    }

    /**
     * Count the number of matching Comment that reference this UsersModel.
     *
     * @return int Number of objects found.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function countCommentObjects() : int {
        /** @var $commentsService Services\CommentsService */
        $commentsService = App::Container()->get(Services\CommentsService::class);
        return $commentsService->countByField('authorId', $this->getId());
    }

    /**
     * @return UsersModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save()
    {
        /** @var $tableGateway TableGateways\UsersTableGateway */
        $tableGateway = App::Container()->get(TableGateways\UsersTableGateway::class);
        return $tableGateway->save($this);
    }

    /**
     * Destroy the current record.
     *
     * @return int Number of affected rows.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function destroy() : int
    {
        /** @var $tableGateway TableGateways\UsersTableGateway */
        $tableGateway = App::Container()->get(TableGateways\UsersTableGateway::class);
        return $tableGateway->delete($this->getPrimaryKeys());
    }

    /**
     * Destroy the current record, and any dependencies upon it, recursively.
     *
     * @return int Number of affected models.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function destroyThoroughly() : int
    {
        return $this->destroy();
    }


    /**
     * Provides an array of all properties in this model.
     * @return array
     */
    public function getListOfProperties()
    {
        return [
            'id',
            'displayName',
            'userName',
            'email',
            'password',
        ];
    }
}