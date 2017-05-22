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

class TooltipType extends AbstractSource
{
    /**
     * @var string
     */
    const SLIDE = 'slide';

    /**
     * @var string
     */
    const POPUP = 'popup';

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
        if ($this->options === null) {
            $this->options = [
                [
                    'value' => self::POPUP,
                    'label' => __('Popup'),
                ],
                [
                    'value' => self::SLIDE,
                    'label' => __('Slide'),
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
