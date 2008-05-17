<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @since      File available since Release 1.3.0
 */

require_once 'HTML/Template/Flexy.php';
require_once 'PEAR.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/Service/FlexyElement.php';

// {{{ Piece_Unity_Service_Rendering_Flexy

/**
 * A rendering service based on HTML_Template_Flexy.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Flexy
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @since      Class available since Release 1.3.0
 */
class Piece_Unity_Service_Rendering_Flexy
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_options;
    var $_controller;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Sets the configuration options for a HTML_Template_Flexy object and
     * an arbitrary controller object to the properties.
     *
     * @param array $options
     * @param mixed $controller
     */
    function Piece_Unity_Service_Rendering_Flexy($options, $controller = null)
    {
        $this->_options = $options;
        $this->_controller = $controller;
    }

    // }}}
    // {{{ render()

    /**
     * Renders a HTML or HTML fragment.
     *
     * @param string                  $file
     * @param Piece_Unity_ViewElement &$viewElement
     * @throws PIECE_UNITY_ERROR_NOT_FOUND
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     */
    function render($file, &$viewElement)
    {
        $flexy = &new HTML_Template_Flexy($this->_options);
        $resultOfCompile = $flexy->compile($file);
        if (PEAR::isError($resultOfCompile)) {
            if ($flexy->currentTemplate === false) {
                Piece_Unity_Error::pushPEARError($resultOfCompile,
                                                 PIECE_UNITY_ERROR_NOT_FOUND,
                                                 "The HTML template file [ $file ] is not found."
                                                 );
                return;
            }

            Piece_Unity_Error::pushPEARError($resultOfCompile,
                                             PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                             'Failed to invoke HTML_Template_Flexy::compile() for any reasons.'
                                             );
            return;
        }

        $viewElements = $viewElement->getElements();
        if (is_null($this->_controller)) {
            $this->_controller = (object)$viewElements;
        } else {
            foreach (array_keys($viewElements) as $element) {
                if (!is_object($viewElements[$element])) {
                    $this->_controller->$element = $viewElements[$element];
                } else {
                    $this->_controller->$element = &$viewElements[$element];
                }
            }
        }

        $flexyElement = &new Piece_Unity_Service_FlexyElement();
        $flexyElement->setViewElement($viewElement);
        $resultOfOutputObject = $flexy->outputObject($this->_controller,
                                                     $flexyElement->createFormElements()
                                                     );
        if (PEAR::isError($resultOfOutputObject)) {
            Piece_Unity_Error::pushPEARError($resultOfOutputObject,
                                             PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                             'Failed to invoke HTML_Template_Flexy::outputObject() for any reasons.'
                                             );
        }
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
?>
