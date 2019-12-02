<?php
namespace Example\BlogApp\TableGateways\Base;
use \⌬\Controllers\Abstracts\TableGateway as AbstractTableGateway;
use \⌬\Controllers\Abstracts\Model;
use \⌬\Database\Db;
use \Example\BlogApp\TableGateways;
use \Example\BlogApp\Models;
use \Zend\Db\Adapter\AdapterInterface;
use \Zend\Db\ResultSet\ResultSet;
use \Gone\AppCore\Exceptions;

/********************************************************
 *             ___                         __           *
 *            / _ \___ ____  ___ ____ ____/ /           *
 *           / // / _ `/ _ \/ _ `/ -_) __/_/            *
 *          /____/\_,_/_//_/\_, /\__/_/ (_)             *
 *                         /___/                        *
 *                                                      *
 * Anything in this file is prone to being overwritten! *
 *                                                      *
 * This file was programatically generated. To modify   *
 * this classes behaviours, do so in the class that     *
 * extends this, or modify the Zenderator Template!     *
 ********************************************************/
// @todo: Make all TableGateways implement a TableGatewayInterface. -MB
abstract class BaseUsersTableGateway extends AbstractTableGateway
{
    protected $table = 'users';

    protected $database = 'mysql';

    protected $model = Models\UsersModel::class;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var Db */
    private $databaseConnector;

    private $databaseAdaptor;


    /**
     * AbstractTableGateway constructor.
     *
     * @param Db $databaseConnector
     *
     * @throws Exceptions\DbException
     */
    public function __construct(
        \Faker\Generator $faker,
        Db $databaseConnector
    )
    {
        $this->faker = $faker;
        $this->databaseConnector = $databaseConnector;

        /** @var $adaptor AdapterInterface */
        // @todo rename all uses of 'adaptor' to 'adapter'. I cannot spell - MB
        $this->databaseAdaptor = $this->databaseConnector->getDatabase($this->database);
        $resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAYOBJECT, new $this->model);
        return parent::__construct($this->table, $this->databaseAdaptor, null, $resultSetPrototype);
    }

    /**
     * @return Models\UsersModel
     */
    public function getNewMockModelInstance()
    {
      $newUsersData = [
        // displayName. Type = varchar. PHPType = string. Has no related objects.
        'displayName' => substr($this->faker->text(45 >= 5 ? 45 : 5), 0, 45),
        // email. Type = varchar. PHPType = string. Has no related objects.
        'email' => substr($this->faker->text(320 >= 5 ? 320 : 5), 0, 320),
        // id. Type = int. PHPType = int. Has no related objects.
        // password. Type = varchar. PHPType = string. Has no related objects.
        'password' => substr($this->faker->text(200 >= 5 ? 200 : 5), 0, 200),
        // userName. Type = varchar. PHPType = string. Has no related objects.
        'userName' => substr($this->faker->text(45 >= 5 ? 45 : 5), 0, 45),
      ];
      $newUsers = $this->getNewModelInstance($newUsersData);
      return $newUsers;
    }

    /**
     * @param array $data
     * @return Models\UsersModel
     */
    public function getNewModelInstance(array $data = [])
    {
        return parent::getNewModelInstance($data);
    }

    /**
     * @param Models\UsersModel $model
     * @return Models\UsersModel
     */
    public function save(Model $model)
    {
        return parent::save($model);
    }
}