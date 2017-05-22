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
namespace Umc\Base\Model;

use Magento\Framework\Escaper;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Attribute\Type\Factory as AttributeTypeFactory;

class Attribute extends AbstractModel implements AttributeInterface
{
    /**
     * type instance
     *
     * @var \Umc\Base\Api\Data\Attribute\TypeInterface
     */
    protected $typeInstance;

    /**
     * attribute type factory
     *
     * @var AttributeTypeFactory
     */
    protected $typeFactory;

    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = self::ENTITY_CODE;

    /**
     * processed options
     *
     * @var null|array
     */
    protected $processedOptions = null;

    /**
     * related entity
     *
     * @var \Umc\Base\Api\Data\EntityInterface
     */
    protected $entity;

    /**
     * constructor
     *
     * @param AttributeTypeFactory $typeFactory
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        AttributeTypeFactory $typeFactory,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($saveAttributesConfig, $formConfig, $escaper, $data);
    }

    /**
     * set related entity
     *
     * @param EntityInterface $entity
     * @return $this
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * get related entity
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * get attribute type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->typeInstance = null;
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * @param string $note
     * @return AttributeInterface|Attribute
     */
    public function setNote($note)
    {
        return $this->setData(self::NOTE, $note);
    }

    /**
     * @return bool
     */
    public function getAdminGridFilter()
    {
        return !$this->getEntity()->getIsTree() && $this->getData(self::ADMIN_GRID_FILTER);
    }

    /**
     * @param int $adminGridFilter
     * @return AttributeInterface|Attribute
     */
    public function setAdminGridFilter($adminGridFilter)
    {
        return $this->setData(self::ADMIN_GRID_FILTER, $adminGridFilter);
    }

    /**
     * get attribute code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param string $code
     * @return AttributeInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @param string $label
     * @return AttributeInterface
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * if attribute is name
     *
     * @return bool
     */
    public function getIsName()
    {
        return (bool)$this->getData(self::IS_NAME);
    }

    /**
     * @param string $isName
     * @return AttributeInterface
     */
    public function setIsName($isName)
    {
        return $this->setData(self::IS_NAME, $isName);
    }

    /**
     * get validation error key
     *
     * @param string $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return 'attribute_'.$this->getEntity()->getIndex().'_'.$this->getIndex().'_'.$field;
    }

    /**
     * get parent entity
     *
     * @return EntityInterface
     */
    public function getParent()
    {
        return $this->getEntity();
    }

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        if ($this->placeholders === null) {
            $this->placeholders = [
                '{{code}}'                  => $this->getCode(),
                '{{CODE}}'                  => strtoupper($this->getCode()),
                '{{codeCamelCase}}'         => $this->getCodeCamelCase(),
                '{{CodeCamelCase}}'         => $this->getCodeCamelCase(true),
                '{{Label}}'                 => $this->getLabel(),
                '{{LabelEscaped}}'          => $this->escaper->escapeHtmlAttr($this->getLabel()),
                '{{adminColumnOptions}}'    => $this->getTypeInstance()->getAdminColumnOptions(),
                '{{note}}'                  => $this->getNote(),
                '{{OptionConstants}}'       => $this->getOptionConstants(),
                '{{toOptionArray}}'         => $this->getToOptionsArray(),
                '{{optionsI18n}}'           => $this->getOptionsI18n(),
                '{{adminColumnType}}'       => $this->getTypeInstance()->getAdminColumnType(),
                '{{filterInput}}'           => $this->getTypeInstance()->getFilterInput(),
                '{{adminColumnConfig}}'     => $this->getTypeInstance()->getAdminColumnConfig(),
                '{{formDataType}}'          => $this->getTypeInstance()->getFormDataType(),
                '{{formElement}}'           => $this->getTypeInstance()->getFormElement(),
                '{{additionalFormConfig}}'  => $this->getAdditionalFormConfig(),
                '{{position}}'              => $this->getPosition(),
                '{{uiFormOptions}}'         => $this->getTypeInstance()->getUiFormOptions(),
                '{{type}}'                  => $this->getTypeInstance()->getType(),
                '{{adminColumnClass}}'      => $this->getTypeInstance()->getAdminColumnClass(),
                '{{additionalFormConfigV2}}'=> $this->getTypeInstance()->getAdditionalFormConfigV2(),
                '{{uiFormOptionsV2}}'       => $this->getTypeInstance()->getUiFormOptionsV2(),
                '{{adminColumnComponentV2}}'=> $this->getTypeInstance()->getAdminColumnComponentV2(),
                '{{adminColumnConfigV2}}'   => $this->getTypeInstance()->getAdminColumnConfigV2(),
            ];
            $this->placeholders = array_merge($this->placeholders, $this->getTypeInstance()->getPlaceholders());
            $this->placeholders = array_merge($this->getEntity()->getPlaceholders(), $this->placeholders);
        }
        return $this->placeholders;
    }

    /**
     * get type instance
     *
     * @return \Umc\Base\Api\Data\Attribute\TypeInterface
     */
    public function getTypeInstance()
    {
        if ($this->typeInstance === null) {
            $this->typeInstance = $this->typeFactory->create($this);
        }
        return $this->typeInstance;
    }

    /**
     * get camel case code
     *
     * @param bool $toUpper
     * @return string
     */
    public function getCodeCamelCase($toUpper = false)
    {
        $code = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getCode())));
        if ($toUpper) {
            return $code;
        }
        return lcfirst($code);
    }

    /**
     * get entity type
     * used in source.xml depends
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->getEntity()->getType();
    }

    /**
     * used in source.xml depends
     *
     * @return bool
     */
    public function getAdminGridNotRestricted()
    {
        return $this->getAdminGrid() || $this->getIsName();
    }

    /**
     * get the admin grid flag
     * @return int
     */
    public function getAdminGrid()
    {
        if ($this->getIsName()) {
            return 0;
        }
        return $this->getData(self::ADMIN_GRID);
    }

    /**
     * @param int $adminGrid
     * @return AttributeInterface|Attribute
     */
    public function setAdminGrid($adminGrid)
    {
        return $this->setData(self::ADMIN_GRID, $adminGrid);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->getData(self::POSITION);
    }

    /**
     * @param int $position
     * @return AttributeInterface|Attribute
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->getData(self::REQUIRED);
    }

    /**
     * @param int $required
     * @return AttributeInterface|Attribute
     */
    public function setRequired($required)
    {
        return $this->setData(self::REQUIRED, $required);
    }

    /**
     * get attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        return ucwords($this->getData(self::LABEL));
    }

    /**
     * get setup for table column
     *
     * @return string
     */
    public function getColumnSetup()
    {
        $eol = $this->getEol();
        $tab = $this->getPadding();
        $tableClass = '{{class Magento\Framework\DB\Ddl\Table}}';
        $lines = [];
        $lines[] = '->addColumn(';
        $lines[] = $tab."'".$this->getCode()."',";
        $lines[] = $tab.$tableClass.'::'.$this->getTypeInstance()->getSqlTypeConst().',';
        $lines[] = $tab.$this->getTypeInstance()->getSetupLength().',';
        if ($this->getRequired()) {
            $lines[] = $tab.'[\'nullable => false\'],';
        } else {
            $lines[] = $tab.'[],';
        }
        $lines[] = $tab.'\''.$this->getEntity()->getLabelSingular(true).' '.$this->getLabel().'\'';
        $lines[] = ')';
        return $tab.$tab.$tab.implode($eol.$tab.$tab.$tab, $lines).$eol;
    }

    /**
     * get the default value after processing
     *
     * @return string
     */
    public function getDefaultValueProcessed()
    {
        return $this->getTypeInstance()->getDefaultValue();
    }

    /**
     * check if attribute has options
     * used in source dependency
     *
     * @return bool
     */
    public function getHasOptions()
    {
        return $this->getTypeInstance()->getHasOptions();
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * @param string $options
     * @return AttributeInterface|Attribute
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @return int
     */
    public function getUiVersion()
    {
        return $this->getEntity()->getModule()->getUiVersion();
    }

    /**
     * get attribute options after processing
     *
     * @return array|null
     */
    protected function getProcessedOptions()
    {
        if ($this->processedOptions === null) {
            $this->processedOptions = [];
            $optionText = $this->getOptions();
            $options = explode("\n", $optionText);
            foreach ($options as $key => $option) {
                $option = trim($option);
                $this->processedOptions[$this->toConstantName($option)] = [
                    'value' => $key + 1,
                    'label' => $option
                ];
            }
        }
        return $this->processedOptions;
    }

    /**
     * generate constants based on the options
     *
     * @return string
     */
    protected function getOptionConstants()
    {
        $constants = $this->getProcessedOptions();
        $result = [];
        foreach ($constants as $name => $settings) {
            $result[] = 'const '.$name.' = '.$settings['value'].';';
        }
        if (count($result)) {
            $tab = $this->getPadding();
            $eol = $this->getEol();
            return $tab.implode($eol.$tab, $result).$eol;
        }
        return '';
    }

    /**
     * generate toOptionsArray method for the source model
     *
     * @return string
     */
    protected function getToOptionsArray()
    {
        $options = $this->getProcessedOptions();
        $tab = $this->getPadding();
        $eol = $this->getEol();
        $result = [];
        $result[] = '$options = [';
        foreach ($options as $key => $settings) {
            $result[] = $tab.'[';
            $result[] = $tab.$tab.'\'value\' => self::'.$key.',';
            $result[] = $tab.$tab.'\'label\' => __(\''.$this->escaper->escapeHtml($settings['label']).'\')';
            $result[] = $tab.'],';
        }
        $result[] = '];';
        $result[] = 'return $options;';
        return $tab.$tab.implode($eol.$tab.$tab, $result).$eol;
    }

    /**
     * transform string to constant name
     *
     * @param string $string
     * @return string
     */
    protected function toConstantName($string)
    {
        $string = str_replace(' ', '_', $string);
        $processed =  preg_replace(
            '/[^A-Za-z0-9_]/',
            '',
            $string
        );
        $processed = strtoupper($processed);
        if (strlen($processed) == 0) {
            return '_EMPTY';
        }
        $first = substr($processed, 0, 1);
        if (is_numeric($first)) {
            $processed = '_'.$processed;
        }
        return $processed;
    }

    /**
     * get options for translation file
     *
     * @return string
     */
    protected function getOptionsI18n()
    {
        $options = $this->getProcessedOptions();
        $texts   = [];
        foreach ($options as $settings) {
            $value = str_replace('"', '""', $settings['label']);
            $texts[] = '"'.$value.'","'.$value.'"';
        }
        return implode($this->getEol(), $texts);
    }

    /**
     * @return bool
     */
    public function getFullText()
    {
        return $this->getTypeInstance()->getFullText();
    }

    /**
     * @return bool
     */
    public function getInlineEdit()
    {
        return $this->getEntity()->getInlineEdit() && $this->getData(self::INLINE_EDIT);
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfig()
    {
        return $this->getTypeInstance()->getAdditionalFormConfig();
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfigV2()
    {
        return $this->getTypeInstance()->getAdditionalFormConfigV2();
    }
}
