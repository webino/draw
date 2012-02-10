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
 * Interface for draw profiler
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
interface Webino_Resource_Draw_Profiler_Interface
{
    /**
     * Start profiler group
     */
    public function start();

    /**
     * Start profile of node adding
     */
    public function startAdd();

    /**
     * Add profile log of node
     *
     * @param DOMNodeList $nodeList
     * @param array       $options
     *
     * @return Webino_Resource_Draw_Profiler
     */
    public function add(DOMNodeList $nodeList, array $options);

    /**
     * Stop profile
     *
     * @param array $cfg
     *
     * @return Webino_Resource_Draw_Profiler 
     */
    public function stop(array $cfg);

    /**
     * Return profile title with values
     *
     * @return string
     */
    public function __toString();
}
