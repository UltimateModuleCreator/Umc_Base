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
namespace Umc\Base\Model\Help;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Config\Help as HelpConfig;
use Umc\Base\Model\Config\Restriction as RestrictionConfig;
use Umc\Base\Model\Config\Source as SourceConfig;
use Umc\Base\Model\Core\Attribute;
use Umc\Base\Model\Core\Entity;
use Umc\Base\Model\Core\Module as CoreModule;
use Umc\Base\Model\Core\Settings;

class Module
{
    /**
     * source config instance
     *
     * @var SourceConfig
     */
    protected $sourceConfig;

    /**
     * Form config instance
     *
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * help config
     *
     * @var HelpConfig
     */
    protected $helpConfig;

    /**
     * restriction config instance
     *
     * @var RestrictionConfig
     */
    protected $restrictionConfig;

    /**
     * module list instance
     *
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * object manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * module instance
     *
     * @var CoreModule
     */
    protected $moduleInstance;

    /**
     * Settings instance
     *
     * @var Settings
     */
    protected $settingsInstance;

    /**
     * entity instance
     *
     * @var Entity
     */
    protected $entityInstance;

    /**
     * attribute instance
     *
     * @var Attribute
     */
    protected $attributeInstance;

    /**
     * code mappings
     *
     * @var array
     */
    protected $codeMapping = [];

    /**
     * constructor
     *
     * @param SourceConfig $sourceConfig
     * @param FormConfig $formConfig
     * @param HelpConfig $helpConfig
     * @param RestrictionConfig $restrictionConfig
     * @param ModuleListInterface $moduleList
     * @param ObjectManagerInterface $objectManager
     * @param CoreModule $moduleInstance
     * @param Settings $settingsInstance
     * @param Entity $entityInstance
     * @param Attribute $attributeInstance
     */
    public function __construct(
        SourceConfig $sourceConfig,
        FormConfig $formConfig,
        HelpConfig $helpConfig,
        RestrictionConfig $restrictionConfig,
        ModuleListInterface $moduleList,
        ObjectManagerInterface $objectManager,
        CoreModule $moduleInstance,
        Settings $settingsInstance,
        Entity $entityInstance,
        Attribute $attributeInstance
    )
    {
        $this->sourceConfig             = $sourceConfig;
        $this->formConfig               = $formConfig;
        $this->helpConfig               = $helpConfig;
        $this->restrictionConfig        = $restrictionConfig;
        $this->moduleList               = $moduleList;
        $this->objectManager            = $objectManager;
        $this->moduleInstance           = $moduleInstance;
        $this->entityInstance           = $entityInstance;
        $this->attributeInstance        = $attributeInstance;
        $this->settingsInstance         = $settingsInstance;
        $this->codeMapping['global']    = $moduleInstance->getEntityCode();
        $this->codeMapping['entity']    = $entityInstance->getEntityCode();
        $this->codeMapping['attribute'] = $attributeInstance->getEntityCode();
        $this->codeMapping['settings']  = $settingsInstance->getEntityCode();
    }

    /**
     * get available modules
     *
     * @return array
     */
    public function getUmcModules()
    {
        $modules = $this->helpConfig->getConfig('module', true, []);
        $installed = [];
        foreach ($modules as $module) {
            $moduleData = $this->moduleList->getOne($module['id']);
            if ($moduleData) {
                $version = $moduleData['setup_version'];
                if (isset($module['build'])) {
                    $version .= '-'.$module['build'];
                }
                $installed[] = [
                    'module' => $module['id'],
                    'version' => $version
                ];
            }
        }
        return $installed;
    }

    /**
     * get generated files
     *
     * @return array
     */
    public function getGeneratedFiles()
    {
        $files = array();
        $sourceFiles = $this->sourceConfig->getConfig('file');
        foreach ($sourceFiles as $file) {
            $file = $this->sourceConfig->preProcessFileConfig($file);
            $files[] = [
                'label'       => __($file['label']),
                'scope'       => $this->sourceConfig->getScopeLabel($file['scope']),
                'destination' => $file['destination'],
                'description' => __($file['description']),
                'condition'   => $this->getFileCondition($file)
            ];
        }
        return $files;
    }

    /**
     * get file conditions
     *
     * @param $fileConfig
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getFileCondition($fileConfig)
    {
        $dependGroupLabels = [];
        if (isset($fileConfig['depends'])) {
            $multipleConditions = false;
            foreach ($fileConfig['depends'] as $dependGroup) {
                $dependFieldsLabels = [];
                foreach ($dependGroup['depend'] as $fieldDepend) {
                    $allowedValues = [];
                    foreach ($fieldDepend['val'] as $value) {
                        $allowedValues[] = $this->getValueLabel(
                            $fieldDepend['id'],
                            $fileConfig['scope'],
                            $value['value']
                        );
                    }
                    if (count($allowedValues) > 0) {
                        $dependFieldsLabel = '<em>'.$this->getFieldLabel(
                            $fieldDepend['id'],
                            $fileConfig['scope']
                        ).'</em>';
                        if (count($allowedValues) == 1) {
                            $dependFieldsLabel .= __(' is ').'<u>'.$allowedValues[0].'</u>';
                        } else {
                            $multipleConditions = true;
                            $dependFieldsLabel .= __(' is one of ').'" <u>'.implode(', ', $allowedValues).' </u>"';
                        }
                        $dependFieldsLabels[] = $dependFieldsLabel;
                    }
                }
                $dependGroupLabels[] = implode(__(' AND '), $dependFieldsLabels);
            }
            if (count($dependGroupLabels) > 1) {
                $multipleConditions = true;
            }
            //TODO: use subclasses here
            switch ($fileConfig['scope']) {
                case 'entity':
                    $returnText = __('For each entity for which'). ' ';
                    if ($multipleConditions) {
                        $returnText .= __('the following conditions are met:');
                    } else {
                        $returnText .= __('the following condition is met:');
                    }
                    $prefix = '';
                    $suffix = '';
                    $implode = __(' OR ');
                    if (count($dependGroupLabels) > 1) {
                        $prefix = '( ';
                        $suffix = ' )';
                        $implode = $suffix.$implode.$prefix;
                    }
                    $returnText .= $prefix.' '.implode($implode, $dependGroupLabels).$suffix;
                    return $returnText;
                    break;
                case 'attribute':
                    $returnText = __('For each attribute for which'). ' ';
                    if ($multipleConditions) {
                        $returnText .= __('the following conditions are met:');
                    } else {
                        $returnText .= __('the following condition is met:');
                    }
                    $prefix = '';
                    $suffix = '';
                    $implode = __(' OR ');
                    if (count($dependGroupLabels) > 1) {
                        $prefix = '( ';
                        $suffix = ' )';
                        $implode = $suffix.$implode.$prefix;
                    }
                    $returnText .= $prefix.' '.implode($implode, $dependGroupLabels).$suffix;
                    return $returnText;
                    break;
                case 'global':
                default:
                    $returnText = '';
                    if ($multipleConditions) {
                        $returnText .= __('The following condition are met:');
                    } else {
                        $returnText .= __('The following condition is met:');
                    }
                    $prefix = ' ';
                    $suffix = '';
                    $implode = __(' OR ');
                    if (count($dependGroupLabels) > 1) {
                        $prefix = ' ( ';
                        $suffix = ' )';
                        $implode = $suffix.$implode.$prefix;
                    }
                    $returnText .= $prefix.' '.implode($implode, $dependGroupLabels).$suffix;
                    return $returnText;
                    break;
            }
        }
        return __('Always generated');
    }

    /**
     * get field label
     *
     * @param $fieldCode
     * @param $scope
     * @return string
     */
    public function getFieldLabel($fieldCode, $scope)
    {
        $field = $this->findField($fieldCode, $scope);
        if ($field) {
            return __($field['label']);
        }
        //look in the depend_labels
        $key = $this->getEntityCode($scope);
        if ($key) {
            $dependLabel = $this->sourceConfig->getConfig('depend_labels/'.$key.'/depend_label/'.$fieldCode.'/label');
            if ($dependLabel) {
                return $dependLabel;
            }
        }
        return $fieldCode;
    }

    /**
     * get value label
     *
     * @param $field
     * @param $scope
     * @param $value
     * @return null|string
     */
    public function getValueLabel($field, $scope, $value)
    {
        $field = $this->findField($field, $scope);
        if (!$field) {
            return $value;
        }
        if (isset($field['source'])) {
            $source = $this->objectManager->get($field['source']);
            $options = $source->toOptionArray();
            $label = $this->getRecursiveOptionLabel($options, $value);
            if (!is_null($label)) {
                return $label;
            }
        }
        return $value;
    }

    /**
     * get option label recursive
     *
     * @param $options
     * @param $value
     * @return null|string
     */
    protected function getRecursiveOptionLabel($options, $value)
    {
        foreach ($options as $option) {
            if (isset($option['value'])) {
                if (is_array($option['value'])) {
                    return $this->getRecursiveOptionLabel($option['value'], $value);
                } elseif ($option['value'] == $value) {
                    if (isset($option['label'])) {
                        return $option['label'];
                    }
                }
            }
        }
        return null;
    }

    /**
     * find field
     *
     * @param $fieldCode
     * @param $scope
     * @return null
     */
    protected function findField($fieldCode, $scope)
    {
        $entityCode = $this->getEntityCode($scope);
        if ($entityCode) {
            $fieldsets = $this->formConfig->getConfig('form/'.$entityCode.'/fieldset', true, []);
            foreach ($fieldsets as $fieldset) {
                if (isset($fieldset['field'][$fieldCode])) {
                    return$fieldset['field'][$fieldCode];
                }
            }
        }
        return null;
    }

    /**
     * get entity code
     *
     * @param string $scope
     * @return null|string
     */
    public function getEntityCode($scope)
    {
        if (isset($this->codeMapping[$scope])) {
            return $this->codeMapping[$scope];
        }
        return null;
    }

    /**
     * get name restrictions
     *
     * @param $entityCode
     * @return array
     */
    protected function getNamingRestrictions($entityCode)
    {
        $restrict = [];
        $restrictions = $this->restrictionConfig->getRestrictions($entityCode, []);
        foreach ($restrictions as $restriction) {
            $groupRestriction = [];
            foreach ($restriction['val'] as $val) {
                $message = isset($val['message']) ? __($val['message'])->render() : __('value is not permitted.')->render();
                if (!isset($groupRestriction[$restriction['id']])) {
                    $groupRestriction[$restriction['id']] = [];

                }
                if (!isset($groupRestriction[$restriction['id']][$message])) {
                    $groupRestriction[$restriction['id']][$message] = [];
                }
                $groupRestriction[$restriction['id']][$message][] = $val['real_val'];
            }
            foreach ($groupRestriction as $fieldId => $messages) {
                foreach ($messages as $oneMessage => $restrictedValues) {
                    $restrict[] = [
                        'field'       => $this->formConfig->getFieldLabelByCode($entityCode, $fieldId, $fieldId),
                        'value'       => implode(', ', $restrictedValues),
                        'message'     => $oneMessage,
                    ];
                }
            }
            if ($this->restrictionConfig->getBoolValue($restriction, 'reserved')) {
                $restrict[] = [
                    'field'       => $this->formConfig->getFieldLabelByCode(
                        $entityCode,
                        $restriction['id'],
                        $restriction['id']
                    ),
                    'value'       => implode(', ', $this->restrictionConfig->getReservedKeywords()),
                    'message'     => __('These are PHP reserved keywords'),
                ];
            }
            if (isset($restriction['class'])) {
                $magic = $this->restrictionConfig->getMagicRestrictedValues($restriction['class']);
                if (count($magic)) {
                    $restrict[] = [
                        'field'       => $this->formConfig->getFieldLabelByCode(
                            $entityCode,
                            $restriction['id'],
                            $restriction['id']
                        ),
                        'value'       => implode(', ', $magic),
                        'message'     => __(
                            'These values would conflict with the magic getters and setters of the generated model'
                        ),
                    ];
                }
            }
        }
        return $restrict;
    }

    /**
     * get module name restrictions
     *
     * @return array
     */
    public function getModuleRestrictions()
    {
        return $this->getNamingRestrictions($this->moduleInstance->getEntityCode());
    }

    /**
     * get entity name restrictions
     *
     * @return array
     */
    public function getEntityRestrictions()
    {
        return $this->getNamingRestrictions($this->entityInstance->getEntityCode());
    }

    /**
     * get attribute name restrictions
     *
     * @return array
     */
    public function getAttributeRestrictions()
    {
        return $this->getNamingRestrictions($this->attributeInstance->getEntityCode());
    }

    /**
     * get form fields
     *
     * @return array
     */
    public function getFields()
    {
        $entities = [
            $this->moduleInstance->getEntityCode() => __('MODULE'),
            $this->settingsInstance->getEntityCode() => __('SETTINGS'),
            $this->entityInstance->getEntityCode() => __('ENTITY'),
            $this->attributeInstance->getEntityCode() => __('ATTRIBUTE'),
        ];
        $data = [];
        foreach ($entities as $entityCode => $entityLabel) {
            $data[] = ['__colspan' => $entityLabel];
            $config = $this->formConfig->getConfig('form/'.$entityCode, true, []);
            foreach ($config['fieldset'] as $fieldset) {
                $data[] = ['__colspan' => $fieldset['label']];
                foreach ($fieldset['field'] as $field) {
                    if ($field['type'] != 'hidden') {
                        $data[] = [
                            'field' => $field['label'],
                            'type'  => $field['type'],
                            'description' => isset($field['tooltip']) ? $field['tooltip'] : ''
                        ];
                    }
                }
            }
        }
        return $data;
    }
}
