<?php
/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Config;

use Magento\Framework\DataObject;
use Magento\Framework\Config\Reader\Filesystem;
use Umc\Base\Model\Umc;

class Config extends Umc
{
    /**
     * config
     *
     * @var array
     */
    protected $config;

    /**
     * enabled config
     *
     * @var array
     */
    protected $enabledConfig;

    /**
     * reader
     *
     * @var Filesystem;
     */
    protected $reader;

    /**
     * root node
     *
     * @var string
     */
    protected $rootNode = 'config';

    /**
     * @param Filesystem $reader
     * @param string $rootNode
     * @param array $data
     */
    public function __construct(
        Filesystem $reader,
        $rootNode,
        array $data = []
    ) {
        $this->reader   = $reader;
        $this->rootNode = $rootNode;
        parent::__construct($data);
    }

    /**
     * get config
     *
     * @param null $path
     * @param bool $ignoreDisabled
     * @param null $default
     * @return array|null
     */
    public function getConfig($path = null, $ignoreDisabled = true, $default = null)
    {
        if ($this->config === null) {
            $config = $this->reader->read();
            $this->config = isset($config[$this->rootNode]) ? $config[$this->rootNode] : [];
            unset($this->config['noNamespaceSchemaLocation']);
        }
        if ($path === null) {
            if ($ignoreDisabled) {
                return $this->getEnabledConfig();
            }
            return $this->config;
        }
        $parts = explode('/', $path);
        if ($ignoreDisabled) {
            $config = $this->getEnabledConfig();
        } else {
            $config = $this->config;
        }

        foreach ($parts as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }
        return $config;
    }

    /**
     * get boolean config value
     *
     * @param array $settings
     * @param string $key
     * @return bool
     */
    public function getBoolValue($settings, $key)
    {
        if (!isset($settings[$key])) {
            return false;
        }
        if (!$settings[$key]) {
            return false;
        }
        if ($settings[$key] === 'false') {
            return false;
        }
        if ($settings[$key] === 1) {
            return true;
        }
        if (!is_string($settings[$key]) && !is_bool($settings[$key])) {
            return false;
        }
        return true;
    }

    /**
     * get only enabled config elements
     *
     * @return array
     */
    public function getEnabledConfig()
    {
        if ($this->enabledConfig === null) {
            $this->enabledConfig = $this->buildEnabledConfig($this->getConfig(null, false, null));
        }
        return $this->enabledConfig;
    }

    /**
     * build the enabled config
     *
     * @param array $node
     * @return array
     */
    protected function buildEnabledConfig($node)
    {
        $enabled = [];
        if (is_array($node)) {
            foreach ($node as $key => $value) {
                if (is_array($value)) {
                    if ($this->getBoolValue($value, 'disabled')) {
                        continue;
                    }
                    $enabled[$key] = $this->buildEnabledConfig($value);
                } else {
                    $enabled[$key] = $value;
                }
            }
        }
        return $enabled;
    }
}
