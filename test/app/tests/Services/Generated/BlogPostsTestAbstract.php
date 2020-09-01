<?php

namespace Benzine\ORM\Tests\Test\Services\Generated;

use Benzine\ORM\Tests\Test as App;
use Benzine\ORM\Tests\TableGateways\BlogPostsTableGateway;
use Benzine\ORM\Tests\Services;
use Benzine\ORM\Tests\Models\BlogPostsModel;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Benzine\Tests\AbstractBaseTestCase;

/**
 * @covers \Benzine\ORM\Tests\Models\BlogPostsModel
 * @covers \Benzine\ORM\Tests\Models\Base\BaseBlogPostsAbstractModel
 * @covers \Benzine\ORM\Tests\Services\BlogPostsService
 * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService
 * @covers \Benzine\ORM\Tests\TableGateways\BlogPostsTableGateway
 * @covers \Benzine\ORM\Tests\TableGateways\Base\BaseBlogPostsAbstractTableGateway
 *
 * @group generated
 * @group services
 * @internal
 **/
class BlogPostsTestAbstract extends AbstractBaseTestCase
{
    protected Services\BlogPostsService $blogPostsService;
    protected BlogPostsTableGateway $blogPostsTableGateway;

    /** @var BlogPostsModel[] */
    private static array $MockData = [];

    /**
     * @beforeClass
     */
    public static function setupBlogPostsMockData(): void
    {
        /** @var BlogPostsTableGateway $blogPostsTableGateway */
        $blogPostsTableGateway = App::DI(BlogPostsTableGateway::class);
        for($i = 0; $i <= 5; $i++){
            self::$MockData[] = $blogPostsTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    /**
     * @before
     */
    public function setupBlogPostsService(): void
    {
        $this->blogPostsService = App::DI(Services\BlogPostsService::class);
    }

    /**
     * @before
     */
    public function setupBlogPostsTableGateway(): void
    {
        $this->blogPostsTableGateway = App::DI(BlogPostsTableGateway::class);
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getNewModelInstance
     */
    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $this->blogPostsService->getNewModelInstance()
        );
    }

    /**
     * @large
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getAll
     */
    public function testGetAll()
    {
        $all = $this->blogPostsService->getAll();
        $this->assertInstanceOf(
            BlogPostsModel::class,
            reset($all)
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getRandom
     */
    public function testGetRandom()
    {
        $random = $this->blogPostsService->getRandom();
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getByField
     */
    public function testGetByPrimaryKeys(BlogPostsModel $random)
    {
        /** @var BlogPostsModel $found */
        // By blogPostId
        $found = $this->blogPostsService->getByField(BlogPostsModel::FIELD_BLOGPOSTID, $random->getblogPostId());
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found
        );
        $this->assertEquals($random, $found);
    }

    /**
     * @depends testGetRandom
     */
    public function testCreateFromArray(BlogPostsModel $random)
    {
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $this->blogPostsService->createFromArray($random->__toArray())
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getMockObject
     */
    public function testGetMockObject()
    {
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $this->blogPostsService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getByField
     */
    public function testGetByField(BlogPostsModel $random)
    {
        $found = $this->blogPostsService->getByField(
            BlogPostsModel::FIELD_BLOGPOSTID,
            $random->getBlogPostId()
        );
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found,
            "Calling Services\\BlogPostsService->getByField((BlogPostsModel::FIELD_BLOGPOSTID) failed to find a BlogPostsModel"
        );
        $found = $this->blogPostsService->getByField(
            BlogPostsModel::FIELD_TITLE,
            $random->getTitle()
        );
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found,
            "Calling Services\\BlogPostsService->getByField((BlogPostsModel::FIELD_TITLE) failed to find a BlogPostsModel"
        );
        $found = $this->blogPostsService->getByField(
            BlogPostsModel::FIELD_DESCRIPTION,
            $random->getDescription()
        );
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found,
            "Calling Services\\BlogPostsService->getByField((BlogPostsModel::FIELD_DESCRIPTION) failed to find a BlogPostsModel"
        );
        $found = $this->blogPostsService->getByField(
            BlogPostsModel::FIELD_USERID,
            $random->getUserId()
        );
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found,
            "Calling Services\\BlogPostsService->getByField((BlogPostsModel::FIELD_USERID) failed to find a BlogPostsModel"
        );
        $found = $this->blogPostsService->getByField(
            BlogPostsModel::FIELD_CREATED,
            $random->getCreated()
        );
        $this->assertInstanceOf(
            BlogPostsModel::class,
            $found,
            "Calling Services\\BlogPostsService->getByField((BlogPostsModel::FIELD_CREATED) failed to find a BlogPostsModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::countByField
     */
    public function testCountByField(BlogPostsModel $random)
    {
        $found = $this->blogPostsService->countByField(BlogPostsModel::FIELD_BLOGPOSTID, $random->getBlogPostId());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\BlogPostsService->countByField(BlogPostsModel::FIELD_BLOGPOSTID) failed to count a BlogPostsModel"
        );
        $found = $this->blogPostsService->countByField(BlogPostsModel::FIELD_TITLE, $random->getTitle());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\BlogPostsService->countByField(BlogPostsModel::FIELD_TITLE) failed to count a BlogPostsModel"
        );
        $found = $this->blogPostsService->countByField(BlogPostsModel::FIELD_DESCRIPTION, $random->getDescription());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\BlogPostsService->countByField(BlogPostsModel::FIELD_DESCRIPTION) failed to count a BlogPostsModel"
        );
        $found = $this->blogPostsService->countByField(BlogPostsModel::FIELD_USERID, $random->getUserId());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\BlogPostsService->countByField(BlogPostsModel::FIELD_USERID) failed to count a BlogPostsModel"
        );
        $found = $this->blogPostsService->countByField(BlogPostsModel::FIELD_CREATED, $random->getCreated());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\BlogPostsService->countByField(BlogPostsModel::FIELD_CREATED) failed to count a BlogPostsModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getManyByField
     */
    public function testGetManyByField(BlogPostsModel $random)
    {
        // Testing get by blogPostId
        $this->assertContainsOnlyInstancesOf(
            BlogPostsModel::class,
            $this->blogPostsService->getManyByField(
                BlogPostsModel::FIELD_BLOGPOSTID,
                $random->getblogPostId(),
                5
            )
        );
        // Testing get by title
        $this->assertContainsOnlyInstancesOf(
            BlogPostsModel::class,
            $this->blogPostsService->getManyByField(
                BlogPostsModel::FIELD_TITLE,
                $random->gettitle(),
                5
            )
        );
        // Testing get by description
        $this->assertContainsOnlyInstancesOf(
            BlogPostsModel::class,
            $this->blogPostsService->getManyByField(
                BlogPostsModel::FIELD_DESCRIPTION,
                $random->getdescription(),
                5
            )
        );
        // Testing get by userId
        $this->assertContainsOnlyInstancesOf(
            BlogPostsModel::class,
            $this->blogPostsService->getManyByField(
                BlogPostsModel::FIELD_USERID,
                $random->getuserId(),
                5
            )
        );
        // Testing get by created
        $this->assertContainsOnlyInstancesOf(
            BlogPostsModel::class,
            $this->blogPostsService->getManyByField(
                BlogPostsModel::FIELD_CREATED,
                $random->getcreated(),
                5
            )
        );
    }

    private function getMockDataFilter(): \Closure
    {
        $mockData = self::$MockData;
        return function (Where $where) use ($mockData) {
            $where
                ->nest()
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[0]->getblogPostId())
                    ->or
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[1]->getblogPostId())
                    ->or
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[2]->getblogPostId())
                    ->or
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[3]->getblogPostId())
                    ->or
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[4]->getblogPostId())
                    ->or
                    ->equalTo(BlogPostsModel::FIELD_BLOGPOSTID, $mockData[5]->getblogPostId())
                ->unnest()
                ;
        };
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getManyMatching
     */
    public function testGetManyMatching(BlogPostsModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->blogPostsService->getManyMatching($filter);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(BlogPostsModel::class, $all);

        $one = $this->blogPostsService->getManyMatching($filter, null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(BlogPostsModel::class, $one);

        $asc  = $this->blogPostsService->getMatching($filter, BlogPostsModel::FIELD_BLOGPOSTID, Select::ORDER_ASCENDING);
        $desc = $this->blogPostsService->getMatching($filter, BlogPostsModel::FIELD_BLOGPOSTID, Select::ORDER_DESCENDING);
        $this->assertEquals(blogPostsModel::class, get_class($asc));
        $this->assertEquals(blogPostsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->blogPostsService->getMatching([BlogPostsModel::FIELD_BLOGPOSTID => $random->getblogPostId()]));
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getMatching
     */
    public function testGetMatching(BlogPostsModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->blogPostsService->getMatching($filter);
        $this->assertEquals(blogPostsModel::class, get_class($all));

        $asc  = $this->blogPostsService->getMatching($filter, BlogPostsModel::FIELD_BLOGPOSTID, Select::ORDER_ASCENDING);
        $desc = $this->blogPostsService->getMatching($filter, BlogPostsModel::FIELD_BLOGPOSTID, Select::ORDER_DESCENDING);
        $this->assertEquals(blogPostsModel::class, get_class($asc));
        $this->assertEquals(blogPostsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->blogPostsService->getMatching([BlogPostsModel::FIELD_BLOGPOSTID => $random->getblogPostId()]));
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::deleteByField
     */
    public function testDeleteByField()
    {
        /** @var BlogPostsModel[] $allDeleted */
        $allDeleted = [];
        /** @var BlogPostsModel $deleteable */
        $deleteable = $this->blogPostsTableGateway
            ->getNewMockModelInstance()
            ->save();
        $this->assertEquals(1, $this->blogPostsService->deleteByField(BlogPostsModel::FIELD_BLOGPOSTID, $deleteable->getblogPostId()));
        $allDeleted[] = $deleteable;
        return $allDeleted;
    }

    /**
     * @depends testDeleteByField
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::deleteByField
     * @param BlogPostsModel[] $allDeleted
     */
    public function testDeleteByFieldVerify(array $allDeleted)
    {
        /** @var BlogPostsModel $deleteable */
        // By blogPostId
        $deleteable = array_pop($allDeleted);
        $this->assertEquals(null, $this->blogPostsService->getByField(BlogPostsModel::FIELD_BLOGPOSTID, $deleteable->getblogPostId()));

    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getTermPlural
     */
    public function testGetTermPlural()
    {
        $this->assertNotEmpty($this->blogPostsService->getTermPlural());
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseBlogPostsAbstractService::getTermSingular
     */
    public function testGetTermSingular()
    {
        $this->assertNotEmpty($this->blogPostsService->getTermSingular());
    }
}
