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
 * View helper for translating variables in string
 *
 * Filters and view helpers can be applied on variables values.
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
class Webino_ViewHelper_VarTranslator
    extends    Zend_View_Helper_Abstract
    implements Webino_ViewHelper_VarTranslator_Interface
{
    /**
     * Name of custom variables options key
     */
    const FETCH_KEYNAME = 'fetch';

    /**
     * Name of filters options key
     *
     * If options are set values will be filtered
     */
    const FILTERS_KEYNAME = 'filters';

    /**
     * Name of helpers options key
     *
     * If options are set values will be processed by view helpers
     */
    const HELPERS_KEYNAME = 'helpers';

    /**
     * Format of variable
     */
    const VAR_FORMAT = '{$%s}';

    /**
     * Array of varname => value to translate
     *
     * @var array
     */
    private $_translation;

    /**
     * Helper method
     *
     * @param array $translation
     * @param array $translation
     *
     * @return Webino_ViewHelper_VarTranslator
     */
    public function varTranslator(array $translation)
    {
        $this->_translation = $translation;

        return $this;
    }

    /**
     * Translate variables in string or array with translation
     *
     * @param string|array $subject
     *
     * @return string|array
     */
    public function translate($subject)
    {
        if (is_array($subject)) {
            $this->_translateArray($subject, $this->_translation);

            return $subject;
        }

        return strtr($subject, $this->_translation);
    }

    /**
     * Return translation array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_translation;
    }

    /**
     * Fetch custom variables to translation
     *
     * example of properties:
     *
     * $properties = array(
     *     'value' => array(
     *         'in' => array(
     *             'the' => array(
     *                 'depth' => 'valueInTheDepth',
     *             ),
     *         ),
     *     ),
     * );
     *
     * example of options:
     *
     * $options = array(
     *     'fetch' => array(
     *         'customVar' => 'value.in.the.depth',
     *     ),
     * );
     * 
     * @param array $properties
     * @param array $options
     * 
     * @return Webino_ViewHelper_VarTranslator
     */
    public function fetch(array $properties, array $options)
    {
        if (!empty($options[self::FETCH_KEYNAME])) {

            foreach ($options[self::FETCH_KEYNAME] as $varName => $varBase) {

                $value = $properties;

                foreach (explode('.', $varBase) as $baseKey) {

                    if (empty($value[$baseKey])) {

                        $value = null;
                        break;
                    }

                    $value = $value[$baseKey];
                }

                $this->_translation[
                    sprintf(self::VAR_FORMAT, $varName)
                ] = $value;

            }
        }

        return $this;
    }

    /**
     * Apply filters and helpers on translation
     *
     * example of options:
     *
     * $options = array(
     *     'filters' => array(
     *         'value' => array(
     *             'stringToUpper' => array(
     *                 'encoding' => array('_PARAM_'),
     *             ),
     *         ),
     *         'undefined' => array(),
     *     ),
     *     'helpers' => array(
     *         'value' => array(
     *             'url' => array(
     *                 'options'  => array(
     *                     'uri' => 'uri/'
     *                 ),
     *             ),
     *         ),
     *     ),
     * );
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_VarTranslator
     */
    public function apply(array $options)
    {
        if (!empty($options[self::FILTERS_KEYNAME])) {
            $this->applyFilters($options[self::FILTERS_KEYNAME]);
        }

        if (!empty($options[self::HELPERS_KEYNAME])) {
            $this->applyHelpers($options[self::HELPERS_KEYNAME]);
        }

        return $this;
    }

    /**
     * Apply filters on translation array
     *
     * example of options:
     * 
     * $options = array(
     *     'filters' => array(
     *         'value' => array(
     *             'stringToUpper' => array(
     *                 'encoding' => 'utf-8,
     *             ),
     *         ),
     *         'undefined' => array(),
     *     ),
     * );
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_VarTranslator
     */
    public function applyFilters(array $options)
    {
        foreach ($options as $varIndex => $filters) {

            $key = sprintf(self::VAR_FORMAT, $varIndex);

            if (!isset($this->_translation[$key])) {

                continue;
            }

            foreach ($filters as $filterName => $filterSetting) {

                $filter = $this->view->getFilter($filterName);

                if (is_array($filterSetting)) {

                    $this->_translateArray($filterSetting, $this->_translation);

                    foreach ($filterSetting as $optionName => $option) {

                        $fc = 'set'. ucfirst($optionName);

                        if (!is_array($option)) {
                            $params = array($option);
                        } else {
                            $params = $option;
                        }

                        call_user_func_array(
                            array(
                                $filter, $fc
                            ), $params
                        );
                    }
                }

                $this->_translation[$key] = $filter->filter(
                    $this->_translation[$key]
                );
            }
        }

        return $this;
    }

    /**
     * Apply view helpers on translation array
     *
     * example of options:
     *
     * $options = array(
     *     'helpers' => array(
     *         'value' => array(
     *             'url' => array(
     *                 'options'  => array(
     *                     'uri' => 'uri/'
     *                 ),
     *             ),
     *         ),
     *     ),
     * );
     *
     * @param array $options
     *
     * @return Webino_ViewHelper_VarTranslator
     */
    public function applyHelpers(array $options)
    {
        foreach ($options as $varIndex => $helpers) {

            $key = sprintf(self::VAR_FORMAT, $varIndex);

            if (!isset($this->_translation[$key])) {

                continue;
            }

            foreach ($helpers as $helperName => $helperParams) {

                $this->_translateArray($helperParams, $this->_translation);

                $this->_translation[$key] = call_user_func_array(
                    array(
                        $this->view->getHelper($helperName), $helperName
                    ), $helperParams
                );
            }
        }

        return $this;
    }

    /**
     * Translate array recursively
     *
     * @param array $array
     * @param array $translation
     *
     * @return array
     */
    private function _translateArray(array &$array, array $translation)
    {
        foreach ($array as &$param) {

            if (is_array($param)) {

                $this->_translateArray($param, $translation);

                continue;
            }

            $param = strtr($param, $translation);
        }

        return $this;
    }
}
