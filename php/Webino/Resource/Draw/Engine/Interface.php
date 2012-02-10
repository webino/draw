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
 * @subpackage Engine
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

/**
 * @category   Webino
 * @package    Draw
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
interface Webino_Resource_Draw_Engine_Interface
{
    /**
     * Set configuration array of nodes to draw with it's options
     *
     * @param array $cfg
     */
    public function setCfg(array $cfg);

    /**
     * Add config runtime
     *
     * Don't forget to set stackIndex if you map to content rendered after
     *
     * @param array $cfg
     *
     * @return Webino_Resource_Draw_Engine_Interface
     */
    public function addCfg(array $cfg);

    /**
     * If it's true nothing happens
     *
     * @param bool $isDisabled
     *
     * @return Webino_Resource_Draw_Engine_Interface
     */
    public function setIsDisabled($isDisabled);

    /**
     * If isXmlHttpRequest is true only body part is returned
     *
     * @param bool $isXmlHttpRequest
     *
     * @return Webino_Resource_Draw_Engine_Interface
     */
    public function setIsXmlHttpRequest($isXmlHttpRequest);

    /**
     * Render XHTML as object
     *
     * @param string $xhtml
     */
    public function render($xhtml);
}
