<?php
namespace Example\BlogApp\Test\Services\Generated;

use \⌬\Config\⌬\⌬ as App;
use \Example\BlogApp\TableGateways\UsersTableGateway;
use \Example\BlogApp\Services\UsersService;
use \Example\BlogApp\Models\UsersModel;
use \Laminas\Db\Sql\Select;
use ⌬\Tests\BaseTestCase;

class UsersTest extends BaseTestCase
{
    /** @var usersService */
    protected $usersService;

    public static function setUpBeforeClass()
    {
        $usersTableGateway = App::Container()->get(UsersTableGateway::class);
        parent::setUpBeforeClass();

        for($i = 0; $i <= 5; $i++){
            $usersTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    public function setUp()
    {
        parent::setUp();

        $this->usersService = App::Container()->get(UsersService::class);
    }

    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            UsersModel::class,
            $this->usersService->getNewModelInstance()
        );
    }

    /**
     * @large
     */
    public function testGetAll()
    {
        $usersService = App::Container()->get(UsersService::class);
        $all = $usersService->getAll();
        $this->assertInstanceOf(
            UsersModel::class,
            reset($all)
        );
    }

    public function testGetRandom()
    {
        $usersService = App::Container()->get(UsersService::class);

        $random = $usersService->getRandom();
        $this->assertInstanceOf(
            UsersModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     */
    public function testGetById(UsersModel $random)
    {
        $usersService = App::Container()->get(UsersService::class);
        $found = $usersService->getById($random->getId());
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
        $usersService = App::Container()->get(UsersService::class);
        $this->assertInstanceOf(
            UsersModel::class,
            $usersService->createFromArray($random->__ToArray())
        );
    }

    public function testGetMockObject()
    {
        $usersService = App::Container()->get(UsersService::class);
        $this->assertInstanceOf(
            UsersModel::class,
            $usersService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetByField(UsersModel $random)
    {
        $usersService = App::Container()->get(UsersService::class);
        $found = $usersService->getByField('id', $random->getid());
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling UsersService->getByField('id') failed to find a UsersModel"
        );
        $found = $usersService->getByField('displayName', $random->getdisplayName());
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling UsersService->getByField('displayName') failed to find a UsersModel"
        );
        $found = $usersService->getByField('userName', $random->getuserName());
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling UsersService->getByField('userName') failed to find a UsersModel"
        );
        $found = $usersService->getByField('email', $random->getemail());
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling UsersService->getByField('email') failed to find a UsersModel"
        );
        $found = $usersService->getByField('password', $random->getpassword());
        $this->assertInstanceOf(
            UsersModel::class,
            $found,
            "Calling UsersService->getByField('password') failed to find a UsersModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testCountByField(UsersModel $random)
    {
        $usersService = App::Container()->get(UsersService::class);
        $found = $usersService->countByField('id', $random->getid());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling UsersService->countByField('id') failed to count a UsersModel"
        );
        $found = $usersService->countByField('displayName', $random->getdisplayName());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling UsersService->countByField('displayName') failed to count a UsersModel"
        );
        $found = $usersService->countByField('userName', $random->getuserName());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling UsersService->countByField('userName') failed to count a UsersModel"
        );
        $found = $usersService->countByField('email', $random->getemail());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling UsersService->countByField('email') failed to count a UsersModel"
        );
        $found = $usersService->countByField('password', $random->getpassword());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling UsersService->countByField('password') failed to count a UsersModel"
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyByField(UsersModel $random)
    {
        $usersService = App::Container()->get(UsersService::class);
        $found = $usersService->getManyByField('id', $random->getid());
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $found
        );
        $found = $usersService->getManyByField('displayName', $random->getdisplayName());
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $found
        );
        $found = $usersService->getManyByField('userName', $random->getuserName());
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $found
        );
        $found = $usersService->getManyByField('email', $random->getemail());
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $found
        );
        $found = $usersService->getManyByField('password', $random->getpassword());
        $this->assertContainsOnlyInstancesOf(
            UsersModel::class,
            $found
        );
    }

    /**
     * @depends testGetRandom
     */
    public function testGetManyMatching(UsersModel $random)
    {
        $all = $this->usersService->getManyMatching([]);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(UsersModel::class, $all);

        $one = $this->usersService->getManyMatching([], null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(UsersModel::class, $one);

        $asc  = $this->usersService->getManyMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->usersService->getManyMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertContainsOnlyInstancesOf(UsersModel::class, $asc);
        $this->assertEquals(count($asc), count($desc));
        $this->assertEquals($asc, array_reverse($desc));

        $keyValue = $this->usersService->getManyMatching(['id' => $random->getid()]);
        $this->assertEquals($random, reset($keyValue));
    }

    /**
     * @depends testGetRandom
     */
    public function testGetMatching(UsersModel $random)
    {
        $all = $this->usersService->getMatching([]);
        $this->assertEquals(usersModel::class, get_class($all));

        $asc  = $this->usersService->getMatching([], 'id', Select::ORDER_ASCENDING);
        $desc = $this->usersService->getMatching([], 'id', Select::ORDER_DESCENDING);
        $this->assertEquals(usersModel::class, get_class($asc));
        $this->assertEquals(usersModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);

        $keyValue = $this->usersService->getMatching(['id' => $random->getid()]);
        $this->assertEquals($random, $keyValue);
    }

    public function testDeleteById()
    {
        $usersService = App::Container()->get(UsersService::class);
        $usersTableGateway = App::Container()->get(UsersTableGateway::class);

        $deletable = $usersTableGateway
            ->getNewMockModelInstance()
            ->save();

        $this->assertEquals(1, $usersService->deleteById($deletable->getId()));

        return $deletable;
    }

    /**
     * @depends testDeleteById
     */
    public function testDeleteByIdVerify(UsersModel $deleted)
    {
        $usersService = App::Container()->get(UsersService::class);
        $this->assertEquals(null, $usersService->getById($deleted->getId()));
    }

    public function testGetTermPlural()
    {
        $usersService = App::Container()->get(UsersService::class);
        $this->assertNotEmpty($usersService->getTermPlural());
    }

    public function testGetTermSingular()
    {
        $usersService = App::Container()->get(UsersService::class);
        $this->assertNotEmpty($usersService->getTermSingular());
    }
}
