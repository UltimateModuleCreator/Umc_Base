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
namespace Umc\Base\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form;
use Magento\Config\Block\System\Config\Form\Field as FieldRenderer;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\Helper\Js as JsHelper;
use Umc\Base\Config\Form as FormConfig;

/**
 * @method Form getForm()
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
     * @var \Umc\Base\Config\Form
     */
    protected $formConfig;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @param FormConfig $formConfig
     * @param FieldRenderer $fieldRenderer
     * @param Context $context
     * @param Session $authSession
     * @param JsHelper $jsHelper
     * @param string $entityCode
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        JsHelper $jsHelper,
        $entityCode,
        FormConfig $formConfig,
        FieldRenderer $fieldRenderer,
        array $data = []
    ) {
        $this->formConfig    = $formConfig;
        $this->fieldRenderer = $fieldRenderer;
        $this->entityCode    = $entityCode;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * get the form name
     *
     * @return mixed
     */
    public function getFormName()
    {
        return $this->entityCode;
    }

    /**
     * render the form
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = '';
        $formName = $this->getFormName();
        $config = $this->formConfig->getConfig('form/'.$formName, true, []);
        if (isset($config['fieldset'])) {
            $html .= $this->_getHeaderHtml($element);
            foreach ($config['fieldset'] as $fieldset) {
                foreach ($fieldset['field'] as $id => $field) {
                    if ($this->formConfig->getBoolValue($field, 'system') && $field['type'] != 'hidden') {
                        $html .= $this->getFieldHtml($element, $id, $field);
                    }
                }
            }
            $html .= $this->_getFooterHtml($element);
        }
        return $html;
    }

    /**
     * @param AbstractElement $fieldset
     * @param string $key
     * @param string $field
     * @return string
     * @throws LocalizedException
     */
    protected function getFieldHtml(AbstractElement $fieldset, $key, $field)
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
            'inherit'               => true,
            'can_restore_to_default'=> true,
            'can_use_default_value' => false,
            'can_use_website_value' => false,
        ];
        $fieldType = $this->formConfig->getFieldType($field);
        if (in_array($fieldType, ['select', 'multiselect'])) {
            if (!isset($field['options'])) {
                throw new LocalizedException(new Phrase("No options provided for field %1", $field['Label']));
            }
            $options = $field['options'];
            if ($options instanceof ArrayInterface) {
                $settings['values'] = $options->toOptionArray();
            } elseif (is_array($options)) {
                $settings['values'] = $options;
            } else {
                throw new LocalizedException(
                    new Phrase(
                        "Options for field %1 should be an instance of %2 or an array",
                        $field['Label'],
                        ArrayInterface::class
                    )
                );
            }
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
