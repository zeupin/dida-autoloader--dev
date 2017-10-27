<?php

use \PHPUnit\Framework\TestCase;
use \Dida\Debug\Debug;

/**
 * AutoloaderTest
 */
class AutoloaderTest extends TestCase
{


    /**
     * 测试phpunit是否正常工作
     */
    public function testPhpUnitWorksWell()
    {
        $value = 1;

        $this->assertEquals(1, $value);
    }


    /**
     * 测试Autoloader可以正常初始化
     */
    public function test_init()
    {
        Autoloader::init();
    }


    public function test_dumpPsr4()
    {
        Autoloader::init();
        Autoloader::addPsr4('Dida', 'D:/Projects/github/dida-db/dev/src/Dida/Db');
        echo Debug::varExport(Autoloader::dump());
    }
}
