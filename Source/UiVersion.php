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

use Magento\Framework\App\ProductMetadataInterface;

class UiVersion extends AbstractSource
{
    /**
     * @var int
     */
    const V1 = 1;

    /**
     * @var int
     */
    const V2 = 2;

    /**
     * @var string
     */
    const BREAKPOINT_VERSION = '2.2';

    /**
     * options
     *
     * @var null|array
     */
    protected $options;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetaData;

    /**
     * UiVersion constructor.
     * @param ProductMetadataInterface $productMetaData
     */
    public function __construct(ProductMetadataInterface $productMetaData)
    {
        $this->productMetaData = $productMetaData;
    }

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
                    'value' => $this->getAutodetectedVersion(),
                    'label' => __('Autodetect'),
                ],
                [
                    'value' => self::V1,
                    'label' => __('2.1 and lower'),
                ],
                [
                    'value' => self::V2,
                    'label' => __('2.2 and higher'),
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

    /**
     * @return int
     */
    protected function getAutodetectedVersion()
    {
        $version = $this->productMetaData->getVersion();
        return version_compare($version, self::BREAKPOINT_VERSION, '<') ? self::V1 : self::V2;
    }
}
