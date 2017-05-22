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
namespace Umc\Base\Model\Attribute\Type;

use Umc\Base\Api\Data\Attribute\TypeInterface;

class Date extends AbstractType implements TypeInterface
{
    const NAME = 'date';

    /**
     * @return string
     */
    public function getFilterRangeClass()
    {
        return ' class="Magento\Ui\Component\Filters\Type\DateRange"';
    }

    /**
     * @return string
     */
    public function getAdminColumnClass()
    {
        return '  class="Magento\Ui\Component\Listing\Columns\Date"';
    }

    /**
     * @return string
     */
    public function getAdminColumnConfig()
    {
        $config = parent::getAdminColumnConfig();
        $config .= '<item name="timezone" xsi:type="boolean">false</item>';
        $config .= '<item name="dateFormat" xsi:type="string">MMM d, y</item>';
        return $config;
    }

    /**
     * @return string
     */
    public function getAdminColumnConfigV2()
    {
        return parent::getAdminColumnConfigV2().
            '<timezone>false</timezone>'.
            '<dateFormat>MMM d, y</dateFormat>';
    }
}
