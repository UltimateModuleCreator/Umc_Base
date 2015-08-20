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

class Dropdown extends AbstractType
{
    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = 'select';

    /**
     * @var string
     */
    protected $columnComponent = 'select';

    /**
     * setup script constant name
     *
     * @var string
     */
    protected $sqlTypeConst = 'TYPE_INTEGER';

    /**
     * setup script length
     *
     * @var string
     */
    protected $setupLength = 'null';

    /**
     * edit for field type
     *
     * @var string
     */
    protected $editFormType = 'select';

    /**
     * attribute has options
     *
     * @var bool
     */
    protected $hasOptions = true;

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
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $attribute = $this->getAttribute();
        $underscore = $this->getUnderscore();
        if ($attribute->getForcedSourceModel()) {
            $sourceModel = $attribute->getForcedSourceModel();
        } else {
            $sourceModel = $attribute->getCodeCamelCase().'Options';
        }

        $options[] = '\'values\' => array_merge([\'\' => \'\'], $this->'.
            $underscore.
            $sourceModel.
            '->toOptionArray()),';
        return $options;
    }

    /**
     * get default value
     *
     * @return int|mixed|string
     */
    public function getDefaultValue()
    {
        $value = $this->getAttribute()->getData('default_value');
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
     * @return array
     */
    public function getPlaceholders()
    {
        $placeholders = parent::getPlaceholders();
        $attribute = $this->getAttribute();
        $entity = $attribute->getEntity();
        $module = $entity->getModule();
        $placeholders['{{GridFilterSourceClass}}'] = $module->getNamespace().'\\'.
            $module->getModuleName().'\\Model\\'.
            $entity->getNameSingular(true).'\\Source\\'.
            $attribute->getCodeCamelCase(true);

        return $placeholders;
    }

}
