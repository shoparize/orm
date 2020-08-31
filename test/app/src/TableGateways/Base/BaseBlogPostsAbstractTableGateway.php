<?php

namespace Benzine\ORM\Tests\TableGateways\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Faker\Generator;
use Laminas\Db\ResultSet\ResultSet;
use Benzine\ORM\Abstracts\AbstractModel;
use Benzine\ORM\Abstracts\AbstractTableGateway as AbstractTableGateway;
use Benzine\ORM\Connection;
use Benzine\ORM\Interfaces\TableGatewayInterface as TableGatewayInterface;
use Benzine\Exceptions\BenzineException;

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
abstract class BaseBlogPostsAbstractTableGateway extends AbstractTableGateway implements TableGatewayInterface
{
    protected $table = 'BlogPosts';
    protected string $model = Models\BlogPostsModel::class;
    protected Generator $faker;
    protected Connection\Databases $databaseConnection;
    protected Connection\Database $database;

    protected TableGateways\UsersTableGateway $usersTableGateway;

    /**
     * AbstractTableGateway constructor.
     *
     * @param TableGateways\UsersTableGateway $usersTableGateway,
     * @param Generator $faker
     * @param Connection\Databases  $databaseConnection
     *
     * @throws BenzineException
     */
    public function __construct(
                TableGateways\UsersTableGateway $usersTableGateway,
                Generator $faker,
        Connection\Databases $databaseConnection
    ) {
        $this->usersTableGateway = $usersTableGateway;
        $this->faker = $faker;
        $this->databaseConnection = $databaseConnection;
        $this->database = $this->databaseConnection->getDatabase('default');

        $resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAYOBJECT, new $this->model());

        parent::__construct($this->table, $this->database->getAdapter(), null, $resultSetPrototype);
    }

    /**
     * @return Models\BlogPostsModel
     */
    public function getNewMockModelInstance()
    {
      // Generate a Random Users.
      $randomUsers = $this->usersTableGateway->fetchRandom();

        return $this->getNewModelInstance([
            // blogPostId. Type = int. PHPType = int. Has no related objects. Default is literal null
            // created. Type = timestamp. PHPType = \DateTime. Has no related objects. Default is interpreted current_timestamp()
            'created' => $this->faker->word,
            // description. Type = text. PHPType = string. Has no related objects. Default is literal null
            'description' => substr($this->faker->text(500), 0, 500),
            // title. Type = varchar. PHPType = string. Has no related objects. Default is literal null
            'title' => substr($this->faker->text(64), 0, 64),
            // userId. Type = int. PHPType = int. Has related objects. Default is literal null
            'userId' =>
                $randomUsers instanceof Models\UsersModel
                    ? $this->usersTableGateway->fetchRandom()->getUserId()
                    : $this->usersTableGateway->getNewMockModelInstance()->save()->getUserId(),
        ]);
    }

    /**
     * @param array $data
     *
     * @return Models\BlogPostsModel
     */
    public function getNewModelInstance(array $data = []): Models\BlogPostsModel
    {
        return parent::getNewModelInstance($data);
    }

    /**
     * @param Models\BlogPostsModel $model
     *
     * @return Models\BlogPostsModel
     */
    public function save(AbstractModel $model): Models\BlogPostsModel
    {
        return parent::save($model);
    }
}
