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
namespace Umc\Base\Api\Data\Attribute;

use Umc\Base\Api\Data\AttributeInterface;

/**
 * @api
 */
interface TypeInterface
{
    const MULTI = 'multi';
    const FILTER_TYPE = 'filter_type';
    const INLINE_EDIT_TYPE = 'inline_edit_type';
    const COLUMN_COMPONENT = 'column_component';
    const ADMIN_COLUMN_TYPE = 'admin_column_type';
    const FULL_TEXT = 'full_text';
    const SQL_TYPE_CONST = 'sql_type_const';
    const SETUP_LENGTH = 'setup_length';
    const DEFAULT_VALUE = 'default_value';
    const HAS_OPTIONS = 'has_options';
    const FILTER_INPUT = 'filter_input';
    const GRID_DATA_TYPE = 'grid_data_type';
    const FORM_DATA_TYPE = 'form_data_type';
    const FORM_ELEMENT = 'form_element';

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders();

    /**
     * set attribute instance
     *
     * @param AttributeInterface $attribute
     * @return TypeInterface
     */
    public function setAttribute(AttributeInterface $attribute);

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions();

    /**
     * @return string
     */
    public function getAdminColumnConfig();

    /**
     * get default value
     *
     * @return string
     */
    public function getDefaultValue();

    /**
     * check if attribute has options
     *
     * @return bool
     */
    public function getHasOptions();

    /**
     * @return string
     */
    public function getFilterRangeClass();

    /**
     * @return string
     */
    public function getFilterInput();

    /**
     * @return bool
     */
    public function getMulti();

    /**
     * @return string
     */
    public function getSqlTypeConst();

    /**
     * @return string
     */
    public function getSetupLength();

    /**
     * @return bool
     */
    public function getFullText();

    /**
     * @return string
     */
    public function getAdminColumnType();

    /**
     * @return string
     */
    public function getFormDataType();

    /**
     * @return string
     */
    public function getFormElement();

    /**
     * @return string
     */
    public function getAdditionalFormConfig();

    /**
     * @return string
     */
    public function getAdditionalFormConfigV2();

    /**
     * @return string
     */
    public function getUiFormOptions();

    /**
     * @return string
     */
    public function getUiFormOptionsV2();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getAdminColumnClass();

    /**
     * @return AttributeInterface
     */
    public function getAttribute();

    /**
     * @return string
     */
    public function getAdminColumnComponentV2();

    /**
     * @return string
     */
    public function getAdminColumnConfigV2();
}
