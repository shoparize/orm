<?php

namespace Benzine\ORM;

use Laminas\Db\Adapter\Platform;
use Laminas\Db\Adapter\Profiler;
use Laminas\Db\ResultSet;

class Adapter extends \Laminas\Db\Adapter\Adapter
{
    public function __construct($driver, Platform\PlatformInterface $platform = null, ResultSet\ResultSetInterface $queryResultPrototype = null, Profiler\ProfilerInterface $profiler = null)
    {
        parent::__construct($driver, $platform, $queryResultPrototype, $profiler);
        //if (!defined('ZEND_PROFILER_DISABLE') || ZEND_PROFILER_DISABLE == false) {
        //    $this->setProfiler(App::Container()->get(\Gone\AppCore\Zend\Profiler::class));
        //}
    }
}
