<?php

namespace Benzine\ORM\Tests\Test\Models\Generated;

use Benzine\ORM\Tests\Test as App;
use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\Models\MigrationsModel;
use Benzine\ORM\Tests\TableGateways;
use Benzine\ORM\Tests\TableGateways\MigrationsTableGateway;
use Gone\UUID\UUID;
use Benzine\Tests\BaseTestCase;

/**
 * @covers \Benzine\ORM\Tests\Models\MigrationsModel
 * @covers \Benzine\ORM\Tests\Models\Base\BaseMigrationsModel
 * @covers \Benzine\ORM\Tests\TableGateways\MigrationsTableGateway
 * @covers \Benzine\ORM\Tests\TableGateways\Base\BaseMigrationsTableGateway
 *
 * @group generated
 * @group models
 * @internal
 */
class MigrationsTest extends BaseTestCase
{
    protected MigrationsModel $testInstance;
    protected MigrationsTableGateway$testTableGateway;

    /**
     * @before
     */
    public function setupDependencies(): void
    {
        $this->testTableGateway = App::DI(MigrationsTableGateway::class);
        $this->testInstance = $this->testTableGateway->getNewMockModelInstance();
    }

    public function testExchangeArray()
    {
        $data = [];
        $data['version'] = self::getFaker()->randomDigitNotNull;
        $data['migration_name'] = self::getFaker()->word;
        $data['start_time'] = self::getFaker()->word;
        $data['end_time'] = self::getFaker()->word;
        $data['breakpoint'] = self::getFaker()->randomDigitNotNull;
        $this->testInstance = new MigrationsModel($data);
        $this->assertEquals($data['version'], $this->testInstance->getVersion());
        $this->assertEquals($data['migration_name'], $this->testInstance->getMigration_name());
        $this->assertEquals($data['start_time'], $this->testInstance->getStart_time());
        $this->assertEquals($data['end_time'], $this->testInstance->getEnd_time());
        $this->assertEquals($data['breakpoint'], $this->testInstance->getBreakpoint());
    }

    public function testGetRandom()
    {
        // If there is no data in the table, create some.
        if (0 == $this->testTableGateway->getCount()) {
            $dummyObject = $this->testTableGateway->getNewMockModelInstance();
            $this->testTableGateway->save($dummyObject);
        }

        $migration = $this->testTableGateway->fetchRandom();
        $this->assertTrue($migration instanceof MigrationsModel, 'Make sure that "'.get_class($migration).'" matches "MigrationsModel"');

        return $migration;
    }

    public function testNewMockModelInstance()
    {
        $new = $this->testTableGateway->getNewMockModelInstance();

        $this->assertInstanceOf(
            Models\MigrationsModel::class,
            $new
        );

        $new->save();

        return $new;
    }

    public function testNewModelFactory()
    {
        $instance = MigrationsModel::factory();

        $this->assertInstanceOf(
            Models\MigrationsModel::class,
            $instance
        );
    }

    public function testSave()
    {
        /** @var Models\MigrationsModel $mockModel */
        /** @var Models\MigrationsModel $savedModel */
        $mockModel = $this->testTableGateway->getNewMockModelInstance();
        $savedModel = $mockModel->save();

        $mockModelArray = $mockModel->__toArray();
        $savedModelArray = $savedModel->__toArray();

        // Remove auto increments from test.
        foreach ($mockModel->getAutoIncrementKeys() as $autoIncrementKey => $discard) {
            foreach ($mockModelArray as $key => $value) {
                if (strtolower($key) == strtolower($autoIncrementKey)) {
                    unset($mockModelArray[$key], $savedModelArray[$key]);
                }
            }
        }

        $this->assertEquals($mockModelArray, $savedModelArray);
    }

    /**
     * @depends testGetRandom
     */
    public function testSettersAndGetters(MigrationsModel $migrations)
    {
        $this->assertTrue(method_exists($migrations, 'getversion'));
        $this->assertTrue(method_exists($migrations, 'setversion'));
        $this->assertTrue(method_exists($migrations, 'getmigration_name'));
        $this->assertTrue(method_exists($migrations, 'setmigration_name'));
        $this->assertTrue(method_exists($migrations, 'getstart_time'));
        $this->assertTrue(method_exists($migrations, 'setstart_time'));
        $this->assertTrue(method_exists($migrations, 'getend_time'));
        $this->assertTrue(method_exists($migrations, 'setend_time'));
        $this->assertTrue(method_exists($migrations, 'getbreakpoint'));
        $this->assertTrue(method_exists($migrations, 'setbreakpoint'));

        $testMigrations = new MigrationsModel();
        $input = self::getFaker()->randomDigitNotNull;
        $testMigrations->setVersion($input);
        $this->assertEquals($input, $testMigrations->getVersion());
        $input = self::getFaker()->word;
        $testMigrations->setMigration_name($input);
        $this->assertEquals($input, $testMigrations->getMigration_name());
        $input = self::getFaker()->word;
        $testMigrations->setStart_time($input);
        $this->assertEquals($input, $testMigrations->getStart_time());
        $input = self::getFaker()->word;
        $testMigrations->setEnd_time($input);
        $this->assertEquals($input, $testMigrations->getEnd_time());
        $input = self::getFaker()->randomDigitNotNull;
        $testMigrations->setBreakpoint($input);
        $this->assertEquals($input, $testMigrations->getBreakpoint());
    }

    /**
     * @large
     */
    public function testDestroy()
    {
        /** @var Models\MigrationsModel $destroyableModel */
        $destroyableModel = $this->testTableGateway->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertTrue(true, $destroyableModel->destroy());
    }

    /**
     * @large
     */
    public function testdestroyRecursively()
    {
        /** @var Models\MigrationsModel $destroyableModel */
        $destroyableModel = $this->testTableGateway->getNewMockModelInstance();
        $destroyableModel->save();
        $this->assertGreaterThan(0, $destroyableModel->destroyRecursively());
    }

    public function testGetPropertyMeta()
    {
        $propertyMeta = $this->testInstance->getPropertyMeta();
        $this->assertTrue(is_array($propertyMeta));
        $this->assertGreaterThan(0, count($propertyMeta));
        $this->assertArrayHasKey(MigrationsModel::FIELD_VERSION, $propertyMeta);
        $this->assertArrayHasKey(MigrationsModel::FIELD_MIGRATION_NAME, $propertyMeta);
        $this->assertArrayHasKey(MigrationsModel::FIELD_START_TIME, $propertyMeta);
        $this->assertArrayHasKey(MigrationsModel::FIELD_END_TIME, $propertyMeta);
        $this->assertArrayHasKey(MigrationsModel::FIELD_BREAKPOINT, $propertyMeta);
    }
}
