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
namespace Umc\Base\Block\Adminhtml\Help;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Umc\Base\Block\Adminhtml\Help\Section\Fieldset;
use Umc\Base\Block\Adminhtml\Help\Section\FieldsetFactory;
use Umc\Base\Model\Help\SectionInterface;

class Section extends Template
{
    /**
     * @var array
     */
    protected $sectionData;

    /**
     * @var FieldsetFactory
     */
    protected $fieldsetFactory;

    /**
     * @var string
     */
    protected $sectionId;

    /**
     * @var Fieldset[]
     */
    protected $fieldsets;

    /**
     * @param Context $context
     * @param FieldsetFactory $fieldsetFactory
     * @param array $sectionData
     * @param array $data
     */
    public function __construct(
        Context $context,
        FieldsetFactory $fieldsetFactory,
        array $sectionData,
        array $data = []
    ) {
        $this->fieldsetFactory = $fieldsetFactory;
        $this->sectionData     = $sectionData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSectionId()
    {
        if ($this->sectionId === null) {
            if (isset($this->sectionData['id'])) {
                $this->sectionId = $this->sectionData['id'];
            } else {
                $this->sectionId = uniqid();
            }
        }
        return $this->sectionId;
    }

    /**
     * @return string
     */
    public function getDescriptionHtml()
    {
        return isset($this->sectionData['description']) ? $this->sectionData['description'] : '';
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        return isset($this->sectionData['label']) ? $this->sectionData['label'] : '';
    }

    /**
     * @return Fieldset[]
     */
    public function getFieldsets()
    {
        if ($this->fieldsets === null) {
            if (isset($this->sectionData['class']) && $this->sectionData['class'] instanceof SectionInterface) {
                $this->fieldsets = $this->sectionData['class']->getFieldsets($this);
            } elseif (isset($this->sectionData['fieldset'])) {
                foreach ($this->sectionData['fieldset'] as $fieldsetData) {
                    $this->fieldsets[] = $this->fieldsetFactory->create([
                        'section' => $this,
                        'fieldsetData' => $fieldsetData
                    ]);
                }
            }
        }
        return $this->fieldsets;
    }
}
