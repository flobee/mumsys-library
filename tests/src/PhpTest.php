<?php

// $Id$

/**
 * Test class for php class.
 */
class PhpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var php
     */
    protected $object;
    protected $_testsDir;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_testsDir = MumsysTestHelper::getTestsBaseDir();
        $this->object = new Php();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }


    public function test__get()
    {
        $this->assertEquals( strtoupper(substr(PHP_OS,0,3)) , Php::$os );
        $this->assertEquals( get_magic_quotes_gpc() , Php::$getMagicQuotesGpc );
        $this->assertEquals( $this->object->os , Php::$os );
    }

    public function test__set()
    {
        if (PHP_VERSION_ID < 70000) {
            $get_magic_quotes_gpc = $this->object->get_magic_quotes_gpc;

            $this->assertEquals( 0 , $get_magic_quotes_gpc );

            $this->object->get_magic_quotes_gpc = true;
            $this->assertEquals( true , $this->object->get_magic_quotes_gpc );

            // set invalid, throw exception
            try {
                $this->object->unknownVariable = 'I\'m wrong at all';
                $this->fail("No Exception was thrown");
            } catch ( Exception $e ) {
                // all fine
            }

            $this->object->get_magic_quotes_gpc = $get_magic_quotes_gpc;
        } else {
            $actual = get_magic_quotes_gpc();
            $expected = false;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testIs_int()
    {
        $this->assertEquals( true , Php::is_int( 0 ) );
        $this->assertEquals( true , Php::is_int( 12 ) );
        $this->assertEquals( true , Php::is_int( '12' ) );
        $this->assertEquals( true , Php::is_int( '1234' ) );
        $this->assertEquals( false , Php::is_int( 1.9 ) );
        $this->assertEquals( false , Php::is_int( '1.9' ) );
        $this->assertEquals( false , Php::is_int( '1.9999' ) );
    }

    /**
     * test_floatval
     */
    public function test_floatval()
    {
        $this->assertEquals( 1.2 , Php::floatval( '1.2' ) );
        $this->assertEquals( 1234.56 , Php::floatval( '1.234,56' ) );
        $this->assertEquals( 12 , Php::floatval( '12' ) );
        $this->assertEquals( 0.12345 , Php::floatval( '0,12345' ) );
        $this->assertEquals( 1234.56 , Php::floatval( '1.234,56' ) );
        $this->assertEquals( 1234.56 , Php::floatval( '1.234,56ABC' ) );
        $this->assertEquals( 1.23456 , Php::floatval( '1,234.56' ) );
    }

    /**
     * test_file_exists
     */
    public function test_file_exists()
    {
        $url = 'http://php.net/';
        $this->assertTrue( Php::file_exists($url) );
        // this will use php's file_exists()
        $this->assertTrue( Php::file_exists(__FILE__) );
        // using fopen to test existense
        $this->assertTrue( Php::file_exists('file://'.__FILE__) );
        // empty url
        $this->assertFalse( Php::file_exists() );
        // not existing url
        $this->assertFalse( Php::file_exists('file://noWay') );
    }

    /**
     * test_file_get_contents
     */
    public function test_file_get_contents()
    {
        $max = 10;
        $c1 = file_get_contents(__FILE__);
        $c2 = file_get_contents(__FILE__, null, null, null, $max);

        $this->assertEquals($c1, Php::file_get_contents(__FILE__) ) ;
        $this->assertEquals($c2, Php::file_get_contents(__FILE__, null, null, null, $max) ) ;
    }

    /**
     * test_ini_get
     */
    public function test_ini_get()
    {
        $oldLimit = Php::ini_get( 'memory_limit' );

        $c = ini_set('memory_limit', '1K');
        $this->assertEquals( (1024) , Php::ini_get( 'memory_limit' ) ) ;

        $c = ini_set('memory_limit', '32M');
        $this->assertEquals( (32*1048576) , Php::ini_get( 'memory_limit' ) ) ;

        $c = ini_set('memory_limit', '1G');
        $this->assertEquals( (1000*1048576) , Php::ini_get( 'memory_limit' ) ) ;
        // other in switch
        $c = ini_set('memory_limit', '1T');
        $this->assertEquals( '1T' , Php::ini_get( 'memory_limit' ) ) ;

        $this->assertEquals( ini_get('display_errors'), Php::ini_get('display_errors') );
        $this->assertNull( Php::ini_get('html_errors') );

        $this->assertEquals('', Php::ini_get('hä?WhatsThis?') );


        ini_set('memory_limit', $oldLimit);
    }

    public function test_Addslashes()
    {
        //
        // addslashes works different than Php::addslashes()!!
        //

        $get_magic_quotes_gpc = $this->object->get_magic_quotes_gpc;


        $this->object->get_magic_quotes_gpc = 1;
        $this->assertEquals('a\'b\'c', $this->object->addslashes('a\'b\'c') ); // ok
        $this->assertEquals( addslashes('a\'b\'c'), $this->object->addslashes('a\\\'b\\\'c') );

        $this->object->get_magic_quotes_gpc = 0;
        $this->assertEquals('a\\\'b\\\'c', $this->object->addslashes('a\'b\'c') );
        $this->assertEquals( addslashes('a\'b\'c'), $this->object->addslashes('a\'b\'c') );


        $this->object->get_magic_quotes_gpc = $get_magic_quotes_gpc;
    }

    public function test_Stripslashes()
    {
        //
        // addslashes works different than Php::addslashes()!!
        //

        $get_magic_quotes_gpc = $this->object->get_magic_quotes_gpc;


        $this->object->get_magic_quotes_gpc = 1;
        $this->assertEquals('a\'b\'c', $this->object->stripslashes('a\\\'b\\\'c') ); // ok
        $this->assertEquals( stripslashes('a\'b\'c'), $this->object->stripslashes('a\\\'b\\\'c') );
        $this->assertEquals( stripslashes('a\\\'b\\\'c'), $this->object->stripslashes('a\\\'b\\\'c') );

        $this->object->get_magic_quotes_gpc = 0;
        $this->assertEquals('a\\\'b\\\'c', $this->object->stripslashes('a\\\'b\\\'c') );
        $this->assertEquals( stripslashes('a\\\\\\\'b\\\\\\\'c'), $this->object->stripslashes('a\\\'b\\\'c') );


        $this->object->get_magic_quotes_gpc = $get_magic_quotes_gpc;
    }

    public function testIn_string()
    {
       $str = 'ABCDEFG';
       $this->assertEquals('CDEFG', Php::in_string('CDE', $str, $insensitive=false));
       $this->assertEquals('CDEFG', Php::in_string('cDe', $str, $insensitive=true));
       $this->assertEquals('AB', Php::in_string('CDE', $str, $insensitive=false, $before_needle=true ));
       $this->assertEquals('AB', Php::in_string('cDe', $str, $insensitive=true, $before_needle=true ));
    }

    /**
     * test_htmlspecialchars
     */
    public function test_htmlspecialchars()
    {
        // ENT_QUOTES
        $this->assertEquals('&amp;', Php::htmlspecialchars('&', ENT_QUOTES));
        $this->assertEquals('&amp; &amp;', Php::htmlspecialchars('& &amp;', ENT_QUOTES));
        // ENT_COMPAT -> only " > and <
        $this->assertEquals('&lt;a href=\'test\'&gt;&amp; Test&lt;/a&gt;', Php::htmlspecialchars('<a href=\'test\'>& Test</a>', ENT_COMPAT));
        // ENT_NOQUOTES no quotes translation
        $this->assertEquals('&lt;a href=\'test\' id="123"&gt;&amp; Test&lt;/a&gt;', Php::htmlspecialchars('<a href=\'test\' id="123">& Test</a>', ENT_NOQUOTES));
        // php vs my php function
        $phpphp = htmlspecialchars("<a href='test'>Test</a>", ENT_QUOTES);
        $myphp = Php::htmlspecialchars("<a href='test'>Test</a>", ENT_QUOTES);
        $this->assertEquals('&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;',$phpphp);
        $this->assertEquals('&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;',$myphp);

        // difference between htmlspecialchars and Php::htmlspecialchars
        $phpphp = htmlspecialchars( '&copy; &#169; &#982; &forall; &#8704; &#dasgibtsnicht; &#x3B1;', ENT_QUOTES );
        $myphp = Php::htmlspecialchars( '&copy; &#169; &#982; &forall; &#8704; &#dasgibtsnicht; &#x3B1;', ENT_QUOTES );
        $this->assertEquals($phpphp,$myphp);
    }

    public function test_xhtmlspecialchars()
    {
        $this->assertEquals('&', Php::xhtmlspecialchars('&amp;'));
        $this->assertEquals('<', Php::xhtmlspecialchars('&lt;'));
        $this->assertEquals('>', Php::xhtmlspecialchars('&gt;'));
        $this->assertEquals('"', Php::xhtmlspecialchars('&quot;'));
        $this->assertEquals("'", Php::xhtmlspecialchars('&#039;'));

        $this->assertEquals('"""', Php::xhtmlspecialchars('"&quot;"', ENT_COMPAT) );
        $this->assertEquals('&"&quot;"', Php::xhtmlspecialchars('&amp;"&quot;"', ENT_NOQUOTES) );

    }

    public function testPhp_nl2br()
    {
        $this->assertEquals("x<br />", Php::nl2br("x\n", true));
        $this->assertEquals("x<br /><br />", Php::nl2br("x\n\n", true) );

        $str1 = "<br />\nnew line<br />\nnew line<br />\n";
        $str2 = '<br /><br />new line<br /><br />new line<br /><br />';
        $this->assertEquals($str2, Php::nl2br($str1, true) );

        $this->assertEquals("x<br>", Php::nl2br("x\n", false));
    }


    public function test_br2nl()
    {
        $string = "test<br />";
        $result = "test\n";
        $this->assertEquals($result , Php::br2nl($string, "\n") );
    }


    public function test_parseUrl()
    {
        $url = 'https://host/path/file.php?query=value#fragment';

        $actual = $this->object->parseUrl('file:///');
        $expected = parse_url('file:///');
        $this->assertEquals($expected, $actual);

        $expected = parse_url('file://');
        $this->assertFalse($expected, '"file://" should return false "file:///" should be valid');

        $actual = $this->object->parseUrl($url);
        $expected = parse_url($url);
        $this->assertEquals($expected, $actual);

        $actual = $this->object->parseUrl($url, PHP_URL_SCHEME);
        $expected = parse_url($url, PHP_URL_SCHEME);
        $this->assertEquals($expected, $actual);
        $this->assertEquals('https', $actual);

//        try {
//            $this->object->parseUrl('file://'); // raise exception
//        } catch (Php_Exception $expected) {
//            return;
//        }
//        $this->fail('Php_Exception: "file://" not allowed, use "file:///" three slashes "///"!');

        $this->setExpectedException('Php_Exception');
        $this->object->parseUrl('file://'); // raise exception
    }

    public function test_parseStr()
    {
        $url = 'https://host/path/file.php?query=value#fragment';

        $actual1 = $this->object->parseStr('file:///');
        parse_str('file:///', $expected1);
        $this->assertEquals($expected1, $actual1);

        $actual1 = $this->object->parseStr('abcde');
        parse_str('abcde', $expected1);
        $this->assertEquals($expected1, $actual1);

        $actual1 = $this->object->parseStr('a=b&c=d&e');
        parse_str('a=b&c=d&e', $expected1);
        $this->assertEquals($expected1, $actual1);
        // keys are arrays, values not!
        $actual1 = $this->object->parseStr('a[]=b&c[]=d[]&e[]');
        parse_str('a[]=b&c[]=d[]&e[]', $expected1);
        $this->assertEquals($expected1, $actual1);

        // empty string will throw error, php will return array()
        $this->setExpectedException('Php_Exception', 'Php::parseStr() failt.');
        $actual1 = $this->object->parseStr('');
        // parse_str('', $expected1);
        // $this->assertEquals($expected1, $actual1);
    }

    public function testNumberPad()
    {
        $actual1 = $this->object->numberPad(123, 6, '0');
        $expected1 = '000123';

        $actual2 = $this->object->numberPad(123, 2, '0');
        $expected2 = '123';

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
    }
    /**
     * @todo: to be checked! "by reference!"
     */
    public function test_current()
    {
        $result = array('a','b');
        $a = next($result);
        $array = array('a','b');
        $b = next($array);

        $php = current($result);
        $my = Php::current($array);
        // b=b
        $this->assertEquals( $php , $my );
    }


    public function test_compareArray()
    {
        $have1 =   array('flo'=>'was', 'bee'=>'here', array('in'=>'side'));
        $totest1 = array('flo'=>'was', 'bee'=>'here', array('in'=>'side'));
        $res1 = Php::compareArray( $have1, $totest1, 'vals');
        $this->assertEquals( array() , $res1 );

        $have2 = array('flo'=>'was', 'bee'=>'here', array('in'=>'side', 'in2'=>'side2'), 'flo'=>'flo', 'was'=>'was');
        $totest2 = array('flo', 'was', 'here', 'flo'=>'flo', 'was'=>'was');
        $res2 = Php::compareArray( $have2, $totest2, 'vals');

        $have2 = array('flo'=>'was', 'bee'=>'here', array('in'=>'side', 'in2'=>'side2'), $have1);
        $totest2 = array('flo', 'was', 'here', $have1);
        $res2 = Php::compareArray( $have2, $totest2, 'keys');



        // to check! $this->assertEquals( array('flo'=>'was','bee'=>'here',array('in'=>'side') ) , $res2 );
    }


    /**
     * @todo method not really working
     */
    public function testArray_keys_search_recursive_check()
    {
        $bigarray =  array(
            'key1' =>
                array(
                    'key2' =>
                        array(
                            'a' => array( 'text'=>'something'),
                            'b' => array( 'id'=>737),
                            'c' => array( 'name'=>'me'),
                        ),
                        array(
                            'a' => array( 'text'=>'something3'),
                            'b' => array( 'id'=>3),
                            'c' => array( 'name'=>'me3'),
                        ),
                )
        );
        $matchedKeys = Php::array_keys_search_recursive_check( 'name', $bigarray);
        $notFound = Php::array_keys_search_recursive_check('noKey', $bigarray);

        $this->assertTrue($matchedKeys);
        $this->assertFalse($notFound);
    }


    /**
     * @todo method not really working
     */
    public function testArray_keys_search_recursive()
    {
        $bigarray = array(
            'key1' => array(
                'key2' => array(
                    'a' => array('text' => 'something'),
                    'b' => array('id' => 1),
                    'c' => array('name' => 'me'),
                ),
                'key3' => array(
                    'a' => array('text' => 'something2'),
                    'b' => array('id' => 2),
                    'c' => array('name' => 'me2'),
                ),
                'key4' => array(
                    'a' => array('text' => 'something3'),
                    'b' => array('id' => 3),
                    'c' => array('name' => 'me3'),
                ),
            ),
            'namex' => 1,
        );
        $matchedKeys1 = Php::array_keys_search_recursive('key1', $bigarray, true);
        $this->assertEquals(array($bigarray), $matchedKeys1);

        $matchedKeys1 = Php::array_keys_search_recursive('name', $bigarray, true);
        $this->assertEquals(array(0 => array('name' => 'me'), 1 => array('name' => 'me2'), 2 => array('name' => 'me3')), $matchedKeys1);
        //
        // check reference, check current
        $this->assertEquals('me', $bigarray['key1']['key2']['c']['name']);
        // check reference,
        $matchedKeys1[0]['name'] = 'new value';
        // print_r($bigarray['key1']['key2']);
        // print_r($matchedKeys1);
        $this->assertEquals($matchedKeys1[0]['name'], $bigarray['key1']['key2']['c']['name']);
    }


    public function testArrayMergeRecursive()
    {
        // simple arrays test
        $array1 = array('name' => 'flobee', 'id' => 1, 0 => 123);
        $array2 = array('company' => 'some company', 'id' => 2);
        $array3 = array('phone' => 666666);
        $actual1 = $this->object->array_merge_recursive($array1, $array2, $array3);
        $expected1 = array(0 => 123, 'name' => 'flobee', 'id' => 2, 'company' => 'some company', 'phone' => 666666);

        // array recursiv check
        $array1 = array('record' => array('id' => 1, 'name' => 'flobee'));
        $array2 = array('record' => array('id' => 2, 'name' => 'user2'));
        $array3 = array('record' => array('phone' => 666666));
        $actual2 = $this->object->array_merge_recursive($array1, $array2, $array3);
        $expected2 = array('record' => array('id' => 2, 'name' => 'user2', 'phone' => 666666));

        // empyt arrays
        $array1 = array();
        $array2 = array();
        $actual3 = $this->object->array_merge_recursive($array1, $array2);
        $expected3 = array();

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
        $this->assertEquals($expected3, $actual3);

        // not an array argurment exception
        $array1 = array('Uta ruf');
        $array2 = 'foo';
        $message = 'Php::array_merge_recursive given argument is not an array "foo"';
        $this->setExpectedException('Mumsys_Exception', $message);
        $this->object->array_merge_recursive($array1, $array2);
    }


    public function testArrayMergeRecursiveExceptionNumArgs()
    {
        $message = 'Php::array_merge_recursive needs at least two arrays as arguments';
        $this->setExpectedException('Mumsys_Exception', $message);
        $this->object->array_merge_recursive(array());
    }


    public function testCheck_disk_free_space()
    {
        $logfile = $this->_testsDir . '/logs/' . __FUNCTION__ . '.log';
        $logOpts = array('logfile'=> $logfile);
        $logger = new Mumsys_Logger($logOpts);

        $cmdLine = 'df -a %1$s';
        if (Php::$os == 'WIN') {
            $cmdLine = 'c:/cygwin/bin/df.exe -a %1$s';
        }

        // basic call
        $basicCall = Php::check_disk_free_space($this->_testsDir . '/tmp', $secCmp=2, $maxSize=92, $logger, $cmdLine);

        // check cache return inside secCmp=60sec.
        $chkCache = Php::check_disk_free_space($this->_testsDir . '/tmp', $secCmp=60, $maxSize=92, $logger, $cmdLine);

        //disk space overflow in cache if disk usage < 1%
        $overflow = Php::check_disk_free_space($this->_testsDir . '/tmp', $secCmp=1, $maxSize=1, $logger, $cmdLine);

        // diskOverflowFirstRun
        $tmp = Php::check_disk_free_space($path='/var', $secCmp=60, $maxSize=2, $logger, $cmdLine);

        // wrong path
        $tmp = Php::check_disk_free_space($path='/123', $secCmp=60, $maxSize=2, $logger, $cmdLine);

        // error accessing a path
        $err = Php::check_disk_free_space($path='/root', $secCmp=60, $maxSize=2, $logger, 'test %1$s');

        @unlink($logfile);

        $this->assertFalse($basicCall);
        $this->assertFalse($chkCache);
        $this->assertTrue($overflow);
        $this->assertTrue($err);
    }


    public function test_memory_get_usage()
    {
        $memDef = 0;
        $memPhp = 1;
        if (function_exists('memory_get_usage')) {
            $memPhp = Php::memory_get_usage();
            $memDef = memory_get_usage();
        }

        $diffBytes = $memPhp - $memDef;
        if ( $diffBytes ) {
            // echo ($memDef/100)/64 .">". $diffBytes;
            $this->assertTrue( (($memDef/100)/64 > $diffBytes) );// 0.015625% ~ 1.3 KB
        } else {
            $this->assertEquals($memDef, $memPhp);
        }
    }


    public function test__callStatic()
    {
        // PHP >= 5.3.0 !!
        if ( PHP_VERSION_ID >= 50300 ) {
            // Php::strstr not implemented in class!
            $this->assertEquals( '12345' , Php::strstr('12345', '123') );
        }
        if ( PHP_VERSION_ID < 50300 ) {
            $this->markTestIncomplete( 'PHP < 5.3.0; Can not be called.' );
        }
    }


    public function test__call()
    {
        // call by callback of a nativ php function
        $this->assertEquals( 'ABCDEF' , $this->object->strstr('ABCDEF', 'ABC') );
    }
}
