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

use Magento\Framework\Escaper;
use Umc\Base\Api\Data\Attribute\TypeInterface;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Model\Umc;
use Umc\Base\Source\Grid;

class AbstractType extends Umc implements TypeInterface
{
    const NAME = 'abstract';

    /**
     * @var AttributeInterface
     */
    protected $attribute;

    /**
     * set the attribute instance
     *
     * @param AttributeInterface $attribute
     * @return $this|mixed
     */
    public function setAttribute(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * get the attribute instance
     *
     * @return AttributeInterface
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
        return $this->getData(self::MULTI);
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

    /**
     * @return string
     */
    public function getAdminColumnConfig()
    {
        $content = '';
        if ($this->getAttribute()->getAdminGridFilter()) {
            $content .= '<item name="filter" xsi:type="string">'.$this->getFilterType().'</item>';
        }
        if ($this->getAttribute()->getInlineEdit()) {
            if ($this->getAttribute()->getRequired()) {
                $content .= '<item name="editor" xsi:type="array">';
                $content .= '<item name="editorType" xsi:type="string">'.$this->getInlineEditType().'</item>';
                $content .= '<item name="validation" xsi:type="array">';
                $content .= '<item name="required-entry" xsi:type="boolean">true</item>';
                $content .= '</item>';
                $content .= '</item>';
            } else {
                $content .= '<item name="editor" xsi:type="string">'.$this->getInlineEditType().'</item>';
            }
        }
        if ($this->getAttribute()->getAdminGrid() == Grid::HIDDEN) {
            $content .= '<item name="visible" xsi:type="boolean">false</item>';
        }
        $content .= $this->getColumnComponent();
        $content .= $this->getGridDataType();
        return $content;
    }

    /**
     * @return string
     */
    public function getFilterType()
    {
        return $this->getData(self::FILTER_TYPE);
    }

    /**
     * @return string
     */
    public function getInlineEditType()
    {
        return $this->getData(self::INLINE_EDIT_TYPE);
    }

    /**
     * @return string
     */
    protected function getColumnComponent()
    {
        $component = $this->getData(self::COLUMN_COMPONENT);
        if ($component) {
            return '<item name="component" xsi:type="string">'.$component.'</item>';
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
        return $this->getData(self::ADMIN_COLUMN_TYPE);
    }

    /**
     * @return string
     */
    public function getFullText()
    {
        return $this->getData(self::FULL_TEXT);
    }

    /**
     * get sql type constant
     *
     * @return string
     */
    public function getSqlTypeConst()
    {
        return $this->getData(self::SQL_TYPE_CONST);
    }

    /**
     * get sql setup length
     *
     * @return string
     */
    public function getSetupLength()
    {
        return $this->getData(self::SETUP_LENGTH);
    }

    /**
     * get default value
     *
     * @return mixed|string
     */
    public function getDefaultValue()
    {
        return $this->getAttribute()->getData(self::DEFAULT_VALUE);
    }

    /**
     * check if attribute has options
     *
     * @return bool
     */
    public function getHasOptions()
    {
        return $this->getData(self::HAS_OPTIONS);
    }

    /**
     * @return string
     */
    public function getFilterRangeClass()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function getFilterInput()
    {
        return $this->getData(self::FILTER_INPUT);
    }

    /**
     * @return string
     */
    protected function getGridDataType()
    {
        return $this->getDataTypeXml(self::GRID_DATA_TYPE);
    }

    /**
     * @return string
     */
    public function getFormDataType()
    {
        return $this->getDataTypeXml(self::FORM_DATA_TYPE);
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getDataTypeXml($field)
    {
        $dataType = $this->getData($field);
        if ($dataType) {
            return '<item name="dataType" xsi:type="string">'.$dataType.'</item>';
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getFormElement()
    {
        return $this->getData(self::FORM_ELEMENT);
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfig()
    {
        $config = '';
        if ($this->getAttribute()->getRequired()) {
            $config .= '<item name="validation" xsi:type="array">';
            $config .= '<item name="required-entry" xsi:type="boolean">true</item>';
            $config .= '</item>';
        }
        if ($default = $this->getAttribute()->getDefaultValueProcessed()) {
            $config .= '<item name="default" xsi:type="string">'.$default.'</item>';
        }
        if ($note = $this->getAttribute()->getNote()) {
            $config .= '<item name="notice" xsi:type="string" translate="true">'.$note.'</item>';
        }
        return $config;
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfigV2()
    {
        $config = '';
        if ($this->getAttribute()->getRequired()) {
            $config .= '<validation>';
            $config .= '<rule name="required-entry" xsi:type="boolean">true</rule>';
            $config .= '</validation>'.$this->getEol();
        }
//        if ($default = $this->getAttribute()->getDefaultValueProcessed()) {
//            $config .= '<default>'.$default.'</default>';
//        }
        if ($note = $this->getAttribute()->getNote()) {
            $config .= '<notice>'.$note.'</notice>';
        }
        return $config;
    }

    /**
     * @return string
     */
    public function getUiFormOptions()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUiFormOptionsV2()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return static::NAME;
    }

    /**
     * @return string
     */
    public function getAdminColumnClass()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAdminColumnComponentV2()
    {
        if ($component = $this->getData(self::COLUMN_COMPONENT)) {
            return ' component="'.$component.'"';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getAdminColumnConfigV2()
    {
        $content = '';
        if ($this->getAttribute()->getAdminGridFilter()) {
            $content .= '<filter>'.$this->getFilterType().'</filter>';
        }
        if ($this->getAttribute()->getInlineEdit()) {
            $content .= '<editor>';
            if ($this->getAttribute()->getRequired()) {
                $content .= '<validation>';
                $content .= '<rule name="required-entry" xsi:type="boolean">true</rule>';
                $content .= '</validation>';
            }
            $content .= '<editorType>'.$this->getInlineEditType().'</editorType>';
            $content .= '</editor>';
        }
        if ($this->getAttribute()->getAdminGrid() == Grid::HIDDEN) {
            $content .= '<visible>false</visible>';
        }
        $content .= '<dataType>'.$this->getData(self::GRID_DATA_TYPE).'</dataType>';
        return $content;
    }
}
