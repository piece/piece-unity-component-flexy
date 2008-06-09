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
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @since      File available since Release 1.0.0
 */

require_once 'HTML/Template/Flexy.php';
require_once 'Piece/Unity/Plugin/Renderer/HTML.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/Service/Rendering/Flexy.php';

// {{{ Piece_Unity_Plugin_Renderer_Flexy

/**
 * A renderer based on HTML_Template_Flexy.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Flexy
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2007 KUMAKURA Yousuke <kumatch@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @since      Class available since Release 1.0.0
 */
class Piece_Unity_Plugin_Renderer_Flexy extends Piece_Unity_Plugin_Renderer_HTML
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_configurationOptions = array('templateDir' => null,
                                       'compileDir'  => null,
                                       'debug'       => 0,
                                       'plugins'     => array()
                                       );
    var $_controller;

    /**#@-*/

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _getOptions()

    /**
     * Gets an array which contains configuration options for
     * a HTML_Template_Flexy object.
     *
     * @return array
     */
    function _getOptions()
    {
        $options = array('fatalError'      => HTML_TEMPLATE_FLEXY_ERROR_RETURN,
                         'privates'        => true,
                         'globals'         => true,
                         'globalfunctions' => true
                         );

        foreach (array_keys($this->_configurationOptions) as $point) {
            $$point = $this->_getConfiguration($point);
            if (!is_null($$point)) {
                $options[$point] = $$point;
            }
        }

        $externalPlugins = $this->_getConfiguration('externalPlugins');
        if (is_array($externalPlugins) && count(array_keys($externalPlugins))) {
            $options['plugins'] = array_merge($options['plugins'], $externalPlugins);
        }

        return $options;
    }

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     */
    function _initialize()
    {
        parent::_initialize();
        $this->_addConfigurationPoint('templateExtension', '.html');
        $this->_addConfigurationPoint('useController', false);
        $this->_addConfigurationPoint('controllerClass');
        $this->_addConfigurationPoint('controllerDirectory');
        $this->_addConfigurationPoint('externalPlugins', array());
        foreach ($this->_configurationOptions as $point => $default) {
            $this->_addConfigurationPoint($point, $default);
        }
    }

    // }}}
    // {{{ _doRender()

    /**
     * Renders a HTML.
     *
     * @param boolean $isLayout
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_UNITY_ERROR_NOT_READABLE
     * @throws PIECE_UNITY_ERROR_NOT_FOUND
     * @throws PIECE_UNITY_ERROR_CANNOT_READ
     */
    function _doRender($isLayout)
    {
        $options = $this->_getOptions();
        if (!$isLayout) {
            $view = $this->_context->getView();
        } else {
            $layoutDirectory = $this->_getConfiguration('layoutDirectory');
            if (!is_null($layoutDirectory)) {
                $options['templateDir'] = $layoutDirectory;
            }

            $layoutCompileDirectory = $this->_getConfiguration('layoutCompileDirectory');
            if (!is_null($layoutCompileDirectory)) {
                $options['compileDir'] = $layoutCompileDirectory;
            }

            $view = $this->_getConfiguration('layoutView');
        }

        if ($this->_getConfiguration('useController')
            && is_null($this->_controller)
            ) {
            $this->_controller = &$this->_createController();
            if (Piece_Unity_Error::hasErrors('exception')) {
                return;
            }
        }

        $file = str_replace('_', '/', str_replace('.', '', $view)) . $this->_getConfiguration('templateExtension');
        $viewElement = &$this->_context->getViewElement();

        $rendering =
            &new Piece_Unity_Service_Rendering_Flexy($options, $this->_controller);
        $rendering->render($file, $viewElement);
        if (Piece_Unity_Error::hasErrors('exception')) {
            $error = Piece_Unity_Error::pop();
            if ($error['code'] == PIECE_UNITY_ERROR_NOT_FOUND) {
                Piece_Unity_Error::push('PIECE_UNITY_PLUGIN_RENDERER_HTML_ERROR_NOT_FOUND',
                                        $error['message'],
                                        'exception',
                                        array(),
                                        $error
                                        );
                return;
            }

            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                    "Failed to invoke the plugin [ {$this->_name} ].",
                                    'exception',
                                    array(),
                                    $error
                                    );
        }
    }

    // }}}
    // {{{ _prepareFallback()

    /**
     * Prepares another view as a fallback.
     */
    function _prepareFallback()
    {
        $config = &$this->_context->getConfiguration();

        $fallbackDirectory = $this->_getConfiguration('fallbackDirectory');
        if (!is_null($fallbackDirectory)) {
            $config->setConfiguration('Renderer_Flexy',
                                      'templateDir',
                                      $fallbackDirectory
                                      );
        }

        $fallbackCompileDirectory =
            $this->_getConfiguration('fallbackCompileDirectory');
        if (!is_null($fallbackCompileDirectory)) {
            $config->setConfiguration('Renderer_Flexy',
                                      'compileDir',
                                      $fallbackCompileDirectory
                                      );
        }
    }

    // }}}
    // {{{ _createController()

    /**
     * Creates a user-defined object used as a controller object by
     * HTML_Template_Flexy::outputObject().
     *
     * @return mixed
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_UNITY_ERROR_NOT_READABLE
     * @throws PIECE_UNITY_ERROR_NOT_FOUND
     * @throws PIECE_UNITY_ERROR_CANNOT_READ
     */
    function &_createController()
    {
        $controllerDirectory = $this->_getConfiguration('controllerDirectory');
        if (is_null($controllerDirectory)) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVALID_CONFIGURATION,
                                    "The value of the configuration point [ controllerDirectory ] on the plug-in [ {$this->_name} ] is required."
                                    );
            $return = null;
            return $return;
        }

        $controllerClass = $this->_getConfiguration('controllerClass');
        if (is_null($controllerClass)) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVALID_CONFIGURATION,
                                    "The value of the configuration point [ controllerClass ] on the plug-in [ {$this->_name} ] is required."
                                    );
            $return = null;
            return $return;
        }

        if (!Piece_Unity_ClassLoader::loaded($controllerClass)) {
            Piece_Unity_ClassLoader::load($controllerClass, $controllerDirectory);
            if (Piece_Unity_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            if (!Piece_Unity_ClassLoader::loaded($controllerClass)) {
                Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                        "The class [ $controllerClass ] not found in the loaded file."
                                        );
                $return = null;
                return $return;
            }
        }

        $controller = &new $controllerClass();
        return $controller;
    }

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
?>
