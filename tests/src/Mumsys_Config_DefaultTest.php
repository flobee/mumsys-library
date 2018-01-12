<?php

/**
 * Mumsys_Config_Default Test
 */
class Mumsys_Config_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Config_Default
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_version = '3.0.0';
        $this->_configs = array('testkey' => 'test value');
        $this->_paths = array(
            __DIR__ . '/', //testconfig.php
            __DIR__ . '/../config/', //credentials.php and sub paths
        );
        $this->_context = new Mumsys_Context();
        $this->_object = new Mumsys_Config_Default($this->_configs, $this->_paths);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = null;
    }

    /**
     * For code coverage
     * @covers Mumsys_Config_Default::__construct
     */
    public function test__construct()
    {
        $this->_object = new Mumsys_Config_Default($this->_configs, $this->_paths);
    }

    /**
     * @covers Mumsys_Config_Default::get
     * @covers Mumsys_Config_Default::_get
     * @covers Mumsys_Config_Default::_load
     * @covers Mumsys_Config_Default::_merge
     * @covers Mumsys_Config_Default::_include
     */
    public function testGet()
    {
        $actual1 = $this->_object->get('testkey');
        $actual2 = $this->_object->get('credentials/database/host', false);
        $actual3 = $this->_object->get('credentials/database/mumsys/config/set', false);
        $actual4 = $this->_object->get(array('credentials', 'database', 'host'), false);
        $actual5 = $this->_object->get('database/mumsys/config/item/search', false);
        $expected1 = 'test value';
        $expected2 = MumsysTestHelper::getContext()->getConfig()->get('credentials/database/host' , 0);

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
        $this->assertFalse($actual3);
        $this->assertEquals($expected2, $actual4);
        $this->assertEquals('SELECT * FROM mumsys_config', $actual5);
    }

    /**
     * @covers Mumsys_Config_Default::getAll
     */
    public function testGetAll()
    {
        $this->assertEquals($this->_configs, $this->_object->getAll());
    }

    /**
     * @covers Mumsys_Config_Default::replace
     * @covers Mumsys_Config_Default::_replace
     */
    public function testReplace()
    {
        $this->_object->replace('testkey', 'value test');
        $actual = $this->_object->get('testkey');

        $this->_object->replace('new key', 'new value');
        $actual2 = $this->_object->get('new key');

        // with path
        $expected3 = array('a' => 'b', 'c' => 'd');
        $this->_object->replace('tests/somevalues', $expected3);
        $actual3 = $this->_object->get('tests/somevalues');
        $this->_object->replace('tests', array());
        $actual4 = $this->_object->get('tests');

        $this->assertEquals('value test', $actual);
        $this->assertEquals('new value', $actual2);
        $this->assertEquals($expected3, $actual3);
        $this->assertEquals(array(), $actual4);
    }

    /**
     * @covers Mumsys_Config_File::addPath
     */
    public function testAddpath()
    {
        $this->_object->addPath(__DIR__ . '/../config');

        $this->setExpectedExceptionRegExp('Mumsys_Config_Exception', '/(Path not found: "(.*)")/i');
        $this->_object->addPath(__DIR__ . '/config');
    }

    /**
     * @covers Mumsys_Config_Default::register
     */
    public function testRegister()
    {
        $this->_object->register('testkey2', 'test');
        $actual = $this->_object->get('testkey2', false);

        // with path
        $expected3 = array('a' => 'b', 'c' => 'd');
        $this->_object->register('tests/somevalues', $expected3);
        $actual3 = $this->_object->get('tests/somevalues');

        $this->assertEquals('test', $actual);
        $this->assertEquals($expected3, $actual3);

        $this->setExpectedExceptionRegExp('Mumsys_Config_Exception', '/(Config key "tests\/somevalues" already exists)/i');
        $this->_object->register('tests/somevalues', array());
    }

    /**
     * @covers Mumsys_Config_Default::load
     */
    public function testLoad()
    {
        $this->setExpectedExceptionRegExp('Mumsys_Config_Exception', '/(Not implemented yet)/i');
        $this->_object->load();
    }

    /**
     * Version check
     */
    public function testCheckVersion()
    {
        $message = 'A new version exists. You should have a look at '
            . 'the code coverage to verify all code was tested and not only '
            . 'all existing tests where checked!';
        $this->assertEquals($this->_version, Mumsys_Config_File::VERSION, $message);
    }

}