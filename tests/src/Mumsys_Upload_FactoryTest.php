<?php

/**
 * Mumsys_Upload_FactoryTest
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2018 by Florian Blasel
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Upload
 * Created: 2018-12
 */


/**
 * Test class to check invalid interface usage
 */
class Mumsys_Upload_MissingIfaceTest
{

}


/**
 * Mumsys_Upload_Factory Test
 */
class Mumsys_Upload_FactoryTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Upload_Factory
     */
    protected $_object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->_object = new Mumsys_Upload_Factory;
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->_object = null;
    }


    /**
     * @covers Mumsys_Upload_Factory::getAdapter
     */
    public function testGetAdapter()
    {
        $fileInput = array(
            'name' => 'name.file',
            'tmp_name' => '/tmp/name.file',
            'size' => 10,
            'error' => 0,
            'type' => 'unknown/unknown',
        );
        $actual1 = $this->_object->getAdapter( 'File', $fileInput );
        $actual2 = $this->_object->getAdapter( 'Mock', $fileInput );
        $this->assertInstanceOf( 'Mumsys_Upload_Interface', $actual1 );
        $this->assertInstanceOf( 'Mumsys_Upload_Interface', $actual2 );

        $this->expectException( 'Mumsys_Upload_Exception' );
        $this->_object->getAdapter( 'NoAdapter' );
    }


    /**
     * @covers Mumsys_Upload_Factory::getAdapter
     */
    public function testGetAdapterException1NoALNUM()
    {
        $this->expectException( 'Mumsys_Upload_Exception' );
        $regex = '/(Invalid characters in class name "Mumsys_Upload_12\$\&3")/i';
        $this->expectExceptionMessageRegExp( $regex );
        $this->_object->getAdapter( '12$&3' );
    }


    /**
     * @covers Mumsys_Upload_Factory::getAdapter
     */
    public function testGetAdapterException1MissingIface()
    {
        $this->expectException( 'Mumsys_Upload_Exception' );
        $regex = '/(Class "Mumsys_Upload_MissingIfaceTest" does not '
            . 'implement interface "Mumsys_Upload_Interface")/i';
        $this->expectExceptionMessageRegExp( $regex );
        $this->_object->getAdapter( 'MissingIfaceTest' );
    }


    /**
     * @covers Mumsys_Upload_Factory::getAdapter
     */
    public function testGetAdapterException2NotAvailable()
    {
        $this->expectException( 'Mumsys_Upload_Exception' );
        $regex = '/(Class "Mumsys_Upload_12345" not available)/i';
        $this->expectExceptionMessageRegExp( $regex );
        $this->_object->getAdapter( 12345 );
    }

}