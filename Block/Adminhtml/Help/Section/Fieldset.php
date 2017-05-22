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
namespace Umc\Base\Block\Adminhtml\Help\Section;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Umc\Base\Block\Adminhtml\Help\Section;
use Umc\Base\Model\Help\HelpInterface;

class Fieldset extends Template
{
    /**
     * @var string
     */
    const DEFAULT_COLUMN_TYPE = 'text';

    /**
     * @var array
     */
    protected $fieldsetData;

    /**
     * @var string
     */
    protected $fieldsetId;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @param Context $context
     * @param Section $section
     * @param array $fieldsetData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Section $section,
        array $fieldsetData,
        array $data = []
    ) {
        $this->section = $section;
        $this->fieldsetData = $fieldsetData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFieldsetId()
    {
        if ($this->fieldsetId === null) {
            if (isset($this->fieldsetData['id'])) {
                $this->fieldsetId = $this->fieldsetData['id'];
            } else {
                $this->fieldsetId = uniqid();
            }
        }
        return $this->fieldsetId;
    }

    /**
     * @return string
     */
    public function getDescriptionHtml()
    {
        return isset($this->fieldsetData['description']) ? $this->fieldsetData['description'] : '';
    }

    /**
     * @return string
     */
    public function getSectionId()
    {
        return $this->section->getSectionId();
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        return isset($this->fieldsetData['label']) ? $this->fieldsetData['label'] : '';
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        if (!isset($this->fieldsetData['source'])) {
            return [];
        }
        $source = $this->fieldsetData['source'];
        if ($source instanceof HelpInterface) {
            return $source->getColumns();
        }
        return [];
    }

    /**
     * @param array $column
     * @return string
     */
    public function getColumnLabelHtml(array $column)
    {
        return isset($column['label']) ? $column['label'] : '';
    }

    /**
     * @return array
     */
    public function getRows()
    {
        if (!isset($this->fieldsetData['source'])) {
            return [];
        }
        $source = $this->fieldsetData['source'];
        if ($source instanceof HelpInterface) {
            return $source->getRows();
        }
        return [];
    }

    /**
     * format value
     *
     * @param string $field
     * @param string $column
     * @return string
     */
    public function getFormatedValueHtml($field, $column)
    {
        if (!isset($column['type'])) {
            $column['type'] = self::DEFAULT_COLUMN_TYPE;
        }
        if (!isset($column['key'])) {
            return '';
        }
        $key = $column['key'];
        $rawValue = $field[$key];
        switch ($column['type']) {
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
