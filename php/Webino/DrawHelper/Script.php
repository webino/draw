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
 * Draw helper to render view script to node
 *
 * example of options:
 *
 * - xpath        = '//*[@data-draw="script-example"]'
 * - helper       = script
 * - insertBefore = 0
 * - script       = "scripts/example.html"
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
class Webino_DrawHelper_Script
    extends Webino_DrawHelper_Abstract
{
    /**
     * Name of path to script key
     */
    const SCRIPT_KEYNAME = 'script';

    /**
     * Name of insert before flag key
     */
    const INSERTBEFORE_KEYNAME = 'insertBefore';

    /**
     * Render script to node
     *
     * @param DOMNode $node
     */
    public function draw(DOMNode $node)
    {
        $frag = $this->frag(
            $this->view->render($this->_options[self::SCRIPT_KEYNAME]),
            $node->ownerDocument
        );

        if (isset($this->_options[self::INSERTBEFORE_KEYNAME])
            && $this->_options[self::INSERTBEFORE_KEYNAME]
        ) {
            $node->parentNode->insertBefore($frag, $node);

        } else {
            $node->nodeValue = '';
            $node->appendChild($frag);
        }
    }
}
