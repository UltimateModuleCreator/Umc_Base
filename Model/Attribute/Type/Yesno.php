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
namespace Umc\Base\Model\Attribute\Type;

class Yesno extends AbstractType
{
    const NAME = 'yesno';

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        $options = parent::getAdminColumnOptions();
        $options .= '<item name="options" xsi:type="object">'.$this->getSourceModel().'</item>';
        return $options;
    }

    /**
     * @return mixed
     */
    protected function getSourceModel()
    {
        return \Magento\Config\Model\Config\Source\Yesno::class;
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfig()
    {
        $config = parent::getAdditionalFormConfig();
        $config .= '<item name="prefer" xsi:type="string">toggle</item>';
        $config .= '<item name="valueMap" xsi:type="array">';
        $config .= '<item name="true" xsi:type="number">1</item>';
        $config .= '<item name="false" xsi:type="number">0</item>';
        $config .= '</item>';
        return $config;
    }

    /**
     * @return string
     */
    public function getAdminColumnConfigV2()
    {
        return parent::getAdminColumnConfigV2().
            '<options class="'.$this->getSourceModel().'"/>';
    }
}
