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

class Source extends Config
{
    /**
     * @var string
     */
    const MODULE_SCOPE    = 'umc_module';

    /**
     * @var string
     */
    const ENTITY_SCOPE    = 'umc_entity';

    /**
     * @var string
     */
    const ATTRIBUTE_SCOPE = 'umc_attribute';

    /**
     * @var string
     */
    const PARENT_ENTITY   = 'umc_parent_entity';

    /**
     * get scope label
     *
     * @param string $scope
     * @return array|null|string
     */
    public function getScopeLabel($scope)
    {
        return $this->getConfig('scope/'.$scope.'/label');
    }

    /**
     * process config array before using
     *
     * @param array $config
     * @return mixed
     */
    public function preProcessFileConfig($config)
    {
        if (!isset($config['destination'])) {
            $config['destination'] = $config['id'];
        }
        if (!isset($config['source'])) {
            $config['source'] = str_replace(['{{', '}}'], '', $config['id']);
        }
        if (!isset($config['scope'])) {
            $config['scope'] = self::MODULE_SCOPE;
        }
        if (!isset($config['abstract'])) {
            $config['abstract'] = false;
        }
        if (!isset($config['interface'])) {
            $config['interface'] = false;
        }
        if (!isset($config['api'])) {
            $config['api'] = false;
        }
        return $config;
    }
}
