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
abstract class BaseBlogPostsModel extends AbstractModel implements ModelInterface
{
    // Declare what fields are available on this object
    public const FIELD_BLOGPOSTID = 'blogPostId';
    public const FIELD_TITLE = 'title';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_USERID = 'userId';
    public const FIELD_CREATED = 'created';

    public const TYPE_BLOGPOSTID = 'int';
    public const TYPE_TITLE = 'varchar';
    public const TYPE_DESCRIPTION = 'text';
    public const TYPE_USERID = 'int';
    public const TYPE_CREATED = 'timestamp';

    // Constant arrays defined by ENUMs

    // Constants defined by ENUMs

    protected array $_primary_keys = [
        'blogPostId' => 'blogPostId',
    ];

    protected array $_autoincrement_keys = [
        'blogPostId' => 'blogPostId',
    ];

    // PHPType: int. DBType: int. Max Length: 2147483647.
    protected ?int $blogPostId = null;

    // PHPType: string. DBType: varchar. Max Length: .
    protected ?string $title = null;

    // PHPType: string. DBType: text. Max Length: .
    protected ?string $description = null;

    // PHPType: int. DBType: int. Max Length: 2147483647.
    protected ?int $userId = null;

    // PHPType: string. DBType: timestamp. Max Length: .
    protected ?string $created = null;


    private Services\UsersService $usersService;

    /** Caching entities **/
    protected array $cache = [];
    protected ?Models\UsersModel $cachedUserObject;

    /**
     * Lazy loading function to get an instance of Services\BlogPostsService
     *
     * @return Services\UsersService
     */
    protected function getUsersService() : Services\UsersService
    {
        if (!isset($this->usersService)){
            $this->usersService = App::DI(Services\UsersService::class);
        }

        return $this->usersService;
    }


    /**
     * @param array $data an array of a Models\BlogPostsModel's properties
     *
     * @return Models\BlogPostsModel
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
        $usersService = App::DI(Services\BlogPostsService::class);
        return [
            self::FIELD_BLOGPOSTID => [
                'type' => self::TYPE_BLOGPOSTID,
            ],
            self::FIELD_TITLE => [
                'type' => self::TYPE_TITLE,
                'length' => 64,
            ],
            self::FIELD_DESCRIPTION => [
                'type' => self::TYPE_DESCRIPTION,
                'length' => 65535,
            ],
            self::FIELD_USERID => [
                'type' => self::TYPE_USERID,
                'remoteOptionsLoader' => $usersService->getAll(),
            ],
            self::FIELD_CREATED => [
                'type' => self::TYPE_CREATED,
            ],
        ];
    }

    public function getBlogPostId(): ?int
    {
        return $this->blogPostId;
    }

    /**
     * @param int $blogPostId
     *
     * @return self
     */
    public function setBlogPostId(int $blogPostId = null): self
    {
        $this->blogPostId = $blogPostId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description = null): self
    {
        $this->description = $description;

        return $this;
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetchUserObject(): ?Models\UsersModel
    {
        if (!isset($this->cachedUserObject)){
            $this->cachedUserObject = $this->getUsersService()
                    ->getByField('userId', $this->getUserId());
        }
        return $this->cachedUserObject;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function save(): Models\BlogPostsModel
    {
        /** @var TableGateways\BlogPostsTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\BlogPostsTableGateway::class);

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
        /** @var TableGateways\BlogPostsTableGateway $tableGateway */
        $tableGateway = App::DI(TableGateways\BlogPostsTableGateway::class);

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
            'blogPostId' => 'blogPostId',
            'title' => 'title',
            'description' => 'description',
            'userId' => 'userId',
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
     * @return Models\BlogPostsModel
     */
    public function exchangeArray(array $data): self
    {
        if (isset($data['blogPostId'])) $this->setBlogPostId($data['blogPostId']);
        if (isset($data['blogPostId'])) $this->setBlogPostId($data['blogPostId']);
        if (isset($data['BlogPostId'])) $this->setBlogPostId($data['BlogPostId']);
        if (isset($data['title'])) $this->setTitle($data['title']);
        if (isset($data['title'])) $this->setTitle($data['title']);
        if (isset($data['Title'])) $this->setTitle($data['Title']);
        if (isset($data['description'])) $this->setDescription($data['description']);
        if (isset($data['description'])) $this->setDescription($data['description']);
        if (isset($data['Description'])) $this->setDescription($data['Description']);
        if (isset($data['userId'])) $this->setUserId($data['userId']);
        if (isset($data['userId'])) $this->setUserId($data['userId']);
        if (isset($data['UserId'])) $this->setUserId($data['UserId']);
        if (isset($data['created'])) $this->setCreated($data['created']);
        if (isset($data['created'])) $this->setCreated($data['created']);
        if (isset($data['Created'])) $this->setCreated($data['Created']);
        return $this;
    }

}
