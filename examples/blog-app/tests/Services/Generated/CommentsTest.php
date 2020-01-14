<?php
namespace Example\BlogApp\Test\Services\Generated;

use \⌬\Config\⌬\⌬ as App;
use \Example\BlogApp\TableGateways\CommentsTableGateway;
use \Example\BlogApp\Services\CommentsService;
use \Example\BlogApp\Models\CommentsModel;
use \Laminas\Db\Sql\Select;
use ⌬\Tests\BaseTestCase;

class CommentsTest extends BaseTestCase
{
    /** @var commentsService */
    protected $commentsService;

    public static function setUpBeforeClass()
    {
        $commentsTableGateway = App::Container()->get(CommentsTableGateway::class);
        parent::setUpBeforeClass();

        for($i = 0; $i <= 5; $i++){
            $commentsTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    public function setUp()
    {
        parent::setUp();

        $this->commentsService = App::Container()->get(CommentsService::class);
    }

    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            CommentsModel::class,
            $this->commentsService->getNewModelInstance()
        );
    }

    /**
     * @large
     */
    public function testGetAll()
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $all = $commentsService->getAll();
        $this->assertInstanceOf(
            CommentsModel::class,
            reset($all)
        );
    }

    public function testGetRandom()
    {
        $commentsService = App::Container()->get(CommentsService::class);

        $random = $commentsService->getRandom();
        $this->assertInstanceOf(
            CommentsModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     */
    public function testGetById(CommentsModel $random)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $found = $commentsService->getById($random->getId());
        $this->assertInstanceOf(
            CommentsModel::class,
            $found
        );
        $this->assertEquals($random, $found);
    }

    /**
     * @depends testGetRandom
     */
    public function testCreateFromArray(CommentsModel $random)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $this->assertInstanceOf(
            CommentsModel::class,
            $commentsService->createFromArray($random->__ToArray())
        );
    }

    public function testGetMockObject()
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $this->assertInstanceOf(
            CommentsModel::class,
            $commentsService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetByField(CommentsModel $random)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $found = $commentsService->getByField('id', $random->getid());
        $this->assertInstanceOf(
            CommentsModel::class,
            $found,
            "Calling CommentsService->getByField('id') failed to find a CommentsModel"
        );
        $found = $commentsService->getByField('comment', $random->getcomment());
        $this->assertInstanceOf(
            CommentsModel::class,
            $found,
            "Calling CommentsService->getByField('comment') failed to find a CommentsModel"
        );
        $found = $commentsService->getByField('authorId', $random->getauthorId());
        $this->assertInstanceOf(
            CommentsModel::class,
            $found,
            "Calling CommentsService->getByField('authorId') failed to find a CommentsModel"
        );
        $found = $commentsService->getByField('publishedDate', $random->getpublishedDate());
        $this->assertInstanceOf(
            CommentsModel::class,
            $found,
            "Calling CommentsService->getByField('publishedDate') failed to find a CommentsModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testCountByField(CommentsModel $random)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $found = $commentsService->countByField('id', $random->getid());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling CommentsService->countByField('id') failed to count a CommentsModel"
        );
        $found = $commentsService->countByField('comment', $random->getcomment());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling CommentsService->countByField('comment') failed to count a CommentsModel"
        );
        $found = $commentsService->countByField('authorId', $random->getauthorId());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling CommentsService->countByField('authorId') failed to count a CommentsModel"
        );
        $found = $commentsService->countByField('publishedDate', $random->getpublishedDate());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling CommentsService->countByField('publishedDate') failed to count a CommentsModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyByField(CommentsModel $random)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $found = $commentsService->getManyByField('id', $random->getid());
        $this->assertContainsOnlyInstancesOf(
            CommentsModel::class,
            $found
        );
        $found = $commentsService->getManyByField('comment', $random->getcomment());
        $this->assertContainsOnlyInstancesOf(
            CommentsModel::class,
            $found
        );
        $found = $commentsService->getManyByField('authorId', $random->getauthorId());
        $this->assertContainsOnlyInstancesOf(
            CommentsModel::class,
            $found
        );
        $found = $commentsService->getManyByField('publishedDate', $random->getpublishedDate());
        $this->assertContainsOnlyInstancesOf(
            CommentsModel::class,
            $found
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyMatching(CommentsModel $random)
    {
        $all = $this->commentsService->getManyMatching([]);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(CommentsModel::class, $all);

        $one = $this->commentsService->getManyMatching([], null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(CommentsModel::class, $one);

        $asc  = $this->commentsService->getManyMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->commentsService->getManyMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertContainsOnlyInstancesOf(CommentsModel::class, $asc);
        $this->assertEquals(count($asc), count($desc));
        $this->assertEquals($asc, array_reverse($desc));

        $keyValue = $this->commentsService->getManyMatching(['id' => $random->getid()]);
        $this->assertEquals($random, reset($keyValue));
    }

    /**
     * @depends testGetRandom
     */
    public function testGetMatching(CommentsModel $random)
    {
        $all = $this->commentsService->getMatching([]);
        $this->assertEquals(commentsModel::class, get_class($all));

        $asc  = $this->commentsService->getMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->commentsService->getMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertEquals(commentsModel::class, get_class($asc));
        $this->assertEquals(commentsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);

        $keyValue = $this->commentsService->getMatching(['id' => $random->getid()]);
        $this->assertEquals($random, $keyValue);
    }

    public function testDeleteById()
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $commentsTableGateway = App::Container()->get(CommentsTableGateway::class);

        $deletable = $commentsTableGateway
            ->getNewMockModelInstance()
            ->save();

        $this->assertEquals(1, $commentsService->deleteById($deletable->getId()));

        return $deletable;
    }

    /**
     * @depends testDeleteById
     */
    public function testDeleteByIdVerify(CommentsModel $deleted)
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $this->assertEquals(null, $commentsService->getById($deleted->getId()));
    }

    public function testGetTermPlural()
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $this->assertNotEmpty($commentsService->getTermPlural());
    }

    public function testGetTermSingular()
    {
        $commentsService = App::Container()->get(CommentsService::class);
        $this->assertNotEmpty($commentsService->getTermSingular());
    }
}
