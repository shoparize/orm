<?php
namespace Example\BlogApp\Test\Models\Generated;

use \Example\BlogApp\TableGateways;
use \Example\BlogApp\TableGateways\UsersTableGateway;
use \Example\BlogApp\Models\UsersModel;
use \Example\BlogApp\Models;
use \Gone\UUID\UUID;


class UsersTest extends \Gone\AppCore\Test\BaseTestCase
{
    /** @var UsersTableGateway */
    protected $testTableGateway;

    /** @var UsersModel */
    protected $testInstance;

    public function setUp()
    {
        parent::setUp();
        $this->testTableGateway = $this->getDIContainer()->get(UsersTableGateway::class);
        $this->testInstance = $this->testTableGateway->getNewMockModelInstance();
    }

    public function testExchangeArray()
    {
        $data = [];
        $data['id'] = self::getFaker()->randomDigitNotNull;
        $data['displayName'] = self::getFaker()->word;
        $data['userName'] = self::getFaker()->word;
        $data['email'] = self::getFaker()->word;
        $data['password'] = self::getFaker()->word;
        $this->testInstance = new UsersModel($data);
        $this->assertEquals($data['id'], $this->testInstance->getId());
        $this->assertEquals($data['displayName'], $this->testInstance->getDisplayName());
        $this->assertEquals($data['userName'], $this->testInstance->getUserName());
        $this->assertEquals($data['email'], $this->testInstance->getEmail());
        $this->assertEquals($data['password'], $this->testInstance->getPassword());
    }

    public function testGetRandom()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);

        // If there is no data in the table, create some.
        if($table->getCount() == 0){
            $dummyObject = $table->getNewMockModelInstance();
            $table->save($dummyObject);
        }

        $user = $table->fetchRandom();
        $this->assertTrue($user instanceof UsersModel, "Make sure that \"" . get_class($user) . "\" matches \"UsersModel\"") ;

        return $user;
    }

    public function testNewMockModelInstance()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        $new = $table->getNewMockModelInstance();

        $this->assertInstanceOf(
            Models\UsersModel::class,
            $new
        );

        $new->save();

        return $new;
    }

    public function testNewModelFactory()
    {
        $instance = UsersModel::factory();

        $this->assertInstanceOf(
            Models\UsersModel::class,
            $instance
        );
    }

    public function testSave()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        /** @var Models\UsersModel $mockModel */
        /** @var Models\UsersModel $savedModel */
        $mockModel = $table->getNewMockModelInstance();
        $savedModel = $mockModel->save();

        $mockModelArray = $mockModel->__toArray();
        $savedModelArray = $savedModel->__toArray();

        // Remove auto increments from test.
        foreach($mockModel->getAutoIncrementKeys() as $autoIncrementKey => $discard){
            foreach($mockModelArray as $key => $value){
                if(strtolower($key) == strtolower($autoIncrementKey)){
                    unset($mockModelArray[$key]);
                    unset($savedModelArray[$key]);
                }
            }
        }


        $this->assertEquals($mockModelArray, $savedModelArray);
    }

    /**
     * @depends testGetRandom
     */
    public function testGetById(UsersModel $users)
    {
        /** @var usersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        $results = $table->select(['id' => $users->getId()]);
        $usersRow = $results->current();
        $this->assertTrue($usersRow instanceof UsersModel);
    }

    /**
     * @depends testGetRandom
     */
    public function testSettersAndGetters(UsersModel $users)
    {
        $this->assertTrue(method_exists($users, "getid"));
        $this->assertTrue(method_exists($users, "setid"));
        $this->assertTrue(method_exists($users, "getdisplayName"));
        $this->assertTrue(method_exists($users, "setdisplayName"));
        $this->assertTrue(method_exists($users, "getuserName"));
        $this->assertTrue(method_exists($users, "setuserName"));
        $this->assertTrue(method_exists($users, "getemail"));
        $this->assertTrue(method_exists($users, "setemail"));
        $this->assertTrue(method_exists($users, "getpassword"));
        $this->assertTrue(method_exists($users, "setpassword"));

        $testUsers = new UsersModel();
        $input = self::getFaker()->randomDigitNotNull;
        $testUsers->setId($input);
        $this->assertEquals($input, $testUsers->getid());
        $input = self::getFaker()->word;
        $testUsers->setDisplayName($input);
        $this->assertEquals($input, $testUsers->getdisplayName());
        $input = self::getFaker()->word;
        $testUsers->setUserName($input);
        $this->assertEquals($input, $testUsers->getuserName());
        $input = self::getFaker()->word;
        $testUsers->setEmail($input);
        $this->assertEquals($input, $testUsers->getemail());
        $input = self::getFaker()->word;
        $testUsers->setPassword($input);
        $this->assertEquals($input, $testUsers->getpassword());
    }


    public function testAutoincrementedIdIsApplied()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        $new = $table->getNewMockModelInstance();

        // Set primary keys to null.
        $new->setid(null);

        // Save the object
        $new->save();

        // verify that the AI keys have been set.
        $this->assertNotNull($new->getId());
    }

    public function testDestroy()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        /** @var Models\UsersModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertTrue(true, $destroyableModel->destroy());
    }

    public function testDestroyThoroughly()
    {
        /** @var UsersTableGateway $table */
        $table = $this->getDIContainer()->get(UsersTableGateway::class);
        /** @var Models\UsersModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertGreaterThan(0, $destroyableModel->destroyThoroughly());
    }


    /**
     * @depends testNewMockModelInstance
     */
    public function test_RemoteObjects_FetchPostObjects(UsersModel $user)
    {
        // Verify the function exists
        $this->assertTrue(method_exists($user, "fetchPostObjects"));

        /** @var TableGateways\PostsTableGateway $tableGateway */
        $tableGateway = $this->getDIContainer()->get(TableGateways\PostsTableGateway::class);

        $user->save();

        $this->assertNotNull($user->getId());

        /** @var Models\PostsModel $newPostsModel */
        $newPostsModel = $tableGateway->getNewMockModelInstance();
        $newPostsModel->setAuthorId($user->getId());

        // Alas, some non-generic business logic has snuck in here.
        // If this model has a field called UUID, make it an actual UUID
        if(method_exists($newPostsModel, 'setUuid')) {
            $newPostsModel->setUuid(UUID::v4());
        }

        // If this model has a 'deleted' column, set it to no.
        if(method_exists($newPostsModel, 'setDeleted')) {
            $newPostsModel->setDeleted(Models\PostsModel::DELETED_NO);
        }

        // Save our new model. Make offerings to the gods of phpunit & transaction rollback to clean it up afterwards.
        $newPostsModel->save();
        $this->assertNotNull($newPostsModel->getAuthorId());

        // Call the singular function on it
        $postsModel = $user->fetchPostObject();

        $this->assertInstanceOf(Models\PostsModel::class, $postsModel);

        // Call the plural function on it
        $postModels = $user->fetchPostObjects();

        $this->assertGreaterThanOrEqual(1, count($postModels), "fetchPostObjects() failed to return atleast 1 Models\PostsModel.");
        $this->assertContainsOnlyInstancesOf(Models\PostsModel::class, $postModels);

        return [$user, $postModels];
    }

    /**
     * @depends test_RemoteObjects_FetchPostObjects
     */
    public function test_RemoteObjects_CountPostObjects($list)
    {
        /**
         * @var $user Models\UsersModel
         * @var $postModels Models\PostsModel[]
         */
        list($user, $postModels) = $list;

        // Verify the function exists
        $this->assertTrue(method_exists($user, "countPostObjects"));

        // Call the function on it
        $count = $user->countPostObjects();

        $this->assertCount($count, $postModels);
    }

    /**
     * @depends testNewMockModelInstance
     */
    public function test_RemoteObjects_FetchCommentObjects(UsersModel $user)
    {
        // Verify the function exists
        $this->assertTrue(method_exists($user, "fetchCommentObjects"));

        /** @var TableGateways\CommentsTableGateway $tableGateway */
        $tableGateway = $this->getDIContainer()->get(TableGateways\CommentsTableGateway::class);

        $user->save();

        $this->assertNotNull($user->getId());

        /** @var Models\CommentsModel $newCommentsModel */
        $newCommentsModel = $tableGateway->getNewMockModelInstance();
        $newCommentsModel->setAuthorId($user->getId());

        // Alas, some non-generic business logic has snuck in here.
        // If this model has a field called UUID, make it an actual UUID
        if(method_exists($newCommentsModel, 'setUuid')) {
            $newCommentsModel->setUuid(UUID::v4());
        }

        // If this model has a 'deleted' column, set it to no.
        if(method_exists($newCommentsModel, 'setDeleted')) {
            $newCommentsModel->setDeleted(Models\CommentsModel::DELETED_NO);
        }

        // Save our new model. Make offerings to the gods of phpunit & transaction rollback to clean it up afterwards.
        $newCommentsModel->save();
        $this->assertNotNull($newCommentsModel->getAuthorId());

        // Call the singular function on it
        $commentsModel = $user->fetchCommentObject();

        $this->assertInstanceOf(Models\CommentsModel::class, $commentsModel);

        // Call the plural function on it
        $commentModels = $user->fetchCommentObjects();

        $this->assertGreaterThanOrEqual(1, count($commentModels), "fetchCommentObjects() failed to return atleast 1 Models\CommentsModel.");
        $this->assertContainsOnlyInstancesOf(Models\CommentsModel::class, $commentModels);

        return [$user, $commentModels];
    }

    /**
     * @depends test_RemoteObjects_FetchCommentObjects
     */
    public function test_RemoteObjects_CountCommentObjects($list)
    {
        /**
         * @var $user Models\UsersModel
         * @var $commentModels Models\CommentsModel[]
         */
        list($user, $commentModels) = $list;

        // Verify the function exists
        $this->assertTrue(method_exists($user, "countCommentObjects"));

        // Call the function on it
        $count = $user->countCommentObjects();

        $this->assertCount($count, $commentModels);
    }

    public function testGetPropertyMeta()
    {
        $propertyMeta = $this->testInstance->getPropertyMeta();
        $this->assertTrue(is_array($propertyMeta));
        $this->assertGreaterThan(0, count($propertyMeta));
        $this->assertArrayHasKey('id', $propertyMeta);
        $this->assertArrayHasKey('displayName', $propertyMeta);
        $this->assertArrayHasKey('userName', $propertyMeta);
        $this->assertArrayHasKey('email', $propertyMeta);
        $this->assertArrayHasKey('password', $propertyMeta);
    }

}
