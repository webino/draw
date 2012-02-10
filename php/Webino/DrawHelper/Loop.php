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

use Webino_ViewHelper_Node_Interface as NodeHelper;

/**
 * Draw helper to draw array lists
 *
 * example of options:
 *
 * - xpath  = '//*[@data-draw="example-list-item"]'
 * - helper = loop
 * - var    = exampleList
 * - nodes.link.xpath                                 = './/a'
 * - nodes.link.value                                 = "{$->name}"
 * - nodes.link.attribs.title                         = "{$->name}"
 * - nodes.link.filters.->name.stringToUpper.encoding = "utf-8"
 * - nodes.link.helpers.->name.url.options.uri        = "{$->name}/?test"
 * - nodes.link.helpers.->name.url.route              = null
 * - nodes.link.helpers.->name.url.reset              = 0
 * - nodes.link.helpers.->name.url.encode             = 1
 *
 * example of options (custom variables):
 *
 * - fetch.customVar                    = value.in.the.depth
 * - nodes.note.xpath                   = './/span'
 * - nodes.note.fetch.customVarRelative = value.in.the.depth
 * - nodes.note.value                   = "{$customVar} - {$customVarRelative}"
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
class Webino_DrawHelper_Loop
    extends Webino_DrawHelper_Loop_Abstract
{
    /**
     * Setting key of initial index
     */
    const INDEX_KEYNAME = 'index';

    /**
     * Setting key of attributes to set
     */
    const ATTRIBS_KEYNAME = 'attribs';

    /**
     * Setting key of nodes in row to draw
     */
    const NODES_KEYNAME = 'nodes';

    /**
     * Setting key of xpath to node in row
     */
    const XPATH_KEYNAME = 'xpath';

    /**
     * Setting key of node in row value
     */
    const VALUE_KEYNAME = 'value';

    /**
     * Setting key of html enabled
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
     * Name of key variable placeholder
     */
    const KEY_VAR = 'key';

    /**
     * Name of index variable placeholder
     */
    const INDEX_VAR = 'index';

    /**
     * Format for variable placholder
     */
    const VAR_FORMAT = '{$%s}';

    /**
     * Format for view variable placholder
     */
    const VIEWVAR_FORMAT = '{$->%s}';

    /**
     * It's fired in draw loop
     *
     * @param DOMNode $newNode
     * @param string  $key
     * @param array   $item
     * @param int     $index
     * @param DOMNode $parentNode
     */
    public function inLoop(
        DOMNode $newNode, $key, array &$item, &$index, DOMNode $parentNode
    )
    {
        parent::inLoop($newNode, $key, $item, $index, $parentNode);

        $node = clone $this->view->node($newNode);

        $translation = $this->view->varTranslator(
            $this->_translationFromItem($item)
        )->fetch($this->view->getVars(), $this->_options)->toArray();

        $this->_addKey($key, $translation)->_addIndex($index, $translation);

        $this->_nodeAttribs(
            $node, $this->_options,
            $this->view->varTranslator($translation)
            ->apply($this->_options)->toArray()
        );

        $this->_nodesCycle($node, $item, $translation);
    }

    /**
     * Add key variable into translation
     *
     * @param int   $key
     * @param array $translation
     *
     * @return Webino_DrawHelper_Loop
     */
    protected function _addKey($key, array &$translation)
    {
        $translation[sprintf(self::VAR_FORMAT, self::KEY_VAR)] = $key;

        return $this;
    }

    /**
     * Add index variable into translation
     *
     * @param int   $index
     * @param array $translation
     *
     * @return Webino_DrawHelper_Loop
     */
    protected function _addIndex($index, array &$translation)
    {
        if (empty($this->_options[self::INDEX_KEYNAME])) {
            $this->_options[self::INDEX_KEYNAME] = 1;
        }

        $translation[sprintf(self::VAR_FORMAT, self::INDEX_VAR)]
            = $index + $this->_options[self::INDEX_KEYNAME];

        return $this;
    }

    /**
     * Get translation table of item variables
     *
     * @param array $item
     *
     * @return array
     */
    protected function _translationFromItem(array $item)
    {
        $translation = array();

        foreach ($item as $property => $value) {
            $translation[sprintf(self::VIEWVAR_FORMAT, $property)] = $value;
        }

        return $translation;
    }

    /**
     * Set node attribs by options
     *
     * @param NodeHelper $node
     * @param array      $options
     * @param array      $translation
     *
     * @return Webino_DrawHelper_Loop
     */
    protected function _nodeAttribs(
        NodeHelper $node, array $options, array $translation
    )
    {
        if (isset($options[self::ATTRIBS_KEYNAME])) {

            foreach ($options[self::ATTRIBS_KEYNAME] as &$attribValue) {
                $attribValue = strtr($attribValue, $translation);
            }

            $node->setAttribs($options[self::ATTRIBS_KEYNAME]);
        }

        return $this;
    }

    /**
     * Perform inLoop nodes cycle
     *
     * @param NodeHelper $node
     * @param array      $item
     * @param array      $translation
     *
     * @return Webino_DrawHelper_Loop
     */
    private function _nodesCycle(
        NodeHelper $node, array $item, array $translation
    )
    {
        if (!isset($this->_options[self::NODES_KEYNAME])) {
            
            return $this;
        }

        foreach ($this->_options[self::NODES_KEYNAME] as $optionNode) {

            $subNode = $node->query($optionNode[self::XPATH_KEYNAME]);

            $nodeTranslation = $this->view->varTranslator(
                $this->_nodeTranslation($subNode, $optionNode, $translation)
            )->fetch($item, $optionNode)->apply($optionNode)->toArray();

            $this->_nodeAttribs($subNode, $optionNode, $nodeTranslation);

            if (!isset($optionNode[self::VALUE_KEYNAME])) {
                
                continue;
            }

            if (isset($optionNode[self::ISHTML_KEYNAME])
                && $optionNode[self::ISHTML_KEYNAME]
            ) {
                $node->query($optionNode[self::XPATH_KEYNAME])->setHtml(
                    strtr($optionNode[self::VALUE_KEYNAME], $nodeTranslation)
                );

            } else {
                $node->query($optionNode[self::XPATH_KEYNAME])->setValue(
                    strtr($optionNode[self::VALUE_KEYNAME], $nodeTranslation)
                );
            }

        }

        return $this;
    }

    /**
     * Add text, html value and attributes from node to translation
     *
     * @param NodeHelper $node
     * @param array      $options
     * @param array      $translation
     *
     * @return array
     */
    protected function _nodeTranslation(
        NodeHelper $node, array $options, array $translation
    )
    {
        $translation[self::VALUE_PLACEHOLDER] = $node->item(0)->nodeValue;

        if (!empty($options[self::VALUE_KEYNAME])
            && false !== strpos(
                $options[self::VALUE_KEYNAME], self::HTML_PLACEHOLDER
            )
        ) {
            $translation[self::HTML_PLACEHOLDER] = $node->getHtml();
        }

        foreach ($node->item(0)->attributes as $attrib) {
            $translation[
                sprintf(self::VAR_FORMAT, $attrib->name)
            ] = $attrib->value;
        }

        return $translation;
    }
}
