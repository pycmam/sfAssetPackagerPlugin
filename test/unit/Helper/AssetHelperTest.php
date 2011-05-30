<?php
namespace Test\sfAssetPackagerPlugin\Helper;
require_once(__DIR__.'/../../../lib/helper/AssetPackagerHelper.php');
require_once(__DIR__ . '/../../../../../lib/vendor/symfony/lib/config/sfConfig.class.php');

use sfConfig, AssetPackagerHelper;


class AssetHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * TearDown
     */
    public function tearDown()
    {
        sfConfig::clear();
    }


    /**
     * Добавить timestamp
     */
    public function testAddTimeStamp()
    {
        sfConfig::set('app_sf_assets_timestamp', 123);

        $stylesheets = array('file1.css' => array(), 'file2.css' => array());
        $result = AssetPackagerHelper::processStylesheets($stylesheets);

        $expected = array(
            'file1.css?123' => array(),
            'file2.css?123' => array(),
        );
        $this->assertEquals($expected, $result);
    }


    /**
     * Ссылка на скомпилированную версию
     */
    public function testLinkToCompliled()
    {
        sfConfig::set('app_sf_assets_compile_dir', $path = '/some/path');
        sfConfig::set('app_sf_assets_compile', true);
        sfConfig::set('app_sf_assets_packages', array('compiled.css' => array('file1.css')));

        $stylesheets = array('compiled.css' => array(), 'file2.css' => array());
        $result = AssetPackagerHelper::processStylesheets($stylesheets);

        $expected = array(
            $path.'/compiled.css' => array(),
            'file2.css' => array(),
        );
        $this->assertEquals($expected, $result);
    }


    /**
     * Развернуть зависимости, если нет компиляции
     */
    public function testExpandsDependencies()
    {
        sfConfig::set('app_sf_assets_timestamp', 123);
        sfConfig::set('app_sf_assets_compile_dir', $path = '/some/path');
        sfConfig::set('app_sf_assets_compile', false);
        sfConfig::set('app_sf_assets_packages', array('compiled.css' => $files = array('file1.css', 'src/file2')));

        $stylesheets = array('compiled.css' => array('option1' => 1), 'file2.css' => array());
        $result = AssetPackagerHelper::processStylesheets($stylesheets);

        $expected = array(
            $files[0].'?123' => $stylesheets['compiled.css'],
            $files[1].'?123' => $stylesheets['compiled.css'],
            'file2.css?123' => array(),
        );
        $this->assertEquals($expected, $result);
    }

}
