<?php
namespace Example\BlogApp\Models\Base;
use \⌬\Config\⌬\⌬ as App;
use \Example\BlogApp\Example\BlogApp;
use \Gone\AppCore\Exceptions;
use \⌬\Controllers\Abstracts\Model as AbstractModel;
use ⌬\Database\Interfaces\ModelInterface as ModelInterface;
use \Example\BlogApp\Services;
use \Example\BlogApp\Models;
use \Example\BlogApp\TableGateways;
use \Example\BlogApp\Models\PostsModel;

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
abstract class BasePostsModel
    extends AbstractModel
    implements ModelInterface
{

    // Declare what fields are available on this object
    const FIELD_ID = 'id';
    const FIELD_TITLE = 'title';
    const FIELD_CONTENT = 'content';
    const FIELD_AUTHORID = 'authorId';
    const FIELD_CREATEDDATE = 'createdDate';
    const FIELD_PUBLISHEDDATE = 'publishedDate';
    const FIELD_DELETED = 'deleted';

    const TYPE_ID = 'int';
    const TYPE_TITLE = 'text';
    const TYPE_CONTENT = 'text';
    const TYPE_AUTHORID = 'int';
    const TYPE_CREATEDDATE = 'datetime';
    const TYPE_PUBLISHEDDATE = 'datetime';
    const TYPE_DELETED = 'enum';

    // Constant arrays defined by ENUMs
    const OPTIONS_DELETED = ["Yes", "No"];

    // Constants defined by ENUMs
    const DELETED_YES = 'Yes';
    const DELETED_NO = 'No';

    protected $_primary_keys = ['id'];

    protected $_autoincrement_keys = ['id'];

    protected $id;
    protected $title;
    protected $content;
    protected $authorId;
    protected $createdDate;
    protected $publishedDate;
    protected $deleted;

    /**
     * @param array $data An array of a PostsModel's properties.
     * @return PostsModel
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
        $authorsService = App::Container()->get(Services\PostsService::class);

        $properties = [
            self::FIELD_ID => [
                    'type' => self::TYPE_ID,
                    'length' => 10,
                ],
            self::FIELD_TITLE => [
                    'type' => self::TYPE_TITLE,
                    'length' => 65535,
                ],
            self::FIELD_CONTENT => [
                    'type' => self::TYPE_CONTENT,
                    'length' => 65535,
                ],
            self::FIELD_AUTHORID => [
                    'type' => self::TYPE_AUTHORID,
                    'length' => 10,
                    'remoteOptionsLoader' => $authorsService->getAll(),
                ],
            self::FIELD_CREATEDDATE => [
                    'type' => self::TYPE_CREATEDDATE,
                ],
            self::FIELD_PUBLISHEDDATE => [
                    'type' => self::TYPE_PUBLISHEDDATE,
                ],
            self::FIELD_DELETED => [
                    'type' => self::TYPE_DELETED,
                    'length' => 3,
                    'options' => [
                        'Yes',
                        'No',
                    ],
                    'default' => '&#039;No&#039;',
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
     * @return PostsModel
     */
    public function setId(int $id = null) : PostsModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : ?string    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return PostsModel
     */
    public function setTitle(string $title = null) : PostsModel
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : ?string    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return PostsModel
     */
    public function setContent(string $content = null) : PostsModel
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId() : ?int    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     * @return PostsModel
     */
    public function setAuthorId(int $authorId = null) : PostsModel
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedDate() : ?string    {
        return $this->createdDate;
    }

    /**
     * @param string $createdDate
     * @return PostsModel
     */
    public function setCreatedDate(string $createdDate = null) : PostsModel
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublishedDate() : ?string    {
        return $this->publishedDate;
    }

    /**
     * @param string $publishedDate
     * @return PostsModel
     */
    public function setPublishedDate(string $publishedDate = null) : PostsModel
    {
        $this->publishedDate = $publishedDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeleted() : ?string    {
        return $this->deleted;
    }

    /**
     * @param string $deleted
     * @return PostsModel
     */
    public function setDeleted(string $deleted = null) : PostsModel
    {
        $this->deleted = $deleted;
        return $this;
    }


    /*****************************************************
     * "Referenced To" Remote Constraint Object Fetchers *
     *****************************************************/
    /**
     * @return null|Models\UsersModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function fetchUserObject() : ?Models\UsersModel
    {
        /** @var $UsersService Services\UsersService */
        $UsersService = App::Container()->get(Services\UsersService::class);
        return $UsersService->getById($this->getAuthorId());
    }


    /**
     * @return PostsModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save()
    {
        /** @var $tableGateway TableGateways\PostsTableGateway */
        $tableGateway = App::Container()->get(TableGateways\PostsTableGateway::class);
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
        /** @var $tableGateway TableGateways\PostsTableGateway */
        $tableGateway = App::Container()->get(TableGateways\PostsTableGateway::class);
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
            'title',
            'content',
            'authorId',
            'createdDate',
            'publishedDate',
            'deleted',
        ];
    }
}