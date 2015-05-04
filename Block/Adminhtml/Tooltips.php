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
namespace Umc\Base\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\TooltipFactory;

class Tooltips extends Template
{
    /**
     * list of tooltips
     *
     * @var \Umc\Base\Model\Tooltip[]|null
     */
    protected $tooltips;

    /**
     * tooltip model factory
     *
     * @var \Umc\Base\Model\TooltipFactory
     */
    protected $factory;

    /**
     * form config
     *
     * @var \Umc\Base\Model\Config\Form
     */
    protected $formConfig;

    /**
     * constructor
     *
     * @param FormConfig $formConfig
     * @param TooltipFactory $factory
     * @param Context $context
     */
    public function __construct(
        FormConfig $formConfig,
        TooltipFactory $factory,
        Context $context
    )
    {
        $this->formConfig = $formConfig;
        $this->factory    = $factory;
        parent::__construct($context);
    }

    /**
     * generate all tooltips
     *
     * @return \Umc\Base\Model\Tooltip[]
     */
    public function getTooltips()
    {
        if (!$this->_scopeConfig->isSetFlag(AbstractTab::XML_TOOLTIPS_ENABLED_PATH)) {
            return [];
        }
        if (is_null($this->tooltips)) {
            $this->tooltips = [];
            foreach ($this->formConfig->getConfig('form') as $entityId => $entitySettings) {
                if (isset($entitySettings['fieldset'])) {
                    foreach ($entitySettings['fieldset'] as $fieldsetSettings) {
                        if (isset($fieldsetSettings['field'])) {
                            foreach ($fieldsetSettings['field'] as $fieldId => $fieldSettings) {
                                if (isset($fieldSettings['tooltip']) && isset($fieldSettings['label'])) {
                                    /** @var \Umc\Base\Model\Tooltip  $tooltip */
                                    $tooltip = $this->factory->create();
                                    $tooltip->setTitle(__($fieldSettings['label']));
                                    $tooltip->setMessage(__($fieldSettings['tooltip']));
                                    $tooltip->setId($entityId.'_'.$fieldId);
                                    $this->tooltips[] = $tooltip;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->tooltips;
    }
}
