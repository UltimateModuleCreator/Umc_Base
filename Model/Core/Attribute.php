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
namespace Umc\Base\Model\Core;

use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Config\Restriction as RestrictionConfig;
use Umc\Base\Model\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Core\Attribute\Type\Factory as AttributeTypeFactory;

/**
 * @method bool getRequired()
 * @method string getOptions()
 * @method string getNote()
 * @method Attribute setCode(\string $code)
 * @method Attribute setLabel(\string $label)
 * @method Attribute setType(\string $type)
 * @method Attribute setForcedSourceModel(\string $forcedSourceModel)
 * @method string getForcedSourceModel()
 */
class Attribute extends AbstractModel implements ModelInterface
{
    /**
     * type instance
     *
     * @var \Umc\Base\Model\Core\Attribute\Type\TypeInterface
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
    protected $entityCode = 'umc_attribute';

    /**
     * processed options
     *
     * @var null|array
     */
    protected $processedOptions = null;

    /**
     * related entity
     *
     * @var \Umc\Base\Model\Core\Entity
     */
    protected $entity;

    /**
     * constructor
     *
     * @param AttributeTypeFactory $typeFactory
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        AttributeTypeFactory $typeFactory,
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($eventManager, $saveAttributesConfig, $formConfig, $restrictionConfig, $escaper, $data);
    }

    /**
     * set related entity
     *
     * @param Entity $entity
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * get related entity
     *
     * @return Entity
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
        return $this->getData('type');
    }

    /**
     * get attribute code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * if attribute is name
     *
     * @return bool
     */
    public function getIsName()
    {
        return !!$this->getData('is_name');
    }

    /**
     * get validation error key
     *
     * @param $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return 'attribute_'.$this->getEntity()->getIndex().'_'.$this->getIndex().'_'.$field;
    }

    /**
     * check if field should be validated
     *
     * @param $field
     * @param $fieldSettings
     * @return bool
     */
    protected function shouldValidateField($field, $fieldSettings)
    {
        if (!parent::shouldValidateField($field, $fieldSettings)) {
            return false;
        }
        $depends = $this->getFormDepends();
        if (isset($depends[$field])) {
            $allValid = false;
            foreach ($depends[$field] as $dependGroup) {
                $isValid = true;
                foreach ($dependGroup as $fieldDepend => $values) {
                    if (isset($values['entity'])) {
                        $allowedValues = $values['entity'];
                        $source = $this->getEntity();
                    } else {
                        $allowedValues = $values['attribute'];
                        $source = $this;
                    }
                    $value = trim($source->getData($fieldDepend));
                    if (!in_array($value, $allowedValues)) {
                        $isValid = false;
                        continue;
                    }
                }
                if ($isValid) {
                    $allValid = true;
                }
            }
            if (!$allValid) {
                return false;
            }
        }
        return true;
    }

    /**
     * get parent entity
     *
     * @return Entity
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
        if (is_null($this->placeholders)) {
            $this->placeholders = [
                '{{code}}'                  => $this->getCode(),
                '{{codeCamelCase}}'         => $this->getCodeCamelCase(),
                '{{CodeCamelCase}}'         => $this->getCodeCamelCase(true),
                '{{Label}}'                 => $this->getLabel(),
                '{{adminColumnOptions}}'    => $this->getAdminColumnOptions(),
                '{{note}}'                  => $this->getNote(),
                '{{grid_header_class}}'     => $this->getGridHeaderClass(),
                '{{grid_column_class}}'     => $this->getGridColumnClass(),
                '{{OptionConstants}}'       => $this->getOptionConstants(),
                '{{toOptionArray}}'         => $this->getToOptionsArray(),
                '{{options_i18n}}'          => $this->getOptionsI18n()
            ];
            $this->placeholders = array_merge($this->placeholders, $this->getTypeInstance()->getPlaceholders());
            $this->placeholders = array_merge($this->getEntity()->getPlaceholders(), $this->placeholders);
        }
        return $this->placeholders;
    }

    /**
     * get type instance
     *
     * @return Attribute\Type\TypeInterface
     */
    public function getTypeInstance()
    {
        if (is_null($this->typeInstance)) {
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
     * check if attribute is multiselect
     *
     * @return bool
     */
    public function getIsMulti()
    {
        return $this->getTypeInstance()->isMulti();
    }

    /**
     * get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->getEntity()->getType();
    }

    /**
     * get the admin grid flag
     * @return bool
     */
    public function getAdminGrid()
    {
        if ($this->getIsName()) {
            return false;
        }
        return $this->getData('admin_grid');
    }

    /**
     * get attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        return ucwords($this->getData('label'));
    }

    /**
     * get admin grid options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        return $this->getTypeInstance()->getAdminColumnOptions();
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
        $lines[] = $tab.$tableClass.'::'.$this->getSqlTypeConst().',';
        $lines[] = $tab.$this->getSetupLength().',';
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
     * get sql setup constant
     *
     * @return string
     */
    public function getSqlTypeConst()
    {
        return $this->getTypeInstance()->getSqlTypeConst();
    }

    /**
     * get sql setup length
     *
     * @return mixed
     */
    public function getSetupLength()
    {
        return $this->getTypeInstance()->getSetupLength();
    }

    /**
     * get edit for field
     *
     * @return string
     */
    public function getEditFormField()
    {
        return $this->getTypeInstance()->getEditFormField();
    }

    /**
     * get admin grid header class
     *
     * @return string
     */
    public function getGridHeaderClass()
    {
        return $this->getTypeInstance()->getGridHeaderClass();
    }

    /**
     * get admin grid column class
     *
     * @return string
     */
    public function getGridColumnClass()
    {
        return $this->getTypeInstance()->getGridColumnClass();
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
     *
     * @return bool
     */
    public function getHasOptions()
    {
        return $this->getTypeInstance()->getHasOptions();
    }

    /**
     * get attribute options after processing
     *
     * @return array|null
     */
    protected function getProcessedOptions()
    {
        if (is_null($this->processedOptions)) {
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
    public function getOptionConstants()
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
    public function getToOptionsArray()
    {
        $options = $this->getProcessedOptions();
        $tab = $this->getPadding();
        $eol = $this->getEol();
        $result = [];
        $result[] = '$options = [';
        foreach ($options as $key => $settings) {
            $result[] = $tab.'[';
            $result[] = $tab.$tab.'\'value\' => self::'.$key.',';
            $result[] = $tab.$tab.'\'label\' => __(\''.$this->escaper->escapeJsQuote($settings['label']).'\')';
            $result[] = $tab.'],';
        }
        $result[] = '];';
        $result[] = 'return $options;';
        return $tab.$tab.implode($eol.$tab.$tab, $result).$eol;
    }

    /**
     * transform string to constant name
     *
     * @param $string
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
}
