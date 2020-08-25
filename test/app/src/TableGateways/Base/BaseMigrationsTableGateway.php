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
abstract class BaseMigrationsTableGateway extends AbstractTableGateway implements TableGatewayInterface
{
    protected $table = 'Migrations';
    protected string $model = Models\MigrationsModel::class;
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
     * @return Models\MigrationsModel
     */
    public function getNewMockModelInstance()
    {
        return $this->getNewModelInstance([
            // breakpoint. Type = tinyint. PHPType = int. Has no related objects. Default is literal null
            'breakpoint' => $this->faker->numberBetween(1, 0.01),
            // end_time. Type = timestamp. PHPType = \DateTime. Has no related objects. Default is literal NULL
            'end_time' => $this->faker->word,
            // migration_name. Type = varchar. PHPType = string. Has no related objects. Default is literal NULL
            'migration_name' => substr($this->faker->text(100), 0, 100),
            // start_time. Type = timestamp. PHPType = \DateTime. Has no related objects. Default is literal NULL
            'start_time' => $this->faker->word,
            // version. Type = bigint. PHPType = int. Has no related objects. Default is literal null
            'version' => $this->faker->numberBetween(1, 0.01),
        ]);
    }

    /**
     * @param array $data
     *
     * @return Models\MigrationsModel
     */
    public function getNewModelInstance(array $data = []): Models\MigrationsModel
    {
        return parent::getNewModelInstance($data);
    }

    /**
     * @param Models\MigrationsModel $model
     *
     * @return Models\MigrationsModel
     */
    public function save(Model $model): Models\MigrationsModel
    {
        return parent::save($model);
    }
}
