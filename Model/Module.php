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
use Magento\Framework\Composer\ComposerInformation;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\FactoryInterface;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;

class Module extends AbstractModel implements ModuleInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = self::ENTITY_CODE;

    /**
     * associated entities
     *
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * @var RelationInterface[]
     */
    protected $relations = [];

    /**
     * @var array
     */
    protected $entityFlags = [];

    /**
     * @var array
     */
    protected $sequenceDependencies;

    /**
     * @var array
     */
    protected $composerDependencies;

    /**
     * @var ComposerInformation
     */
    protected $composerInformation;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var string
     */
    protected $processedComposerDependencies;

    /**
     * @var ModelInterfaceFactory[]
     */
    protected $factories;

    /**
     * Module constructor.
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param ComposerInformation $composerInformation
     * @param Composer $composer
     * @param array $factories
     * @param array $sequenceDependencies
     * @param array $composerDependencies
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        ComposerInformation $composerInformation,
        Composer $composer,
        array $factories = [],
        array $sequenceDependencies = [],
        array $composerDependencies = [],
        array $data = []
    ) {
        $this->factories            = $factories;
        $this->composerInformation  = $composerInformation;
        $this->composer             = $composer;
        $this->sequenceDependencies = $sequenceDependencies;
        $this->composerDependencies = $composerDependencies;
        parent::__construct($saveAttributesConfig, $formConfig, $escaper, $data);
    }

    /**
     * get module namespace
     *
     * @param bool $toLower
     * @return mixed|string
     */
    public function getNamespace($toLower = false)
    {
        $namespace = $this->getData(self::NAMESPACE_FIELD);
        if ($toLower) {
            $namespace = strtolower($namespace);
        }
        return $namespace;
    }

    /**
     * @param string $namespace
     * @return ModuleInterface|Module
     */
    public function setNamespace($namespace)
    {
        return $this->setData(self::NAMESPACE_FIELD, $namespace);
    }

    /**
     * get module name
     *
     * @param bool $toLower
     * @return string
     */
    public function getModuleName($toLower = false)
    {
        $moduleName = $this->getData(self::MODULE_NAME);
        if ($toLower) {
            $moduleName = strtolower($moduleName);
        }
        return $moduleName;
    }

    /**
     * @param string $moduleName
     * @return ModuleInterface|Module
     */
    public function setModuleName($moduleName)
    {
        return $this->setData(self::MODULE_NAME, $moduleName);
    }

    /**
     * get extension name
     *
     * @return string
     */
    public function getExtensionName()
    {
        if ($this->getNamespace() && $this->getModuleName()) {
            return $this->getNamespace() . '_' . $this->getModuleName();
        }
        return '';
    }

    /**
     * @param string $menuParent
     * @return ModuleInterface
     */
    public function setMenuParent($menuParent)
    {
        return $this->setData(self::MENU_PARENT, $menuParent);
    }

    /**
     * @return string
     */
    public function getMenuParent()
    {
        return $this->getData(self::MENU_PARENT);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->getData(self::VERSION);
    }

    /**
     * @param string $version
     * @return ModuleInterface|Module
     */
    public function setVersion($version)
    {
        return $this->setData(self::VERSION, $version);
    }

    /**
     * @return string
     */
    public function getMenuText()
    {
        return $this->getData(self::MENU_TEXT);
    }

    /**
     * @param string $menuText
     * @return ModuleInterface|Module
     */
    public function setMenuText($menuText)
    {
        return $this->setData(self::MENU_TEXT, $menuText);
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     * @return ModuleInterface|Module
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @return string
     */
    public function getCreateComposer()
    {
        return $this->getData(self::CREATE_COMPOSER);
    }

    /**
     * @param string $createComposer
     * @return ModuleInterface|Module
     */
    public function setCreateComposer($createComposer)
    {
        return $this->setData(self::CREATE_COMPOSER, $createComposer);
    }

    /**
     * @return string
     */
    public function getComposerName()
    {
        return $this->getData(self::COMPOSER_NAME);
    }

    /**
     * @param string $composerName
     * @return ModuleInterface|Module
     */
    public function setComposerName($composerName)
    {
        return $this->setData(self::COMPOSER_NAME, $composerName);
    }

    /**
     * @return string
     */
    public function getComposerVersion()
    {
        return $this->getData(self::COMPOSER_VERSION);
    }

    /**
     * @param string $composerVersion
     * @return ModuleInterface
     */
    public function setComposerVersion($composerVersion)
    {
        return $this->setData(self::COMPOSER_VERSION, $composerVersion);
    }

    /**
     * @return string
     */
    public function getComposerDescription()
    {
        return $this->getData(self::COMPOSER_DESCRIPTION);
    }

    /**
     * @param string $composerDescription
     * @return ModuleInterface|Module
     */
    public function setComposerDescription($composerDescription)
    {
        return $this->setData(self::COMPOSER_DESCRIPTION, $composerDescription);
    }

    /**
     * @return string
     */
    public function getComposerLicense()
    {
        return $this->getData(self::COMPOSER_LICENSE);
    }

    /**
     * @param string $composerLicense
     * @return ModuleInterface|Module
     */
    public function setComposerLicense($composerLicense)
    {
        return $this->setData(self::COMPOSER_LICENSE, $composerLicense);
    }

    /**
     * @return string
     */
    public function getCreateLicense()
    {
        return $this->getData(self::CREATE_LICENSE);
    }

    /**
     * @param string $createLicense
     * @return ModuleInterface|Module
     */
    public function setCreateLicense($createLicense)
    {
        return $this->setData(self::CREATE_LICENSE, $createLicense);
    }

    /**
     * @return string
     */
    public function getCreateReadme()
    {
        return $this->getData(self::CREATE_README);
    }

    /**
     * @param string $createReadme
     * @return ModuleInterface|Module
     */
    public function setCreateReadme($createReadme)
    {
        return $this->setData(self::CREATE_README, $createReadme);
    }

    /**
     * @return string
     */
    public function getReadme()
    {
        return $this->getData(self::README);
    }

    /**
     * @param string $readme
     * @return ModuleInterface|Module
     */
    public function setReadme($readme)
    {
        return $this->setData(self::README, $readme);
    }

    /**
     * @return int
     */
    public function getUiVersion()
    {
        return $this->getData(self::UI_VERSION);
    }

    /**
     * @param int $uiVersion
     * @return ModuleInterface
     */
    public function setUiVersion($uiVersion)
    {
        return $this->setData(self::UI_VERSION, $uiVersion);
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
    ) {
        $xml = '';
        if ($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if ($rootName === null) {
            $rootName = $this->getEntityCode();
        }
        if ($rootName !== false) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        $xml .= parent::toXml($arrAttributes, '', false, $addCdata);
        if (count($this->getEntities())) {
            $entitiesTag = 'entities';
            $xml .= '<'.$entitiesTag.'>';
            foreach ($this->getEntities() as $entity) {
                $xml .= $entity->toXml();
            }
            $xml .= '</'.$entitiesTag.'>';
        }
        if (count($this->getRelations())) {
            $relationsTag = 'relations';
            $xml .= '<' . $relationsTag . '>';
            foreach ($this->getRelations() as $relation) {
                $xml .= $relation->toXml();
            }
            $xml .= '</' . $relationsTag . '>';
        }
        if ($rootName != false) {
            $xml.= '</'.$rootName.'>'."\n";
        }
        return $xml;
    }

    /**
     * @param string $key
     * @return ModelInterfaceFactory
     * @throws \Exception
     */
    protected function getFactory($key)
    {
        if (!isset($this->factories[$key])) {
            throw new \Exception("Factory not found for Key: {$key}");
        }
        $factory = $this->factories[$key];
        if (!($factory instanceof ModelInterfaceFactory)) {
            throw new \Exception("Factory for Key: {$key} must implement ". ModelInterfaceFactory::class);
        }
        return $factory;
    }

    /**
     * get error key
     *
     * @param string $field
     * @return string
     */
    public function getValidationErrorKey($field)
    {
        return self::ENTITY_CODE.$field;
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
        $entitiesByIndex = [];
        if (isset($data['entity'])) {
            $entities = $data['entity'];
            if (is_array($entities)) {
                foreach ($entities as $key => $entityData) {
                    if (!$entityData) {
                        continue;
                    }
                    /** @var \Umc\Base\Api\Data\EntityInterface $entity */
                    $entity = $this->getFactory(FactoryInterface::ENTITY_FACTORY_KEY)->create();
                    foreach ($this->initAttributes($entityData) as $attribute) {
                        $entity->addAttribute($attribute);
                    }
                    unset($entityData['attribute']);
                    $entity->addData($entityData);
                    $entity->setIndex($key);
                    $this->addEntity($entity);
                    $entitiesByIndex[$key] = $entity;
                }
            }
        }
        if (isset($data['relation'])) {
            foreach ($data['relation'] as $index => $values) {
                /** @var \Umc\Base\Api\Data\RelationInterface  $relation */
                $relation = $this->getFactory(FactoryInterface::RELATION_FACTORY_KEY)->create();
                $relation->addData($values);
                $relation->setEntities(
                    $entitiesByIndex[$values['entity_one']],
                    $entitiesByIndex[$values['entity_two']]
                );
                $relation->setType($values['type']);
                $relation->setIndex($index);
                $this->addRelation($relation);
            }
        }
        return $this;
    }

    /**
     * @param array $entityData
     * @return AttributeInterface[]
     */
    protected function initAttributes($entityData)
    {
        $attributes = [];
        if (isset($entityData['attributes']) && is_array($entityData['attributes'])) {
            if (isset($entityData['attributes']['is_name'])) {
                $isName = $entityData['attributes']['is_name'];
                unset($entityData['attributes']['is_name']);
                if (isset($entityData['attributes'][$isName])) {
                    $entityData['attributes'][$isName]['is_name'] = 1;
                }
            }
            foreach ($entityData['attributes'] as $attrKey => $attributeData) {
                /** @var \Umc\Base\Api\Data\AttributeInterface $attribute */
                $attribute = $this->getFactory(FactoryInterface::ATTRIBUTE_FACTORY_KEY)->create();
                $attribute->addData($attributeData);
                $attribute->setIndex($attrKey);
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }

    /**
     * @param EntityInterface $entity
     * @return $this
     * @throws \Exception
     */
    public function addEntity(EntityInterface $entity)
    {
        if (isset($this->entities[$entity->getNameSingular()])) {
            throw new \Exception("You cannot have 2 entities with the code ".$entity->getNameSingular());
        }
        $this->entityFlags = [];
        $entity->setData('sort_order', (count($this->entities) + 1) * 10);
        $entity->setModule($this);
        $this->entities[$entity->getNameSingular()] = $entity;
        return $this;
    }

    /**
     * get entities
     *
     * @return EntityInterface[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param RelationInterface $relation
     * @return $this
     * @throws \Exception
     */
    public function addRelation(RelationInterface $relation)
    {
        $key = $relation->getUniqueKey();
        if (isset($this->relations[$key])) {
            throw new \Exception("You cannot have 2 relations between the same entities with the same code");
        }
        $relation->setModule($this);
        $this->relations[$key] = $relation;
        return $this;
    }

    /**
     * @return RelationInterface[]
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
            $json[$relation->getIndex()] = [
                'entity_one' => $entities[0]->getIndex(),
                'entity_two' => $entities[1]->getIndex()
            ];
            $relation->setReversed($reversed);
        }
        return json_encode($json);
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
                '{{Namespace}}'                     => $this->getNamespace(),
                '{{Module}}'                        => $this->getModuleName(),
                '{{version}}'                       => $this->getVersion(),
                '{{sequence}}'                      => $this->getSequenceAsString(),
                '{{module}}'                        => $this->getModuleName(true),
                '{{menuText}}'                      => $this->getMenuText(),
                '{{menuSortOrder}}'                 => $this->getSortOrder(),
                '{{namespace}}'                     => $this->getNamespace(true),
                '{{menuParentValue}}'               => $this->getParentMenuValue(),
                '{{composerVersion}}'               => $this->getComposerVersion(),
                '{{composerNameEscaped}}'           => $this->escaper->escapeHtml($this->getComposerName()),
                '{{composerDescriptionEscaped}}'    => $this->escaper->escapeHtml($this->getComposerDescription()),
                '{{composerLicenseEscaped}}'        => $this->escaper->escapeHtml($this->getComposerLicense()),
                '{{license}}'                       => $this->getLicenseProcessed(),
                '{{readme}}'                        => $this->getReadmeProcessed(),
                '{{composerDependencies}}'          => $this->getComposerDependencies(),
                '{{_}}'                             => $this->getUnderscoreValue(),
                '{{Y}}'                             => date('Y'),
            ];
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
     * @return array
     */
    public function getSequence()
    {
        return $this->sequenceDependencies;
    }

    /**
     * format dependencies
     *
     * @param string $tag
     * @param array $dependencies
     * @return string
     */
    protected function formatDependency($tag, $dependencies)
    {
        asort($dependencies);
        $content = '';
        if (count($dependencies)) {
            $content .= '<'.$tag.'>';
            foreach ($dependencies as $key => $position) {
                $content .= '<module name="'.$key.'" />';
            }
            $content .= '</'.$tag.'>';
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
     * @param string $flag
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
     * @return ModelInterface[]
     */
    public function getChildModels()
    {
        return $this->getEntities();
    }

    /**
     * get grand child models (attributes)
     *
     * @return ModelInterface[]
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
    public function getNameAttributes()
    {
        $names = [];
        foreach ($this->getEntities() as $entity) {
            $names[$entity->getIndex()] = $entity->getNameAttribute()->getIndex();
        }
        return json_encode($names);
    }

    /**
     * get prefix for protected methods
     *
     * @return string
     */
    public function getUnderscoreValue()
    {
        if ($this->getUnderscore()) {
            return '_';
        }
        return '';
    }

    /**
     * @return bool
     */
    public function getUnderscore()
    {
        return $this->getData(self::UNDERSCORE);
    }

    /**
     * @return bool
     */
    public function getQualified()
    {
        return $this->getData(self::QUALIFIED);
    }

    /**
     * @param bool $qualified
     * @return ModuleInterface|Module
     */
    public function setQualified($qualified)
    {
        return $this->setData(self::QUALIFIED, $qualified);
    }

    /**
     * @return bool
     */
    public function getAnnotation()
    {
        return $this->getData(self::ANNOTATION);
    }

    /**
     * @param bool $annotation
     * @return ModuleInterface|Module
     */
    public function setAnnotation($annotation)
    {
        return $this->setData(self::ANNOTATION, $annotation);
    }

    /**
     * @return string
     */
    public function getLicense()
    {
        return $this->getData(self::LICENSE);
    }

    /**
     * @param string $license
     * @return ModuleInterface|Module
     */
    public function setLicense($license)
    {
        return $this->setData(self::LICENSE, $license);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function getProcessedFieldValue($value)
    {
        $replace = [
            '{{Namespace}}' => $this->getNamespace(),
            '{{Module}}' => $this->getModuleName(),
            '{{Y}}' => date('Y')
        ];
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $value
        );
    }

    /**
     * @return mixed
     */
    protected function getReadmeProcessed()
    {
        return $this->getProcessedFieldValue($this->getReadme());
    }

    /**
     * @return mixed
     */
    protected function getLicenseProcessed()
    {
        return $this->getProcessedFieldValue($this->getLicense());
    }

    /**
     * @return string
     */
    protected function getComposerDependencies()
    {
        if ($this->processedComposerDependencies === null) {
            $dependencies = [];
            $dependencies[] = '"php": "' . $this->composerInformation->getRequiredPhpVersion() . '"';
            $installed = $this->composer->getVersions();
            foreach ($this->composerDependencies as $key => $dependency) {
                if (!isset($dependency['name'])) {
                    continue;
                }
                $name = $dependency['name'];
                if (!isset($dependency['version'])) {
                    if (!isset($installed[$name])) {
                        continue;
                    }
                    $dependency['version'] = $installed[$name];
                }
                $dependencies[] = '"'.$name.'": "'.$dependency['version'].'"';
            }
            $glue = ','.$this->getEol().$this->getPadding();
            $this->processedComposerDependencies = $this->getPadding().implode($glue, $dependencies);
        }
        return $this->processedComposerDependencies;
    }
}
