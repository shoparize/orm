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
use \Example\BlogApp\Models\CommentsModel;

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
abstract class BaseCommentsModel
    extends AbstractModel
    implements ModelInterface
{

    // Declare what fields are available on this object
    const FIELD_ID = 'id';
    const FIELD_COMMENT = 'comment';
    const FIELD_AUTHORID = 'authorId';
    const FIELD_PUBLISHEDDATE = 'publishedDate';

    const TYPE_ID = 'int';
    const TYPE_COMMENT = 'text';
    const TYPE_AUTHORID = 'int';
    const TYPE_PUBLISHEDDATE = 'datetime';

    // Constant arrays defined by ENUMs

    // Constants defined by ENUMs

    protected $_primary_keys = ['id'];

    protected $_autoincrement_keys = ['id'];

    protected $id;
    protected $comment;
    protected $authorId;
    protected $publishedDate;

    /**
     * @param array $data An array of a CommentsModel's properties.
     * @return CommentsModel
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
        $authorsService = App::Container()->get(Services\CommentsService::class);

        $properties = [
            self::FIELD_ID => [
                    'type' => self::TYPE_ID,
                    'length' => 10,
                ],
            self::FIELD_COMMENT => [
                    'type' => self::TYPE_COMMENT,
                    'length' => 65535,
                ],
            self::FIELD_AUTHORID => [
                    'type' => self::TYPE_AUTHORID,
                    'length' => 10,
                    'remoteOptionsLoader' => $authorsService->getAll(),
                ],
            self::FIELD_PUBLISHEDDATE => [
                    'type' => self::TYPE_PUBLISHEDDATE,
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
     * @return CommentsModel
     */
    public function setId(int $id = null) : CommentsModel
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment() : ?string    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return CommentsModel
     */
    public function setComment(string $comment = null) : CommentsModel
    {
        $this->comment = $comment;
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
     * @return CommentsModel
     */
    public function setAuthorId(int $authorId = null) : CommentsModel
    {
        $this->authorId = $authorId;
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
     * @return CommentsModel
     */
    public function setPublishedDate(string $publishedDate = null) : CommentsModel
    {
        $this->publishedDate = $publishedDate;
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
     * @return CommentsModel
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save()
    {
        /** @var $tableGateway TableGateways\CommentsTableGateway */
        $tableGateway = App::Container()->get(TableGateways\CommentsTableGateway::class);
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
        /** @var $tableGateway TableGateways\CommentsTableGateway */
        $tableGateway = App::Container()->get(TableGateways\CommentsTableGateway::class);
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
            'comment',
            'authorId',
            'publishedDate',
        ];
    }
}