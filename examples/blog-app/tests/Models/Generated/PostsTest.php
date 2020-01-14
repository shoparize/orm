<?php
namespace Example\BlogApp\Test\Models\Generated;

use \Example\BlogApp\TableGateways;
use \Example\BlogApp\TableGateways\PostsTableGateway;
use \Example\BlogApp\Models\PostsModel;
use \Example\BlogApp\Models;
use \Gone\UUID\UUID;
use ⌬\Tests\BaseTestCase;


class PostsTest extends BaseTestCase
{
    /** @var PostsTableGateway */
    protected $testTableGateway;

    /** @var PostsModel */
    protected $testInstance;

    public function setUp()
    {
        parent::setUp();
        $this->testTableGateway = $this->getDIContainer()->get(PostsTableGateway::class);
        $this->testInstance = $this->testTableGateway->getNewMockModelInstance();
    }

    public function testExchangeArray()
    {
        $data = [];
        $data['id'] = self::getFaker()->randomDigitNotNull;
        $data['title'] = self::getFaker()->word;
        $data['content'] = self::getFaker()->word;
        $data['authorId'] = self::getFaker()->randomDigitNotNull;
        $data['createdDate'] = self::getFaker()->word;
        $data['publishedDate'] = self::getFaker()->word;
        $data['deleted'] = self::getFaker()->word;
        $this->testInstance = new PostsModel($data);
        $this->assertEquals($data['id'], $this->testInstance->getId());
        $this->assertEquals($data['title'], $this->testInstance->getTitle());
        $this->assertEquals($data['content'], $this->testInstance->getContent());
        $this->assertEquals($data['authorId'], $this->testInstance->getAuthorId());
        $this->assertEquals($data['createdDate'], $this->testInstance->getCreatedDate());
        $this->assertEquals($data['publishedDate'], $this->testInstance->getPublishedDate());
        $this->assertEquals($data['deleted'], $this->testInstance->getDeleted());
    }

    public function testGetRandom()
    {
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);

        // If there is no data in the table, create some.
        if($table->getCount() == 0){
            $dummyObject = $table->getNewMockModelInstance();
            $table->save($dummyObject);
        }

        $post = $table->fetchRandom();
        $this->assertTrue($post instanceof PostsModel, "Make sure that \"" . get_class($post) . "\" matches \"PostsModel\"") ;

        return $post;
    }

    public function testNewMockModelInstance()
    {
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
        $new = $table->getNewMockModelInstance();

        $this->assertInstanceOf(
            Models\PostsModel::class,
            $new
        );

        $new->save();

        return $new;
    }

    public function testNewModelFactory()
    {
        $instance = PostsModel::factory();

        $this->assertInstanceOf(
            Models\PostsModel::class,
            $instance
        );
    }

    public function testSave()
    {
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
        /** @var Models\PostsModel $mockModel */
        /** @var Models\PostsModel $savedModel */
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
    public function testGetById(PostsModel $posts)
    {
        /** @var postsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
        $results = $table->select(['id' => $posts->getId()]);
        $postsRow = $results->current();
        $this->assertTrue($postsRow instanceof PostsModel);
    }

    /**
     * @depends testGetRandom
     */
    public function testSettersAndGetters(PostsModel $posts)
    {
        $this->assertTrue(method_exists($posts, "getid"));
        $this->assertTrue(method_exists($posts, "setid"));
        $this->assertTrue(method_exists($posts, "gettitle"));
        $this->assertTrue(method_exists($posts, "settitle"));
        $this->assertTrue(method_exists($posts, "getcontent"));
        $this->assertTrue(method_exists($posts, "setcontent"));
        $this->assertTrue(method_exists($posts, "getauthorId"));
        $this->assertTrue(method_exists($posts, "setauthorId"));
        $this->assertTrue(method_exists($posts, "getcreatedDate"));
        $this->assertTrue(method_exists($posts, "setcreatedDate"));
        $this->assertTrue(method_exists($posts, "getpublishedDate"));
        $this->assertTrue(method_exists($posts, "setpublishedDate"));
        $this->assertTrue(method_exists($posts, "getdeleted"));
        $this->assertTrue(method_exists($posts, "setdeleted"));

        $testPosts = new PostsModel();
        $input = self::getFaker()->randomDigitNotNull;
        $testPosts->setId($input);
        $this->assertEquals($input, $testPosts->getid());
        $input = self::getFaker()->word;
        $testPosts->setTitle($input);
        $this->assertEquals($input, $testPosts->gettitle());
        $input = self::getFaker()->word;
        $testPosts->setContent($input);
        $this->assertEquals($input, $testPosts->getcontent());
        $input = self::getFaker()->randomDigitNotNull;
        $testPosts->setAuthorId($input);
        $this->assertEquals($input, $testPosts->getauthorId());
        $input = self::getFaker()->word;
        $testPosts->setCreatedDate($input);
        $this->assertEquals($input, $testPosts->getcreatedDate());
        $input = self::getFaker()->word;
        $testPosts->setPublishedDate($input);
        $this->assertEquals($input, $testPosts->getpublishedDate());
        $input = self::getFaker()->word;
        $testPosts->setDeleted($input);
        $this->assertEquals($input, $testPosts->getdeleted());
    }


    public function testAutoincrementedIdIsApplied()
    {
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
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
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
        /** @var Models\PostsModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertTrue(true, $destroyableModel->destroy());
    }

    public function testDestroyThoroughly()
    {
        /** @var PostsTableGateway $table */
        $table = $this->getDIContainer()->get(PostsTableGateway::class);
        /** @var Models\PostsModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertGreaterThan(0, $destroyableModel->destroyThoroughly());
    }


    /**
     * @depends testNewMockModelInstance
     */
    public function test_RelatedObjects_FetchUserObject(PostsModel $post)
    {
        // Verify the function exists
        $this->assertTrue(method_exists($post, "fetchUserObject"));

        // Call the function on it
        $postModel = $post->fetchUserObject();

        $this->assertInstanceOf(Models\UsersModel::class, $postModel);
    }


    public function testGetPropertyMeta()
    {
        $propertyMeta = $this->testInstance->getPropertyMeta();
        $this->assertTrue(is_array($propertyMeta));
        $this->assertGreaterThan(0, count($propertyMeta));
        $this->assertArrayHasKey('id', $propertyMeta);
        $this->assertArrayHasKey('title', $propertyMeta);
        $this->assertArrayHasKey('content', $propertyMeta);
        $this->assertArrayHasKey('authorId', $propertyMeta);
        $this->assertArrayHasKey('createdDate', $propertyMeta);
        $this->assertArrayHasKey('publishedDate', $propertyMeta);
        $this->assertArrayHasKey('deleted', $propertyMeta);
    }

}
