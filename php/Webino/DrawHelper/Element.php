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
 * Draw helper to set node value and attributes
 *
 * Supports filters and helpers.
 * 
 * example of options:
 *
 * - xpath         = '//*[@data-draw="element-example"]'
 * - helper        = element
 * - value         = 'node value'
 * - attribs.href  = 'http://...'
 * - attribs.title = '...'
 *
 * example of options (remove node):
 *
 * - remove = 1
 *
 * example of options (as HTML):
 *
 * - value  = 'node value'
 * - isHtml = 1
 *
 * example of options (with value placeholder):
 *
 * - value  = 'prefix {$value} suffix'
 *
 * example of options (with html placeholder):
 *
 * - value  = '&lt;div&gt;{$html}&lt;/div&gt;'
 * - isHtml = 1
 *
 * example of options (with attribute to value):
 *
 * - value  = 'Title attribute: {$title}'
 *
 * example of options (as child):
 *
 * - asChild.name  = 'span'
 * - asChild.value = 'node value'
 * - attribs.href  = 'http://...'
 * - attribs.title = '...'
 *
 * example of options (as child, inserted before):
 * 
 * - asChild.name         = 'span'
 * - asChild.insertBefore = 1
 *
 * example of options (using filters, helpers):
 *
 * - filters.value.stringToUpper.encoding = "utf-8"
 * - helpers.value.formButton.name        = 'example_btn'
 * - helpers.value.formButton.value       = '{$value}'
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
class Webino_DrawHelper_Element
    extends Webino_DrawHelper_Abstract
{
    /**
     * Name of remove node option key
     *
     * Node is removed if this option is set like:
     * - remove = 1
     */
    const REMOVE_KEYNAME = 'remove';
    
    /**
     * Name of as child option key
     *
     * New node is added as child,
     * if this option is set like:
     * - asChild.name = span
     */
    const ASCHILD_KEYNAME = 'asChild';

    /**
     * Name of insert before key
     *
     * New node is inserted before selected node,
     * if this option is set like:
     * - insertBefore = 1
     */
    const BEFORE_KEYNAME = 'insertBefore';

    /**
     * Name of attributes key
     *
     * Array of node attributes in name = value pair
     */
    const ATTRIBS_KEYNAME = 'attribs';

    /**
     * Name of node name key
     *
     * Name of new node, only if asChild is set.
     */
    const NAME_KEYNAME = 'name';

    /**
     * Name of node value key
     *
     * Text value of node.
     */
    const VALUE_KEYNAME = 'value';

    /**
     * Name of html node content enabled key
     *
     * If this option is true node value is added like HTML
     */
    const ISHTML_KEYNAME = 'isHtml';

    /**
     * Replacement for default value of node
     */
    const VALUE_PLACEHOLDER = '{$value}';

    /**
     * Replacement for for HTML code of node
     */
    const HTML_PLACEHOLDER = '{$html}';

    /**
     * Variable format
     */
    const VAR_FORMAT = '{$%s}';

    /**
     * Helper for replacing variables in options
     *
     * @var Webino_ViewHelper_VarTranslator
     */
    protected $_varTranslator;

    /**
     * Object wrapper for DOMNode
     *
     * @var Webino_ViewHelper_Node
     */
    protected $_nodeHelper;

    /**
     * Draw template node
     *
     * @param DOMNode $node
     */
    public function draw(DOMNode $node)
    {
        if (!empty($this->_options[self::REMOVE_KEYNAME])) {
            $node->parentNode->removeChild($node);

            return false;
        }

        $this->_nodeHelper = $this->view->node($node);

        $this->_varTranslator = $this->view->varTranslator(
            $this->_translation($node)
        )->fetch(
            $this->view->getVars(), $this->_options
        )->apply($this->_options);

        $this->_value();

        $this->_nodeHelper = $this->view->node($this->_asChild($node));

        $this->_asChildValue()->_attribs()->_appendTo($node);
    }

    /**
     * Return translation array from node
     *
     * @param DOMNode $node
     *
     * @return array
     */
    protected function _translation(DOMNode $node)
    {
        $translation = array(
            self::VALUE_PLACEHOLDER => $node->nodeValue,
        );

        if (!empty($this->_options[self::VALUE_KEYNAME])
            && false !== strpos(
                $this->_options[self::VALUE_KEYNAME], self::HTML_PLACEHOLDER
            )
        ) {
            $translation[
                self::HTML_PLACEHOLDER
            ] = $this->_nodeHelper->getHtml();
        }

        foreach ($node->attributes as $attrib) {
            $translation[
                sprintf(self::VAR_FORMAT, $attrib->name)
            ] = $attrib->value;
        }

        return $translation;
    }

    /**
     * Create new node as child or before selected node
     *
     * @param DOMNode $node
     *
     * @return WebinoDraw_Helper_Node
     */
    protected function _asChild(DOMNode $node)
    {
        if (!isset($this->_options[self::ASCHILD_KEYNAME])) {

            return $node;
        }

        return $node->ownerDocument->createElement(
            $this->_options[self::ASCHILD_KEYNAME][self::NAME_KEYNAME]
        );
    }

    /**
     * Append child to node
     *
     * @param DOMNode $node
     *
     * @return Webino_DrawHelper_Element
     */
    protected function _appendTo(DOMNode $node)
    {
        if (!isset($this->_options[self::ASCHILD_KEYNAME])) {

            return $this;
        }

        $this->_nodeHelper->appendTo(
            $node, !empty($this->_options[self::BEFORE_KEYNAME])
        );

        return $this;
    }

    /**
     * Set node attributes from options
     *
     * @return Webino_DrawHelper_Element
     */
    protected function _attribs()
    {
        if (empty($this->_options[self::ATTRIBS_KEYNAME])) {
            
            return $this;
        }

        $attribs = array();

        foreach ($this->_options[self::ATTRIBS_KEYNAME] as $name => $value) {
            $attribs[$name] = $this->_varTranslator->translate($value);
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

        $value = $this->_varTranslator->translate(
            $this->_options[self::ASCHILD_KEYNAME][self::VALUE_KEYNAME]
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
     * If html is enabled set value as html string else escape
     *
     * @param DOMNode $node
     */
    protected function _value()
    {
        if (!isset($this->_options[self::VALUE_KEYNAME])) {

            return $this;
        }

        $value = $this->_varTranslator->translate(
            $this->_options[self::VALUE_KEYNAME]
        );

        if (!empty($this->_options[self::ISHTML_KEYNAME])) {
            $this->_nodeHelper->setHtml($value);

        } else {
            $this->_nodeHelper->setValue($value);
        }

        return $this;
    }
}
