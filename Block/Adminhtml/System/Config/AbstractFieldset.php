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
namespace Umc\Base\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Field as FieldRenderer;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Helper\Js as JsHelper;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Core\AbstractModel;

/**
 * @method getForm()
 * @method getConfigData()
 */
class AbstractFieldset extends Fieldset
{
    /**
     * fieldset renderer
     *
     * @var \Magento\Config\Block\System\Config\Form\Field
     */
    protected $fieldRenderer;

    /**
     * form config
     *
     * @var \Umc\Base\Model\Config\Form
     */

    protected $formConfig;

    /**
     * object manager reference
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * model in question
     *
     * @var \Umc\Base\Model\Core\AbstractModel
     */
    protected $model;

    /**
     * constructor
     *
     * @param FormConfig $formConfig
     * @param ObjectManagerInterface $objectManager
     * @param FieldRenderer $fieldRenderer
     * @param AbstractModel $model
     * @param Context $context
     * @param Session $authSession
     * @param JsHelper $jsHelper
     * @param array $data
     */
    public function __construct(
        FormConfig $formConfig,
        ObjectManagerInterface $objectManager,
        FieldRenderer $fieldRenderer,
        AbstractModel $model,
        Context $context,
        Session $authSession,
        JsHelper $jsHelper,
        array $data = []
    ) {
        $this->formConfig    = $formConfig;
        $this->objectManager = $objectManager;
        $this->fieldRenderer = $fieldRenderer;
        $this->model         = $model;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * get the form name
     *
     * @return mixed
     */
    public function getFormName()
    {
        return $this->model->getEntityCode();
    }

    /**
     * render the form
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);
        $formName = $this->getFormName();
        $config = $this->formConfig->getConfig('form/'.$formName, true, []);
        foreach ($config['fieldset'] as $fieldset) {
            foreach ($fieldset['field'] as $id => $field) {
                if ($this->formConfig->getBoolValue($field, 'system') && $field['type'] != 'hidden') {
                    $html.= $this->getFieldHtml($element, $id, $field);
                }
            }
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    /**
     * get field html
     *
     * @param $fieldset
     * @param $key
     * @param $field
     * @return mixed
     */
    protected function getFieldHtml($fieldset, $key, $field)
    {
        $configData = $this->getConfigData();
        $formName = $this->getFormName();
        $path = 'umc/'.$formName.'/' . $key;
        if (isset($configData[$path])) {
            $data = $configData[$path];
        } else {
            $data = $this->getForm()->getConfigValue($path);
        }
        $settings = [
            'name'                  => 'groups['.$formName.'][fields]['.$key.'][value]',
            'label'                 => __($field['label']),
            'value'                 => $data,
            'inherit'               => false,
            'can_use_default_value' => false,
            'can_use_website_value' => false,
        ];
        $fieldType = $this->formConfig->getFieldType($field);
        if (in_array($fieldType, ['select', 'multiselect'])) {
            $settings['values'] = $this->objectManager->create($field['source'])
                ->toOptionArray(($fieldType == 'select'));
        }
        if (isset($field['tooltip'])) {
            $settings['tooltip'] = $field['tooltip'];
        }
        $field = $fieldset->addField(
                $formName.$key,
                $this->formConfig->getFieldType($field),
                $settings
            )
            ->setRenderer($this->fieldRenderer);
        return $field->toHtml();
    }
}
