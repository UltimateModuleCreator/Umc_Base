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
use Umc\Base\Model\Core\Entity\Type\Factory as TypeFactory;
use Umc\Base\Model\Core\Entity\Type\TypeInterface;

/**
 * @method int getSortOrder()
 */
class Entity extends AbstractModel implements ModelInterface
{

    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_entity';

    /**
     * parent module
     *
     * @var Module
     */
    protected $module;

    /**
     * attributes
     *
     * @var Attribute[]
     */
    protected $attributes = [];

    /**
     * related entities
     *
     * @var array
     */
    protected $relatedEntities = [];

    /**
     * entity type instance
     *
     * @var TypeInterface
     */
    protected $typeInstance;

    /**
     * entity type factory
     *
     * @var TypeFactory
     */
    protected $typeFactory;

    /**
     * attribute that acts as name
     *
     * @var Attribute|null
     */
    protected $nameAttribute;

    /**
     * constructor
     *
     * @param TypeFactory $typeFactory
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        TypeFactory $typeFactory,
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
     * set module reference
     *
     * @param Module $module
     * @return $this
     */
    public function setModule(Module $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * get module reference
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * get entity type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * get entity label singular
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getLabelSingular($ucFirst = false)
    {
        $label = $this->getData('label_singular');
        if ($ucFirst) {
            $label = ucfirst($label);
        }
        return $label;
    }

    /**
     * get entity label plural
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getLabelPlural($ucFirst = false)
    {
        $label = $this->getData('label_plural');
        if ($ucFirst) {
            $label = ucfirst($label);
        }
        return $label;
    }

    /**
     * add attribute
     *
     * @param Attribute $attribute
     * @return $this
     */
    public function addAttribute(Attribute $attribute)
    {
        $attribute->setEntity($this);
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * get entity attributes
     *
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * save as XML
     *
     * @param array $arrAttributes
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml(array $arrAttributes = [], $rootName = 'entity', $addOpenTag = false, $addCdata = true)
    {
        $xml = '';
        if ($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if (!empty($rootName)) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        $xml .= parent::toXml($arrAttributes, '', false, $addCdata);
        $xml .= '<attributes>';
        foreach ($this->getAttributes() as $attribute) {
            $xml .= $attribute->toXml([], 'attribute', false, $addCdata);
        }
        $xml .= '</attributes>';
        if (!empty($rootName)) {
            $xml.= '</'.$rootName.'>'."\n";
        }
        return $xml;
    }

    /**
     * get type instance
     *
     * @return TypeInterface
     */
    public function getTypeInstance()
    {
        if (is_null($this->typeInstance)) {
            $this->typeInstance = $this->typeFactory->create($this);
        }
        return $this->typeInstance;
    }

    /**
     * validate entity
     *
     * @return array
     */
    public function validate()
    {
        $errors = parent::validate();
        if ($this->getNameSingular() == $this->getNamePlural()) {
            $errors[$this->getValidationErrorKey('name_plural')][] = __(
                'Singular and Plural names should not be the same'
            );
        }
        foreach ($this->getAttributes() as $attribute) {
            $attributeErrors = $attribute->validate();
            $errors = array_merge($errors, $attributeErrors);
        }
        $nameAttribute = $this->getNameAttribute();
        if (is_null($nameAttribute)) {
            $errors[''][] = __(
                'Entity "%1" does not have an attribute that behaves as name',
                $this->getLabelSingular()
            );
        }
        return $errors;
    }

    /**
     * get name attribute
     *
     * @return null|Attribute
     */
    public function getNameAttribute()
    {
        if (is_null($this->nameAttribute)) {
            foreach ($this->getAttributes() as $attribute) {
                if ($attribute->getIsName()) {
                    $this->nameAttribute = $attribute;
                    break;
                }
            }
        }
        return $this->nameAttribute;
    }

    /**
     * get validation error key
     *
     * @param $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return 'entity_'.$this->getIndex().'_'.$field;
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
                    $allowedValues = $values['entity'];
                    $value = trim($this->getData($fieldDepend));
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
     * check if a certain attribute type exists
     *
     * @param $type
     * @return bool
     */
    public function getHasAttributeType($type)
    {
        return $this->getTypeInstance()->getHasAttributeType($type);
    }

    /**
     * __call magic method
     * handle the getHasAttributeType calls
     *
     * @param string $method
     * @param array $args
     * @return bool|mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, strlen('getHasAttributeType')) == 'getHasAttributeType') {
            $key = $this->_underscore(substr($method, strlen('getHasAttributeType')));
            return $this->getHasAttributeType($key);
        }
        return parent::__call($method, $args);
    }

    /**
     * check if there is an attribute with editor
     *
     * @return bool
     */
    public function getHasEditor()
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getData('editor')) {
                return true;
            }
        }
        return false;
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
                '{{entity}}'            => $this->getNameSingular(),
                '{{Entity}}'            => $this->getNameSingular(true),
                '{{EntityLabel}}'       => $this->getLabelSingular(true),
                '{{EntitiesLabel}}'     => $this->getLabelPlural(true),
                '{{entities}}'          => $this->getNamePlural(),
                '{{Entities}}'          => $this->getNamePlural(true),
                '{{sort_order}}'        => $this->getSortOrder(),
                '{{nameAttributeCode}}' => $this->getNameAttributeCode(),
                '{{NameAttributeCode}}' => $this->getNameAttributeCode(true),
                '{{NameAttributeLabel}}'=> $this->getNameAttribute()->getLabel(),
                '{{dateAttributeCodes}}'=> $this->getDateAttributeCodes(),
                '{{columnsSetup}}'      => $this->getColumnsSetup(),
                '{{editFormFields}}'    => $this->getEditFormFields(),
                '{{defaultAttributeValues}}' => $this->getDefaultAttributeValues()
            ];
            $this->placeholders = array_merge($this->placeholders, $this->getTypeInstance()->getPlaceholders());
            $this->placeholders = array_merge($this->getModule()->getPlaceholders(), $this->placeholders);
        }
        return $this->placeholders;
    }

    /**
     * get entity name singular
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getNameSingular($ucFirst = false)
    {
        $name = $this->getData('name_singular');
        if ($ucFirst) {
            $name = ucfirst($name);
        }
        return $name;
    }

    /**
     * get entity name plural
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getNamePlural($ucFirst = false)
    {
        $name = $this->getData('name_plural');
        if ($ucFirst) {
            $name = ucfirst($name);
        }
        return $name;
    }

    /**
     * get parent module
     *
     * @return ModelInterface|Module
     */
    public function getParent()
    {
        return $this->getModule();
    }

    /**
     * @param $ucFirst
     * @return string
     * @throws \Exception
     */
    protected function getNameAttributeCode($ucFirst = false)
    {
        $attribute = $this->getNameAttribute();
        if (!$attribute) {
            throw new \Exception("Entity ".$this->getNameSingular().' does not have a name attribute set');
        }
        $code = $attribute->getCode();
        $code = $this->_underscore($code);
        if ($ucFirst) {
            return ucfirst($code);
        }
        return $code;
    }

    /**
     * check if entity has multi select elements
     *
     * @return mixed
     */
    public function getHasMulti()
    {
        return $this->getTypeInstance()->getHasMulti();
    }

    /**
     * get date attribute codes
     *
     * @return string
     */
    public function getDateAttributeCodes()
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getType() == 'date') {
                $attributes[] = $attribute->getCode();
            }
        }
        return '[\''.implode('\', \'', $attributes).'\']';
    }

    /**
     * get all table columns
     *
     * @return string
     */
    public function getColumnsSetup()
    {
        $setup = '';
        foreach ($this->getAttributes() as $attribute) {
            $setup .= $attribute->getColumnSetup();
        }
        return $setup;
    }

    /**
     * get all edit form fields
     *
     * @return string
     */
    public function getEditFormFields()
    {
        $fields = '';
        foreach ($this->getAttributes() as $attribute) {
            $fields .= $attribute->getEditFormField();
        }
        return $fields;
    }

    /**
     * get default values for add form
     *
     * @return string
     */
    public function getDefaultAttributeValues()
    {
        $tab = $this->getPadding();
        $lines = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getData('default_value'))  {
                $lines[] = $tab.$tab.'$values[\''.$attribute->getCode().'\'] = \''.
                    $this->escaper->escapeJsQuote($attribute->getDefaultValueProcessed()).'\';';
            }
        }
        $value = implode($this->getEol(), $lines);
        return $value;
    }

    /**
     * get child models (attributes)
     *
     * @return AbstractModel[]
     */
    public function getChildModels()
    {
        return $this->getAttributes();
    }

    /**
     * TODO: add events for different types
     * @return string
     */
    public function getUninstallLines()
    {
        return ['DROP TABLE '.$this->getModule()->getNamespace(true).'_'.
            $this->getModule()->getModuleName(true).'_'.
            $this->getNameSingular().';'];
    }
}
