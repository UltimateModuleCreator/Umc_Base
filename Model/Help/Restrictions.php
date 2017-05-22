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
namespace Umc\Base\Model\Help;

use Umc\Base\Config\Restriction as RestrictionConfig;
use Umc\Base\Config\Form as FormConfig;

class Restrictions extends AbstractHelp implements HelpInterface
{
    /**
     * @var RestrictionConfig
     */
    protected $restrictionConfig;

    /**
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var string
     */
    protected $entityCode = 'umc_module';

    /**
     * @param array $columns
     * @param RestrictionConfig $restrictionConfig
     * @param FormConfig $formConfig
     * @param string $entityCode
     */
    public function __construct(
        array $columns,
        RestrictionConfig $restrictionConfig,
        FormConfig $formConfig,
        $entityCode
    ) {
        $this->restrictionConfig = $restrictionConfig;
        $this->formConfig        = $formConfig;
        $this->entityCode        = $entityCode;
        parent::__construct($columns);
    }

    /**
     * @param array $restriction
     * @return array
     */
    protected function getValueRestrictions(array $restriction)
    {
        $rows = [];
        $groupRestriction = [];
        foreach ($restriction['val'] as $val) {
            $message = isset($val['message']) ? __($val['message'])->render() : __('value is not permitted.')->render();
            if (!isset($groupRestriction[$restriction['id']])) {
                $groupRestriction[$restriction['id']] = [];

            }
            if (!isset($groupRestriction[$restriction['id']][$message])) {
                $groupRestriction[$restriction['id']][$message] = [];
            }
            $groupRestriction[$restriction['id']][$message][] = $val['real_val'];
        }
        foreach ($groupRestriction as $fieldId => $messages) {
            foreach ($messages as $oneMessage => $restrictedValues) {
                $rows[] = [
                    'field' => $this->formConfig->getFieldLabelByCode($this->entityCode, $fieldId, $fieldId),
                    'value' => implode(', ', $restrictedValues),
                    'message' => $oneMessage,
                ];
            }
        }
        return $rows;
    }

    /**
     * @param array $restriction
     * @return array
     */
    protected function getReservedRestrictions(array $restriction)
    {
        $rows = [];
        if ($this->restrictionConfig->getBoolValue($restriction, 'reserved')) {
            $rows[] = [
                'field' => $this->formConfig->getFieldLabelByCode(
                    $this->entityCode,
                    $restriction['id'],
                    $restriction['id']
                ),
                'value' => implode(', ', $this->restrictionConfig->getReservedKeywords()),
                'message' => __('These are PHP reserved keywords'),
            ];
        }
        return $rows;
    }

    /**
     * @param array $restriction
     * @return array
     */
    protected function getClassRestrictions(array $restriction)
    {
        $rows = [];
        if (isset($restriction['class'])) {
            $magic = $this->restrictionConfig->getMagicRestrictedValues($restriction['class']);
            if (isset($magic[0])) {
                $rows[] = [
                    'field' => $this->formConfig->getFieldLabelByCode(
                        $this->entityCode,
                        $restriction['id'],
                        $restriction['id']
                    ),
                    'value' => implode(', ', $magic),
                    'message' => __(
                        'These values would conflict with the magic getters and setters of the generated model'
                    ),
                ];
            }
        }
        return $rows;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        if ($this->rows === null) {
            $this->rows = [];
            $restrictions = $this->restrictionConfig->getRestrictions($this->entityCode);
            foreach ($restrictions as $restriction) {
                $this->rows = array_merge($this->rows, $this->getValueRestrictions($restriction));
                $this->rows = array_merge($this->rows, $this->getReservedRestrictions($restriction));
                $this->rows = array_merge($this->rows, $this->getClassRestrictions($restriction));
            }
        }
        return $this->rows;
    }
}
