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

use Zend_Config     as Config;
use Zend_Config_Ini as IniConfig;

/**
 * Resource class to bootstrap draw
 *
 * example of options:
 *
 * - engine      = Webino_Resource_Draw_Engine
 * - environment = APPLICATION_ENV
 * - map.content = PEAR_PHP_DIR "/Webino/configs/draw/content.ini"
 *
 * @package    Draw
 * @subpackage Resource
 * @author     Peter Bačinský <peter@bacinsky.sk>
 * @copyright  2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license    http://www.webino.org/license/ New BSD License
 * @version    Release: @@PACKAGE_VERSION@@
 * @link       http://pear.webino.org/draw/
 */
class Webino_Resource_Draw
    extends    Zend_Application_Resource_ResourceAbstract
    implements Webino_Resource_Draw_Interface
{
    /**
     * Name of config environment index key
     */
    const ENVIRONMENT_KEYNAME = 'environment';

    /**
     * Name of draw helper stack index key
     */
    const STACKINDEX_KEYNAME = 'stackIndex';
    
    /**
     * Name of draw maps options key
     */
    const MAPS_KEYNAME = 'maps';

    /**
     * Engine to draw XHTML layout injected to plugin
     *
     * @var Webino_Resource_Draw_Engine_Interface
     */
    private $_engine;

    /**
     * List of nodes configuration
     *
     * @var array
     */
    private $_cfg;

    /**
     * Register of configuration files
     *
     * @var array
     */
    private $_maps;

    /**
     * Register draw plugin to controller
     *
     * - inject draw engine to plugin
     * - inject view to draw engine
     * - register helper path to view
     *
     * @return Webino_Resource_Draw_Engine_Interface
     */
    public function init()
    {
        $this->_setMaps($this->_options[self::MAPS_KEYNAME]);

        return $this->getEngine();
    }

    /**
     * Set array of config maps paths to draw
     *
     * @param array $paths
     *
     * @return Webino_Resource_Draw
     */
    private function _setMaps(array $paths)
    {
        $this->_maps = $paths;

        $this->_setMapToCfg($paths);

        $this->getEngine()->setCfg($this->_cfg);

        return $this;
    }

    /**
     * Return draw engine object
     *
     * @return Webino_Resource_Draw_Engine_Interface
     */
    public function getEngine()
    {
        return $this->_engine;
    }

    /**
     * Inject draw engine object
     *
     * @param string|Webino_Resource_Draw_Engine_Interface $engine
     *
     * @return Webino_Resource_Draw
     */
    public function setEngine($engine)
    {
        if (is_string($engine)) {
            $engine = new $engine;
        }

        $this->_engine = $engine;
        
        return $this;
    }

    /**
     * Inject array of draw instructions
     *
     * @param array $cfg
     *
     * @return Webino_Resource_Draw
     */
    public function setCfg(array $cfg)
    {
        $array = array();
        $index = 0;
        
        foreach ($cfg as $key=>$item) {

            if (isset($item[self::STACKINDEX_KEYNAME])) {
                $array[$item[self::STACKINDEX_KEYNAME]][$key] = $item;
            } else {
                $array[$index][$key] = $item;
            }

            $index++;
        }

        ksort($array);

        $this->_cfg = $array;
        
        return $this;
    }

    /**
     * Add array of config maps runtime
     *
     * Generally in controller
     *
     * Don't forget to set stackIndex if you map to content rendered after
     *
     * @param array $paths
     *
     * @return Webino_Resource_Draw
     */
    public function addMaps(array $paths)
    {
        foreach ($paths as $path) {
            $this->getEngine()->addCfg(
                $this->_mapToCfg(array($path))
            );
        }

        return $this;
    }


    /**
     * Loop through paths array of draw configs and returns array of them
     *
     * @param array $paths
     * 
     * @return Webino_Resource_Draw
     */
    private function _setMapToCfg(array $paths)
    {
        $this->setCfg(
            $this->_mapToCfg($paths)
        );

        return $this;
    }

    /**
     * From array of paths to draw maps return array of draw config
     *
     * @param array $paths
     *
     * @return array
     */
    private function _mapToCfg(array $paths)
    {
        $cfg = new Config(array(), true);

        foreach ($paths as $path) {
            $cfg->merge(
                new IniConfig($path, $this->_options[self::ENVIRONMENT_KEYNAME])
            );
        }

        return $cfg->toArray();
    }
}
