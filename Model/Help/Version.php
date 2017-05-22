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
namespace Umc\Base\Model\Help;

use Magento\Framework\Module\ModuleListInterface;
use Umc\Base\Config\Version as VersionConfig;

class Version extends AbstractHelp implements HelpInterface
{
    /**
     * @var VersionConfig
     */
    protected $versionConfig;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @param array $columns
     * @param VersionConfig $versionConfig
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        array $columns,
        VersionConfig $versionConfig,
        ModuleListInterface $moduleList
    ) {
        $this->versionConfig = $versionConfig;
        $this->moduleList = $moduleList;
        parent::__construct($columns);
    }

    /**
     * @return array|null
     */
    public function getRows()
    {
        if ($this->rows === null) {
            $modules = $this->versionConfig->getConfig('module', true, []);
            foreach ($modules as $module) {
                $moduleData = $this->moduleList->getOne($module['id']);
                if ($moduleData) {
                    $version = $moduleData['setup_version'];
                    if (isset($module['build'])) {
                        $version .= '-'.$module['build'];
                    }
                    $this->rows[] = [
                        'module' => $module['id'],
                        'version' => $version
                    ];
                }
            }
        }
        return $this->rows;
    }
}
