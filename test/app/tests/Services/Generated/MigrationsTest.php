<?php

namespace Benzine\ORM\Tests\Test\Services\Generated;

use Benzine\ORM\Tests\Test as App;
use Benzine\ORM\Tests\TableGateways\MigrationsTableGateway;
use Benzine\ORM\Tests\Services;
use Benzine\ORM\Tests\Models\MigrationsModel;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Benzine\Tests\BaseTestCase;

/**
 * @covers \Benzine\ORM\Tests\Models\MigrationsModel
 * @covers \Benzine\ORM\Tests\Models\Base\BaseMigrationsModel
 * @covers \Benzine\ORM\Tests\Services\MigrationsService
 * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService
 * @covers \Benzine\ORM\Tests\TableGateways\MigrationsTableGateway
 * @covers \Benzine\ORM\Tests\TableGateways\Base\BaseMigrationsTableGateway
 *
 * @group generated
 * @group services
 * @internal
 **/
class MigrationsTest extends BaseTestCase
{
    protected Services\MigrationsService $migrationsService;
    protected MigrationsTableGateway $migrationsTableGateway;

    /** @var MigrationsModel[] */
    private static array $MockData = [];

    /**
     * @beforeClass
     */
    public static function setupMigrationsMockData(): void
    {
        /** @var MigrationsTableGateway $migrationsTableGateway */
        $migrationsTableGateway = App::DI(MigrationsTableGateway::class);
        for($i = 0; $i <= 5; $i++){
            self::$MockData[] = $migrationsTableGateway
                ->getNewMockModelInstance()
                ->save();
        }
    }

    /**
     * @before
     */
    public function setupMigrationsService(): void
    {
        $this->migrationsService = App::DI(Services\MigrationsService::class);
    }

    /**
     * @before
     */
    public function setupMigrationsTableGateway(): void
    {
        $this->migrationsTableGateway = App::DI(MigrationsTableGateway::class);
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getNewModelInstance
     */
    public function testGetNewModelInstance()
    {
        $this->assertInstanceOf(
            MigrationsModel::class,
            $this->migrationsService->getNewModelInstance()
        );
    }

    /**
     * @large
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getAll
     */
    public function testGetAll()
    {
        $all = $this->migrationsService->getAll();
        $this->assertInstanceOf(
            MigrationsModel::class,
            reset($all)
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getRandom
     */
    public function testGetRandom()
    {
        $random = $this->migrationsService->getRandom();
        $this->assertInstanceOf(
            MigrationsModel::class,
            $random
        );

        return $random;
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getByField
     */
    public function testGetByPrimaryKeys(MigrationsModel $random)
    {
        /** @var MigrationsModel $found */
        // By version
        $found = $this->migrationsService->getByField(MigrationsModel::FIELD_VERSION, $random->getversion());
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found
        );
        $this->assertEquals($random, $found);
    }

    /**
     * @depends testGetRandom
     */
    public function testCreateFromArray(MigrationsModel $random)
    {
        $this->assertInstanceOf(
            MigrationsModel::class,
            $this->migrationsService->createFromArray($random->__toArray())
        );
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getMockObject
     */
    public function testGetMockObject()
    {
        $this->assertInstanceOf(
            MigrationsModel::class,
            $this->migrationsService->getMockObject()
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getByField
     */
    public function testGetByField(MigrationsModel $random)
    {
        $found = $this->migrationsService->getByField(
            MigrationsModel::FIELD_VERSION,
            $random->getVersion()
        );
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found,
            "Calling Services\\MigrationsService->getByField((MigrationsModel::FIELD_VERSION) failed to find a MigrationsModel"
        );
        $found = $this->migrationsService->getByField(
            MigrationsModel::FIELD_MIGRATION_NAME,
            $random->getMigration_name()
        );
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found,
            "Calling Services\\MigrationsService->getByField((MigrationsModel::FIELD_MIGRATION_NAME) failed to find a MigrationsModel"
        );
        $found = $this->migrationsService->getByField(
            MigrationsModel::FIELD_START_TIME,
            $random->getStart_time()
        );
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found,
            "Calling Services\\MigrationsService->getByField((MigrationsModel::FIELD_START_TIME) failed to find a MigrationsModel"
        );
        $found = $this->migrationsService->getByField(
            MigrationsModel::FIELD_END_TIME,
            $random->getEnd_time()
        );
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found,
            "Calling Services\\MigrationsService->getByField((MigrationsModel::FIELD_END_TIME) failed to find a MigrationsModel"
        );
        $found = $this->migrationsService->getByField(
            MigrationsModel::FIELD_BREAKPOINT,
            $random->getBreakpoint()
        );
        $this->assertInstanceOf(
            MigrationsModel::class,
            $found,
            "Calling Services\\MigrationsService->getByField((MigrationsModel::FIELD_BREAKPOINT) failed to find a MigrationsModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::countByField
     */
    public function testCountByField(MigrationsModel $random)
    {
        $found = $this->migrationsService->countByField(MigrationsModel::FIELD_VERSION, $random->getVersion());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\MigrationsService->countByField(MigrationsModel::FIELD_VERSION) failed to count a MigrationsModel"
        );
        $found = $this->migrationsService->countByField(MigrationsModel::FIELD_MIGRATION_NAME, $random->getMigration_name());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\MigrationsService->countByField(MigrationsModel::FIELD_MIGRATION_NAME) failed to count a MigrationsModel"
        );
        $found = $this->migrationsService->countByField(MigrationsModel::FIELD_START_TIME, $random->getStart_time());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\MigrationsService->countByField(MigrationsModel::FIELD_START_TIME) failed to count a MigrationsModel"
        );
        $found = $this->migrationsService->countByField(MigrationsModel::FIELD_END_TIME, $random->getEnd_time());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\MigrationsService->countByField(MigrationsModel::FIELD_END_TIME) failed to count a MigrationsModel"
        );
        $found = $this->migrationsService->countByField(MigrationsModel::FIELD_BREAKPOINT, $random->getBreakpoint());
        $this->assertGreaterThanOrEqual(
            1,
            $found,
            "Calling Services\\MigrationsService->countByField(MigrationsModel::FIELD_BREAKPOINT) failed to count a MigrationsModel"
        );
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getManyByField
     */
    public function testGetManyByField(MigrationsModel $random)
    {
        // Testing get by version
        $this->assertContainsOnlyInstancesOf(
            MigrationsModel::class,
            $this->migrationsService->getManyByField(
                MigrationsModel::FIELD_VERSION,
                $random->getversion(),
                5
            )
        );
        // Testing get by migration_name
        $this->assertContainsOnlyInstancesOf(
            MigrationsModel::class,
            $this->migrationsService->getManyByField(
                MigrationsModel::FIELD_MIGRATION_NAME,
                $random->getmigration_name(),
                5
            )
        );
        // Testing get by start_time
        $this->assertContainsOnlyInstancesOf(
            MigrationsModel::class,
            $this->migrationsService->getManyByField(
                MigrationsModel::FIELD_START_TIME,
                $random->getstart_time(),
                5
            )
        );
        // Testing get by end_time
        $this->assertContainsOnlyInstancesOf(
            MigrationsModel::class,
            $this->migrationsService->getManyByField(
                MigrationsModel::FIELD_END_TIME,
                $random->getend_time(),
                5
            )
        );
        // Testing get by breakpoint
        $this->assertContainsOnlyInstancesOf(
            MigrationsModel::class,
            $this->migrationsService->getManyByField(
                MigrationsModel::FIELD_BREAKPOINT,
                $random->getbreakpoint(),
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
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[0]->getversion())
                    ->or
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[1]->getversion())
                    ->or
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[2]->getversion())
                    ->or
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[3]->getversion())
                    ->or
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[4]->getversion())
                    ->or
                    ->equalTo(MigrationsModel::FIELD_VERSION, $mockData[5]->getversion())
                ->unnest()
                ;
        };
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getManyMatching
     */
    public function testGetManyMatching(MigrationsModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->migrationsService->getManyMatching($filter);
        $this->assertGreaterThan(0, count($all));
        $this->assertContainsOnlyInstancesOf(MigrationsModel::class, $all);

        $one = $this->migrationsService->getManyMatching($filter, null, Select::ORDER_ASCENDING, 1);
        $this->assertEquals(1, count($one));
        $this->assertContainsOnlyInstancesOf(MigrationsModel::class, $one);

        $asc  = $this->migrationsService->getMatching($filter, MigrationsModel::FIELD_VERSION, Select::ORDER_ASCENDING);
        $desc = $this->migrationsService->getMatching($filter, MigrationsModel::FIELD_VERSION, Select::ORDER_DESCENDING);
        $this->assertEquals(migrationsModel::class, get_class($asc));
        $this->assertEquals(migrationsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->migrationsService->getMatching([MigrationsModel::FIELD_VERSION => $random->getversion()]));
    }

    /**
     * @depends testGetRandom
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getMatching
     */
    public function testGetMatching(MigrationsModel $random)
    {
        $filter = $this->getMockDataFilter();

        $all = $this->migrationsService->getMatching($filter);
        $this->assertEquals(migrationsModel::class, get_class($all));

        $asc  = $this->migrationsService->getMatching($filter, MigrationsModel::FIELD_VERSION, Select::ORDER_ASCENDING);
        $desc = $this->migrationsService->getMatching($filter, MigrationsModel::FIELD_VERSION, Select::ORDER_DESCENDING);
        $this->assertEquals(migrationsModel::class, get_class($asc));
        $this->assertEquals(migrationsModel::class, get_class($desc));
        $this->assertNotEquals($asc, $desc);
        $this->assertEquals($random, $this->migrationsService->getMatching([MigrationsModel::FIELD_VERSION => $random->getversion()]));
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::deleteByField
     */
    public function testDeleteByField()
    {
        /** @var MigrationsModel[] $allDeleted */
        $allDeleted = [];
        /** @var MigrationsModel $deleteable */
        $deleteable = $this->migrationsTableGateway
            ->getNewMockModelInstance()
            ->save();
        $this->assertEquals(1, $this->migrationsService->deleteByField(MigrationsModel::FIELD_VERSION, $deleteable->getversion()));
        $allDeleted[] = $deleteable;
        return $allDeleted;
    }

    /**
     * @depends testDeleteByField
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::deleteByField
     * @param MigrationsModel[] $allDeleted
     */
    public function testDeleteByFieldVerify(array $allDeleted)
    {
        /** @var MigrationsModel $deleteable */
        // By version
        $deleteable = array_pop($allDeleted);
        $this->assertEquals(null, $this->migrationsService->getByField(MigrationsModel::FIELD_VERSION, $deleteable->getversion()));

    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getTermPlural
     */
    public function testGetTermPlural()
    {
        $this->assertNotEmpty($this->migrationsService->getTermPlural());
    }

    /**
     * @covers \Benzine\ORM\Tests\Services\Base\BaseMigrationsService::getTermSingular
     */
    public function testGetTermSingular()
    {
        $this->assertNotEmpty($this->migrationsService->getTermSingular());
    }
}
