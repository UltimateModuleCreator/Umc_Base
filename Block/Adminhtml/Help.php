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
namespace Umc\Base\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Umc\Base\Block\Adminhtml\Help\Section;
use Umc\Base\Config\Help as HelpConfig;
use Umc\Base\Block\Adminhtml\Help\SectionFactory as SectionBlockFactory;

/**
 * @api
 */
class Help extends GenericForm
{
    /**
     * @var SectionBlockFactory
     */
    protected $sectionFactory;

    /**
     * @var \Umc\Base\Config\Help
     */
    protected $helpConfig;

    /**
     * @var \Umc\Base\Block\Adminhtml\Help\Section[]
     */
    protected $sectionBlocks;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelpConfig $helpConfig
     * @param SectionBlockFactory $sectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelpConfig $helpConfig,
        SectionBlockFactory $sectionFactory,
        array $data = []
    ) {
        $this->helpConfig     = $helpConfig;
        $this->sectionFactory = $sectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * get help sections
     *
     * @return Section[]
     */
    public function getSections()
    {
        if ($this->sectionBlocks === null) {
            $this->sectionBlocks = [];
            foreach ($this->helpConfig->getConfig('section', true, []) as $sectionId => $sectionData) {
                $section = $this->sectionFactory->create([
                    'sectionData' => $sectionData
                ]);
                $this->sectionBlocks[] = $section;
            }
        }
        return $this->sectionBlocks;
    }

    /**
     * format value
     *
     * @param string $field
     * @param array $column
     * @return string
     * @deprecated
     */
    public function getFormatedValue($field, $column)
    {
        if (!isset($column['type'])) {
            $column['type'] = Section\Fieldset::DEFAULT_COLUMN_TYPE;
        }
        if (!isset($column['key'])) {
            return '';
        }
        $key = $column['key'];
        $rawValue = $field[$key];
        switch($column['type']){
            case 'bool':
                $value = (bool)(string)$rawValue;
                if ($value == 1) {
                    return __('Yes');
                }
                return __('No');
            case 'text':
                //intentional fall through
            default:
                return $rawValue;
        }
    }
}
