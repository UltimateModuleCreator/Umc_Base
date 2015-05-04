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
use Umc\Base\Model\Umc;

class AbstractModel extends Umc
{
    /**
     * event prefix
     *
     * @var string
     */
    protected $eventPrefix = 'umc_model';

    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_model';

    /**
     * @var array|null
     */
    protected $placeholders;

    /**
     * event manage instance
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * save attributes config
     *
     * @var SaveAttributesConfig;
     */
    protected $saveAttributesConfig;

    /**
     * form config
     *
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * form dependencies
     *
     * @var array
     */
    protected $formDepends;

    /**
     * restrictions config
     *
     * @var RestrictionConfig
     */
    protected $restrictionConfig;

    /**
     * text escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * constructor
     *
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        array $data = []
    ) {
        $this->eventManager         = $eventManager;
        $this->saveAttributesConfig = $saveAttributesConfig;
        $this->formConfig           = $formConfig;
        $this->restrictionConfig    = $restrictionConfig;
        $this->escaper              = $escaper;
        parent::__construct($data);
    }

    /**
     * save as XML
     *
     * @param array $keys
     * @param null $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml(array $keys = [], $rootName = null, $addOpenTag = false, $addCdata = true)
    {
        if (is_null($rootName)) {
            $rootName = $this->getEntityCode();
        }
        if (empty($keys)) {
            $keys = $this->getXmlAttributes();
        }
        return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * get parent entity
     *
     * @return $this
     */
    public function getParent()
    {
        return $this;
    }

    /**
     * get attributes to save
     *
     * @return array
     */
    public function getXmlAttributes()
    {
        return $this->saveAttributesConfig->getAttributes($this->getEntityCode());
    }

    /**
     * get entity code
     *
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     * get form dependencies
     *
     * @return array
     */
    public function getFormDepends()
    {
        if (is_null($this->formDepends)) {
            $this->formDepends = $this->formConfig->getDepends($this->getEntityCode(), false);
        }
        return $this->formDepends;
    }

    /**
     * validate dependencies
     *
     * @param $config
     * @return bool
     */
    public function validateDepend($config)
    {
        if (!isset($config['depends'])) {
            return true;
        }
        foreach ($config['depends'] as $dependsGroup) {
            //groups are combined using OR
            $groupValid = true;
            //depend tag values are combined using AND
            if (!isset($dependsGroup['depend'])) {
                $dependsGroup['depend'] = [];
            }
            foreach ($dependsGroup['depend'] as $field => $fieldSettings) {
                $allowedValues = [];
                if (!isset($fieldSettings['val'])) {
                    $fieldSettings['val'] = [];
                }
                foreach ($fieldSettings['val'] as $val) {
                    $allowedValues[] = $val['value'];
                }
                //data value must be in allowed values
                if (!in_array($this->getDataUsingMethod($field), $allowedValues)) {
                    $groupValid = false;
                    break;
                }
            }
            //if at least one group is valid then everything is valid
            if ($groupValid) {
                return true;
            }
        }
        return false;
    }

    /**
     * check if field should be validated
     *
     * @param $field
     * @param $fieldSettings
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function shouldValidateField($field, $fieldSettings)
    {
        return true;
    }

    /**
     * set index
     *
     * @param $index
     * @return $this
     */
    public function setIndex($index)
    {
        return $this->setData('index', $index);
    }

    /**
     * get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->getData('index');
    }

    /**
     * validate model
     *
     * @return array
     */
    public function validate()
    {
        $configData = $this->formConfig->getConfig('form/'.$this->getEntityCode(), true, []);
        $errors = [];
        if (isset($configData['fieldset'])) {
            foreach ($configData['fieldset'] as $settings) {
                foreach ($settings['field'] as $fieldId => $fieldSettings) {
                    if ($this->shouldValidateField($fieldId, $fieldSettings)) {
                        $fieldErrors = $this->validateField($fieldId, $fieldSettings);
                        if (count($fieldErrors)) {
                            $key = $this->getValidationErrorKey($fieldId);
                            if (!isset($errors[$key])) {
                                $errors[$key] = [];
                            }
                            $errors[$key] = array_merge($errors[$key], $fieldErrors);
                        }
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * get validation error key
     *
     * @param $field
     * @return mixed
     */
    public function getValidationErrorKey($field)
    {
        return $field;
    }

    /**
     * validate field
     *
     * @param $fieldId
     * @param $fieldSettings
     * @return array
     */
    protected function validateField($fieldId, $fieldSettings)
    {
        $errors = [];
        if ($this->formConfig->getBoolValue($fieldSettings, 'required')) {
            $value = trim($this->getData($fieldId));
            if (strlen(trim($value)) == 0) {
                $errors[] = __('Field %1 is required', $fieldSettings['label']);
            }
        }
        $restrictions = $this->restrictionConfig->getRestrictions($this->getEntityCode());
        if (isset($restrictions[$fieldId])) {
            if ($this->restrictionConfig->getBoolValue($restrictions[$fieldId], 'reserved')) {
                $reserved = $this->restrictionConfig->getReservedKeywords();
                if (in_array(strtolower($this->getData($fieldId)), $reserved)) {
                    $errors[] = __(
                        'You cannot use "%1" here. It is a PHP reserved keyword',
                        $this->getData($fieldId)
                    );
                }
            }
            if (isset($restrictions[$fieldId]['class'])) {
                $magic = $this->restrictionConfig->getMagicRestrictedValues($restrictions[$fieldId]['class']);
                if (in_array(strtolower($this->getData($fieldId)), $magic)) {
                    $errors[] = __(
                        'You cannot use "%1" here. it will conflict with the magic getter or setters of the model',
                        $this->getData($fieldId)
                    );
                }
            }
            if (isset($restrictions[$fieldId]['val'])) {
                foreach ($restrictions[$fieldId]['val'] as $value) {
                    if ($this->getData($fieldId) == $value['real_val']) {
                        if (isset($value['depend'])) {
                            foreach ($value['depend'] as $dependField => $dependency) {
                                if ($dependency['type'] == 'parent') {
                                    $source = $this->getParent();
                                } elseif ($dependency['type'] == 'grandparent') {
                                    $source = $this->getParent()->getParent();
                                } else {
                                    $source = $this;
                                }
                                $restrictedValues = [];
                                foreach ($dependency['depend_val'] as $val) {
                                    $restrictedValues[] = $val['value'];
                                }
                                if (in_array($source->getDataUsingMethod($dependField), $restrictedValues)) {
                                    if (isset($value['message'])) {
                                        $error = __($value['message']);
                                    } else {
                                        $error = __('"%1" value is not permitted.', $value['real_val']);
                                    }
                                    $errors[] = $error;
                                }
                            }
                        } else {
                            if (isset($value['message'])) {
                                $error = __($value['message']);
                            } else {
                                $error = __('"%1" value is not permitted.', $value['real_val']);
                            }
                            $errors[] = $error;
                        }
                    }
                }
            }

        }
        return $errors;
    }

    /**
     * check validation dependency
     *
     * @param $fieldId
     * @param $restrictions
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function checkValidationDepend($fieldId, $restrictions)
    {
        return true;
    }

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return [];
    }

    /**
     * filter content
     *
     * @param string $content
     * @return string
     */
    public function filterContent($content)
    {
        $placeholders = $this->getPlaceholders();
        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }

    /**
     * get child models
     *
     * @return AbstractModel[]
     */
    public function getChildModels()
    {
        return [];
    }

    /**
     * get grand child models
     *
     * @return AbstractModel[]
     */
    public function getGrandChildModels()
    {
        return [];
    }
}
