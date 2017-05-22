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
namespace Umc\Base\Validator;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\Restriction as RestrictionConfig;

class FieldValidator implements ValidatorInterface
{
    /**
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * @var RestrictionConfig
     */
    protected $restrictionConfig;

    /**
     * @var array
     */
    protected $formDepends = [];

    /**
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     */
    public function __construct(
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig
    ) {
        $this->formConfig        = $formConfig;
        $this->restrictionConfig = $restrictionConfig;
    }

    /**
     * @param ModelInterface $model
     * @return array
     */
    public function validate(ModelInterface $model)
    {
        $configData = $this->formConfig->getConfig('form/'.$model->getEntityCode().'/fieldset', true, []);
        $errors = [];
        foreach ($configData as $settings) {
            foreach ($settings['field'] as $fieldId => $fieldSettings) {
                $fieldErrors = $this->validateField($model, $fieldId, $fieldSettings);
                if (!empty($fieldErrors)) {
                    $key = $model->getValidationErrorKey($fieldId);
                    if (!isset($errors[$key])) {
                        $errors[$key] = [];
                    }
                    $errors[$key] = array_merge($errors[$key], $fieldErrors);
                }
            }
        }
        return $errors;
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @param array $fieldSettings
     * @return array
     */
    protected function validateRequired(ModelInterface $model, $fieldId, array $fieldSettings)
    {
        $errors = [];
        if ($this->formConfig->getBoolValue($fieldSettings, 'required')) {
            $value = trim($model->getData($fieldId));
            if (strlen(trim($value)) == 0) {
                $errors[] = __('Field %1 is required', $fieldSettings['label']);
            }
        }
        return $errors;
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @param array $fieldSettings
     * @return array
     */
    protected function validateField(ModelInterface $model, $fieldId, array $fieldSettings)
    {
        if (!$this->shouldValidateField($model, $fieldId)) {
            return [];
        }
        $errors = $this->validateRequired($model, $fieldId, $fieldSettings);
        $restrictions = $this->restrictionConfig->getRestrictions($model->getEntityCode());
        if (isset($restrictions[$fieldId])) {
            $errors = array_merge($errors, $this->validateReservedWord($model, $fieldId));
            $errors = array_merge($errors, $this->validateMagicMethods($model, $fieldId));
            $errors = array_merge($errors, $this->validateRestrictions($model, $fieldId));
        }
        return $errors;
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @return bool
     */
    protected function shouldValidateField(ModelInterface $model, $fieldId)
    {
        $depends = $this->getFormDepends($model->getEntityCode());
        if (isset($depends[$fieldId])) {
            $allValid = false;
            foreach ($depends[$fieldId] as $dependGroup) {
                $isValid = true;
                foreach ($dependGroup as $fieldDepend => $values) {
                    $source = $this->getDependencySource($model, $values);
                    $allowedValues = $this->getAllowedValues($values);
                    if (!$allowedValues || !$source) {
                        continue;
                    }
                    $value = trim($source->getDataUsingMethod($fieldDepend));
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
     * @return array
     */
    protected function getDependencyOrder()
    {
        return [
            FormConfig::DEPENDENCY_TYPE_SELF,
            FormConfig::DEPENDENCY_TYPE_PARENT,
            FormConfig::DEPENDENCY_TYPE_GRANDPARENT
        ];
    }

    /**
     * @param array $values
     * @return array|bool
     */
    protected function getAllowedValues($values)
    {
        foreach ($this->getDependencyOrder() as $dependency) {
            if (isset($values[$dependency])) {
                return $values[$dependency];
            }
        }
        return false;
    }

    /**
     * @param ModelInterface $model
     * @param array $values
     * @return bool|ModelInterface
     */
    protected function getDependencySource(ModelInterface $model, $values)
    {
        if (isset($values[FormConfig::DEPENDENCY_TYPE_SELF])) {
            return $model;
        } elseif (isset($values[FormConfig::DEPENDENCY_TYPE_PARENT])) {
            return $model->getParent();
        } elseif (isset($values[FormConfig::DEPENDENCY_TYPE_GRANDPARENT])) {
            return $model->getParent()->getParent();
        }
        return false;
    }

    /**
     * @param string $entityCode
     * @return array
     */
    protected function getFormDepends($entityCode)
    {
        if (!isset($this->formDepends[$entityCode])) {
            $this->formDepends[$entityCode] = $this->formConfig->getDepends($entityCode, false);
        }
        return $this->formDepends[$entityCode];
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @return array
     */
    protected function validateReservedWord(ModelInterface $model, $fieldId)
    {
        $errors = [];
        $restrictions = $this->restrictionConfig->getRestrictions($model->getEntityCode());
        if ($this->restrictionConfig->getBoolValue($restrictions[$fieldId], 'reserved')) {
            $reserved = $this->restrictionConfig->getReservedKeywords();
            if (in_array(strtolower($model->getDataUsingMethod($fieldId)), $reserved)) {
                $errors[] = __(
                    'You cannot use "%1" here. It is a PHP reserved keyword',
                    $model->getDataUsingMethod($fieldId)
                );
            }
        }
        return $errors;
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @return array
     */
    protected function validateMagicMethods(ModelInterface $model, $fieldId)
    {
        $errors = [];
        $restrictions = $this->restrictionConfig->getRestrictions($model->getEntityCode());
        if (isset($restrictions[$fieldId]['class'])) {
            $magic = $this->restrictionConfig->getMagicRestrictedValues($restrictions[$fieldId]['class']);
            if (in_array(strtolower($model->getDataUsingMethod($fieldId)), $magic)) {
                $errors[] = __(
                    'You cannot use "%1" here. it will conflict with the magic getter or setters of the model',
                    $model->getDataUsingMethod($fieldId)
                );
            }
        }
        return $errors;
    }

    /**
     * @param ModelInterface $model
     * @param string $fieldId
     * @return array
     */
    protected function validateRestrictions(ModelInterface $model, $fieldId)
    {
        $errors = [];
        $restrictions = $this->restrictionConfig->getRestrictions($model->getEntityCode());
        if (!isset($restrictions[$fieldId]['val'])) {
            return $errors;
        }
        foreach ($restrictions[$fieldId]['val'] as $value) {
            if ($model->getDataUsingMethod($fieldId) == $value['real_val']) {
                if (isset($value['depend'])) {
                    foreach ($value['depend'] as $dependField => $dependency) {
                        $source = $this->getDependencySource($model, [$dependency['type'] => []]);
                        if (in_array($source->getDataUsingMethod($dependField), $dependency['depend_val'])) {
                            $errors[] = $this->getErrorMessage($value);
                        }
                    }
                } else {
                    $errors[] = $this->getErrorMessage($value);
                }
            }
        }
        return $errors;
    }

    /**
     * @param array $value
     * @return \Magento\Framework\Phrase|string
     */
    protected function getErrorMessage($value)
    {
        if (isset($value['message'])) {
            return $value['message'];
        }
        if (isset($value['real_val'])) {
            return __('"%1" value is not permitted.', $value['real_val']);
        }
        return __('Value is not permitted.');
    }
}
