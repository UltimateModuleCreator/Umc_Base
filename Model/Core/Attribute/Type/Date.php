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

class Date extends AbstractType
{
    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $options[] = '\'date_format\' => $this->_localeDate->getDateFormat(';
        $options[] = $this->getPadding().'{{class IntlDateFormatter}}::SHORT';
        $options[] = '),';
        $options[] = '\'class\' => \'validate-date\',';
        return $options;
    }

    /**
     * get header class for admin grid
     *
     * @return string
     */
    public function getGridHeaderClass()
    {
        return trim('col-period '.parent::getGridHeaderClass());
    }

    /**
     * @return string
     */
    public function getFilterRangeClass()
    {
        return ' class="Magento\Ui\Component\Filters\Type\DateRange"';
    }
}
