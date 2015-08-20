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
use Umc\Base\Model\Core\AttributeFactory;
use Umc\Base\Model\Core\Entity\Type\Factory as TypeFactory;
use Umc\Base\Model\Core\Entity\Type\TypeInterface;
use Umc\Base\Model\Core\Relation\Type\ParentRelation;
use Umc\Base\Model\Core\Relation\Type\SiblingRelation;

/**
 * @method int getSortOrder()
 * @method int getIsTree()
 * @method Entity setIsParent(\bool $parent)
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
     * @var
     */
    protected $attributeFactory;

    /**
     * attribute that acts as name
     *
     * @var Attribute|null
     */
    protected $nameAttribute;

    /**
     * @var array
     */
    protected $parentAttributes = [];

    /**
     * @var array
     */
    protected $placeholdersAsSibling;

    /**
     * constructor
     *
     * @param TypeFactory $typeFactory
     * @param AttributeFactory $attributeFactory
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        TypeFactory $typeFactory,
        AttributeFactory $attributeFactory,
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        array $data = []
    ) {
        $this->typeFactory      = $typeFactory;
        $this->attributeFactory = $attributeFactory;
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
     * check if a certain attribute type exists
     *
     * @param $type
     * @return bool
     */
    public function getHasAttributeTypeRequired($type)
    {
        return $this->getTypeInstance()->getHasAttributeTypeRequired($type);
    }

    /**
     * __call magic method
     * handle the getHasAttributeType, getHasRelationType calls
     *
     * @param string $method
     * @param array $args
     * @return bool|mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, strlen('getHasAttributeTypeRequired')) == 'getHasAttributeTypeRequired') {
            $key = $this->_underscore(substr($method, strlen('getHasAttributeTypeRequired')));
            return $this->getHasAttributeTypeRequired($key);
        }
        if (substr($method, 0, strlen('getHasAttributeType')) == 'getHasAttributeType') {
            $key = $this->_underscore(substr($method, strlen('getHasAttributeType')));
            return $this->getHasAttributeType($key);
        }
        if (substr($method, 0, strlen('getHasRelationType')) == 'getHasRelationType') {
            $key = $this->_underscore(substr($method, strlen('getHasRelationType')));
            return $this->hasRelationType($key);
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
     * check if there is an attribute with editor
     *
     * @return bool
     */
    public function getHasEditorRequired()
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getData('editor') && $attribute->getRequired()) {
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
                '{{entity}}'                => $this->getNameSingular(),
                '{{Entity}}'                => $this->getNameSingular(true),
                '{{EntityLabel}}'           => $this->getLabelSingular(true),
                '{{EntitiesLabel}}'         => $this->getLabelPlural(true),
                '{{entities}}'              => $this->getNamePlural(),
                '{{Entities}}'              => $this->getNamePlural(true),
                '{{sortOrder}}'             => $this->getSortOrder(),
                '{{nameAttributeCode}}'     => $this->getNameAttributeCode(),
                '{{NameAttributeCode}}'     => $this->getNameAttributeCode(true),
                '{{NameAttributeLabel}}'    => $this->getNameAttribute()->getLabel(),
                '{{nameAttributeType}}'     => $this->getNameAttribute()->getTypeInstance()->getAdminColumnType(),
                '{{dateAttributeCodes}}'    => $this->getDateAttributeCodes(),
                '{{columnsSetup}}'          => $this->getColumnsSetup(),
                '{{editFormFields}}'        => $this->getEditFormFields(),
                '{{defaultAttributeValues}}'=> $this->getDefaultAttributeValues(),
                '{{isActiveTreeSuggest}}'   => $this->getIsActiveTreeSuggest(),
                '{{treeClass}}'             => $this->getTreeClass(),
                '{{editSpecificJsAction}}'  => $this->getEditSpecificJsAction(),
                '{{AdminFormParentClass}}'  => $this->getAdminFormParentClass(),
                '{{editFormFieldsAsNew}}'   => $this->getEditFormFieldsAsNew(),
                '{{quickSaveAttributes}}'   => $this->getQuickSaveAttributes(),
                '{{clearAttributes}}'       => $this->getClearAttributes(),
                '{{fullTextFields}}'        => $this->getFullTextFields(),
            ];
            $this->placeholders = array_merge($this->placeholders, $this->getTypeInstance()->getPlaceholders());
            $this->placeholders = array_merge($this->getModule()->getPlaceholders(), $this->placeholders);
        }
        return $this->placeholders;
    }

    /**
     * @return array
     */
    public function getPlaceholdersAsSibling()
    {
        if (is_null($this->placeholdersAsSibling)) {
            $this->placeholdersAsSibling = [
                '{{sibling}}'       => $this->getNameSingular(),
                '{{Sibling}}'       => $this->getNameSingular(true),
                '{{SiblingLabel}}'  => $this->getLabelSingular(true),
                '{{SiblingsLabel}}' => $this->getLabelPlural(true),
                '{{siblings}}'      => $this->getNamePlural(),
                '{{Siblings}}'      => $this->getNamePlural(true),
                '{{SiblingNameAttributeLabel}}' => $this->getNameAttribute()->getLabel(),
                '{{siblingNameAttributeCode}}' => $this->getNameAttribute()->getCode(),
                '{{siblingTabParent}}' => $this->getSiblingTabParent(),
                '{{siblingTabParentAlias}}' => $this->getSiblingTabParentAlias(),
            ];
        }
        return $this->placeholdersAsSibling;
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
        /** @var Attribute[] $allAttributes */
        $allAttributes = array_merge(
            $this->getParentsAttributes('setup'),
            $this->getAttributes(),
            $this->getSystemAttributes()
        );
        foreach ($allAttributes as $attribute) {
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
        $parents = $this->getParentsAttributes('edit_form');
        foreach ($parents as $parent) {
            $fields .= $parent->getEditFormField();
        }
        foreach ($this->getAttributes() as $attribute) {
            $fields .= $attribute->getEditFormField();
        }
        return $fields;
    }

    public function getEditFormFieldsAsNew()
    {
        $fields = '';
        $prefix = 'new_'.$this->getNameSingular().'_';
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getRequired() && !$attribute->getIsName()) {
                $clone = $this->attributeFactory->create();
                $clone->setEntity($this);
                $clone->setData($attribute->getData());
                $clone->setCode($prefix.$clone->getCode());
                $fields .= $clone->getEditFormField();
            }
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

    /**
     * //TODO: should be pluginezed when 'is_active' attribute support is added
     * @return string
     */
    public function getIsActiveTreeSuggest()
    {
        return 'true';
    }

    /**
     * //TODO: should be pluginezed when 'is_active' attribute support is added
     * @return string
     */
    public function getTreeClass()
    {
        return 'active-category';
    }

    /**
     * @return Attribute[]
     */
    public function getSystemAttributes()
    {
        $attributes = [];
        if ($this->getIsTree()) {
            $attribute = $this->attributeFactory->create();
            $attribute->setCode('parent_id');
            $attribute->setLabel('Parent id');
            $attribute->setType('int');
            $attribute->setEntity($this);
            $attributes[] = $attribute;

            $attribute = $this->attributeFactory->create();
            $attribute->setCode('path');
            $attribute->setLabel('Path');
            $attribute->setType('text');
            $attribute->setEntity($this);
            $attributes[] = $attribute;

            $attribute = $this->attributeFactory->create();
            $attribute->setCode('position');
            $attribute->setLabel('Position');
            $attribute->setType('int');
            $attribute->setEntity($this);
            $attributes[] = $attribute;

            $attribute = $this->attributeFactory->create();
            $attribute->setCode('level');
            $attribute->setLabel('Level');
            $attribute->setType('int');
            $attribute->setEntity($this);
            $attributes[] = $attribute;

            $attribute = $this->attributeFactory->create();
            $attribute->setCode('children_count');
            $attribute->setLabel('Children Count');
            $attribute->setType('int');
            $attribute->setEntity($this);
            $attributes[] = $attribute;
        }
        return $attributes;
    }

    /**
     * //todo: pluginize when is_active attribute is added
     * @return string
     */
    public function getEditSpecificJsAction()
    {
        return '';
    }

    public function getAdminFormParentClass()
    {
        $module = $this->getModule();
        return (!$this->getIsTree())
            ? 'Magento\Backend\Block\Widget\Form\Generic'
            : $module->getNamespace().'\\'.$module->getModuleName().'\Block\Adminhtml\\'.$this->getNameSingular(true).'\\Abstract'.$this->getNameSingular(true);
    }

    /**
     * @param $type
     * @param Entity $entity
     * @return $this
     */
    public function addRelatedEntity($type, Entity $entity)
    {
        if (!isset($this->relatedEntities[$type])) {
            $this->relatedEntities[$type] = [];
        }
        $this->relatedEntities[$type][] = $entity;
        return $this;
    }

    /**
     * @param null $type
     * @return array|Entity[]
     */
    public function getRelatedEntities($type = null)
    {
        if (is_null($type)) {
            return $this->relatedEntities;
        }
        if (!isset($this->relatedEntities[$type])) {
            return [];
        }
        return $this->relatedEntities[$type];
    }


    /**
     * @param string $forWhat
     * @return Attribute[]
     */
    public function getParentsAttributes($forWhat = 'setup')
    {
        if (!isset($this->parentAttributes[$forWhat])) {
            $this->parentAttributes[$forWhat] = [];
            $parents = $this->getRelatedEntities(ParentRelation::RELATION_TYPE_PARENT);
            switch ($forWhat) {
                case 'edit_form' :
                case 'grid':
                    $this->parentAttributes[$forWhat] = $this->getFormattedParentAttributesEditForm($parents);
                    break;
                case 'setup':
                default:
                    $this->parentAttributes[$forWhat] = $this->getFormattedParentAttributesSetup($parents);
                    break;
            }
        }
        return $this->parentAttributes[$forWhat];
    }

    /**
     * @param Entity[] $parents
     * @return array
     */
    protected function getFormattedParentAttributesSetup($parents)
    {
        $attributes = [];
        foreach ($parents as $parent) {
            /** @var Attribute $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->setType('int');
            $attribute->setCode($parent->getNameSingular().'_id');
            $attribute->setLabel($parent->getLabelSingular(true));
            $attribute->setEntity($this);
            $attributes[] = $attribute;
        }
        return $attributes;
    }
    /**
     * @param Entity[] $parents
     * @return array
     */
    protected function getFormattedParentAttributesEditForm($parents)
    {
        $attributes = [];
        foreach ($parents as $parent) {
            /** @var Attribute $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->setType('dropdown');
            $attribute->setCode($parent->getNameSingular().'_id');
            $attribute->setLabel($parent->getLabelSingular(true));
            $attribute->setForcedSourceModel($parent->getNameSingular().'SourceModel');
            $attribute->setEntity($this);
            $attributes[] = $attribute;
        }
        return $attributes;
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasRelationType($type)
    {
        return count($this->getRelatedEntities($type)) > 0;
    }

    /**
     * @return bool
     */
    public function getHasTreeSibling()
    {
        $siblings = $this->getRelatedEntities(SiblingRelation::RELATION_TYPE_SIBLING);
        foreach ($siblings as $sibling) {
            if ($sibling->getIsTree()) {
                return true;
            }
        }
        return false;

    }

    /**
     * @return bool
     */
    public function getHasNonTreeSibling()
    {
        $siblings = $this->getRelatedEntities(SiblingRelation::RELATION_TYPE_SIBLING);
        foreach ($siblings as $sibling) {
            if (!$sibling->getIsTree()) {
                return true;
            }
        }
        return false;

    }

    /**
     * @return string
     */
    public function getSiblingTabParent()
    {
        if ($this->getIsTree()) {
            return 'Magento\Backend\Block\Widget\Form\Generic';
        }
        return 'Magento\Backend\Block\Widget\Grid\Extended';
    }

    /**
     * @return string
     */
    public function getSiblingTabParentAlias()
    {
        if ($this->getIsTree()) {
            return 'GenericForm';
        }
        return 'ExtendedGrid';
    }

    /**
     * @return string
     */
    public function getQuickSaveAttributes()
    {
        $lines = [];
        $prefix = 'new_'.$this->getNameSingular().'_';
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getRequired() || $attribute->getIsName()) {
                $lines[] = $attribute->getCode().' : '. "$('#".$prefix.$attribute->getCode()."').val()";
            }
        }
        $padd = $this->getPadding(9);
        return $padd.implode(','.$this->getEol().$padd, $lines);
    }

    /**
     * @return string
     */
    public function getClearAttributes()
    {
        $prefix = 'new_'.$this->getNameSingular().'_';
        $lines = ['#'.$prefix.'parent-suggest'];
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getRequired() || $attribute->getIsName()) {
                $lines[] = '#'.$prefix.$attribute->getCode();
            }
        }
        return implode(', ', $lines);
    }

    /**
     * @return string
     */
    public function getFullTextFields()
    {
        $fields = [];
        $fields[$this->getNameAttribute()->getCode()] = 1;
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getFullText()) {
                $fields[$attribute->getCode()] = 1;
            }
        }
        return "'".implode("','", array_keys($fields))."'";
    }
}
