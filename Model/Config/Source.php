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
 * @copyright 2015 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Model\Config;

use Umc\Base\Model\Config;

class Source extends Config
{
    /**
     * root node
     *
     * @var string
     */
    protected $rootNode = 'source';

    /**
     * get scope label
     *
     * @param $scope
     * @return array|null|string
     */
    public function getScopeLabel($scope)
    {
        return __($this->getConfig('scope/'.$scope.'/label'));
    }

    /**
     * process config array before using
     *
     * @param $config
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
            $config['scope'] = 'global'; //TODO: use constant here
        }
        return $config;
    }
}
