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
namespace Umc\Base\Model\Source\Attribute;

use Umc\Base\Model\Source\AbstractSource;

class Grid extends AbstractSource
{
    const NO = 0;
    const YES = 1;
    const HIDDEN = 2;
    /**
     * options
     *
     * @var null|array
     */
    protected $options;

    /**
     * get list of types as options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        if (is_null($this->options)) {
            $this->options = [
                [
                    'value' => self::YES,
                    'label' => __('Yes'),
                ],
                [
                    'value' => self::NO,
                    'label' => __('No'),
                ],
                [
                    'value' => self::HIDDEN,
                    'label' => __('Yes but hidden'),
                ],
            ];
        }
        $options = $this->options;
        if ($withEmpty) {
            array_unshift(
                $options,
                [
                    'value' => '',
                    'label' => ''
                ]
            );
        }
        return $options;
    }
}
