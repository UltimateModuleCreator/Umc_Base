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
namespace Umc\Base\Model\Core\Attribute\Type;

class Yesno extends AbstractType
{
    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        $options = parent::getAdminColumnOptions();
        $options .= $this->getEol().$this->getPadding(5).'<item name="options" xsi:type="object">'.$this->getSourceModel().'</item>';
        return $options;
    }

    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $underscore = $this->getUnderscore();
        $options[] = '\'values\' => $this->'.$underscore.'booleanOptions->toOptionArray(),';
        return $options;
    }

    protected function getSourceModel()
    {
        return 'Magento\Config\Model\Config\Source\Yesno';
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        $placeholders = parent::getPlaceholders();
        $placeholders['{{GridFilterSourceClass}}'] = $this->getSourceModel();
        return $placeholders;
    }
}
