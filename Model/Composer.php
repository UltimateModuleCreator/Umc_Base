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

namespace Umc\Base\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Umc\Base\Model\Composer\Exception;

class Composer
{
    /**
     * @var DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var array
     */
    protected $versions;

    /**
     * Composer constructor.
     * @param DeploymentConfig $deploymentConfig
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param ReadFactory $readFactory
     */
    public function __construct(
        DeploymentConfig $deploymentConfig,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
    }

    /**
     * @param string $moduleName
     * @return mixed
     * @throws Exception
     * @throws \Exception
     */
    protected function getModuleVersion($moduleName)
    {
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleName
        );
        $directoryRead = $this->readFactory->create($path);
        try {
            $composerJsonData = $directoryRead->readFile('composer.json');
            $data = json_decode($composerJsonData, true);
        } catch (FileSystemException $exception) {
            $data = [];
        } catch (\Exception $e) {
            throw $e;
        }

        if (empty($data['version']) || empty($data['name'])) {
            throw new Exception(__('Missing version or name for module %1', $moduleName));
        }
        return [
            'name' => $data['name'],
            'version' => $data['version']
        ];
    }

    /**
     * @param int $wildcardLevel
     * @return array
     * @throws \Exception
     */
    public function getVersions($wildcardLevel = 2)
    {
        if ($this->versions === null) {
            $modules = $this->deploymentConfig->get('modules');
            $this->versions = [];
            foreach ($modules as $moduleName => $isEnabled) {
                if (!$isEnabled) {
                    continue;
                }
                try {
                    $versionData = $this->getModuleVersion($moduleName);
                    $this->versions[$versionData['name']] = $this->formatVersion(
                        $versionData['version'],
                        $wildcardLevel
                    );
                } catch (Exception $e) {
                    continue;
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
        return $this->versions;
    }

    /**
     * @param string $version
     * @param int $wildcardLevel
     * @return string
     */
    protected function formatVersion($version, $wildcardLevel)
    {
        if ($wildcardLevel < 1) {
            return $version;
        }
        $parts = explode('.', $version);
        $count = count($parts);
        for ($i = 0; $i < $wildcardLevel; $i++) {
            $parts[$count - 1 - $i] = '*';
        }
        return implode('.', $parts);
    }
}
