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
 * Draw helper to set view variable value to node
 *
 * Supports filters and helpers.
 *
 * example of options:
 *
 * - xpath                                      = '//*[@class="example"]'
 * - helper                                     = viewVar
 * - value                                      = "{$value} {$->viewVar}"
 * - attribs.test                               = "{$->viewVar}"
 * - attribs.test2                              = "{$->viewVar2}"
 * - filters.->viewVar.stringToLower.encoding   = "utf-8"
 * - filters.->viewVar.pregReplace.matchPattern = '~footer~'
 * - filters.->viewVar.pregReplace.replacement  = 'footer replacement'
 * - helpers.->viewVar.currency.value           = "{$->shopcartPrice}"
 * - helpers.->viewVar.currency.currency        = "sk_SK"
 * - helpers.->viewVar.url.value.uri            = "{$->shopcartPrice}"
 *
 * example of options (custom variables):
 *
 * - fetch.customVar  = value.in.the.depth
 * - fetch.customVar2 = value.deepest.value
 * - value            = "{$customVar} - {$->shopcartPrice} - {$customVar2}"
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
class Webino_DrawHelper_ViewVar
    extends Webino_DrawHelper_Element
{
    /**
     * Variable match pattern
     */
    const VIEWVAR_PATTERN = '~\{\$->([a-zA-Z0-9_]+)\}~';

    /**
     * Variables translation
     *
     * @var array
     */
    protected $_translation;

    /**
     * Return translation with view variables
     *
     * @param string $string
     *
     * @return array
     */
    protected function _viewVarsTranslation($string)
    {
        preg_match_all(self::VIEWVAR_PATTERN, $string, $match);

        $translation = array();

        foreach ($match[1] as $key => $varName) {

            if (!isset($this->view->$varName)) {
                continue;
            }

            $translation[$match[0][$key]] = $this->view->$varName;

        }

        return $translation;
    }

    /**
     * Set attributes with variables to node
     *
     * @return Webino_DrawHelper_ViewVar
     */
    protected function _attribs()
    {
        if (empty($this->_options[self::ATTRIBS_KEYNAME])) {

            return $this;
        }

        $attribs = array();

        foreach ($this->_options[self::ATTRIBS_KEYNAME] as $name => $value) {

            $attribs[$name] = strtr($value, $this->_valueTranslation($value));
        }

        $this->_nodeHelper->setAttribs($attribs);

        return $this;
    }

    /**
     * Set value to asChild node
     *
     * @return Webino_DrawHelper_Element
     */
    protected function _asChildValue()
    {
        if (
            empty($this->_options[self::ASCHILD_KEYNAME][self::VALUE_KEYNAME])
        ) {
            return $this;
        }

        $value = strtr(
            $this->_options[self::ASCHILD_KEYNAME][self::VALUE_KEYNAME],
            $this->_valueTranslation(
                $this->_options[self::ASCHILD_KEYNAME][self::VALUE_KEYNAME]
            )
        );

        if (
            !empty($this->_options[self::ASCHILD_KEYNAME][self::ISHTML_KEYNAME])
        ) {

            $this->_nodeHelper->setHtml($value);

            return $this;

        }

        $this->_nodeHelper->setValue($value);

        return $this;
    }

    /**
     * Return translation from value
     *
     * @param string $value
     *
     * @return array
     */
    protected function _valueTranslation($value)
    {
        return array_merge(
            $this->_translation,
            $this->view->varTranslator(
                $this->_viewVarsTranslation($value)
            )->apply($this->_options)->toArray()
        );
    }

    /**
     * Set value with variables to node
     *
     * @return Webino_DrawHelper_ViewVar
     */
    protected function _value()
    {
        if (!isset($this->_options[self::VALUE_KEYNAME])) {

            return $this;
        }

        $value = strtr(
            $this->_options[self::VALUE_KEYNAME],
            $this->_valueTranslation($this->_options[self::VALUE_KEYNAME])
        );

        if (!empty($this->_options[self::ISHTML_KEYNAME])) {
            $this->_nodeHelper->setHtml($value);

        } else {
            $this->_nodeHelper->setValue($value);
        }

        return $this;
    }

    /**
     * Set view variables to node
     *
     * @param DOMNode $node
     *
     * @return void
     */
    public function draw(DOMNode $node)
    {
        $this->_nodeHelper = $this->view->node($node);

        $this->_varTranslator = $this->view->varTranslator(
            $this->_translation($node)
        );

        $this->_translation = $this->_varTranslator->fetch(
            $this->view->getVars(), $this->_options
        )->apply($this->_options)->toArray();

        $this->_value();

        $this->_nodeHelper = $this->view->node($this->_asChild($node));

        $this->_asChildValue()->_attribs()->_appendTo($node);
    }
}
