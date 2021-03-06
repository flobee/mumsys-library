<?php

/**
 * Mumsys_Variable_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 *
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @copyright Copyright (c) 2006 by Florian Blasel for FloWorks Company
 * @author Florian Blasel <flobee.code@gmail.com>
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 * Created: 2006 based on Mumsys_Field, renew 2016 PHP >= 7
 */


/**
 * Abstact class for common features for the item and/or manager
 *
 * @category    Mumsys
 * @package     Library
 * @subpackage  Variable
 */
abstract class Mumsys_Variable_Abstract
    extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.2.1';

    /**
     * Variable / validation types.
     *
     * PHP types and optional additional types for the item to set, for the
     * manger to be implemented.
     * @var array List of types
     */
    protected static $_types = array(
        'string',
        'char',
        'varchar',
        'text',
        'tinytext',
        'longtext',
        'int',
        'integer',
        'smallint',
        'float',
        'double',
        'numeric',
        'boolean',
        'array',
        'object',
        'date',
        'datetime',
        'timestamp',
        'email',
        'ipv4',
        'ipv6',
        'unittest'
    );


    /**
     * Returns the list of possible item types.
     * {@link Mumsys_Variable_Abstract::getTypes()}
     *
     * @return array List of types for the item
     */
    public static function getTypes()
    {
        return self::$_types;
    }

}
