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
 * Abstract class for draw helpers
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
abstract class Webino_DrawHelper_Abstract
    extends Webino_ViewHelper_Abstract
{
    /**
     * Name of method to draw node
     */
    const DRAW_METHOD = 'draw';
    
    /**
     * Draw helper options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Cycle index
     *
     * @var int
     */
    protected $_cycleIndex = null;

    /**
     * This method should be implemented
     *
     * @param DOMNode $node
     */
    abstract public function draw(DOMNode $node);

    /**
     * Call draw method on this helper with prepared options
     *
     * @param string $name 
     * @param array  $params
     */
    public function __call($name, $params)
    {
        unset($name);

        $this->setOptions($params[1]);

        foreach ( $params[0] as $this->_cycleIndex => $node ) {
            call_user_func_array(
                array($this, self::DRAW_METHOD), array(
                    $node
                )
            );
        }
    }
    
    /**
     * Inject options
     *
     * @param array $options
     *
     * @return Webino_DrawHelper_Abstract
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;

        return $this;
    }
}
