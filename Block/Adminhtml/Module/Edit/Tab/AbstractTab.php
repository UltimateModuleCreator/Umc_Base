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
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Core\ModelInterface;

class AbstractTab extends GenericForm
{
    /**
     * config path to tooltips enabled flag
     *
     * @var string
     */
    const XML_TOOLTIPS_ENABLED_PATH = 'umc/umc_general/tooltips';

    /**
     * default field type
     *
     * @var string
     */
    const DEFAULT_FIELD_TYPE    = 'text';

    /**
     * default textarea rows
     *
     * @var int
     */
    const DEFAULT_ROWS          = 5;

    /**
     * max tab index supported
     *
     * @var int
     */
    const MAX_TAB_INDEX         = 32767;

    /**
     * form config
     *
     * @var \Umc\Base\Model\Config\Form
     */
    protected $formConfig;

    /**
     * model instance
     *
     * @var \Umc\Base\Model\Core\ModelInterface
     */
    protected $model;

    /**
     * object manager access
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * constructor
     *
     * @param FormConfig $formConfig
     * @param ObjectManagerInterface $objectManager
     * @param ModelInterface $model
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        FormConfig $formConfig,
        ObjectManagerInterface $objectManager,
        ModelInterface $model,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    )
    {
        $this->formConfig    = $formConfig;
        $this->objectManager = $objectManager;
        $this->model         = $model;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * build a form
     *
     * @param $configData
     * @return $this
     */
    public function buildForm($configData)
    {
        $form   = $this->_formFactory->create();
        $this->setForm($form);
        if (!isset($configData['fieldset']) ||
            !is_array($configData['fieldset'])) {
            return $this;
        }

        $hasTooltip = '';
        if ($this->_scopeConfig->isSetFlag(self::XML_TOOLTIPS_ENABLED_PATH)) {
            $hasTooltip = 'has-tooltip';
        }

        foreach ($configData['fieldset'] as $id => $settings) {
            $fieldset = $form->addFieldset(
                $id,
                [
                    'legend'      => isset($settings['label']) ? __($settings['label']) : '',
                    'class'       => 'fieldset-wide'. (isset($settings['class']) ? ' '.$settings['class']: ''),
                    'collapsable' => (int)($this->formConfig->getBoolValue($settings, 'collapsible'))
                ]
            );
            $index = 0;
            foreach ($settings['field'] as $fieldId => $fieldSettings) {
                $fieldType = $this->formConfig->getFieldType($fieldSettings);
                $fieldConfig = [
                    'name'      => $fieldId,
                    'label'     => __($fieldSettings['label']),
                    'title'     => $this->escapeHtml(__($fieldSettings['label'])),
                ];
                if ($this->formConfig->getBoolValue($fieldSettings, 'required')) {
                    $fieldConfig['required'] = true;
                }
                if ($this->formConfig->getBoolValue($fieldSettings, 'readonly')) {
                    $fieldConfig['readonly'] = true;
                }
                if ($this->_scopeConfig->isSetFlag(self::XML_TOOLTIPS_ENABLED_PATH)) {
                    if (isset($fieldSettings['tooltip']) && $fieldType != 'hidden') {
                        $fieldConfig['before_element_html'] = $this->getTooltipHtml(
                            $this->model->getEntityCode(),
                            $fieldSettings['id']
                        );
                    }
                }
                if (isset($fieldSettings['note'])) {
                    $fieldConfig['note'] = $fieldSettings['note'];
                }
                $fieldConfig['class'] = (($index % 2 == 0 ) ? 'even' : 'odd').' '.$hasTooltip;
                $fieldConfig['css_class'] = $fieldConfig['class'];
                $index++;

                if (isset($fieldSettings['class'])) {
                    $fieldConfig['class'] .= ' '.$fieldSettings['class'];
                }
                if (isset($fieldSettings['reloader-class'])) {
                    $fieldConfig['class'] .= ' '.$fieldSettings['reloader-class'];
                }
                if (in_array($fieldType, ['select', 'multiselect'])) {
                    $fieldConfig['values'] = $this->objectManager->get($fieldSettings['source'])
                        ->toOptionArray(($fieldType == 'select'));
                }
                if ($fieldType == 'textarea') {
                    $fieldConfig['rows'] = $this->formConfig->getTextareaRows($fieldSettings);
                }
                $fieldset->addField($fieldSettings['id'], $fieldType, $fieldConfig);
            }
        }
        return $this;
    }

    /**
     * get tooltip html
     *
     * @param $entity
     * @param $field
     * @return string
     */
    public function getTooltipHtml($entity, $field)
    {
        return '<a class="umc-tooltip-trigger" href="#"'.
            ' tabindex="'.self::MAX_TAB_INDEX.'" onclick="jQuery(\'#'.$entity.'_'.$field.'\').trigger(\'openModal\');return false;"></a>';
    }

    /**
     * prepare the form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $configData = $this->formConfig->getConfig('form/'.$this->model->getEntityCode(), true, []);
        $this->buildForm($configData);
        return $this;
    }
}
