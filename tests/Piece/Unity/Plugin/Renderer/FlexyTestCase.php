<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 *               2007 KUMAKURA Yousuke <kumatch@users.sourceforge.net>,
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
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2007 KUMAKURA Yousuke <kumatch@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 1.0.0
 */

require_once realpath(dirname(__FILE__) . '/../../../../prepare.php');
require_once 'Piece/Unity/Plugin/Renderer/HTML/CompatibilityTests.php';
require_once 'Piece/Unity/Config.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/Service/FlexyElement.php';
require_once 'Piece/Unity/Plugin/Renderer/Flexy.php';

// {{{ Piece_Unity_Plugin_Renderer_FlexyTestCase

/**
 * Some tests for Piece_Unity_Plugin_Renderer_Flexy.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Flexy
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2007 KUMAKURA Yousuke <kumatch@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Piece_Unity_Plugin_Renderer_FlexyTestCase extends Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_target = 'Flexy';
    var $_expectedOutput = '<body>
  <form name="theform" action="http://pear.php.net" method="post" enctype="application/x-www-form-urlencoded">    <textarea name="test_textarea">Blogs</textarea>
    <select name="test_select"><option value="123">a select option</option><option value="1234" selected>another select option</option></select>
    <input name="test_checkbox" type="checkbox" value="1" checked>
    <input name="test_checkbox_array[]" type="checkbox" value="1" id="tmpId1" checked>1<br>
    <input name="test_checkbox_array[]" type="checkbox" value="2" id="tmpId2" checked>2<br>
    <input name="test_checkbox_array[]" type="checkbox" value="3" id="tmpId3">3<br>

    <input name="test_radio" type="radio" id="test_radio_yes" value="yes" checked>yes<br>
    <input name="test_radio" type="radio" id="test_radio_no" value="no">no<br>
  </form>
</body>
';

    /**#@-*/

    /**#@+
     * @access public
     */

    function testAutomaticFormElements()
    {
        $flexyElement = &new Piece_Unity_Service_FlexyElement();
        $flexyElement->addForm('theform', 'http://pear.php.net');
        $flexyElement->setValue('test_textarea', 'Blogs');
        $flexyElement->setOptions('test_select',
                                  array('123' => 'a select option',
                                        '1234' => 'another select option')
                                  );
        $flexyElement->setValue('test_select', '1234');
        $flexyElement->setValue('test_checkbox', '1');
        $flexyElement->setValue('test_checkbox_array', array(1, 2));
        $flexyElement->setValue('test_radio', 'yes');
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}AutomaticFormElements");
        $config = &$this->_getConfig();
        $context->setConfiguration($config);

        $this->assertEquals($this->_expectedOutput, $this->_render());
    }

    function testDebug()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}NonExistingTemplate");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'debug', 1);
        $context->setConfiguration($config);

        $this->assertTrue(strstr($this->_render(), 'FLEXY DEBUG:'));
    }

    function testControllerShouldBeUsedIfUseControllerIsTrue()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ControllerShouldBeUsedIfUseControllerIsTrue");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'useController', true);
        $config->setConfiguration('Renderer_Flexy', 'controllerClass', 'Piece_Unity_Plugin_Renderer_FlexyTestCase_Controller');
        $config->setConfiguration('Renderer_Flexy', 'controllerDirectory', "{$this->_cacheDirectory}/lib");
        $context->setConfiguration($config);

        $this->assertEquals('<p>bar</p>', rtrim($this->_render()));
    }

    function testControllerShouldNotBeUsedIfUseControllerIsFalse()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ControllerShouldBeUsedIfUseControllerIsTrue");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'useController', false);
        $config->setConfiguration('Renderer_Flexy', 'controllerClass', 'Piece_Unity_Plugin_Renderer_FlexyTestCase_Controller');
        $config->setConfiguration('Renderer_Flexy', 'controllerDirectory', "{$this->_cacheDirectory}/lib");
        $context->setConfiguration($config);

        $this->assertEquals('<p></p>', rtrim($this->_render()));
    }

    function testExceptionShouldBeRaisedIfControllerDirectoryIsNotSpecified()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ControllerShouldBeUsedIfUseControllerIsTrue");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'useController', true);
        $config->setConfiguration('Renderer_Flexy', 'controllerDirectory', "{$this->_cacheDirectory}/lib");
        $context->setConfiguration($config);
        Piece_Unity_Error::disableCallback();
        $this->_render();
        Piece_Unity_Error::enableCallback();

        $this->assertTrue(Piece_Unity_Error::hasErrors());

        $error = Piece_Unity_Error::pop();

        $this->assertEquals(PIECE_UNITY_ERROR_INVOCATION_FAILED, $error['code']);
    }

    function testExceptionShouldBeRaisedIfControllerClassIsNotSpecified()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ControllerShouldBeUsedIfUseControllerIsTrue");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'useController', true);
        $config->setConfiguration('Renderer_Flexy', 'controllerClass', 'Piece_Unity_Plugin_Renderer_FlexyTestCase_Controller');
        $context->setConfiguration($config);
        Piece_Unity_Error::disableCallback();
        $this->_render();
        Piece_Unity_Error::enableCallback();

        $this->assertTrue(Piece_Unity_Error::hasErrors());

        $error = Piece_Unity_Error::pop();

        $this->assertEquals(PIECE_UNITY_ERROR_INVOCATION_FAILED, $error['code']);
    }

    function testExternalPluginShouldBeAbleToUseByExternalPlugins()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ExternalPluginShouldBeAbleToUseByExternalPlugins");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $viewElement->setElement('bar', 1000);
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy',
                                  'externalPlugins',
                                  array('Piece_Unity_Plugin_Renderer_FlexyTestCase_Plugin' => 'Piece/Unity/Plugin/Renderer/FlexyTestCase/Plugin.php')
                                  );
        $context->setConfiguration($config);
        $oldIncludePath = set_include_path("$this->_cacheDirectory/lib" . PATH_SEPARATOR . get_include_path());

        $this->assertEquals('bar:bar:[pear_error: message=&quot;could not find plugin with method: \'numberformat\'&quot; code=0 mode=return level=notice prefix=&quot;&quot; info=&quot;&quot;]:[pear_error: message="could not find plugin with method: \'numberformat\'" code=0 mode=return level=notice prefix="" info=""]', rtrim($this->_render()));

        set_include_path($oldIncludePath);
    }

    function testFlexyBuiltinPluginShouldBeAbleToUseByPlugins()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ExternalPluginShouldBeAbleToUseByExternalPlugins");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $viewElement->setElement('bar', 1000);
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'plugins', array('Savant'));
        $context->setConfiguration($config);
        $oldIncludePath = set_include_path("$this->_cacheDirectory/lib" . PATH_SEPARATOR . get_include_path());

        $this->assertEquals('[pear_error: message=&quot;could not find plugin with method: \'lowerCase\'&quot; code=0 mode=return level=notice prefix=&quot;&quot; info=&quot;&quot;]:[pear_error: message="could not find plugin with method: \'lowerCase\'" code=0 mode=return level=notice prefix="" info=""]:1,000.00:1,000.00', rtrim($this->_render()));

        set_include_path($oldIncludePath);
    }

    function testFlexyBuiltinPluginAndExternalPluginShouldBeAbleToUseTogether()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}ExternalPluginShouldBeAbleToUseByExternalPlugins");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('foo', 'BAR');
        $viewElement->setElement('bar', 1000);
        $config = &$this->_getConfig();
        $config->setConfiguration('Renderer_Flexy', 'plugins', array('Savant'));
        $config->setConfiguration('Renderer_Flexy',
                                  'externalPlugins',
                                  array('Piece_Unity_Plugin_Renderer_FlexyTestCase_Plugin' => 'Piece/Unity/Plugin/Renderer/FlexyTestCase/Plugin.php')
                                  );
        $context->setConfiguration($config);
        $oldIncludePath = set_include_path("$this->_cacheDirectory/lib" . PATH_SEPARATOR . get_include_path());

        $this->assertEquals('bar:bar:1,000.00:1,000.00', rtrim($this->_render()));

        set_include_path($oldIncludePath);
    }

    /**
     * @since Method available since Release 1.3.0
     */
    function testShouldInstantiateAControllerOnce()
    {
        $GLOBALS['PIECE_UNITY_Plugin_Renderer_FlexyTestCase_Controller_InstantiationCount'] = 0;
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}LayoutContent");
        $config = &$this->_getConfig();
        $config->setConfiguration("Renderer_{$this->_target}", 'useLayout', true);
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutView', "{$this->_target}Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutDirectory', "{$this->_cacheDirectory}/templates/Layout");
        $config->setConfiguration("Renderer_{$this->_target}", 'layoutCompileDirectory', "{$this->_cacheDirectory}/compiled-templates/Layout");
        $config->setConfiguration('Renderer_Flexy', 'useController', true);
        $config->setConfiguration('Renderer_Flexy', 'controllerClass', 'Piece_Unity_Plugin_Renderer_FlexyTestCase_Controller');
        $config->setConfiguration('Renderer_Flexy', 'controllerDirectory', "{$this->_cacheDirectory}/lib");
        $context->setConfiguration($config);
        $this->_render();

        $this->assertEquals(1, $GLOBALS['PIECE_UNITY_Plugin_Renderer_FlexyTestCase_Controller_InstantiationCount']);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function &_getConfig()
    {
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Flexy', 'templateDir', "{$this->_cacheDirectory}/templates/Content");
        $config->setConfiguration('Renderer_Flexy', 'compileDir', "{$this->_cacheDirectory}/compiled-templates/Content");

        return $config;
    }

    function _doSetUp()
    {
        $this->_cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
    }

    /**
     * @since Method available since Release 1.2.0
     */
    function &_getConfigForLayeredStructure()
    {
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Flexy', 'templateDir', "{$this->_cacheDirectory}/templates");
        $config->setConfiguration('Renderer_Flexy', 'compileDir', "{$this->_cacheDirectory}/compiled-templates");

        return $config;
    }

    /**#@-*/

    // }}}
}

// }}}

function setBaz(&$foo)
{
    $foo->bar = 'baz';
}

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
