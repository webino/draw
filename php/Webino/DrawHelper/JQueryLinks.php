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
 * Draw helper to support jQuery
 *
 * example of options:
 *
 * - stackIndex = 900
 * - xpath      = '//html'
 * - helper     = jQueryLinks
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
class Webino_DrawHelper_JQueryLinks
    extends Webino_DrawHelper_Abstract
{
    /**
     * Name of node to find head and body
     */
    const NODE_TAGNAME = 'html';

    /**
     * Name of node to place links to style
     */
    const HEAD_TAGNAME = 'head';

    /**
     * Xpath to find existent node of link
     */
    const LINKNODE_XPATH = '//head/link';

    /**
     * Xpath to find existent node script
     */
    const SCRIPTNODE_XPATH = '//head/script';

    /**
     * Render jquery helper to DOM layout
     * 
     * @uses ZendX_JQuery
     *
     * @param DomElement $node
     * 
     * @return void
     */
    public function draw(DOMNode $node)
    {
        if ( self::NODE_TAGNAME != $node->nodeName ) {
            throw new Exception(
                sprintf(
                    'JQueryLinks should be mapped only on "%s" node.',
                    self::NODE_TAGNAME
                )
            );
        }
        
        $jquery = $this->view->jQuery();

        if ( !$jquery->isEnabled() ) {
            
            return null;
        }

        // Styles
        $head = $node->getElementsByTagName(self::HEAD_TAGNAME)->item(0);
        $jquery->setRenderMode(ZendX_JQuery::RENDER_STYLESHEETS);
        $this->_append((string)$jquery, $head, self::LINKNODE_XPATH);

        // Scripts
        $jquery->setRenderMode(
            ZendX_JQuery::RENDER_LIBRARY
            | ZendX_JQuery::RENDER_SOURCES
            | ZendX_JQuery::RENDER_JQUERY_ON_LOAD
        );
        $this->_append((string)$jquery, $head, self::SCRIPTNODE_XPATH);
    }

    /**
     * Append XHTML to parent node or after existent child nodes,
     * behaviour depends on beforeXpath parameter
     *
     * @param string  $xml
     * @param DOMNode $node
     * @param string  $beforeXpath
     *
     * @return Webino_DrawHelper_JQueryLinks
     */
    private function _append($xml, DOMNode $node, $beforeXpath)
    {
        $xml = trim($xml);

        if ( !$xml ) {
            
            return $this;
        }

        $beforeNode = $this->view->node($node)->query(
            $beforeXpath
        )->item(0);

        if ( $beforeNode ) {
            $node->insertBefore(
                $this->frag($xml, $node->ownerDocument), $beforeNode
            );
        } else {
            $node->appendChild(
                $this->frag($xml, $node->ownerDocument)
            );
        }
        
        return $this;
    }
}
