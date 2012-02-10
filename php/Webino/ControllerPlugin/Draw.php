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
 * @subpackage ControllerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

use Webino_Resource_Draw_Engine_Interface as DrawEngine;

/**
 * Plugin for rendering layout as DOM by draw engine
 *
 * @category   Webino
 * @package    Draw
 * @subpackage ControllerPlugin
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
class Webino_ControllerPlugin_Draw
    extends    Zend_Controller_Plugin_Abstract
    implements Webino_ControllerPlugin_Draw_Interface
{
    /**
     * The draw engine
     *
     * @var DrawEngine
     */
    private $_engine;

    /**
     * Inject draw object
     *
     * @param DrawEngine $engine
     *
     * @return Webino_ControllerPlugin_Draw
     */
    public function setEngine(DrawEngine $engine)
    {
        $this->_engine = $engine;

        return $this;
    }

    /**
     * Render response with draw engine
     */
    public function dispatchLoopShutdown()
    {
        parent::dispatchLoopShutdown();

        $this->getResponse()->setBody(
            $this->_engine->render(
                $this->getResponse()->getBody()
            )
        );
    }
}
