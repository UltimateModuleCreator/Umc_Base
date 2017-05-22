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

use Umc\Base\Api\Data\Attribute\TypeInterface;

class Dropdown extends AbstractType implements TypeInterface
{
    const NAME = 'dropdown';

    /**
     * @var string
     */
    protected $uiFormOptionV2Tag = 'select';

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
     * @return string
     */
    protected function getSourceModel()
    {
        $attribute  = $this->getAttribute();
        $entity     = $attribute->getEntity();
        $module     = $entity->getModule();

        $model = $module->getNamespace().'\\'.
            $module->getModuleName().
            '\\Model\\'.
            $entity->getNameSingular(true).
            '\\Source\\'.
            $attribute->getCodeCamelCase(true);
        return $model;
    }

    /**
     * get default value
     *
     * @return int|mixed|string
     */
    public function getDefaultValue()
    {
        $value = $this->getAttribute()->getData(self::DEFAULT_VALUE);
        $values = $this->getAttribute()->getData('options');
        $values = explode("\n", $values);
        foreach ($values as $key => $label) {
            if (trim($label) == trim($value)) {
                return $key + 1;
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUiFormOptions()
    {
        return '<item name="options" xsi:type="object">'.$this->getSourceModel().'</item>';
    }

    /**
     * @return string
     */
    public function getUiFormOptionsV2()
    {
        $config = '<formElements><'.$this->uiFormOptionV2Tag.'><settings>'.
            '<options class="'.$this->getSourceModel().'"/></settings></'.$this->uiFormOptionV2Tag.'></formElements>';
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
