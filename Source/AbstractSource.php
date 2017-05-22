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
namespace Umc\Base\Source;

use \Magento\Framework\Option\ArrayInterface;

abstract class AbstractSource implements ArrayInterface
{
    /**
     * get options as array
     *
     * @param bool $withEmpty
     * @return array
     */
    abstract public function toOptionArray($withEmpty = false);

    /**
     * get all options as pair key value
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = [];
        foreach ($this->toOptionArray($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
