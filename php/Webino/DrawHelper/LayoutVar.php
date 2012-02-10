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
 * Draw helper to set view layout var value to node
 *
 * example of options:
 *
 * - xpath  = '//*[@class="content"]'
 * - helper = layoutVar
 * - var    = content
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
class Webino_DrawHelper_LayoutVar
    extends Webino_DrawHelper_Abstract
{
    /**
     * Name of variable name key
     */
    const VAR_KEYNAME = 'var';

    /**
     * Set value of node
     *
     * @param DOMNode $node
     *
     * @return bool Returns false if value is empty
     */
    public function draw(DOMNode $node)
    {        
        $node->nodeValue = '';

        $layout = $this->view->layout();

        if (empty($layout->{$this->_options[self::VAR_KEYNAME]})) {

            return false;
        }

        $node->appendChild(
            $this->frag(
                $layout->{$this->_options[self::VAR_KEYNAME]},
                $node->ownerDocument
            )
        );
    }
}
