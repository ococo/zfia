<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Models_AllTests::main');
}

require_once dirname(__FILE__) . '/PlacesTest.php';

class Models_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ZFiA Places - Model Tests');

        $suite->addTestSuite('models_PlacesTest');
        $suite->addTestSuite('models_SearchIndexerTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Models_AllTests::main') {
    Models_AllTests::main();
}
