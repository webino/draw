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
 * @subpackage ViewHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    GIT: $Id$
 * @link       http://pear.webino.org/draw/
 */

/**
 * Interface for variable translator view helper
 *
 * @category   Webino
 * @package    Draw
 * @subpackage ViewHelper
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
interface Webino_ViewHelper_VarTranslator_Interface
{
    /**
     * Translate variables in string or array
     *
     * @param string|array $subject
     * 
     * @return string|array
     */
    public function translate($subject);

    /**
     * Return translation as array
     *
     * @return array
     */
    public function toArray();

    /**
     * Fetch custom variables to translation
     *
     * @param array $properties
     * @param array $options
     *
     * @return Webino_ViewHelper_VarTranslator_Interface
     */
    public function fetch(array $properties, array $options);

    /**
     * Apply filters and helpers on translation
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_VarTranslator_Interface
     */
    public function apply(array $options);

    /**
     * Apply filters on values by options
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_TranslateArray
     */
    public function applyFilters(array $options);

    /**
     * Apply view helpers on values by options
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_TranslateArray
     */
    public function applyHelpers(array $options);
}
