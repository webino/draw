<?php
/**
 * Webino
 *
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://www.webino.org/license/
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@webino.org
 * so we can send you a copy immediately.
 *
 * @category   Webino
 * @package    Draw
 * @subpackage DrawHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

/**
 * Draw helper to set function return value to node value
 *
 * Filters and helpers supported.
 *
 * example of options:
 * 
 * - xpath    = '//*[@data-draw="function-example"]'
 * - helper   = function
 * - function = date
 * - params.0 = Y
 *
 * example of options (custom variables):
 *
 * - function        = strtoupper
 * - fetch.customVar = value.in.the.depth
 * - params.0        = "{$customVar}"
 *
 * example of options (decorating result):
 *
 * - function        = rand
 * - fetch.customVar = value.in.the.depth
 * - value           = "Random number: {$result} {$customVar}"
 *
 * @category   Webino
 * @package    Draw
 * @subpackage DrawHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
class Webino_DrawHelper_Function
    extends Webino_DrawHelper_Abstract
{
    /**
     * Name of function key
     */
    const FUNCTION_KEYNAME = 'function';

    /**
     * Name of function parameters key
     */
    const PARAMS_KEYNAME = 'params';

    /**
     * Name of value key
     */
    const VALUE_KEYNAME = 'value';

    /**
     * Placeholder for function result
     */
    const RESULT_VAR = '{$result}';

    /**
     * Set value of node to function return value
     *
     * @param DOMNode $node
     */
    public function draw(DOMNode $node)
    {
        if (!isset($this->_options[self::PARAMS_KEYNAME])) {
            $this->_options[self::PARAMS_KEYNAME] = array();
        }

        if (!isset($this->_options[self::VALUE_KEYNAME])) {
            $this->_options[self::VALUE_KEYNAME] = self::RESULT_VAR;
        }

        $node->nodeValue = $this->view->escape(
            $this->view->varTranslator(
                array(self::RESULT_VAR => $this->_result())
            )->fetch($this->view->getVars(), $this->_options)
            ->apply($this->_options)
            ->translate($this->_options[self::VALUE_KEYNAME])
        );
    }

    /**
     * Return function result
     *
     * @return string
     */
    private function _result()
    {
        return call_user_func_array(
            $this->_options[self::FUNCTION_KEYNAME],
            $this->view->varTranslator(array())->fetch(
                $this->view->getVars(), $this->_options
            )->apply($this->_options)
            ->translate($this->_options[self::PARAMS_KEYNAME])
        );
    }
}
