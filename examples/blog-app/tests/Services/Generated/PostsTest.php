<?php
namespace Example\BlogApp\Test\Services\Generated;

use \⌬\Config\⌬\⌬ as App;
use \Example\BlogApp\TableGateways\PostsTableGateway;
use \Example\BlogApp\Services\PostsService;
use \Example\BlogApp\Models\PostsModel;
use \Laminas\Db\Sql\Select;
use ⌬\Tests\BaseTestCase;

class PostsTest extends BaseTestCase
{
    /** @var postsService */
    protected $postsService;

    public static function setUpBeforeClass()
    {
        $postsTableGateway = App::Container()->get(PostsTableGateway::class);
        parent::setUpBeforeClass();

        for($i = 0; $i <= 5; $i++){
            $postsTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    public function setUp()
    {
        parent::setUp();

        $this->postsService = App::Container()->get(PostsService::class);
    }

    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            PostsModel::class,
            $this->postsService->getNewModelInstance()
        );
    }

    /**
     * @large
     */
    public function testGetAll()
    {
        $postsService = App::Container()->get(PostsService::class);
        $all = $postsService->getAll();
        $this->assertInstanceOf(
            PostsModel::class,
            reset($all)
        );
    }

    public function testGetRandom()
    {
        $postsService = App::Container()->get(PostsService::class);

        $random = $postsService->getRandom();
        $this->assertInstanceOf(
            PostsModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     */
    public function testGetById(PostsModel $random)
    {
        $postsService = App::Container()->get(PostsService::class);
        $found = $postsService->getById($random->getId());
        $this->assertInstanceOf(
            PostsModel::class,
            $found
        );
        $this->assertEquals($random, $found);
    }

    /**
     * @depends testGetRandom
     */
    public function testCreateFromArray(PostsModel $random)
    {
        $postsService = App::Container()->get(PostsService::class);
        $this->assertInstanceOf(
            PostsModel::class,
            $postsService->createFromArray($random->__ToArray())
        );
    }

    public function testGetMockObject()
    {
        $postsService = App::Container()->get(PostsService::class);
        $this->assertInstanceOf(
            PostsModel::class,
            $postsService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetByField(PostsModel $random)
    {
        $postsService = App::Container()->get(PostsService::class);
        $found = $postsService->getByField('id', $random->getid());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('id') failed to find a PostsModel"
        );
        $found = $postsService->getByField('title', $random->gettitle());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('title') failed to find a PostsModel"
        );
        $found = $postsService->getByField('content', $random->getcontent());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('content') failed to find a PostsModel"
        );
        $found = $postsService->getByField('authorId', $random->getauthorId());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('authorId') failed to find a PostsModel"
        );
        $found = $postsService->getByField('createdDate', $random->getcreatedDate());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('createdDate') failed to find a PostsModel"
        );
        $found = $postsService->getByField('publishedDate', $random->getpublishedDate());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('publishedDate') failed to find a PostsModel"
        );
        $found = $postsService->getByField('deleted', $random->getdeleted());
        $this->assertInstanceOf(
            PostsModel::class,
            $found,
            "Calling PostsService->getByField('deleted') failed to find a PostsModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testCountByField(PostsModel $random)
    {
        $postsService = App::Container()->get(PostsService::class);
        $found = $postsService->countByField('id', $random->getid());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('id') failed to count a PostsModel"
        );
        $found = $postsService->countByField('title', $random->gettitle());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('title') failed to count a PostsModel"
        );
        $found = $postsService->countByField('content', $random->getcontent());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('content') failed to count a PostsModel"
        );
        $found = $postsService->countByField('authorId', $random->getauthorId());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('authorId') failed to count a PostsModel"
        );
        $found = $postsService->countByField('createdDate', $random->getcreatedDate());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('createdDate') failed to count a PostsModel"
        );
        $found = $postsService->countByField('publishedDate', $random->getpublishedDate());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('publishedDate') failed to count a PostsModel"
        );
        $found = $postsService->countByField('deleted', $random->getdeleted());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling PostsService->countByField('deleted') failed to count a PostsModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyByField(PostsModel $random)
    {
        $postsService = App::Container()->get(PostsService::class);
        $found = $postsService->getManyByField('id', $random->getid());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('title', $random->gettitle());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('content', $random->getcontent());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('authorId', $random->getauthorId());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('createdDate', $random->getcreatedDate());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('publishedDate', $random->getpublishedDate());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
        $found = $postsService->getManyByField('deleted', $random->getdeleted());
        $this->assertContainsOnlyInstancesOf(
            PostsModel::class,
            $found
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyMatching(PostsModel $random)
    {
        $all = $this->postsService->getManyMatching([]);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(PostsModel::class, $all);

        $one = $this->postsService->getManyMatching([], null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(PostsModel::class, $one);

        $asc  = $this->postsService->getManyMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->postsService->getManyMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertContainsOnlyInstancesOf(PostsModel::class, $asc);
        $this->assertEquals(count($asc), count($desc));
        $this->assertEquals($asc, array_reverse($desc));

        $keyValue = $this->postsService->getManyMatching(['id' => $random->getid()]);
        $this->assertEquals($random, reset($keyValue));
    }

    /**
     * @depends testGetRandom
     */
    public function testGetMatching(PostsModel $random)
    {
        $all = $this->postsService->getMatching([]);
        $this->assertEquals(postsModel::class, get_class($all));

        $asc  = $this->postsService->getMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->postsService->getMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertEquals(postsModel::class, get_class($asc));
        $this->assertEquals(postsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);

        $keyValue = $this->postsService->getMatching(['id' => $random->getid()]);
        $this->assertEquals($random, $keyValue);
    }

    public function testDeleteById()
    {
        $postsService = App::Container()->get(PostsService::class);
        $postsTableGateway = App::Container()->get(PostsTableGateway::class);

        $deletable = $postsTableGateway
            ->getNewMockModelInstance()
            ->save();

        $this->assertEquals(1, $postsService->deleteById($deletable->getId()));

        return $deletable;
    }

    /**
     * @depends testDeleteById
     */
    public function testDeleteByIdVerify(PostsModel $deleted)
    {
        $postsService = App::Container()->get(PostsService::class);
        $this->assertEquals(null, $postsService->getById($deleted->getId()));
    }

    public function testGetTermPlural()
    {
        $postsService = App::Container()->get(PostsService::class);
        $this->assertNotEmpty($postsService->getTermPlural());
    }

    public function testGetTermSingular()
    {
        $postsService = App::Container()->get(PostsService::class);
        $this->assertNotEmpty($postsService->getTermSingular());
    }
}
