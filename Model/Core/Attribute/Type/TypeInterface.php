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
namespace Umc\Base\Model\Core\Attribute\Type;

use Umc\Base\Model\Core\Attribute;

interface TypeInterface
{
    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders();

    /**
     * set attribute instance
     *
     * @param \Umc\Base\Model\Core\Attribute $attribute
     * @return $this
     */
    public function setAttribute(Attribute $attribute);

    /**
     * check if attribute is multiple select
     *
     * @return bool
     */
    public function isMulti();

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions();

    /**
     * get sql type constant
     *
     * @return string
     */
    public function getSqlTypeConst();

    /**
     * get sql setup lenth
     *
     * @return string
     */
    public function getSetupLength();

    /**
     * get code for admin add/edit form
     *
     * @return string
     */
    public function getEditFormField();

    /**
     * get class for grid header
     *
     * @return string
     */
    public function getGridHeaderClass();

    /**
     * get class for grid column
     *
     * @return string
     */
    public function getGridColumnClass();

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
}
