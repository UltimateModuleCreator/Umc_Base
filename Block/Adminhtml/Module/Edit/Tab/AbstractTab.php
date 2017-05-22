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
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Umc\Base\Config\Form as FormConfig;

class AbstractTab extends GenericForm
{
    /**
     * config path to tooltips enabled flag
     *
     * @var string
     */
    const XML_TOOLTIPS_ENABLED_PATH = 'umc/umc_general/tooltips';

    /**
     * config path to tooltip type
     *
     * @var string
     */
    const XML_TOOLTIP_TYPE_PATH     = 'umc/umc_general/tooltip_type';

    /**
     * default field type
     *
     * @var string
     */
    const DEFAULT_FIELD_TYPE    = 'text';

    /**
     * max tab index supported
     *
     * @var int
     */
    const MAX_TAB_INDEX         = 32767;

    /**
     * form config
     *
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @var bool
     */
    protected $tooltipEnabled;

    /**
     * @param FormConfig $formConfig
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param string $entityCode
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FormConfig $formConfig,
        $entityCode,
        array $data = []
    ) {
        $this->formConfig = $formConfig;
        $this->entityCode = $entityCode;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return bool
     */
    protected function isTooltipEnabled()
    {
        if ($this->tooltipEnabled === null) {
            $this->tooltipEnabled = $this->_scopeConfig->isSetFlag(self::XML_TOOLTIPS_ENABLED_PATH);
        }
        return $this->tooltipEnabled;
    }

    /**
     * @param array $configData
     * @return $this
     * @throws LocalizedException
     */
    protected function buildForm($configData)
    {
        $form   = $this->_formFactory->create();
        $this->setForm($form);
        if (!isset($configData['fieldset']) ||
            !is_array($configData['fieldset'])) {
            return $this;
        }

        foreach ($configData['fieldset'] as $id => $settings) {
            $class = ['fieldset-wide'];
            if (isset($settings['class'])) {
                if (!is_array($settings['class'])) {
                    $settings['class'] = [$settings['class']];
                }
                $class = array_merge($class, $settings['class']);
            }
            $class = implode(' ', $class);
            $fieldset = $form->addFieldset(
                $id,
                [
                    'legend'      => isset($settings['label']) ? __($settings['label']) : '',
                    'class'       => $class,
                    'collapsable' => (int)($this->formConfig->getBoolValue($settings, 'collapsible'))
                ]
            );
            $even = true;
            foreach ($settings['field'] as $fieldId => $fieldSettings) {
                $this->addField($fieldId, $fieldSettings, $fieldset, $even);
                $even = !$even;
            }
        }
        return $this;
    }

    /**
     * @param string $fieldId
     * @param array $fieldSettings
     * @param Fieldset $fieldset
     * @param bool $even
     * @return $this
     * @throws LocalizedException
     */
    protected function addField($fieldId, $fieldSettings, Fieldset $fieldset, $even)
    {
        $fieldType = $this->formConfig->getFieldType($fieldSettings);
        $fieldConfig = [
            'name'      => $fieldId,
            'label'     => $fieldSettings['label'],
            'title'     => $this->escapeHtml($fieldSettings['label']),
        ];
        if ($this->formConfig->getBoolValue($fieldSettings, 'required')) {
            $fieldConfig['required'] = true;
        }
        if ($this->formConfig->getBoolValue($fieldSettings, 'readonly')) {
            $fieldConfig['readonly'] = true;
        }
        if ($this->_scopeConfig->isSetFlag(self::XML_TOOLTIPS_ENABLED_PATH)) {
            if (isset($fieldSettings['tooltip']) && $fieldType != 'hidden') {
                $fieldConfig['after_element_html'] = $this->getTooltipHtml(
                    $this->entityCode,
                    $fieldSettings['id']
                );
            }
        }
        if (isset($fieldSettings['note'])) {
            $fieldConfig['note'] = $fieldSettings['note'];
        }
        $fieldConfig['class'] = ($even ? 'even' : 'odd').' '.$this->generateFieldClasses($fieldSettings);
        if ($values = $this->getFieldOptions($fieldId, $fieldSettings)) {
            $fieldConfig['values'] = $values;
        }
        if ($fieldType == 'textarea') {
            $fieldConfig['rows'] = $this->formConfig->getTextareaRows($fieldSettings);
        }
        $fieldset->addField($fieldSettings['id'], $fieldType, $fieldConfig);
        return $this;
    }

    /**
     * get tooltip html
     *
     * @param string $entity
     * @param string $field
     * @return string
     */
    public function getTooltipHtml($entity, $field)
    {
        return '<a class="umc-tooltip-trigger" href="#"'.
            ' tabindex="'.self::MAX_TAB_INDEX.'" onclick="jQuery(\'#'.$entity.'_'.$field.'\').trigger(\'openModal\');
            return false;"></a>';
    }

    /**
     * prepare the form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $entityCode = $this->entityCode;
        $configData = $this->formConfig->getConfig('form/'.$entityCode, true, []);
        $this->buildForm($configData);
        return $this;
    }

    /**
     * @param array $fieldSettings
     * @return array
     */
    protected function generateFieldClasses($fieldSettings)
    {
        $classes = [];
        if ($this->isTooltipEnabled()) {
            $classes[] = 'has-tooltip';
        }
        $classSources = ['class', 'reloader-class', 'validation'];
        foreach ($classSources as $classSource) {
            if (isset($fieldSettings[$classSource])) {
                $addClasses = (is_array($fieldSettings[$classSource]))
                    ? $fieldSettings[$classSource]
                    : [$fieldSettings[$classSource]];
                $classes = array_merge($classes, $addClasses);
            }
        }
        return implode(' ', $classes);
    }

    /**
     * @param string $fieldId
     * @param array $fieldSettings
     * @return array|null
     * @throws LocalizedException
     */
    protected function getFieldOptions($fieldId, $fieldSettings)
    {
        $fieldOptions = null;
        $fieldType = $this->formConfig->getFieldType($fieldSettings);
        if (in_array($fieldType, ['select', 'multiselect'])) {
            if (!isset($fieldSettings['options'])) {
                throw new LocalizedException(__("No options provided for field %1", $fieldId));
            }
            $options = $fieldSettings['options'];
            if ($options instanceof ArrayInterface) {
                $fieldOptions = $options->toOptionArray();
            } elseif (is_array($options)) {
                $fieldOptions = $options;
            } else {
                throw new LocalizedException(
                    __(
                        "Options for field %1 should be an instance of %2 or an array",
                        [
                            $fieldId,
                            ArrayInterface::class
                        ]
                    )
                );
            }
        }
        return $fieldOptions;
    }
}
