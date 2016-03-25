<?php

/*{{{*/
/**
 * Mumsys_Mvc_Templates_Text_Abstract
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * @copyright Copyright (c) 2016 by Florian Blasel for FloWorks Company
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Mvc
 * Created: 2016-02-04
 * @filesource
 */
/*}}}*/


/**
 * Default abstract class for stdout output e.g. text for the shell output
 */
abstract class Mumsys_Mvc_Templates_Text_Abstract
    extends Mumsys_Mvc_Display_Control_Stdout_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * Page title for the output
     * @var string
     */
    protected $_pagetitle = '';


    /**
     * Initialize the display text object.
     *
     * @param Mumsys_Context $context Context object
     * @param array $opts Optional options to setup the frontend controller
     */
    public function __construct( Mumsys_Context $context, array $options = array() )
    {
        if (isset($options['pageTitle'])) {
            $this->_pagetitle = (string)$options['pageTitle'];
        }
    }

}
