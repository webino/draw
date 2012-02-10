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
 * Draw helper to absolutize URLs
 *
 * example of options:
 * 
 * - stackIndex = 999
 * - xpath.href = '//@href[not(starts-with(., "http"))
 * and not(starts-with(., "#")) and not(starts-with(., "?"))
 * and not(starts-with(., "/")) and not(starts-with(., "mailto:"))]'
 * - xpath.src  = '//@src[not(starts-with(., "http"))
 * and not(starts-with(., "?")) and not(starts-with(., "/"))]'
 * - helper     = absolutize
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
class Webino_DrawHelper_Absolutize
    extends Webino_DrawHelper_Abstract
{
    /**
     * Char to represent base url
     */
    const BASE_URL = '.';

    /**
     * Uri delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Set absolute URL to attribute value
     *
     * @param DOMNode $attr 
     */
    public function draw(DOMNode $attr)
    {
        if (self::BASE_URL == $attr->nodeValue) {
            $attr->nodeValue = $this->view->baseUrl() . self::URI_DELIMITER;

            return;
        }

        $attr->nodeValue = $this->view->baseUrl()
            . self::URI_DELIMITER . $attr->nodeValue;
    }
}
