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
namespace Umc\Base\Model\Config;

use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Umc\Base\Model\Config;

class Form extends Config
{
    /**
     * root node
     *
     * @var string
     */
    protected $rootNode = 'forms';

    /**
     * get the field type
     *
     * @param $settings
     * @return string
     */
    public function getFieldType($settings)
    {
        if (isset($settings['type'])) {
            return $settings['type'];
        }
        return AbstractTab::DEFAULT_FIELD_TYPE;
    }

    /**
     * get textarea rows
     *
     * @param $settings
     * @return int
     */
    public function getTextareaRows($settings)
    {
        if (!isset($settings['rows'])) {
            return AbstractTab::DEFAULT_ROWS;
        }
        if ((int)$settings['rows'] < 1) {
            return AbstractTab::DEFAULT_ROWS;
        }
        return (int)$settings['rows'];
    }

    /**
     * get dependencies
     *
     * @param $code
     * @param bool $asJson
     * @return array|string
     */
    public function getDepends($code, $asJson = true)
    {
        $config = $this->getConfig('form/'.$code.'/fieldset', true, []);
        $depends = [];
        foreach ($config as $fieldset) {
            if (!isset($fieldset['field']) || !is_array($fieldset['field'])) {
                continue;
            }
            foreach ($fieldset['field'] as $field) {
                if (!isset($field['depends']) || !is_array($field['depends'])) {
                    continue;
                }
                foreach ($field['depends'] as $dependGroup) {
                    if (!isset($dependGroup['depend']) || !is_array($dependGroup['depend'])) {
                        continue;
                    }
                    $group = [];
                    foreach ($dependGroup['depend'] as $dependElem) {
                        $values = [];
                        foreach ($dependElem['val'] as $valueElem) {
                            $values[] = $valueElem['value'];
                        }
                        if (count($values) > 0) {
                            $group[$dependElem['id']][$dependElem['type']] = $values;
                        }
                    }
                    if (count($group) > 0) {
                        if (!isset($depends[$field['id']])) {
                            $depends[$field['id']] = [];
                        }
                        $depends[$field['id']][] = $group;
                    }
                }
            }
        }
        if ($asJson) {
            return json_encode($depends);
        }
        return $depends;
    }

    /**
     * get field label by code
     *
     * @param $entityCode
     * @param $fieldCode
     * @param string $default
     * @return string
     */
    public function getFieldLabelByCode($entityCode, $fieldCode, $default = '')
    {
        $fieldsets = $this->getConfig('form/'.$entityCode.'/fieldset', true, []);
        foreach ($fieldsets as $fieldset) {
            if (isset($fieldset['field'][$fieldCode]['label'])) {
                return $fieldset['field'][$fieldCode]['label'];
            }
        }
        return $default;
    }
}
