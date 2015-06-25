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
use Umc\Base\Model\Core\EntityFactory;
use Umc\Base\Model\Core\Relation\Type\SiblingRelation;
use Umc\Base\Model\Core\SettingsFactory;

/**
 * @method string getVersion()
 * @method string getMenuText()
 * @method int getSortOrder()
 * @method string getMenuParent()
 * @method string getUnderscore()
 */
class Module extends AbstractModel implements ModelInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_module';

    /**
     * @var RelationFactory
     */
    protected $relationFactory;
    /**
     * settings factory
     *
     * @var SettingsFactory
     */
    protected $settingsFactory;

    /**
     * entity factory
     *
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * attribute factory
     *
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * settings reference
     *
     * @var Settings
     */
    protected $settings;

    /**
     * associated entities
     *
     * @var Entity[]
     */
    protected $entities = [];

    /**
     * @var array
     */
    protected $entityFlags = [];

    /**
     * @var Relation[]
     */
    protected $relations = [];

    /**
     * @param RelationFactory $relationFactory
     * @param SettingsFactory $settingsFactory
     * @param EntityFactory $entityFactory
     * @param AttributeFactory $attributeFactory
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        RelationFactory $relationFactory,
        SettingsFactory $settingsFactory,
        EntityFactory $entityFactory,
        AttributeFactory $attributeFactory,
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        array $data = []
    )
    {
        $this->relationFactory  = $relationFactory;
        $this->settingsFactory  = $settingsFactory;
        $this->entityFactory    = $entityFactory;
        $this->attributeFactory = $attributeFactory;
        parent::__construct($eventManager, $saveAttributesConfig, $formConfig, $restrictionConfig, $escaper, $data);
    }

    /**
     * get module namespace
     *
     * @param bool $toLower
     * @return mixed|string
     */
    public function getNamespace($toLower = false)
    {
        $namespace = $this->getData('namespace');
        if ($toLower) {
            $namespace = strtolower($namespace);
        }
        return $namespace;
    }

    /**
     * get module name
     *
     * @param bool $toLower
     * @return string
     */
    public function getModuleName($toLower = false)
    {
        $moduleName = $this->getData('module_name');
        if ($toLower) {
            $moduleName = strtolower($moduleName);
        }
        return $moduleName;
    }

    /**
     * get extension name
     *
     * @return string
     */
    public function getExtensionName()
    {
        return $this->getNamespace().'_'.$this->getModuleName();
    }

    /**
     * set the module settings
     *
     * @param Settings $settings
     * @return $this
     */
    public function setSettings(Settings $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * get module settings
     *
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
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
    public function toXml(
        array $arrAttributes = [],
        $rootName = null,
        $addOpenTag = false,
        $addCdata = false
    )
    {
        $xml = '';
        if ($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if ($rootName !== false) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        $xml .= parent::toXml($arrAttributes, '', false, $addCdata);
        if ($this->getSettings())
        {
            $xml .= $this->getSettings()->toXml();
        }
        if (count($this->getEntities()))  {
            $entitiesTag = 'entities';
            $xml .= '<'.$entitiesTag.'>';
            foreach ($this->getEntities() as $entity) {
                $xml .= $entity->toXml();
            }
            $xml .= '</'.$entitiesTag.'>';
        }
        $relationsTag = 'relations';
        $xml .= '<'.$relationsTag.'>';
        foreach ($this->getRelations() as $relation) {
            $xml .= $relation->toXml();
        }
        $xml .= '</'.$relationsTag.'>';
        if ($rootName != false) {
            $xml.= '</'.$rootName.'>'."\n";
        }
        return $xml;
    }

    /**
     * validate module
     *
     * @return array
     */
    public function validate()
    {
        $errors = parent::validate();
        $errors = array_merge($errors, $this->getSettings()->validate());
        if (count($this->getEntities()) == 0 ) {
            $errors[''][] = __('Each module must contain at least one entity.');
        }
        foreach ($this->getEntities() as $entity) {
            $entityErrors = $entity->validate();
            $errors = array_merge($errors, $entityErrors);
        }
        return $errors;
    }

    /**
     * get error key
     *
     * @param $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return 'module'.$field;
    }

    /**
     * init module from data
     *
     * @param array $data
     * @return $this
     */
    public function initFromData(array $data)
    {
        if (isset($data[$this->getEntityCode()])) {
            $this->addData($data[$this->getEntityCode()]);
        }
        $settings = $this->settingsFactory->create();
        if (isset($data[$settings->getEntityCode()])) {
            $settings->setData($data[$settings->getEntityCode()]);
            $this->setSettings($settings);
        }
        $entitiesByIndex = [];
        if (isset($data['entity'])) {
            $entities = $data['entity'];
            if (is_array($entities)) {
                foreach ($entities as $key => $entityData) {
                    if (!$entityData) {
                        continue;
                    }
                    /** @var \Umc\Base\Model\Core\Entity $entity */
                    $entity = $this->entityFactory->create();
                    if (isset($entityData['attributes']) && is_array($entityData['attributes'])) {
                        if (isset($entityData['attributes']['is_name'])) {
                            $isName = $entityData['attributes']['is_name'];
                            unset($entityData['attributes']['is_name']);
                            if (isset($entityData['attributes'][$isName])) {
                                $entityData['attributes'][$isName]['is_name'] = 1;
                            }
                        }
                        foreach ($entityData['attributes'] as $attrKey => $attributeData) {
                            /** @var \Umc\Base\Model\Core\Attribute $attribute */
                            $attribute = $this->attributeFactory->create();
                            $attribute->addData($attributeData);
                            $attribute->setIndex($attrKey);
                            $entity->addAttribute($attribute);
                        }
                    }
                    unset($data['attribute']);
                    $entity->addData($entityData);
                    $entity->setIndex($key);
                    $this->addEntity($entity);
                    $entitiesByIndex[$key] = $entity;
                }
            }
        }
        if (isset($data['relation'])) {
            foreach ($data['relation'] as $index => $values) {
                foreach ($values as $jndex => $type) {
                    if (isset($entitiesByIndex[$index]) && isset($entitiesByIndex[$jndex])) {
                        /** @var \Umc\Base\Model\Core\Relation $relation */
                        $relation = $this->relationFactory->create();
                        $relation->setEntities($entitiesByIndex[$index], $entitiesByIndex[$jndex], $type);
                        $this->addRelation($relation);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * add entity
     *
     * @param Entity $entity
     * @return $this
     */
    public function addEntity(Entity $entity)
    {
        $this->entityFlags = [];
        $entity->setData('sort_order', (count($this->entities) + 1) * 10);
        $entity->setModule($this);
        $this->entities[] = $entity;
        return $this;
    }

    /**
     * get entities
     *
     * @return Entity[]
     */
    public function getEntities()
    {
        return $this->entities;
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
                '{{Namespace}}'         => $this->getNamespace(),
                '{{Module}}'            => $this->getModuleName(),
                '{{version}}'           => $this->getVersion(),
                '{{sequence}}'          => $this->getSequenceAsString(),
                '{{module}}'            => $this->getModuleName(true),
                '{{menu_text}}'         => $this->getMenuText(),
                '{{menu_sort_order}}'   => $this->getSortOrder(),
                '{{namespace}}'         => $this->getNamespace(true),
                '{{menu_parent_value}}' => $this->getParentMenuValue(),
                '{{requireJsDialogs}}'  => $this->getRequireJsDialogs(),
            ];
            $this->placeholders = array_merge($this->placeholders, $this->getSettings()->getPlaceholders());
        }
        return $this->placeholders;
    }

    /**
     * get module sequence as string
     *
     * @return string
     */
    public function getSequenceAsString()
    {
        return $this->formatDependency('sequence', $this->getSequence());
    }

    /**
     * get module sequence
     *
     * @return array
     */
    public function getSequence()
    {
        return [
            'Magento_Backend' => 10
        ];
    }

    /**
     * format dependencies
     *
     * @param $tag
     * @param $dependencies
     * @return string
     */
    protected function formatDependency($tag, $dependencies)
    {
        asort($dependencies);
        $content = '';
        if (count($dependencies)) {
            $content .= $this->getEol();
            $content .= $this->getPadding(2).'<'.$tag.'>'.$this->getEol();
            foreach ($dependencies as $key => $position) {
                $content .= $this->getPadding(3).'<module name="'.$key.'" />'.$this->getEol();
            }
            $content .= $this->getPadding(2).'</'.$tag.'>';
        }
        return $content;
    }

    /**
     * get parent menu value
     *
     * @return string
     */
    public function getParentMenuValue()
    {
        if ($parent = $this->getMenuParent()) {
            return ' parent="'.$parent.'"';
        }
        return '';
    }

    /**
     * get any flag set on the entities
     *
     * @param $flag
     * @return mixed
     */
    public function getEntityFlag($flag)
    {
        if (!isset($this->entityFlags[$flag])) {
            $this->entityFlags[$flag] = false;
            foreach ($this->getEntities() as $entity) {
                if ($entity->getDataUsingMethod($flag)) {
                    $this->entityFlags[$flag] = true;
                    break;
                }
            }
        }
        return $this->entityFlags[$flag];
    }

    /**
     * __call magic method
     * handle getEntityFlag and getHasAttributeType calls
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, strlen('getEntityFlag')) == 'getEntityFlag') {
            $key = $this->_underscore(substr($method, strlen('getEntityFlag')));
            return $this->getEntityFlag($key);
        }
        if (substr($method, 0, strlen('getHasAttributeType')) == 'getHasAttributeType') {
            $key = $this->_underscore(substr($method, 0, 3));
            return $this->getEntityFlag($key);
        }
        return parent::__call($method, $args);
    }

    /**
     * check if there is at least a flat entity with upload
     *
     * @return bool
     */
    public function getHasFlatUpload()
    {
        foreach ($this->getEntities() as $entity) {
            if ($entity->getType() == 'flat' &&
                ($entity->getHasAttributeType('file') || $entity->getHasAttributeType('image'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * get child models (entities)
     *
     * @return AbstractModel[]
     */
    public function getChildModels()
    {
        return $this->getEntities();
    }

    /**
     * get grand child models (attribtues)
     *
     * @return AbstractModel[]
     */
    public function getGrandChildModels()
    {
        $models = [];
        foreach ($this->getEntities() as $entity) {
            $models = array_merge($models, $entity->getAttributes());
        }
        return $models;
    }

    /**
     * @return string
     */
    public function getUninstallScript()
    {
        return implode("\n", $this->getUninstallLines());
    }

    /**
     * @return array
     */
    public function getUninstallLines()
    {
        $lines = [];
        foreach ($this->getRelations() as $relation) {
            $lines = array_merge($lines, $relation->getUninstallLines());
        }
        foreach ($this->getEntities() as $entity) {
            $lines = array_merge($lines, $entity->getUninstallLines());
        }
        $lines[] = "DELETE FROM `setup_module` WHERE `module` = '".$this->getNamespace().'_'.$this->getModuleName()."';";
        return $lines;
    }

    /**
     * @return string
     */
    public function getNameAttributes()
    {
        $names = [];
        foreach ($this->getEntities() as $entity) {
            $names[$entity->getIndex()] = $entity->getNameAttribute()->getIndex();
        }
        return json_encode($names);
    }
    /**
     * add an entity relation
     *
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(Relation $relation)
    {
        $relation->setModule($this);
        $this->relations[] = $relation;
        return $this;
    }

    /**
     * get relations
     *
     * @return Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }
    /**
     * get relations as json
     *
     * @return string
     */
    public function getRelationsAsJson()
    {
        $json = [];
        $relations = $this->getRelations();
        foreach ($relations as $relation) {
            $reversed = $relation->getReversed();
            $relation->setReversed(false);
            $entities = $relation->getEntities();
            $json[$entities[0]->getIndex().'_'.$entities[1]->getIndex()] = $relation->getType();
            $relation->setReversed($reversed);
        }
        return json_encode($json);
    }

    /**
     * @return bool
     */
    public function getHasTreeRelatedEntities()
    {
        if (!$this->hasData('has_tree_related_entities')) {
            $this->setData('has_tree_related_entities', false);
            foreach ($this->getEntities() as $entity) {
                if ($entity->getIsTree() && $entity->hasRelationType(SiblingRelation::RELATION_TYPE_SIBLING)) {
                    $this->setData('has_tree_related_entities', true);
                    break;
                }
            }
        }
        return $this->getData('has_tree_related_entities');
    }

    /**
     * @return string
     */
    public function getRequireJsDialogs()
    {
        $config = [];
        foreach ($this->getEntities() as $entity) {
            if ($entity->getIsTree() && $entity->hasRelationType(SiblingRelation::RELATION_TYPE_SIBLING)) {
                $config[] = $this->getPadding(3).
                    "new".
                    $entity->getNameSingular(true)."Dialog: '".
                    $this->getNamespace().'_'.
                    $this->getModuleName()."/".
                    $entity->getNameSingular()."/new-".
                    $entity->getNameSingular()."-dialog'";
                $config[] = $this->getPadding(3).
                    $entity->getNameSingular()."Form: '".
                    $this->getNamespace().'_'.
                    $this->getModuleName()."/".
                    $entity->getNameSingular()."/form'";
            }
        }
        return implode(','.$this->getEol(), $config);
    }
}
