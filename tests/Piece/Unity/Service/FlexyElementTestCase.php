<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Flexy
 * @copyright  2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.0.0
 */

require_once realpath(dirname(__FILE__) . '/../../../prepare.php');
require_once 'PHPUnit.php';
require_once 'Piece/Unity/Service/FlexyElement.php';
require_once 'Piece/Unity/Context.php';

// {{{ Piece_Unity_Service_FlexyElementTestCase

/**
 * TestCase for Piece_Unity_Service_FlexyElement
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Flexy
 * @copyright  2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Piece_Unity_Service_FlexyElementTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    function tearDown()
    {
        Piece_Unity_Context::clear();
    }

    function testAssociativeArrayShouldBeAbleToSetAsAttribute()
    {
        $attributes = array('bar' => 'baz', 'baz' => 'qux');
        $flexyForm = &new Piece_Unity_Service_FlexyElement();
        $flexyForm->setAttributes('foo', $attributes);
        $context = &Piece_Unity_Context::singleton();
        $viewElement = &$context->getViewElement();
        $elements = $viewElement->getElement('_elements');

        $this->assertEquals($attributes, $elements['foo']['_attributes']);
    }

    /**
     * @since Method available since Release 1.1.0
     */
    function testFieldValuesShouldBeRestoredByValidationSetAndContainer()
    {
        $cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
        $context = &Piece_Unity_Context::singleton();
        $validation = &$context->getValidation();
        $validation->setConfigDirectory($cacheDirectory);
        $validation->setCacheDirectory($cacheDirectory);
        $config = &$validation->getConfiguration();
        $config->addValidation('bar', 'Length', array('min' => 1, 'max' => 255));
        $container = &new stdClass();
        $container->foo = 'bar';
        $container->bar = 'baz';
        $flexyForm = &new Piece_Unity_Service_FlexyElement();
        $flexyForm->restoreValues('FieldValues', $container);

        $viewElement = &$context->getViewElement();
        $elements = $viewElement->getElement('_elements');

        $this->assertEquals(2, count(array_keys($elements)));
        $this->assertTrue(array_key_exists('foo', $elements));
        $this->assertEquals('bar', $elements['foo']['_value']);
        $this->assertTrue(array_key_exists('bar', $elements));
        $this->assertEquals('baz', $elements['bar']['_value']);

        $cache = &new Cache_Lite_File(array('cacheDir' => "$cacheDirectory/",
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
