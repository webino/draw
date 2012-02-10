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
 * Interface for draw loop helpers
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
interface Webino_DrawHelper_Loop_Interface
{
    /**
     * Initialization of helper
     *
     * @param DOMNode $node
     */
    public function init(DOMNode $node);

    /**
     * It's fired before draw loop start
     *
     * @param DOMNode $node
     * @param string  $viewVarName
     */
    public function beforeLoopStart(DOMNode $node, &$viewVarName);

    /**
     * It's fired in draw loop
     *
     * @param DOMNode $newNode
     * @param string   $key
     * @param array   $item
     * @param int     $index
     * @param DOMNode $parentNode
     */
    public function inLoop(
        DOMNode $newNode, $key, array &$item, &$index, DOMNode $parentNode
    );

    /**
     * It's fired after draw loop finish
     *
     * @param DOMNode $newNode
     * @param string   $key
     * @param array   $item
     * @param int     $index
     * @param DOMNode $parentNode
     */
    public function afterLoopFinish(
        $newNode, $key, array &$item, &$index, DOMNode $parentNode
    );
}
