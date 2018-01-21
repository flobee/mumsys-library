<?php

/**
 * Mumsys_Weather_Item_Unit_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2013 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 * @verion      1.0.0
 * Created: 2013, renew 2018
 */

/**
 * Abstract unit item for weather unit properties.
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Weather
 */
abstract class Mumsys_Weather_Item_Unit_Abstract
    extends Mumsys_Abstract
{
    /**
     * Values to be set: 'key', 'label', 'sign', 'code'
     * @var array
     */
    protected $_input = array();

    /**
     * Modification flag. If item properties has changed or not.
     * @var boolean
     */
    protected $_modified;

    /**
     * Application domain prefix to be set in contrete implementation.
     * E.g: 'weather.item.unit.default.' for the default unit item
     * @var string
     */
    protected $_domainPrefix;


    /**
     * Initialize the unit item.
     *
     * @param array $input List of input parametery as follow:
     *  - 'key' (string) Name of the unit eg: 'fahrenheit'
     *  - 'label' (string) Label for the 'key' e.g. for translation like
     * 'Degrees Fahrenheit'
     *  - 'sign' (string) Sign/ short symbol like: '°'|'°F'
     *  - 'code' => 'F',
     */
    public function __construct( array $input = array() )
    {
        $this->_input = $input;
    }


    /**
     * Returns the item key/identifier name.
     *
     * @param mixed $default Default (null) return value if key was not set
     *
     * @return string Item name key/identifier
     */
    public function getKey( $default = null )
    {
        return (isset( $this->_input['key'] ) ? (string) $this->_input['key'] : $default);
    }


    /**
     * Sets the item key name/ identifier.
     *
     * @param string $value Item key/itenifier
     *
     * @return void
     */
    public function setKey( string $value ): void
    {
        if ( isset( $this->_input['key'] ) && $value === $this->_input['key'] ) {
            return;
        }

        $this->_input['key'] = (string) $value;
        $this->_modified = true;
    }


    /**
     * Returns the item label or $default if not set
     *
     * @param mixed $default Default return value if label not exists
     *
     * @return string|mixed Returns the item label or $default
     */
    public function getLabel( $default = null )
    {
        return (isset( $this->_input['label'] )) ? (string) $this->_input['label'] : $default;
    }


    /**
     * Sets the item label.
     *
     * @param string $value Item label to be set
     *
     * @return void
     */
    public function setLabel( string $value ): void
    {
        if ( $value === $this->getLabel() ) {
            return;
        }

        $this->_input['label'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the item sign/symbol or $default if not set
     *
     * @param mixed $default Default return value if sign not exists
     *
     * @return string|mixed Returns the item sign or $default
     */
    public function getSign( $default = null )
    {
        return (isset( $this->_input['sign'] )) ? (string) $this->_input['sign'] : $default;
    }


    /**
     * Sets the item sign/ symbol sign.
     *
     * @param string $value Item label to be set
     *
     * @return void
     */
    public function setSign( string $value ): void
    {
        if ( $value === $this->getSign() ) {
            return;
        }

        $this->_input['sign'] = $value;
        $this->_modified = true;
    }


    /**
     * Returns the item code (short code of the key name) or $default.
     *
     * @param mixed $default Default return value if code not exists
     *
     * @return string|mixed Returns the item sign or $default
     */
    public function getCode( $default = null )
    {
        return (isset( $this->_input['code'] )) ? (string) $this->_input['code'] : $default;
    }


    /**
     * Sets the item code (short code of the key name).
     *
     * @param string $value Item code to be set
     *
     * @return void
     */
    public function setCode( string $value ): void
    {
        if ( $value === $this->getCode() ) {
            return;
        }

        $this->_input['code'] = $value;
        $this->_modified = true;
    }


    /**
     * Tests if the Item was modified or not.
     *
     * @return boolean True if modified otherwise false
     */
    public function isModified()
    {
        return $this->_modified;
    }


    /**
     * Sets the modified flag of the object.
     */
    public function setModified()
    {
        $this->_modified = true;
    }


    /**
     * Returns the list of key/values pairs of item properties HTML encoded.
     *
     * Formats item values HTML compilant e.g: & goes &amp; , " goes &quot; ...
     *
     * @return array Returns item properties as key/value pairs
     */
    public function toHtml()
    {
        return array(
            'key' => htmlspecialchars( $this->getKey(), ENT_QUOTES, 'UTF-8', false ),
            'label' => htmlspecialchars( $this->getLabel(), ENT_QUOTES, 'UTF-8', false ),
            'sign' => htmlspecialchars( $this->getSign(), ENT_QUOTES, 'UTF-8', false ),
            'code' => $this->getCode(),
        );
    }


    /**
     * Returns the list of key/values pairs of item properties.
     *
     * @return array Returns item properties as key/value pairs
     */
    public function toArray()
    {
        return array(
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            'sign' => $this->getSign(),
            'code' => $this->getCode(),
        );
    }

}