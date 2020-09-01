<?php

namespace Benzine\ORM\Tests\Test\Services\Generated;

use Benzine\ORM\Tests\Test as App;
use Benzine\ORM\Tests\TableGateways\UsersTableGateway;
use Benzine\ORM\Tests\Services;
use Benzine\ORM\Tests\Models\UsersModel;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Benzine\Tests\AbstractBaseTestCase;

/**
 * @covers \Benzine\ORM\Tests\Models\UsersModel
 * @covers \Benzine\ORM\Tests\Models\Base\BaseUsersAbstractModel
 * @covers \Benzine\ORM\Tests\Services\UsersService
 * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService
 * @covers \Benzine\ORM\Tests\TableGateways\UsersTableGateway
 * @covers \Benzine\ORM\Tests\TableGateways\Base\BaseUsersAbstractTableGateway
 *
 * @group generated
 * @group services
 * @internal
 **/
class UsersTestAbstract extends AbstractBaseTestCase
{
    protected Services\UsersService $usersService;
    protected UsersTableGateway $usersTableGateway;

    /** @var UsersModel[] */
    private static array $MockData = [];

    /**
     * @beforeClass
     */
    public static function setupUsersMockData(): void
    {
        /** @var UsersTableGateway $usersTableGateway */
        $usersTableGateway = App::DI(UsersTableGateway::class);
        for($i = 0; $i <= 5; $i++){
            self::$MockData[] = $usersTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    /**
     * @before
     */
    public function setupUsersService(): void
    {
        $this->usersService = App::DI(Services\UsersService::class);
    }

    /**
     * @before
     */
    public function setupUsersTableGateway(): void
    {
        $this->usersTableGateway = App::DI(UsersTableGateway::class);
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getNewModelInstance
     */
    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            UsersModel::class,
            $this->usersService->getNewModelInstance()
        );
    }

    /**
     * @large
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getAll
     */
    public function testGetAll()
    {
        $all = $this->usersService->getAll();
        $this->assertInstanceOf(
            UsersModel::class,
            reset($all)
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getRandom
     */
    public function testGetRandom()
    {
        $random = $this->usersService->getRandom();
        $this->assertInstanceOf(
            UsersModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getByField
     */
    public function testGetByPrimaryKeys(UsersModel $random)
    {
        /** @var UsersModel $found */
        // By userId
        $found = $this->usersService->getByField(UsersModel::FIELD_USERID, $random->getuserId());
        $this->assertInstanceOf(
            UsersModel::class,
            $found
        );
        $this->assertEquals($random, $found);
    }

    /**
     * @depends testGetRandom
     */
    public function testCreateFromArray(UsersModel $random)
    {
        $this->assertInstanceOf(
            UsersModel::class,
            $this->usersService->createFromArray($random->__toArray())
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getMockObject
     */
    public function testGetMockObject()
    {
        $this->assertInstanceOf(
            UsersModel::class,
            $this->usersService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getByField
     */
    public function testGetByField(UsersModel $random)
    {
        $found = $this->usersService->getByField(
            UsersModel::FIELD_USERID,
            $random->getUserId()
        );
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling Services\\UsersService->getByField((UsersModel::FIELD_USERID) failed to find a UsersModel"
        );
        $found = $this->usersService->getByField(
            UsersModel::FIELD_NAME,
            $random->getName()
        );
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling Services\\UsersService->getByField((UsersModel::FIELD_NAME) failed to find a UsersModel"
        );
        $found = $this->usersService->getByField(
            UsersModel::FIELD_EMAIL,
            $random->getEmail()
        );
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling Services\\UsersService->getByField((UsersModel::FIELD_EMAIL) failed to find a UsersModel"
        );
        $found = $this->usersService->getByField(
            UsersModel::FIELD_CREATED,
            $random->getCreated()
        );
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling Services\\UsersService->getByField((UsersModel::FIELD_CREATED) failed to find a UsersModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::countByField
     */
    public function testCountByField(UsersModel $random)
    {
        $found = $this->usersService->countByField(UsersModel::FIELD_USERID, $random->getUserId());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\UsersService->countByField(UsersModel::FIELD_USERID) failed to count a UsersModel"
        );
        $found = $this->usersService->countByField(UsersModel::FIELD_NAME, $random->getName());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\UsersService->countByField(UsersModel::FIELD_NAME) failed to count a UsersModel"
        );
        $found = $this->usersService->countByField(UsersModel::FIELD_EMAIL, $random->getEmail());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\UsersService->countByField(UsersModel::FIELD_EMAIL) failed to count a UsersModel"
        );
        $found = $this->usersService->countByField(UsersModel::FIELD_CREATED, $random->getCreated());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\UsersService->countByField(UsersModel::FIELD_CREATED) failed to count a UsersModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getManyByField
     */
    public function testGetManyByField(UsersModel $random)
    {
        // Testing get by userId
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $this->usersService->getManyByField(
                UsersModel::FIELD_USERID,
                $random->getuserId(),
                5
            )
        );
        // Testing get by name
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $this->usersService->getManyByField(
                UsersModel::FIELD_NAME,
                $random->getname(),
                5
            )
        );
        // Testing get by email
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $this->usersService->getManyByField(
                UsersModel::FIELD_EMAIL,
                $random->getemail(),
                5
            )
        );
        // Testing get by created
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $this->usersService->getManyByField(
                UsersModel::FIELD_CREATED,
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
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[0]->getuserId())
                    ->or
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[1]->getuserId())
                    ->or
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[2]->getuserId())
                    ->or
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[3]->getuserId())
                    ->or
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[4]->getuserId())
                    ->or
                    ->equalTo(UsersModel::FIELD_USERID, $mockData[5]->getuserId())
                ->unnest()
                ;
        };
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getManyMatching
     */
    public function testGetManyMatching(UsersModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->usersService->getManyMatching($filter);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(UsersModel::class, $all);

        $one = $this->usersService->getManyMatching($filter, null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(UsersModel::class, $one);

        $asc  = $this->usersService->getMatching($filter, UsersModel::FIELD_USERID, Select::ORDER_ASCENDING);
        $desc = $this->usersService->getMatching($filter, UsersModel::FIELD_USERID, Select::ORDER_DESCENDING);
        $this->assertEquals(usersModel::class, get_class($asc));
        $this->assertEquals(usersModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->usersService->getMatching([UsersModel::FIELD_USERID => $random->getuserId()]));
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getMatching
     */
    public function testGetMatching(UsersModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->usersService->getMatching($filter);
        $this->assertEquals(usersModel::class, get_class($all));

        $asc  = $this->usersService->getMatching($filter, UsersModel::FIELD_USERID, Select::ORDER_ASCENDING);
        $desc = $this->usersService->getMatching($filter, UsersModel::FIELD_USERID, Select::ORDER_DESCENDING);
        $this->assertEquals(usersModel::class, get_class($asc));
        $this->assertEquals(usersModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->usersService->getMatching([UsersModel::FIELD_USERID => $random->getuserId()]));
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::deleteByField
     */
    public function testDeleteByField()
    {
        /** @var UsersModel[] $allDeleted */
        $allDeleted = [];
        /** @var UsersModel $deleteable */
        $deleteable = $this->usersTableGateway
            ->getNewMockModelInstance()
            ->save();
        $this->assertEquals(1, $this->usersService->deleteByField(UsersModel::FIELD_USERID, $deleteable->getuserId()));
        $allDeleted[] = $deleteable;
        return $allDeleted;
    }

    /**
     * @depends testDeleteByField
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::deleteByField
     * @param UsersModel[] $allDeleted
     */
    public function testDeleteByFieldVerify(array $allDeleted)
    {
        /** @var UsersModel $deleteable */
        // By userId
        $deleteable = array_pop($allDeleted);
        $this->assertEquals(null, $this->usersService->getByField(UsersModel::FIELD_USERID, $deleteable->getuserId()));

    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getTermPlural
     */
    public function testGetTermPlural()
    {
        $this->assertNotEmpty($this->usersService->getTermPlural());
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseUsersAbstractService::getTermSingular
     */
    public function testGetTermSingular()
    {
        $this->assertNotEmpty($this->usersService->getTermSingular());
    }
}
