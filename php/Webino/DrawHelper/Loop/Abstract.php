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
 * Abstract class for draw loop helpers
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
abstract class Webino_DrawHelper_Loop_Abstract
    extends    Webino_DrawHelper_Abstract
    implements Webino_DrawHelper_Loop_Interface
{
    /**
     * If node has next sibling new node is inserted before
     * 
     * @var DOMNode
     */
    protected $_insertBefore;
    
    /**
     * Name of view variable list array
     */
    const VIEWVAR_KEYNAME = 'var';

    /**
     * Initialization of helper
     *
     * @param DOMNode $node
     */
    public function init(DOMNode $node)
    {

    }

    /**
     * It's fired before draw loop start
     *
     * @param DOMNode $node
     * @param string  $viewVarName
     */
    public function beforeLoopStart(DOMNode $node, &$viewVarName)
    {

    }

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

    }

    /**
     * It's fired after draw loop finish
     *
     * @param DOMNode $newNode
     * @param string  $key
     * @param array   $item
     * @param int     $index
     * @param DOMNode $parentNode
     */
    public function afterLoopFinish(
        $newNode, $key, array &$item, &$index, DOMNode $parentNode
    )
    {

    }

    /**
     * Draw view array var in loop
     *
     * @param DOMNode $node
     */
    public function draw(DOMNode $node)
    {
        if ($node->nextSibling) {
            $this->_insertBefore = $node->nextSibling;
        }
        
        $this->init($node);

        $nodeClone = clone $node;

        $parentNode = $node->parentNode;

        $viewVarName = $this->_options[self::VIEWVAR_KEYNAME];

        $node->parentNode->removeChild($node);

        $this->beforeLoopStart($node, $viewVarName);

        $index = -1;
        foreach ($this->view->{$viewVarName} as $key => $item) {
            $index++;

            $newNode = clone $nodeClone;

            $this->inLoop($newNode, $key, $item, $index, $parentNode);

            if ($this->_insertBefore) {
                $parentNode->insertBefore(
                    $newNode, $this->_insertBefore
                );
            } else {
                $parentNode->appendChild($newNode);
            }

        }

        $this->afterLoopFinish($newNode, $key, $item, $index, $parentNode);
    }
}
