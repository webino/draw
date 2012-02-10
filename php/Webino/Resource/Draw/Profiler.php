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
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

/**
 * Profiler for draw resource
 *
 * @category   Webino
 * @package    Draw
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
class Webino_Resource_Draw_Profiler
    extends    Webino_Resource_Profiler_Abstract
    implements Webino_Resource_Draw_Profiler_Interface
{
    /**
     * Profiler title
     *
     * @var string
     */
    private $_title = 'Rendering Performance (%s nodes in {$time}):';

    /**
     * Start profile group
     */
    public function start()
    {
        $this->_startGroup($this);
    }

    /**
     * Start profile of node adding
     */
    public function startAdd()
    {
        $this->_start();
    }

    /**
     * Add profile log of node
     *
     * @param DOMNodeList $nodeList
     * @param array       $options
     *
     * @return Webino_Resource_Draw_Profiler
     */
    public function add(DOMNodeList $nodeList, array $options)
    {
        $this->_debug($nodeList->length . 'x in {$time}', $options);

        return $this;
    }

    /**
     * Stop profile
     *
     * @param array $cfg
     *
     * @return Webino_Resource_Draw_Profiler 
     */
    public function stop(array $cfg)
    {
        $this->_title = $this->_stop(
            sprintf($this->_title, count($cfg))
        );

        return $this;
    }

    /**
     * Return profile title with values
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_title;
    }
}
