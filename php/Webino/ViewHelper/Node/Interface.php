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
 * Interface for node view helper
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
interface Webino_ViewHelper_Node_Interface
{
    /**
     * Query for node by xpath
     *
     * @param string $xpath
     *
     * @return Webino_ViewHelper_Node_Interface
     */
    public function query($xpath);

    /**
     * Return item from node list
     *
     * @param int $index
     *
     * @return DOMNode
     */
    public function item($index);

    /**
     * Set node value
     *
     * @param string $value
     *
     * @return Webino_ViewHelper_Node_Interface
     */
    public function setValue($value);

    /**
     * Set node value as XHTML string
     *
     * @param string $html
     *
     * @return Webino_ViewHelper_Node_Interface
     */
    public function setHtml($html);

    /**
     * Set node value as HTML string
     *
     * @param string $html
     *
     * @return Webino_ViewHelper_Node_Interface
     */
    public function getHtml();

    /**
     * Set node attributes as array
     *
     * @param array $attribs
     *
     * @return Webino_ViewHelper_Node_Interface
     */
    public function setAttribs(array $attribs);

    /**
     * Append child to node
     *
     * @param DOMNode $node
     * @param bool    $insertBefore
     *
     * @return Webino_ViewHelper_Node
     */
    public function appendTo(DOMNode $node, $insertBefore = false);
}
