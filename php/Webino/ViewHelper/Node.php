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
 * @subpackage ViewHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

/**
 * View helper of methods over node
 *
 * @category   Webino
 * @package    Draw
 * @subpackage ViewHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
class Webino_ViewHelper_Node
    extends    Webino_ViewHelper_Abstract
    implements Webino_ViewHelper_Node_Interface
{
    /**
     * XHTMl document node
     *
     * @var DOMNode
     */
    private $_node;

    /**
     * XHTMl document node
     *
     * @var DOMNode
     */
    private $_nodeList;

    /**
     * Find node by xpath relative to node
     *
     * @param DOMNode $node
     * 
     * @return Webino_ViewHelper_Node
     */
    public function node(DOMNode $node)
    {
        $this->_node     = $node;
        $this->_nodeList = null;

        return $this;
    }

    /**
     * Return item from node list
     *
     * @param int $index
     *
     * @return DOMNode
     */
    public function item($index)
    {
        if (!$this->_nodeList) {
            
            return $this->_node;
        }

        return $this->_nodeList->item($index);
    }

    /**
     * Query for node by xpath
     *
     * @param string $xpath
     *
     * @return Webino_ViewHelper_Node
     */
    public function query($xpath)
    {
        $this->_nodeList = $this->_node->ownerDocument->xpath->query(
            $xpath, $this->_node
        );

        return $this;
    }

    /**
     * Set node value
     *
     * @param string $value
     *
     * @return Webino_ViewHelper_Node
     */
    public function setValue($value)
    {
        if (!$this->_nodeList) {
            $list = array($this->_node);
        } else {
            $list = &$this->_nodeList;
        }

        foreach ($list as $node) {
            $node->nodeValue = $this->view->escape($value);
        }

        return $this;
    }

    /**
     * Set node value as HTML string
     *
     * @param string $html
     *
     * @return Webino_ViewHelper_Node
     */
    public function setHtml($html)
    {
        if (!$this->_nodeList) {
            $list = array($this->_node);
        } else {
            $list = &$this->_nodeList;
        }
        
        foreach ($list as $node) {
            $node->nodeValue = '';
            $node->appendChild($this->frag($html, $node->ownerDocument));
        }

        return $this;
    }

    /**
     * Set node value as HTML string
     *
     * @param string $html
     *
     * @return Webino_ViewHelper_Node
     */
    public function getHtml()
    {
        if (!$this->_nodeList) {
            $list = array($this->_node);
        } else {
            $list = &$this->_nodeList;
        }

        $html = '';

        foreach ($list as $node) {
            foreach ($node->childNodes as $child) {
                $html.= $child->ownerDocument->saveXML($child);
            }
        }

        return $html;
    }

    /**
     * Set node attributes as array
     *
     * @param array $attribs
     *
     * @return Webino_ViewHelper_Node
     */
    public function setAttribs(array $attribs)
    {
        if (!$this->_nodeList) {
            $list = array($this->_node);
        } else {
            $list = &$this->_nodeList;
        }

        foreach ($list as $node) {
            foreach ($attribs as $name => $value) {
                $node->setAttribute($name, $value);
            }
        }

        return $this;
    }

    /**
     * Append child to node
     *
     * @param DOMNode $node
     * @param bool    $insertBefore
     *
     * @return Webino_ViewHelper_Node
     */
    public function appendTo(DOMNode $node, $insertBefore = false)
    {
        if (!$this->_nodeList) {
            $list = array($this->_node);
        } else {
            $list = &$this->_nodeList;
        }

        foreach ($list as $child) {
            if ($insertBefore) {
                $node->parentNode->insertBefore($child, $node);
            } else {
                $node->appendChild($child);
            }
        }

        return $this;
    }
}
