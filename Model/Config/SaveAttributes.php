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

class SaveAttributes extends Config
{
    /**
     * root node
     *
     * @var string
     */
    protected $rootNode = 'save_attributes';

    /**
     * get attributes to save
     *
     * @param $code
     * @return array
     */
    public function getAttributes($code)
    {
        $attributes = $this->getConfig('entity/'.$code.'/attribute');
        $validAttributes = [];
        if ($attributes) {
            $validAttributes = array_keys($attributes);
        }
        return $validAttributes;
    }
}
