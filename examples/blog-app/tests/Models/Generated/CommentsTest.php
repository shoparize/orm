<?php
namespace Example\BlogApp\Test\Models\Generated;

use \Example\BlogApp\TableGateways;
use \Example\BlogApp\TableGateways\CommentsTableGateway;
use \Example\BlogApp\Models\CommentsModel;
use \Example\BlogApp\Models;
use \Gone\UUID\UUID;
use ⌬\Tests\BaseTestCase;


class CommentsTest extends BaseTestCase
{
    /** @var CommentsTableGateway */
    protected $testTableGateway;

    /** @var CommentsModel */
    protected $testInstance;

    public function setUp()
    {
        parent::setUp();
        $this->testTableGateway = $this->getDIContainer()->get(CommentsTableGateway::class);
        $this->testInstance = $this->testTableGateway->getNewMockModelInstance();
    }

    public function testExchangeArray()
    {
        $data = [];
        $data['id'] = self::getFaker()->randomDigitNotNull;
        $data['comment'] = self::getFaker()->word;
        $data['authorId'] = self::getFaker()->randomDigitNotNull;
        $data['publishedDate'] = self::getFaker()->word;
        $this->testInstance = new CommentsModel($data);
        $this->assertEquals($data['id'], $this->testInstance->getId());
        $this->assertEquals($data['comment'], $this->testInstance->getComment());
        $this->assertEquals($data['authorId'], $this->testInstance->getAuthorId());
        $this->assertEquals($data['publishedDate'], $this->testInstance->getPublishedDate());
    }

    public function testGetRandom()
    {
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);

        // If there is no data in the table, create some.
        if($table->getCount() == 0){
            $dummyObject = $table->getNewMockModelInstance();
            $table->save($dummyObject);
        }

        $comment = $table->fetchRandom();
        $this->assertTrue($comment instanceof CommentsModel, "Make sure that \"" . get_class($comment) . "\" matches \"CommentsModel\"") ;

        return $comment;
    }

    public function testNewMockModelInstance()
    {
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
        $new = $table->getNewMockModelInstance();

        $this->assertInstanceOf(
            Models\CommentsModel::class,
            $new
        );

        $new->save();

        return $new;
    }

    public function testNewModelFactory()
    {
        $instance = CommentsModel::factory();

        $this->assertInstanceOf(
            Models\CommentsModel::class,
            $instance
        );
    }

    public function testSave()
    {
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
        /** @var Models\CommentsModel $mockModel */
        /** @var Models\CommentsModel $savedModel */
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
    public function testGetById(CommentsModel $comments)
    {
        /** @var commentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
        $results = $table->select(['id' => $comments->getId()]);
        $commentsRow = $results->current();
        $this->assertTrue($commentsRow instanceof CommentsModel);
    }

    /**
     * @depends testGetRandom
     */
    public function testSettersAndGetters(CommentsModel $comments)
    {
        $this->assertTrue(method_exists($comments, "getid"));
        $this->assertTrue(method_exists($comments, "setid"));
        $this->assertTrue(method_exists($comments, "getcomment"));
        $this->assertTrue(method_exists($comments, "setcomment"));
        $this->assertTrue(method_exists($comments, "getauthorId"));
        $this->assertTrue(method_exists($comments, "setauthorId"));
        $this->assertTrue(method_exists($comments, "getpublishedDate"));
        $this->assertTrue(method_exists($comments, "setpublishedDate"));

        $testComments = new CommentsModel();
        $input = self::getFaker()->randomDigitNotNull;
        $testComments->setId($input);
        $this->assertEquals($input, $testComments->getid());
        $input = self::getFaker()->word;
        $testComments->setComment($input);
        $this->assertEquals($input, $testComments->getcomment());
        $input = self::getFaker()->randomDigitNotNull;
        $testComments->setAuthorId($input);
        $this->assertEquals($input, $testComments->getauthorId());
        $input = self::getFaker()->word;
        $testComments->setPublishedDate($input);
        $this->assertEquals($input, $testComments->getpublishedDate());
    }


    public function testAutoincrementedIdIsApplied()
    {
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
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
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
        /** @var Models\CommentsModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertTrue(true, $destroyableModel->destroy());
    }

    public function testDestroyThoroughly()
    {
        /** @var CommentsTableGateway $table */
        $table = $this->getDIContainer()->get(CommentsTableGateway::class);
        /** @var Models\CommentsModel $destroyableModel */
        $destroyableModel = $table->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertGreaterThan(0, $destroyableModel->destroyThoroughly());
    }


    /**
     * @depends testNewMockModelInstance
     */
    public function test_RelatedObjects_FetchUserObject(CommentsModel $comment)
    {
        // Verify the function exists
        $this->assertTrue(method_exists($comment, "fetchUserObject"));

        // Call the function on it
        $commentModel = $comment->fetchUserObject();

        $this->assertInstanceOf(Models\UsersModel::class, $commentModel);
    }


    public function testGetPropertyMeta()
    {
        $propertyMeta = $this->testInstance->getPropertyMeta();
        $this->assertTrue(is_array($propertyMeta));
        $this->assertGreaterThan(0, count($propertyMeta));
        $this->assertArrayHasKey('id', $propertyMeta);
        $this->assertArrayHasKey('comment', $propertyMeta);
        $this->assertArrayHasKey('authorId', $propertyMeta);
        $this->assertArrayHasKey('publishedDate', $propertyMeta);
    }

}
