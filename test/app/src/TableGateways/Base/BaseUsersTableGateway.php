<?php

namespace Benzine\ORM\Tests\TableGateways\Base;

use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\TableGateways;
use Faker\Generator;
use Laminas\Db\ResultSet\ResultSet;
use Benzine\ORM\Abstracts\Model;
use Benzine\ORM\Abstracts\TableGateway as AbstractTableGateway;
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
abstract class BaseUsersTableGateway extends AbstractTableGateway implements TableGatewayInterface
{
    protected $table = 'Users';
    protected string $model = Models\UsersModel::class;
    protected Generator $faker;
    protected Connection\Databases $databaseConnection;
    protected Connection\Database $database;

    /**
     * AbstractTableGateway constructor.
     *
     * @param Generator $faker
     * @param Connection\Databases  $databaseConnection
     *
     * @throws BenzineException
     */
    public function __construct(
        Generator $faker,
        Connection\Databases $databaseConnection
    ) {
        $this->faker = $faker;
        $this->databaseConnection = $databaseConnection;
        $this->database = $this->databaseConnection->getDatabase('default');

        $resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAYOBJECT, new $this->model());

        parent::__construct($this->table, $this->database->getAdapter(), null, $resultSetPrototype);
    }

    /**
     * @return Models\UsersModel
     */
    public function getNewMockModelInstance()
    {
        return $this->getNewModelInstance([
            // created. Type = timestamp. PHPType = \DateTime. Has no related objects. Default is interpreted current_timestamp()
            'created' => $this->faker->word,
            // email. Type = varchar. PHPType = string. Has no related objects. Default is literal null
            'email' => substr($this->faker->text(320), 0, 320),
            // name. Type = varchar. PHPType = string. Has no related objects. Default is literal null
            'name' => substr($this->faker->text(32), 0, 32),
            // userId. Type = int. PHPType = int. Has no related objects. Default is literal null
        ]);
    }

    /**
     * @param array $data
     *
     * @return Models\UsersModel
     */
    public function getNewModelInstance(array $data = []): Models\UsersModel
    {
        return parent::getNewModelInstance($data);
    }

    /**
     * @param Models\UsersModel $model
     *
     * @return Models\UsersModel
     */
    public function save(Model $model): Models\UsersModel
    {
        return parent::save($model);
    }
}
