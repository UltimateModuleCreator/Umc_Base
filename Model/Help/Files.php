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
namespace Umc\Base\Model\Help;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Option\ArrayInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\Source as SourceConfig;
use Umc\Base\Model\Help\Condition\ConditionInterface;

class Files extends AbstractHelp implements HelpInterface
{
    /**
     * @var string
     */
    const DEFAULT_CONDITION_PROCESSOR_KEY = 'global';

    /**
     * @var string
     */
    const NO_CONDITION_PROCESSOR_KEY = 'no_condition';

    /**
     * @var SourceConfig
     */
    protected $sourceConfig;

    /**
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var ConditionInterface[]
     */
    protected $conditionProcessors;

    /**
     * @param array $columns
     * @param SourceConfig $sourceConfig
     * @param FormConfig $formConfig
     * @param array $conditionProcessors
     */
    public function __construct(
        array $columns,
        SourceConfig $sourceConfig,
        FormConfig $formConfig,
        array $conditionProcessors
    ) {
        $this->sourceConfig        = $sourceConfig;
        $this->formConfig          = $formConfig;
        $this->conditionProcessors = $conditionProcessors;
        parent::__construct($columns);
    }

    /**
     * @return array
     */
    public function getRows()
    {
        if ($this->rows === null) {
            $this->rows = [];
            $sourceFiles = $this->sourceConfig->getConfig('file');
            foreach ($sourceFiles as $file) {
                $file = $this->sourceConfig->preProcessFileConfig($file);
                $this->rows[] = [
                    'label'       => $file['label'],
                    'scope'       => $this->sourceConfig->getScopeLabel($file['scope']),
                    'destination' => $file['destination'],
                    'description' => $file['description'],
                    'condition'   => $this->getFileCondition($file)
                ];
            }
        }
        return $this->rows;
    }

    /**
     * get file conditions
     *
     * @param array $fileConfig
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getFileCondition($fileConfig)
    {
        $dependGroupLabels = [];
        if (isset($fileConfig['depends'])) {
            foreach ($fileConfig['depends'] as $dependGroup) {
                $dependFieldsLabels = [];
                foreach ($dependGroup['depend'] as $fieldDepend) {
                    $allowedValues = [];
                    foreach ($fieldDepend['val'] as $value) {
                        $allowedValues[] = $this->getValueLabel(
                            $fieldDepend['id'],
                            $fileConfig['scope'],
                            $value['value'],
                            (isset($value['bool']) ? $value['bool'] : false)
                        );
                    }
                    if (isset($allowedValues[0]) > 0) {
                        $dependFieldsLabel = '<em>'.$this->getFieldLabel(
                            $fieldDepend['id'],
                            $fileConfig['scope']
                        ).'</em>';
                        if (!isset($allowedValues[1]) == 1) {
                            $dependFieldsLabel .= __(' is ').'<u>'.$allowedValues[0].'</u>';
                        } else {
                            $dependFieldsLabel .= __(' is one of ').'" <u>'.implode(', ', $allowedValues).' </u>"';
                        }
                        $dependFieldsLabels[] = $dependFieldsLabel;
                    }
                }
                $dependGroupLabels[] = implode(__(' AND '), $dependFieldsLabels);
            }
            return $this->getConditionProcessor($fileConfig['scope'])->getConditionText($dependGroupLabels);
        }
        return $this->getConditionProcessor(self::NO_CONDITION_PROCESSOR_KEY)->getConditionText($dependGroupLabels);
    }

    /**
     * @param string $key
     * @return ConditionInterface
     * @throws \Exception
     */
    protected function getConditionProcessor($key)
    {
        if (!isset($this->conditionProcessors[$key])) {
            $key = self::DEFAULT_CONDITION_PROCESSOR_KEY;
        }
        if (!isset($this->conditionProcessors[$key])) {
            throw new \Exception("Default condition processor not found");
        }
        $condition = $this->conditionProcessors[$key];
        if ($condition instanceof ConditionInterface) {
            return $condition;
        }
        throw new \Exception("Condition processor for ".$key.' is not an instance of '.ConditionInterface::class);
    }

    /**
     * @param string $field
     * @param string $scope
     * @param string $value
     * @param bool $asBool
     * @return \Magento\Framework\Phrase|null|string
     * @throws LocalizedException
     */
    public function getValueLabel($field, $scope, $value, $asBool = false)
    {
        $field = $this->findField($field, $scope);
        if (!$field) {
            if ($asBool) {
                return $this->getBoolValue($value);
            }
            return $value;
        }

        if (isset($field['source'])) {
            $source = $field['source'];
            if ($source instanceof ArrayInterface) {
                $options = $source->toOptionArray();
            } elseif (is_array($source)) {
                $options = $source;
            } else {
                throw new LocalizedException(
                    __(
                        "Options for field %1 should be an instance of %2 or an array",
                        $field['Label'],
                        ArrayInterface::class
                    )
                );
            }
            $label = $this->getRecursiveOptionLabel($options, $value);
            if (!$label === null) {
                return $label;
            }
        }
        if ($asBool) {
            return $this->getBoolValue($value);
        }
        return $value;
    }

    /**
     * @param mixed $val
     * @return \Magento\Framework\Phrase
     */
    public function getBoolValue($val)
    {
        return ($val ? __('True') : __('False'));
    }

    /**
     * find field
     *
     * @param string $fieldCode
     * @param string $scope
     * @return null
     */
    protected function findField($fieldCode, $scope)
    {
        $entityCode = $scope;
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
     * get option label recursive
     *
     * @param array $options
     * @param string $value
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
     * get field label
     *
     * @param string $fieldCode
     * @param string $scope
     * @return string
     */
    public function getFieldLabel($fieldCode, $scope)
    {
        $field = $this->findField($fieldCode, $scope);
        if ($field) {
            return $field['label'];
        }
        //look in the depend_labels
        $key = $scope;
        if ($key) {
            $dependLabel = $this->sourceConfig->getConfig('depend_labels/'.$key.'/depend_label/'.$fieldCode.'/label');
            if ($dependLabel) {
                return $dependLabel;
            }
        }
        return $fieldCode;
    }
}
