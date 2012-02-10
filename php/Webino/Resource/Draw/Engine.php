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

use Zend_Config                             as Config;
use Zend_View_Interface                     as ViewInterface;
use Zend_View_Helper_Interface              as HelperInterface;
use Webino_Resource_Draw_Profiler_Interface as Profiler;

/**
 * Provides cycle of draw helper calls on DOM layout
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
class Webino_Resource_Draw_Engine
    implements Webino_Resource_Draw_Engine_Interface
{
    /**
     * XPath query or
     */
    const XPATH_OR = '|';

    /**
     * Draw config node xpath key
     */
    const XPATH_KEYNAME = 'xpath';

    /**
     * Draw config node helper key 
     */
    const HELPER_KEYNAME = 'helper';

    /**
     * XML version if DOMDOcument was not injected
     */
    const BODYTAG_XPATH = '//body';
    
    /**
     * XML version if DOMDOcument was not injected
     */
    const DOM_VERSION = '1.0';

    /**
     * XML encoding if DOMDOcument was not injected
     */
    const DOM_ENCODING = 'utf-8';

    /**
     * XML encoding tag
     */
    const ENCODING_TAG = '<?xml encoding="%s"?>';

    /**
     * Name of draw helper stack index key
     */
    const STACKINDEX_KEYNAME = 'stackIndex';
    
    /**
     * List of nodes to draw, each node is array eg:
     * ...->setCfg(array(
     *  'xpath' => '//title',
     *  'helper' => 'drawTitle',
     * ));
     *
     * @var array
     */
    private $_cfg;

    /**
     * Zend View object with script path to draw helpers
     *
     * @var ViewInterface
     */
    private $_view;
    
    /**
     * Events resource profiler
     *
     * @var Profiler
     */
    private $_profiler;

    /**
     * XHTML code is loaded into Document Object Model
     *
     * @var DOMDocument
     */
    private $_domDoc;

    /**
     * Xpath is used to find nodes
     *
     * @var DOMXpath
     */
    private $_domXpath;

    /**
     * Own helpers can be injected, otherwise they'll be loaded with zend view
     *
     * @var array
     */
    private $_helpers = array();

    /**
     * Returns only body content if it's true
     *
     * @var bool
     */
    private $_isXmlHttpRequest = false;

    /**
     * Set true to disable render
     *
     * @var bool
     */
    private $_isDisabled = false;

    /**
     * Render configuration array, list of nodes with xpath(s), helper
     * and options depends on that helper
     *
     * @param array $cfg 
     * 
     * @return Webino_Resource_Draw_Engine
     */
    public function setCfg(array $cfg)
    {
        $this->_cfg = $cfg;

        return $this;
    }

    /**
     * Add config runtime
     *
     * Don't forget to set stackIndex if you map to content rendered after
     *
     * @param array $cfg
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function addCfg(array $cfg)
    {
        foreach ($this->_cfg as $mapKey => &$map) {

            $cfgKey = key($map);
            if (isset($cfg[$cfgKey])) {
                // Merge
                $config = new Config($map, true);

                $cfgMap = $config->merge(
                    new Config(
                        array(
                            $cfgKey => $cfg[$cfgKey],
                        )
                    )
                )->toArray();
                
                if (isset($cfg[$cfgKey][self::STACKINDEX_KEYNAME])) {
                    // Change stackIndex
                    unset($this->_cfg[$mapKey]);
                    $this->_cfg[
                        $cfg[$cfgKey][self::STACKINDEX_KEYNAME]
                    ] = $cfgMap;
                } else {
                    $map = $cfgMap;
                }

                unset($cfg[$cfgKey]);
            }
        }

        foreach ($cfg as $cfgKey => $cfgMap) {

            if (isset($cfgMap[self::STACKINDEX_KEYNAME]) ) {

                if (isset($this->_cfg[$cfgMap[self::STACKINDEX_KEYNAME]])) {
                    throw new Webino_Resource_Draw_Engine_Exception(
                        sprintf(
                            'Stack index "%s" already exists for "%s"',
                            $cfgMap[self::STACKINDEX_KEYNAME], $cfgKey
                        )
                    );
                }

                $this->_cfg[
                    $cfgMap[self::STACKINDEX_KEYNAME]
                ][$cfgKey] = $cfgMap;

            } else {
                array_unshift(
                    $this->_cfg, array(
                        $cfgKey => $cfgMap
                    )
                );
            }

        }

        return $this;
    }

    /**
     * Zend View is used to load helpers
     * 
     * @param ViewInterface $view
     * 
     * @return Webino_Resource_Draw_Engine
     */
    public function setView(ViewInterface $view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Inject profiler
     *
     * @param Profiler $profiler
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function setProfiler(Profiler $profiler = null)
    {
        $this->_profiler = $profiler;

        return $this;
    }

    /**
     * The default DOMDocument object
     *
     * @return DOMDocument
     */
    private function _getDomDoc()
    {
        if (!$this->_domDoc) {
            $doc = new DOMDocument(self::DOM_VERSION, self::DOM_ENCODING);
            $doc->preserveWhiteSpace  = false;
            $doc->formatOutput        = false;
            $doc->substituteEntities  = false;
            $doc->strictErrorChecking = false;
            $this->setDomDoc($doc);
        }

        return $this->_domDoc;
    }

    /**
     * Inject your own DOMDocument object
     *
     * @param DOMDocument $doc
     * 
     * @return Webino_Resource_Draw_Engine
     */
    public function setDomDoc(DOMDocument $doc)
    {
        $this->_domDoc = $doc;

        return $this;
    }

    /**
     * The default DOMXpath object
     *
     * @return DOMXpath
     */
    private function _getDomXpath()
    {
        if (!$this->_domXpath) {
            $this->setDomXpath(new DOMXpath($this->_getDomDoc()));
        }

        return $this->_domXpath;
    }

    /**
     * Inject own DOMXpath object
     *
     * @param DomXpath $xpath
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function setDomXpath(DomXpath $xpath)
    {
        $this->_domXpath = $xpath;

        return $this;
    }

    /**
     * Cycle of view helpers called on nodes
     * no cycle if not nodes cfg or load xhtml fail
     *
     * @param string $xhtml
     * 
     * @return string DOCTYPE with XHTML
     */
    public function render($xhtml)
    {
        if (!$this->_cfg || $this->_isDisabled) {
            
            return $xhtml;
        }

        $doc = $this->_getDomDoc();

        $xmlEncoding = sprintf(self::ENCODING_TAG, self::DOM_ENCODING);

        libxml_use_internal_errors(true);

        $doc->loadHtml($xmlEncoding . $xhtml);

        $doc->xpath = $this->_getDomXpath();

        ksort($this->_cfg);

        !$this->_profiler or
            $this->_profiler->start();

        $this->_cycle($this->_cfg, $doc);

        !$this->_profiler or
            $this->_profiler->stop($this->_cfg);

        if ($this->_isXmlHttpRequest) {
            $body = $doc->xpath->query(self::BODYTAG_XPATH)->item(0);
            $xhtml = trim($doc->saveXML($body));
            $xhtml = substr($xhtml, 6, strlen($xhtml)-13);

            return $xhtml;
        }

        $xhtml = trim($doc->saveHTML());
        $xhtml = str_replace($xmlEncoding, '', $xhtml);

        return $xhtml;
    }

    /**
     * If it's true nothing happens
     *
     * @param bool $isDisabled
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function setIsDisabled($isDisabled)
    {
        $this->_isDisabled = $isDisabled;

        return $this;
    }

    /**
     * If isXmlHttpRequest is true only body part is returned
     *
     * @param bool $isXmlHttpRequest
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function setIsXmlHttpRequest($isXmlHttpRequest)
    {
        $this->_isXmlHttpRequest = $isXmlHttpRequest;

        return $this;
    }

    /**
     * Each node is rendered in cycle by draw view helper
     *
     * @param array $nodes List of node array config
     * 
     * @param DOMDocument $doc
     */
    private function _cycle(array $nodes, DOMDocument $doc)
    {
        foreach ($nodes as $node) {

            $node = current($node);

            if (is_array($node[self::XPATH_KEYNAME])) {
                $node[self::XPATH_KEYNAME] = join(
                    self::XPATH_OR, $node[self::XPATH_KEYNAME]
                );
            }

            !$this->_profiler or
                $this->_profiler->startAdd();

            $domNode = $doc->xpath->query($node[self::XPATH_KEYNAME]);

            // render nodes with draw view helper
            $this->_getHelper(
                $node[self::HELPER_KEYNAME]
            )->{$node[self::HELPER_KEYNAME]}($domNode, $node);

            !$this->_profiler or
                $this->_profiler->add($domNode, $node);
        }
    }

    /**
     * Get registered or load by view helper object
     *
     * @param string $name Zend standard view helper name
     *
     * @return HelperInterface
     */
    private function _getHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $this->setHelper(
                $name, $this->_view->getHelper($name)
            );
        }

        return $this->_helpers[$name];
    }
    
    /**
     * Inject own helper object
     *
     * @param string          $name Zend standard view helper name
     * @param HelperInterface $helper
     *
     * @return Webino_Resource_Draw_Engine
     */
    public function setHelper($name, HelperInterface $helper)
    {
        $this->_helpers[$name] = $helper;
        
        return $this;
    }
}
