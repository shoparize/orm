<?php

namespace Benzine\ORM\Tests\Models\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Benzine\ORM\Tests\Services;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Benzine\ORM\Abstracts\Model as AbstractModel;
use Benzine\ORM\Interfaces\ModelInterface as ModelInterface;
use Benzine\App as App;

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
abstract class BaseUsersModel extends AbstractModel implements ModelInterface
{
    // Declare what fields are available on this object
    public const FIELD_USERID = 'userId';
    public const FIELD_NAME = 'name';
    public const FIELD_EMAIL = 'email';
    public const FIELD_CREATED = 'created';

    public const TYPE_USERID = 'int';
    public const TYPE_NAME = 'varchar';
    public const TYPE_EMAIL = 'varchar';
    public const TYPE_CREATED = 'timestamp';

    // Constant arrays defined by ENUMs

    // Constants defined by ENUMs

    protected array $_primary_keys = [
        'userId' => 'userId',
    ];

    protected array $_autoincrement_keys = [
        'userId' => 'userId',
    ];

    // PHPType: int. DBType: int. Max Length: 2147483647.
    protected ?int $userId = null;

    // PHPType: string. DBType: varchar. Max Length: .
    protected ?string $name = null;

    // PHPType: string. DBType: varchar. Max Length: .
    protected ?string $email = null;

    // PHPType: string. DBType: timestamp. Max Length: .
    protected ?string $created = null;


    private Services\BlogPostsService $blogPostsService;

    /** Caching entities **/
    protected array $cache = [];
    protected ?Models\BlogPostsModel $cachedBlogPostObject;


    /**
     * Lazy loading function to get an instance of Services\BlogPostsService
     *
     * @return Services\BlogPostsService
     */
    protected function getBlogPostsService() : Services\BlogPostsService
    {
        if (!isset($this->blogPostsService)){
            $this->blogPostsService = App::DI(Services\BlogPostsService::class);
        }

        return $this->blogPostsService;
    }

    /**
     * @param array $data an array of a Models\UsersModel's properties
     *
     * @return Models\UsersModel
     */
    public static function factory(array $data = [])
    {
        return parent::factory($data);
    }

    /**
     * Returns an array describing the properties of this model.
     *
     * @return array
     */
    public function getPropertyMeta(): array
    {
        return [
            self::FIELD_USERID => [
                'type' => self::TYPE_USERID,
            ],
            self::FIELD_NAME => [
                'type' => self::TYPE_NAME,
                'length' => 32,
            ],
            self::FIELD_EMAIL => [
                'type' => self::TYPE_EMAIL,
                'length' => 320,
            ],
            self::FIELD_CREATED => [
                'type' => self::TYPE_CREATED,
            ],
        ];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return self
     */
    public function setUserId(int $userId = null): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email = null): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    /**
     * @param string $created
     *
     * @return self
     */
    public function setCreated(string $created = null): self
    {
        $this->created = $created;

        return $this;
    }


    /**
     * Fetch a singular BlogPost that references this Models\UsersModel.
     *
     * Local class: BlogPosts
     * Remote class: Users
     *
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\BlogPostsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return Models\BlogPostsModel|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetchBlogPostObject(
        $orderBy = null,
        $orderDirection='ASC'
    ): ?Models\BlogPostsModel {
        if (!isset($this->cachedBlogPostObject)){
            $this->cachedBlogPostObject = $this->getBlogPostsService()
                ->getByField(
                    Models\UsersModel::FIELD_USERID,
                    $this->getUserId(),
                    $orderBy,
                    $orderDirection
                );
        }
        return $this->cachedBlogPostObject;
    }

    /**
     * Fetch all matching BlogPost that reference this Models\UsersModel.
     *
     * Local class: BlogPosts
     * Remote class: Users
     *
     * @param $limit int Number to fetch maximum
     * @param $orderBy string Column to order by. Recommended to use the Constants in Models\BlogPostsModel::
     * @param $orderDirection string Either "DESC" or "ASC". Recommend using Select::ORDER_ASCENDING or Select::ORDER_DESCENDING
     *
     * @return Models\BlogPostsModel[]|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetchBlogPostObjects(
        int $limit = null,
        string $orderBy = null,
        string $orderDirection='ASC'
    ): ?array {
        if (!isset($this->cachedBlogPostObjects)){
            $this->cachedBlogPostObjects = $this
                ->getBlogPostsService()
                    ->getManyByField(
                        Models\BlogPostsModel::FIELD_USERID,
                        $this->getUserId(),
                        $limit,
                        $orderBy,
                        $orderDirection
                    );
        }

        return $this->cachedBlogPostObjects;
    }

    /**
     * Count the number of matching BlogPost that reference this Models\UsersModel.
     * Returns the number of objects found.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function countBlogPostObjects(): int {
        return $this
            ->getBlogPostsService()
                ->countByField(
                    Models\UsersModel::FIELD_USERID,
                    $this->getUserId()
                );
    }
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function save(): Models\UsersModel
    {
        /** @var TableGateways\UsersTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\UsersTableGateway::class);

        return $tableGateway->save($this);
    }

    /**
     * Destroy the current record.
     * Returns the number of affected rows.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroy(): int
    {
        /** @var TableGateways\UsersTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\UsersTableGateway::class);

        return $tableGateway->delete($this->getPrimaryKeys_dbColumns());
    }

    /**
     * Destroy the current record, and any dependencies upon it, recursively.
     * Returns the number of affected rows.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function destroyThoroughly(): int
    {
        return $this->destroy();
    }

    /**
     * Provides an array of all properties in this model.
     *
     * @return string[]
     */
    public function getListOfProperties(): array
    {
        return [
            'userId' => 'userId',
            'name' => 'name',
            'email' => 'email',
            'created' => 'created',
        ];
    }

    /**
     * Take an input array $data, and turn that array into a hydrated object.
     *
     * This has been re-written to be as permissive as possible with loading in data. This at some point will need to
     * be re-re-written as a less messy solution (ie: picking one input field style and sticking with it)
     *
     * @todo re-rewrite this: pick one input field style and stick with it
     *
     * @param array $data dehydated object array
     *
     * @return Models\UsersModel
     */
    public function exchangeArray(array $data): self
    {
        if (isset($data['userId'])) $this->setUserId($data['userId']);
        if (isset($data['userId'])) $this->setUserId($data['userId']);
        if (isset($data['UserId'])) $this->setUserId($data['UserId']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['Name'])) $this->setName($data['Name']);
        if (isset($data['email'])) $this->setEmail($data['email']);
        if (isset($data['email'])) $this->setEmail($data['email']);
        if (isset($data['Email'])) $this->setEmail($data['Email']);
        if (isset($data['created'])) $this->setCreated($data['created']);
        if (isset($data['created'])) $this->setCreated($data['created']);
        if (isset($data['Created'])) $this->setCreated($data['Created']);
        return $this;
    }

}
