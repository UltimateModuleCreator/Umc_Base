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

use Magento\Framework\Escaper;
use Umc\Base\Model\Core\Attribute;
use Umc\Base\Model\Umc;
use Umc\Base\Model\Source\Attribute\Grid;

class AbstractType extends Umc implements TypeInterface
{
    /**
     * string escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * constructor
     *
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Escaper $escaper,
        array $data = []
    )
    {
        $this->escaper = $escaper;
        parent::__construct($data);
    }

    /**
     * set the attribute instance
     *
     * @param Attribute $attribute
     * @return $this|mixed
     */
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * get the attribute instance
     *
     * @return \Umc\Base\Model\Core\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function getMulti()
    {
        return $this->getData('multi');
    }

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        return '';
    }

    public function getAdminColumnConfig()
    {
        $content = '';
        if ($this->getAttribute()->getAdminGridFilter()) {
            $content .= $this->getEol().$this->getPadding(5).'<item name="filter" xsi:type="string">'.$this->getFilterType().'</item>';
        }
        if ($this->getAttribute()->getInlineEdit()) {
            if ($this->getAttribute()->getRequired()) {
                $content .= $this->getEol(). $this->getPadding(5).'<item name="editor" xsi:type="array">'.$this->getEol();
                $content .= $this->getPadding(6).'<item name="editorType" xsi:type="string">'.$this->getInlineEditType().'</item>'.$this->getEol();;
                $content .= $this->getPadding(6).'<item name="validation" xsi:type="array">'.$this->getEol();;
                $content .= $this->getPadding(7).'<item name="required-entry" xsi:type="boolean">true</item>'.$this->getEol();;
                $content .= $this->getPadding(6).'</item>'.$this->getEol();;
                $content .= $this->getPadding(5).'</item>';
            } else {
                $content .= $this->getEol().$this->getPadding(5).'<item name="editor" xsi:type="string">'.$this->getInlineEditType().'</item>';
            }
        }
        if ($this->getAttribute()->getAdminGrid() == Grid::HIDDEN) {
            $content .= $this->getEol().$this->getPadding(5).'<item name="visible" xsi:type="boolean">false</item>';
        }
        return $content;
    }

    public function getFilterType()
    {
        return $this->getData('filter_type');
    }

    public function getInlineEditType()
    {
        return $this->getData('inline_edit_type');
    }

    public function getColumnComponent()
    {
        $component = $this->getData('column_component');
        if ($component) {
            return $this->getEol().$this->getPadding(5).'<item name="component" xsi:type="string">Magento_Ui/js/grid/columns/'.$component.'</item>';
        }
        return '';
    }

    /**
     * get admin column type
     *
     * @return string
     */
    public function getAdminColumnType()
    {
        return $this->getData('admin_column_type');
    }

    public function getFullText()
    {
        return $this->getData('full_text');
    }

    /**
     * get sql type constant
     *
     * @return string
     */
    public function getSqlTypeConst()
    {
        return $this->getData('sql_type_const');
    }

    /**
     * get sql setup length
     *
     * @return string
     */
    public function getSetupLength()
    {
        return $this->getData('setup_length');
    }

    public function getEditFormField()
    {
        $lines = [];
        $tab = $this->getPadding();
        $eol = $this->getEol();
        $lines[] = '$fieldset->addField(';
        $lines[] = $tab.'\''.$this->getAttribute()->getCode().'\',';
        $lines[] = $tab.'\''.$this->getEditFormType().'\',';
        $lines[] = $tab.'[';
        $lines[] = $tab.$tab.'\'name\'  => \''.$this->getAttribute()->getCode().'\',';
        $lines[] = $tab.$tab.'\'label\' => __(\''.$this->getAttribute()->getLabel().'\'),';
        $lines[] = $tab.$tab.'\'title\' => __(\''.$this->getAttribute()->getLabel().'\'),';
        if ($this->getAttribute()->getRequired()) {
            $lines[] = $tab.$tab.'\'required\' => true,';
        }
        if ($note = $this->getAttribute()->getNote()) {
            $lines[] = $tab.$tab.'\'note\' => __(\''.$this->escaper->escapeJsQuote($note).'\'),';
        }
        foreach ($this->getAdditionalEditFormOptions() as $option) {
            $lines[] = $tab.$tab.$option;
        }
        $lines[] = $tab.']';
        $lines[] = ');';
        return $tab.$tab.implode($eol.$tab.$tab, $lines).$eol;
    }

    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        return [];
    }

    /**
     * get entity type for form
     *
     * @return string
     */
    public function getEditFormType()
    {
        return $this->getData('edit_form_type');
    }

    /**
     * get entity
     *
     * @return \Umc\Base\Model\Core\Entity
     */
    public function getEntity()
    {
        return $this->getAttribute()->getEntity();
    }

    /**
     * get module
     *
     * @return \Umc\Base\Model\Core\Module
     */
    public function getModule()
    {
        return $this->getEntity()->getModule();
    }

    /**
     * get header class for admin grid
     *
     * @return string
     */
    public function getGridHeaderClass()
    {
        return 'col-'.str_replace('_', '-', $this->getAttribute()->getCode());
    }

    /**
     * get class for admin grid
     *
     * @return string
     */
    public function getGridColumnClass()
    {
        return $this->getGridHeaderClass();
    }

    /**
     * get underscore value for protected members
     *
     * @return string
     */
    public function getUnderscore()
    {
        return $this->getModule()->getSettings()->getUnderscoreValue();
    }

    /**
     * get default value
     *
     * @return mixed|string
     */
    public function getDefaultValue()
    {
        return $this->getAttribute()->getData('default_value');
    }

    /**
     * check if attribute has options
     *
     * @return bool
     */
    public function getHasOptions()
    {
        return $this->getData('has_options');
    }


    public function getFilterRangeClass()
    {
        return '';
    }

    public function getFilterInput()
    {
        return $this->getData('filter_input');
    }

    public function getGridDataType()
    {
        $dataType = $this->getData('grid_data_type');
        if ($dataType) {
            return $this->getEol().$this->getPadding(5).'<item name="dataType" xsi:type="string">'.$dataType.'</item>';
        }
        return '';
    }
}

