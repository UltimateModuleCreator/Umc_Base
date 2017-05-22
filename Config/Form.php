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
namespace Umc\Base\Config;

use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;

class Form extends Config
{
    /**
     * @var string
     */
    const DEPENDENCY_TYPE_SELF        = 'self';

    /**
     * @var string
     */
    const DEPENDENCY_TYPE_PARENT      = 'parent';

    /**
     * @var string
     */
    const DEPENDENCY_TYPE_GRANDPARENT = 'grandparent';

    /**
     * default textarea rows
     *
     * @var int
     */
    const DEFAULT_ROWS                = 5;

    /**
     * get the field type
     *
     * @param array $settings
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
     * @param array $settings
     * @return int
     */
    public function getTextareaRows($settings)
    {
        if (!isset($settings['rows'])) {
            return self::DEFAULT_ROWS;
        }
        if ((int)$settings['rows'] < 1) {
            return self::DEFAULT_ROWS;
        }
        return (int)$settings['rows'];
    }

    /**
     * get dependencies
     *
     * @param string $code
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
                $fieldDepends = $this->getDependsGroup($field['depends']);
                if (!isset($depends[$field['id']])) {
                    $depends[$field['id']] = [];
                }
                $depends[$field['id']] = array_merge($depends[$field['id']], $fieldDepends);
            }
        }
        if ($asJson) {
            return json_encode($depends);
        }
        return $depends;
    }

    /**
     * @param array $fieldDependencies
     * @return array
     */
    protected function getDependsGroup($fieldDependencies)
    {
        $depends = [];
        foreach ($fieldDependencies as $dependGroup) {
            if (!isset($dependGroup['depend']) || !is_array($dependGroup['depend'])) {
                continue;
            }
            $group = [];
            foreach ($dependGroup['depend'] as $dependElem) {
                $values = [];
                foreach ($dependElem['val'] as $valueElem) {
                    $values[] = (string)$valueElem['value'];
                }
                if (isset($values[0])) {
                    $group[$dependElem['id']][$dependElem['type']] = $values;
                }
            }
            $groupValues = array_values($group);
            if (isset($groupValues[0])) {
                $depends[] = $group;
            }
        }
        return $depends;
    }

    /**
     * get field label by code
     *
     * @param string $entityCode
     * @param string $fieldCode
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
