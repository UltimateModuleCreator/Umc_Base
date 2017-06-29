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

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Framework\Escaper;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\AttributeInterfaceFactory;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Attribute\Type\Dropdown;
use Umc\Base\Model\Attribute\Type\File;
use Umc\Base\Model\Attribute\Type\Image;
use Umc\Base\Model\Attribute\Type\Integer;
use Umc\Base\Model\Attribute\Type\Text;
use Umc\Base\Model\Entity\Type\Factory as TypeFactory;
use Umc\Base\Api\Data\Entity\TypeInterface;
use Umc\Base\Model\Relation\Type\ParentRelation;
use Umc\Base\Source\Grid;

/**
 * @method int getSortOrder()
 */
class Entity extends AbstractModel implements EntityInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = self::ENTITY_CODE;

    /**
     * parent module
     *
     * @var ModuleInterface
     */
    protected $module;

    /**
     * attributes
     *
     * @var AttributeInterface[]
     */
    protected $attributes = [];

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
     * @var AttributeInterfaceFactory
     */
    protected $attributeFactory;

    /**
     * attribute that acts as name
     *
     * @var AttributeInterface|null
     */
    protected $nameAttribute;

    /**
     * @var array
     */
    protected $parentAttributes = [];

    /**
     * @var array
     */
    protected $systemAttributes = [];

    /**
     * Entity constructor.
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param TypeFactory $typeFactory
     * @param AttributeInterfaceFactory $attributeFactory
     * @param array $systemAttributes
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        TypeFactory $typeFactory,
        AttributeInterfaceFactory $attributeFactory,
        array $systemAttributes,
        array $data = []
    ) {
        $this->typeFactory      = $typeFactory;
        $this->attributeFactory = $attributeFactory;
        $this->systemAttributes = $systemAttributes;
        parent::__construct($saveAttributesConfig, $formConfig, $escaper, $data);
    }

    /**
     * set module reference
     *
     * @param ModuleInterface $module
     * @return $this
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * get module reference
     *
     * @return ModuleInterface
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param bool $isTree
     * @return EntityInterface|Entity
     */
    public function setIsTree($isTree)
    {
        return $this->setData(self::IS_TREE, $isTree);
    }

    /**
     * @return bool
     */
    public function getIsTree()
    {
        return false; //tree behavior not implemented yet
    }

    /**
     * get entity label singular
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getLabelSingular($ucFirst = false)
    {
        $label = $this->getData(self::LABEL_SINGULAR);
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
        $label = $this->getData(self::LABEL_PLURAL);
        if ($ucFirst) {
            $label = ucfirst($label);
        }
        return $label;
    }

    /**
     * add attribute
     *
     * @param AttributeInterface $attribute
     * @return $this
     */
    public function addAttribute(AttributeInterface $attribute)
    {
        $attribute->setEntity($this);
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * get entity attributes
     *
     * @return AttributeInterface[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $labelSingular
     * @return EntityInterface|Entity
     */
    public function setLabelSingular($labelSingular)
    {
        return $this->setData(self::LABEL_SINGULAR, $labelSingular);
    }

    /**
     * @param string $labelPlural
     * @return EntityInterface|Entity
     */
    public function setLabelPlural($labelPlural)
    {
        return $this->setData(self::LABEL_PLURAL, $labelPlural);
    }

    /**
     * @param string $nameSingular
     * @return EntityInterface|Entity
     */
    public function setNameSingular($nameSingular)
    {
        return $this->setData(self::NAME_SINGULAR, $nameSingular);
    }

    /**
     * @param string $namePlural
     * @return EntityInterface|Entity
     */
    public function setNamePlural($namePlural)
    {
        return $this->setData(self::NAME_PLURAL, $namePlural);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return EntityInterface|Entity
     */
    public function setType($type)
    {
        $this->typeInstance = null;
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return string
     */
    public function getAddCreatedToGrid()
    {
        return $this->getData(self::ADD_CREATED_TO_GRID);
    }

    /**
     * @param string $addCreatedToGrid
     * @return EntityInterface|Entity
     */
    public function setAddCreatedToGrid($addCreatedToGrid)
    {
        return $this->setData(self::ADD_CREATED_TO_GRID, $addCreatedToGrid);
    }

    /**
     * @return string
     */
    public function getAddUpdatedToGrid()
    {
        return $this->getData(self::ADD_UPDATED_TO_GRID);
    }

    /**
     * @param string $addUpdatedToGrid
     * @return EntityInterface|Entity
     */
    public function setAddUpdatedToGrid($addUpdatedToGrid)
    {
        return $this->setData(self::ADD_UPDATED_TO_GRID, $addUpdatedToGrid);
    }

    /**
     * @return string
     */
    public function getInlineEdit()
    {
        return $this->getData(self::INLINE_EDIT);
    }

    /**
     * @param string $inlineEdit
     * @return EntityInterface|Entity
     */
    public function setInlineEdit($inlineEdit)
    {
        return $this->setData(self::INLINE_EDIT, $inlineEdit);
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->getData(self::SEARCH);
    }

    /**
     * @param string $search
     * @return EntityInterface|Entity
     */
    public function setSearch($search)
    {
        return $this->setData(self::SEARCH, $search);
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
    public function toXml(array $arrAttributes = [], $rootName = null, $addOpenTag = false, $addCdata = true)
    {
        $xml = '';
        if ($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if ($rootName === null) {
            $rootName = $this->getEntityCode();
        }
        if (!empty($rootName)) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        $xml .= parent::toXml($arrAttributes, '', false, $addCdata);
        $xml .= '<attributes>';
        foreach ($this->getAttributes() as $attribute) {
            $xml .= $attribute->toXml([], AttributeInterface::ENTITY_CODE, false, $addCdata);
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
        if ($this->typeInstance === null) {
            $this->typeInstance = $this->typeFactory->create($this);
        }
        return $this->typeInstance;
    }

    /**
     * get name attribute
     *
     * @return null|AttributeInterface
     */
    public function getNameAttribute()
    {
        if ($this->nameAttribute === null) {
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
     * @param string $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return 'entity_'.$this->getIndex().'_'.$field;
    }

    /**
     * check if a certain attribute type exists
     *
     * @param string $type
     * @return bool
     */
    public function getHasAttributeType($type)
    {
        return $this->getTypeInstance()->getHasAttributeType($type);
    }

    /**
     * @return bool
     */
    public function getHasUpload()
    {
        return $this->getTypeInstance()->getHasAttributeType(Image::NAME) ||
            $this->getTypeInstance()->getHasAttributeType(File::NAME);
    }

    /**
     * check if a certain attribute type exists
     *
     * @param string $type
     * @return bool
     */
    public function getHasAttributeTypeRequired($type)
    {
        return $this->getTypeInstance()->getHasAttributeTypeRequired($type);
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
        if (substr($method, 0, strlen('getHasAttributeTypeRequired')) == 'getHasAttributeTypeRequired') {
            $key = $this->_underscore(substr($method, strlen('getHasAttributeTypeRequired')));
            return $this->getHasAttributeTypeRequired($key);
        }
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
        if ($this->placeholders === null) {
            $this->placeholders = [
                '{{entity}}'                => $this->getNameSingular(),
                '{{Entity}}'                => $this->getNameSingular(true),
                '{{ENTITY}}'                => strtoupper($this->getNameSingular()),
                '{{EntityLabel}}'           => $this->getLabelSingular(true),
                '{{EntityLabelEscaped}}'    => $this->escaper->escapeHtmlAttr($this->getLabelSingular(true)),
                '{{EntitiesLabel}}'         => $this->getLabelPlural(true),
                '{{EntitiesLabelEscaped}}'  => $this->escaper->escapeHtmlAttr($this->getLabelPlural(true)),
                '{{entities}}'              => $this->getNamePlural(),
                '{{Entities}}'              => $this->getNamePlural(true),
                '{{sortOrder}}'             => $this->getSortOrder(),
                '{{nameAttributeCode}}'     => $this->getNameAttributeCode(),
                '{{NameAttributeCode}}'     => $this->getNameAttributeCode(true),
                '{{NameAttributeLabel}}'    => $this->getNameAttribute()->getLabel(),
                '{{NameAttributeLabelEscaped}}' => $this->escaper->escapeHtmlAttr(
                    $this->getNameAttribute()->getLabel()
                ),
                '{{nameAttributeType}}'     => $this->getNameAttribute()->getTypeInstance()->getAdminColumnType(),
                '{{dateAttributeCodes}}'    => $this->getDateAttributeCodes(),
                '{{columnsSetup}}'          => $this->getColumnsSetup(),
                '{{defaultAttributeValues}}'=> $this->getDefaultAttributeValues(),
                '{{isActiveTreeSuggest}}'   => $this->getIsActiveTreeSuggest(),
                '{{treeClass}}'             => $this->getTreeClass(),
                '{{AdminFormParentClass}}'  => $this->getAdminFormParentClass(),
                '{{quickSaveAttributes}}'   => $this->getQuickSaveAttributes(),
                '{{clearAttributes}}'       => $this->getClearAttributes(),
                '{{fullTextFields}}'        => $this->getFullTextFields(),
                '{{createdAtHidden}}'       => $this->getCreatedAtHidden(),
                '{{updatedAtHidden}}'       => $this->getUpdatedAtHidden(),
                '{{nameAttributePosition}}' => $this->getNameAttribute()->getPosition(),
                '{{nameAttributeAdditionalFormConfig}}' => $this->getNameAttribute()->getAdditionalFormConfig(),
                '{{SaveUploaders}}'         => $this->getSaveUploaders(),
                '{{nameAttributeAdditionalFormConfigV2}}' => $this->getNameAttribute()->getAdditionalFormConfigV2(),
                '{{updatedAtHiddenV2}}'     => $this->getUpdatedAtHiddenV2(),
                '{{createdAtHiddenV2}}'     => $this->getCreatedAtHiddenV2()
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
     * @return ModelInterface|ModuleInterface
     */
    public function getParent()
    {
        return $this->getModule();
    }

    /**
     * @param bool $ucFirst
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
        if (!count($attributes)) {
            return '[]';
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
        /** @var AttributeInterface[] $allAttributes */
        $allAttributes = array_merge(
            $this->getParentEntityFields(),
            $this->getAttributes(),
            $this->getSystemAttributes()
        );
        foreach ($allAttributes as $attribute) {
            $setup .= $attribute->getColumnSetup();
        }
        return $setup;
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
            if ($attribute->getData('default_value')) {
                $lines[] = $tab.$tab.'$values[\''.$attribute->getCode().'\'] = \''.
                    $this->escaper->escapeJs($attribute->getDefaultValueProcessed()).'\';';
            }
        }
        $value = implode($this->getEol(), $lines);
        return $value;
    }

    /**
     * get child models (attributes)
     *
     * @return AttributeInterface[]
     */
    public function getChildModels()
    {
        return $this->getAttributes();
    }

    /**
     * @return string
     */
    public function getIsActiveTreeSuggest()
    {
        return 'true';
    }

    /**
     * @return string
     */
    public function getTreeClass()
    {
        return 'active-category';
    }

    /**
     * @return AttributeInterface[]
     */
    public function getSystemAttributes()
    {
        $attributes = [];
        foreach ($this->systemAttributes as $systemAttribute) {
            $valid = true;
            if (isset($systemAttribute['condition']['method']) && isset($systemAttribute['condition']['value'])) {
                $method = $systemAttribute['condition']['method'];
                $value = $systemAttribute['condition']['value'];
                $valid = ($this->$method() == $value);
                unset($systemAttribute['condition']);
            }
            if ($valid) {
                $attribute = $this->attributeFactory->create();
                $attribute->setData($systemAttribute);
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * @return AttributeInterface[]
     */
    public function getParentEntityFields()
    {
        $attributes = [];
        foreach ($this->getModule()->getRelations() as $relation) {
            if ($relation->getType() == ParentRelation::RELATION_TYPE_PARENT) {
                $entities = $relation->getEntities();
                if ($entities[1]->getNameSingular() == $this->getNameSingular()) {
                    $attribute = $this->attributeFactory->create();
                    $relationCode = $relation->getCode();
                    $code = ($relationCode) ? $relationCode . '_' : '';
                    $code .= $entities[0]->getNameSingular().'_id';
                    $attribute->setCode($code);
                    $attribute->setLabel($relation->getTitle().' '. $entities[0]->getLabelSingular());
                    $attribute->setType(Integer::NAME);
                    $attribute->setEntity($this);
                    $attribute->setRequired($relation->getRequired());
                    $attributes[] = $attribute;
                }
            }
        }
        return $attributes;
    }

    /**
     * @return string
     */
    public function getAdminFormParentClass()
    {
        $module = $this->getModule();
        return (!$this->getIsTree())
            ? GenericForm::class
            : $module->getNamespace().'\\'.$module->getModuleName().
            '\Block\Adminhtml\\'.$this->getNameSingular(true).'\\Abstract'.$this->getNameSingular(true);
    }

    /**
     * @param EntityInterface[] $parents
     * @return array
     * @deprecated
     */
    protected function getFormattedParentAttributesEditForm($parents)
    {
        $attributes = [];
        foreach ($parents as $parent) {
            /** @var Attribute $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->setType(Dropdown::NAME);
            $attribute->setCode($parent->getNameSingular().'_id');
            $attribute->setLabel($parent->getLabelSingular(true));
            $attribute->setData('forced_source_model', $parent->getNameSingular().'SourceModel');
            $attribute->setEntity($this);
            $attributes[] = $attribute;
        }
        return $attributes;
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
        return "'".implode("', '", array_keys($fields))."'";
    }

    /**
     * @return string
     */
    public function getCreatedAtHidden()
    {
        if ($this->getAddCreatedToGrid() == Grid::HIDDEN) {
            return '<item name="visible" xsi:type="boolean">false</item>';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCreatedAtHiddenV2()
    {
        if ($this->getAddCreatedToGrid() == Grid::HIDDEN) {
            return '<visible>false</visible>';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUpdatedAtHidden()
    {
        if ($this->getAddUpdatedToGrid() == Grid::HIDDEN) {
            return '<item name="visible" xsi:type="boolean">false</item>';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUpdatedAtHiddenV2()
    {
        if ($this->getAddUpdatedToGrid() == Grid::HIDDEN) {
            return '<visible>false</visible>';
        }
        return '';
    }

    /**
     * @return int
     */
    public function getUiVersion()
    {
        return $this->getModule()->getUiVersion();
    }

    /**
     * @return string
     */
    public function getSaveUploaders()
    {
        $uploaders = '';
        $namespace = $this->getModule()->getNamespace();
        $module = $this->getModule()->getModuleName();
        $entity = $this->getNameSingular(true);
        if ($this->getHasAttributeType(Image::NAME)) {
            $uploaders .= '<item name="image" xsi:type="object">'.$namespace.$module.$entity.'ImageUploader</item>';
        }
        if ($this->getHasAttributeType(File::NAME)) {
            $uploaders .= '<item name="file" xsi:type="object">'.$namespace.$module.$entity.'FileUploader</item>';
        }
        return $uploaders;
    }
}
