<?php

namespace Benzine\ORM\Tests\Test\Models;

use Benzine\ORM\Abstracts\AbstractModel;
use Benzine\ORM\Tests\App as App;
use Benzine\ORM\Tests\Models;
use Benzine\ORM\Tests\Models\UsersModel;
use Benzine\ORM\Tests\Services\UsersService;
use Benzine\ORM\Tests\TableGateways;
use Benzine\ORM\Tests\TableGateways\UsersTableGateway;
use Carbon\Carbon;
use Gone\UUID\UUID;
use Benzine\Tests\AbstractBaseTestCase;

class DateTimeFieldsTestAbstract extends AbstractBaseTestCase
{
    /** @var AbstractModel[] */
    private $entititesToCleanUp = [];

    private UsersService $usersService;

    public function setUp(): void
    {
        parent::setUp();
        $this->usersService = \Benzine\App::DI(UsersService::class);
    }

    public function tearDown(): void
    {
        foreach($this->entititesToCleanUp as $model){
            $model->destroy();
        }
        parent::tearDown();
    }

    /**
     * @covers \Benzine\ORM\Abstracts\AbstractModel::__toRawArray()
     * @covers \Benzine\ORM\Abstracts\AbstractModel::exchangeArray()
     */
    public function testCreateWithDateTime(){

        $user = new UsersModel();
        $user->setName("Matthew Baggett");
        $user->setEmail("matthew@baggett.me");

        $dateTime = new Carbon();
        $dateTime->setDate(1990, 06,01);
        $dateTime->setTime(04,00,00);
        $user->setCreated($dateTime);

        $saved = $user->save();

        $this->entititesToCleanUp[] = $saved;

        // Assert that it actually saved.
        $this->assertGreaterThan(0, $saved->getUserId());

        $this->assertEquals("1990-06-01 04:00:00", $saved->getCreated()->format("Y-m-d H:i:s"));

        $reloaded = $this->usersService->getByField(UsersModel::FIELD_USERID, $saved->getUserId());

        $this->assertEquals("1990-06-01 04:00:00", $reloaded->getCreated()->format("Y-m-d H:i:s"));
    }
}