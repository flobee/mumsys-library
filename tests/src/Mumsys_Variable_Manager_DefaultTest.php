<?php


/**
 * Callback test class
 */
class cbtest_class
{
    public static function runA( Mumsys_Variable_Item_Interface $item, $data = null, $params = null )
    {
        return true;
    }


    public static function runB( Mumsys_Variable_Item_Interface $item, $data = null, $params = null )
    {
        return true;
    }


    public static function runC( Mumsys_Variable_Item_Interface $item, $data = null, $params = null )
    {
        return true;
    }


    public static function runFalse( Mumsys_Variable_Item_Interface $item, $data = null,
        $params = null )
    {
        return false;
    }

}


/**
 * Callback test function
 */
function cbtest_function( Mumsys_Variable_Item_Interface $item, $data = null, $params = null )
{
    return true;
}


/**
 * Mumsys_Variable_Manager_Default Test
 */
class Mumsys_Variable_Manager_DefaultTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_Variable_Manager_Default
     */
    protected $_object;
    protected $_version = '2.2.4';
    protected $_values = array();
    protected $_config = array();


    protected function setUp()
    {
        $this->_config = array(
            'username' => array(
                'label' => 'Username',
                'type' => 'string', // string, array (list), email, numeric, float, integer, date, datetime, ipv4, ipv6
                'minlen' => 1,
                'maxlen' => 45,
                'allowEmpty' => false,
                'required' => true,
                'regex' => false,
                'default' => '',
                'value' => null,
                'errors' => array(),
            ),
        );
        $this->_values = array('username' => 'unittest');

        $this->_object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
    }


    protected function tearDown()
    {
        $this->_object = null;
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::__construct
     */
    public function test_construct()
    {
        $object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );

        $this->_config['username']['name'] = 'username';
        $this->_config['username']['value'] = 'unittest';
        $expected = array('username' => new Mumsys_Variable_Item_Default( $this->_config['username'] ));
        $this->assertingEquals( $expected, $this->_object->getItems() );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validate
     */
    public function testValidate()
    {
        $item = $this->_object->getItem( 'username' );

        $actual1 = $this->_object->validate();

        $item->setValue( '' );
        $actual2 = $this->_object->validate();

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validateType
     */
    public function testValidateType()
    {
        $_types = array(
            'string' => 'test string',
            'array' => array('unittest'),
            'email' => 'thisisallowed@host.tld',
            'numeric' => '1234',
            'float' => 1.234,
            'integer' => 12345,
            'date' => '2000-12-31',
            'datetime' => '2000-12-31 23:58:59',
            'ipv4' => '127.0.0.1',
            'ipv6' => '::1',
        );
        $item = $this->_object->getItem( 'username' );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );

            $actual = $this->_object->validateType( $item );
            $this->assertingTrue( $actual, print_r( $item->getErrorMessages(), true ) );

            // generate failures
            switch ( $type ) {
                case 'string': $item->setValue( array($value) );
                    break;
                case 'array': $item->setValue( 'unittest' );
                    break;
                case 'email': $item->setValue( 'unittest@3' );
                    break;
                case 'numeric': $item->setValue( 'unittest' );
                    break;
                case 'float': $item->setValue( 'unittest' );
                    break;
                case 'integer': $item->setValue( 'unittest' );
                    break;
                case 'date': $item->setValue( 'unittest' );
                    break;
                case 'datetime':$item->setValue( 'unittest' );
                    break;
                case 'ipv4': $item->setValue( 'localhost' );
                    break;
                case 'ipv6': $item->setValue( 'localhost' );
                    break;
            }
            $actual = $this->_object->validateType( $item );
            $this->assertingFalse( $actual );
        }

        $this->expectingExceptionMessageRegex( '/(Type "unittest" not implemented)/i' );
        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        $item->setType( 'unittest' );
        $this->_object->validateType( $item );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validateMinMax
     */
    public function testValidateMinMax()
    {
        $_types = array(
            'string' => 'test',
            'integer' => 4,
            'float' => 4.123,
            'numeric' => '4.123',
        );
        $item = $this->_object->getItem( 'username' );

        foreach ( $_types as $type => $value ) {
            $item->setType( $type );
            $item->setValue( $value );

            $item->setMinLength( 1 );
            $item->setMaxLength( 4.123 );

            $actualA = $this->_object->validateMinMax( $item );
            $this->assertingTrue( $actualA );

            // generate failures
            $item->setMinLength( 5 );
            $item->setMaxLength( 1 );
            $actualB = $this->_object->validateMinMax( $item );
            $this->assertingFalse( $actualB );
        }

        // for code coverage
        $item = $this->_object->createItem( array('value' => array('unittest', 'a'=>'b', 'c'=>'d')) );

        $actualC = $this->_object->validateMinMax( $item );
        $this->assertingTrue( $actualC ); // no min/max set, just return

        $item->setType( 'array' );
        $item->setMinLength( 4 );
        $item->setMaxLength( 1 );
        $actualD = $this->_object->validateMinMax( $item );
        $this->assertingFalse( $actualD );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validateRegex
     */
    public function testValidateRegex()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setValue( 'uNiTtEsT' );
        $item->setRegex( '/^(unittest)$/i' );

        $actual1 = $this->_object->validateRegex( $item );

        $item->setRegex( '/^(somtest)$/i' );
        $actual2 = $this->_object->validateRegex( $item );

        // regex error
        $displayErrors = ini_get( 'display_errors' );
        $errorReporting = ini_get( 'error_reporting' );

        $item->setRegex( '\d' ); // invalid regex / syntax error

        ini_set( 'display_errors', false );
        error_reporting( 0 );

        $actual3 = $this->_object->validateRegex( $item );

        ini_set( 'display_errors', $displayErrors );
        error_reporting( $errorReporting );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
        $this->assertingFalse( $actual3 );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validateIpv4
     */
    public function testValidateIPv4()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv4' );
        $item->setValue( '11.22.33.44' );
        $actual1 = $this->_object->validateIpv4( $item );

        $item2 = clone $item;
        $item2->setValue( 'noipv4' );
        $actual2 = $this->_object->validateIpv4( $item2 );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::validateIpv6
     */
    public function testValidateIPv6()
    {
        $item = $this->_object->getItem( 'username' );
        $item->setType( 'ipv6' );
        $item->setValue( '::1' );
        $actual1 = $this->_object->validateIpv6( $item );

        $item2 = clone $item;
        $item2->setValue( 'noipv6' );
        $actual2 = $this->_object->validateIpv6( $item2 );

        $this->assertingTrue( $actual1 );
        $this->assertingFalse( $actual2 );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::isValid
     * @covers Mumsys_Variable_Manager_Default::validateType
     * @covers Mumsys_Variable_Manager_Default::validateMinMax
     * @covers Mumsys_Variable_Manager_Default::validateRegex
     */
    public function testIsValid()
    {
        $item = $this->_object->getItem( 'username' );
        $actual1 = $this->_object->isValid( $item );

        // generate failures for code coverage

        $item->setValue( '' );
        $item->setAllowEmpty( true );
        $actual2 = $this->_object->isValid( $item );

        $item->setValue( null );
        $item->setRequired( true );
        $item->setAllowEmpty( false );
        $actual3 = $this->_object->isValid( $item );

        $item->setRequired( false );
        $item->setAllowEmpty( false );
        $actual4 = $this->_object->isValid( $item );

        $item->setRegex( '/(\d)/' );
        $item->setValue( 'unittest' );
        $actual5 = $this->_object->isValid( $item );

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( $actual2 );
        $this->assertingFalse( $actual3 );
        $this->assertingFalse( $actual4 );
        $this->assertingFalse( $actual5 );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::getItem
     * @covers Mumsys_Variable_Manager_Default::getItems
     */
    public function testGetItems()
    {
        $this->_config['username']['name'] = 'username';
        $this->_config['username']['value'] = 'unittest';
        $expected = array(
            'username' => new Mumsys_Variable_Item_Default( $this->_config['username'] ));
        $this->assertingEquals( $expected, $this->_object->getItems() );
        $this->assertingEquals( $expected['username'], $this->_object->getItem( 'username' ) );
        $this->assertingFalse( $this->_object->getItem( 'unknown' ) );
    }


    public function testRegisterItem()
    {
        $itemA = $this->_object->getItem( 'username' );
        $itemA->setName( 'user2' );
        $this->_object->registerItem( 'user2', $itemA );
        $this->assertingEquals( $itemA, $this->_object->getItem( 'user2' ) );

        $itemB = $this->_object->createItem( array('value' => 'some value') );
        $this->_object->registerItem( 'user3', $itemB );
        $this->assertingEquals( $itemB, $this->_object->getItem( 'user3' ) );

        $this->expectingExceptionMessageRegex( '/(Item "username" already set)/i' );
        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        $this->_object->registerItem( 'username', $itemA );
    }

    public function testRegisterItemExceptionB()
    {
        $item = $this->_object->getItem( 'username' );

        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        $mesg = 'Item name "username" and item address/key "keyFails" are not identical. '
            . 'Change item "name" or "$key"';
        $this->expectingExceptionMessage( $mesg );
        $this->_object->registerItem( 'keyFails', $item );
    }

    /**
     * @covers Mumsys_Variable_Manager_Default::createItem
     */
    public function testCreateItem()
    {
        $expected = new Mumsys_Variable_Item_Default( $this->_config['username'] );
        $actual = $this->_object->createItem( $this->_config['username'] );
        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::getErrorMessages
     */
    public function testGetErrormessages()
    {
        $this->_config['username']['errors'] = array('REQUIRED_MISSING' => 'Missing required value');
        $item = new Mumsys_Variable_Item_Default( $this->_config['username'] );

        $item->setName( 'testuser' );
        $this->_object->registerItem( 'testuser', $item );
        $actual = $this->_object->getErrorMessages();
        $expected = array('testuser' => array('REQUIRED_MISSING' => 'Missing required value'));

        $this->assertingEquals( $expected, $actual );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::getMessageTemplates
     * @covers Mumsys_Variable_Manager_Default::setMessageTemplates
     */
    public function testGetSetMessageTemplates()
    {
        $expected = array(
            'REQUIRED_MISSING' => 'Missing required value',
            'ALLOWEMPTY_ERROR' => 'Missing value',
            'REGEX_FAILURE' => 'Value "%1$s" does not match the regex rule: "%2$s"',
            'REGEX_ERROR' => 'Error in regular expression',
            'TYPE_INVALID_STRING' => 'Value (json):"%1$s" is not a "string"',
            'TYPE_INVALID_ARRAY' => 'Value (json):"%1$s" is not an "array"',
            'TYPE_INVALID_EMAIL' => 'Value "%1$s" is not a valid value for type "email"',
            'TYPE_INVALID_NUMERIC' => 'Value (json):"%1$s" is not a "numeric" type',
            'TYPE_INVALID_FLOAT' => 'Value (json):"%1$s" is not a "float" type',
            'TYPE_INVALID_INT' => 'Value (json):"%1$s" is not an "integer" type',
            'TYPE_INVALID_DATE' => 'Value (json):"%1$s" is not a "date" type',
            'TYPE_INVALID_DATETIME' => 'Value (json):"%1$s" is not a "datetime" type',
            'MINMAX_TOO_SHORT_STR' => 'Value "%1$s" must contain at least "%2$s" characters',
            'MINMAX_TOO_LONG_STR' => 'Value "%1$s" must contain maximum of "%2$s" characters, "%3$s" given',
            'MINMAX_TOO_SHORT_NUM' => 'Value "%1$s" must be minimum "%2$s"',
            'MINMAX_TOO_LONG_NUM' => 'Value "%1$s" can be maximum "%2$s"',
            'MINMAX_TYPE_ERROR' =>
                'Min/max type error "%1$s". Must be "string", "integer", "numeric", "float" or "double"',
        );

        $actual1 = $this->_object->getMessageTemplates();
        $actual2 = $actual1 = $this->_object->getMessageTemplates( $this->_object->setMessageTemplates( $expected ) );

        $this->assertingEquals( $expected, $actual1 );
        $this->assertingEquals( $expected, $actual2 );
        $this->assertingEquals( count( $expected ), count( $actual1 ) );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::setMessageTemplate
     */
    public function testSetSingleMessage()
    {
        $this->_object->setMessageTemplate( 'unittest', 'Unittest template message' );
        $actual1 = $this->_object->getMessageTemplates();

        $this->assertingEquals( $actual1['unittest'], 'Unittest template message' );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::setAttributes
     */
    public function testGetSetAttributes()
    {
        // value for some items
        $attributes = array('values' => array('username' => 'unittest value'));
        $this->_object->setAttributes( $attributes );

        $items = $this->_object->getItems();
        foreach ( $items as $item ) {
            $this->assertingEquals( 'unittest value', $item->getValue() );
        }

        // value for all items
        $attributes = array('value' => '2nd. unittest value');
        $this->_object->setAttributes( $attributes );
        $items = $this->_object->getItems();
        foreach ( $items as $item ) {
            $this->assertingEquals( '2nd. unittest value', $item->getValue() );
        }

        // labels for some items
        $attributes = array('labels' => array('username' => 'unittest label'));
        $this->_object->setAttributes( $attributes );
        $item = $this->_object->getItem( 'username' );
        $this->assertingEquals( 'unittest label', $item->getLabel() );

        // "state" for all items
        $attributes = array('state' => 'onSave');
        $this->_object->setAttributes( $attributes );
        $item = $this->_object->getItem( 'username' );
        $this->assertingEquals( 'unittest label', $item->getLabel() );

        $this->expectingExceptionMessageRegex( '/(Set item attributes for "unittest" not implemented)/i' );
        $this->expectingException( 'Mumsys_Variable_Manager_Exception' );
        $attributes = array('unittest' => 'throw an exception');
        $this->_object->setAttributes( $attributes );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::toArray
     */
    public function testToArray()
    {
        $actualA = $this->_object->toArray();
        $expectedA = array('username' => 'unittest');

        $itmProps = array(
            'name' => 'domain.unittest2',
            'value' => 'Unittest 2',
        );
        $newItem = $this->_object->createItem( $itmProps );
        $this->_object->registerItem( 'domain.unittest2', $newItem );
        $actualB = $this->_object->toArray( 'domain.' );
        $expectedB = array('username' => 'unittest', 'unittest2' => 'Unittest 2');

        $this->assertingEquals( $expectedA, $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * Just 4 code coverage.
     * @covers Mumsys_Variable_Manager_Default::externalsApply
     */
    public function testExternalsApply()
    {
        $this->_object->externalsApply();

        $actual = $this->_object->getErrorMessages();
        $expected = array();
        $this->assertingEquals( $expected, $actual );
    }


    /**
     * Just 4 code coverage.
     * @covers Mumsys_Variable_Manager_Default::filtersApply
     */
    public function testFiltersApply()
    {
        // A
        $this->_object->filtersApply();
        $actualA = $this->_object->getErrorMessages();

        // B
        $this->_config['username']['filters'] = array('onView'=>'imNotAFilter');
        $this->_object = new Mumsys_Variable_Manager_Default( $this->_config, $this->_values );
        $this->_object->filtersApply();
        $actualB = $this->_object->getErrorMessages();
        $expectedB = array(
            'username' => array(
                'FILTER_NOTFOUND' => 'Filter function "imNotAFilter" not found for item: "Username"'
            )
        );

        $this->assertingEquals( array(), $actualA );
        $this->assertingEquals( $expectedB, $actualB );
    }


    /**
     * @covers Mumsys_Variable_Manager_Default::filterItem
     * @covers Mumsys_Variable_Manager_Default::_execExternal
     */
    public function testFilterItem()
    {
        $itmProps = array(
            'name' => 'unittest',
            'value' => ' unittest & test value ',
            'filters' => array(
                'onSave' => array(
                    'trim',
                    'htmlentities' => array('%value%'),
                    'htmlspecialchars' => '%value%',
                    'str_replace' => array(';amp', '', '%value%'),
                    'functionNotExistsError' => 'parameterAsString',
                ),
                'onEdit' => array(
                    'htmlspecialchars' => 'newresult',
                    'htmlentities'
                ),
            )
        );
        $newItem = $this->_object->createItem( $itmProps );
        $this->_object->registerItem( 'unittest', $newItem );

        // default state: "onView"
        $newItem->stateSet( 'onView' );
        $expected1 = ' unittest & test value ';
        $actual1 = $this->_object->filterItem( $newItem );
        $actual2 = $newItem->getValue();

        $expected2 = 'unittest &amp; test value';
        $newItem->stateSet( 'onSave' );
        $actual3 = $this->_object->filterItem( $newItem );
        $actual4 = $newItem->getValue();

        $actual5 = $this->_object->getErrorMessages();
        $expected5 = array(
            'unittest' => array(
                'FILTER_NOTFOUND' => 'Filter function "functionNotExistsError" not found for item: "unittest"')
        );

        $newItem->stateSet( 'onEdit' );
        $actual6 = $this->_object->filterItem( $newItem );
        $expected66 = 'newresult';
        $actual66 = $newItem->getValue();

        $newItem->setValue( false );
        $newItem->stateSet( 'onSave' );
        $newItem->filterAdd( 'onSave', 'is_object' );
        $actual7 = $this->_object->filterItem( $newItem );
        $expected7 = $newItem->getValue();
        $actual8 = $this->_object->getErrorMessages(); //$newItem->getValue();
        $expected8 = array(
            'unittest' => array(
                'FILTER_NOTFOUND' => 'Filter function "functionNotExistsError" not found for item: "unittest"',
                'FILTER_ERROR' => 'Filter "is_object" failt for label/name: "unittest"',
            )
        );

        $this->assertingTrue( $actual1 );
        $this->assertingEquals( $expected1, $actual2 );
        $this->assertingFalse( $actual3 ); // err
        $this->assertingEquals( $expected2, $actual4 ); // in=out on errors
        $this->assertingEquals( $expected5, $actual5 );
        $this->assertingTrue( $actual6 );
        $this->assertingEquals( $expected66, $actual66 );
        $this->assertingEquals( $expected7, $actual7 ); // casted to empty string
        $this->assertingEquals( $expected8, $actual8 );
    }


    /**
     * Just 4 code coverage.
     * @covers Mumsys_Variable_Manager_Default::callbacksApply
     * @covers Mumsys_Variable_Manager_Default::callbackItem
     */
    public function testCallbacksApply()
    {
        $this->_object->callbacksApply();

        $itmProps = array(
            'name' => 'unittest',
            'value' => ' unittest & test value ',
            'callbacks' => array(
                'onView' => array(
                    'cbtest_function' => array('%value%', 12, 34, 56),
                    'cbtest_class::runA' => '%value%',
                    'cbtest_class::runB' => '123',
                    'cbtest_class::runC',
                    'callbackNotExists',
                    'cbtest_class::runFalse',
                ),
            )
        );
        $newItem = $this->_object->createItem( $itmProps );
        $this->_object->registerItem( 'unittest', $newItem );
        $actual1 = $this->_object->callbackItem( $newItem );
        $expected1 = false;

        $actual2 = $this->_object->getErrorMessages();
        $expected2 = array(
            'unittest' => array(
                'CALLBACK_NOTFOUND' => 'Callback function "callbackNotExists" not found for item: "unittest"',
                'CALLBACK_ERROR' => 'Callback "cbtest_class::runFalse" for "unittest" failt for value (json): "true"',
            )
        );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
    }


    /**
     * Tests constants
     */
    public function testConstants()
    {
        $message = 'A new version exists. You should have a look at '
            . 'the code coverage to verify all code was tested and not only '
            . 'all existing tests where checked!';
        $this->assertingEquals( $this->_version, Mumsys_Variable_Manager_Default::VERSION, $message );
    }

}
