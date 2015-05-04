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

class Country extends AbstractType
{
    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = 'country';

    /**
     * setup script constant name
     *
     * @var string
     */
    protected $sqlTypeConst = 'TYPE_TEXT';

    /**
     * setup script length
     *
     * @var string
     */
    protected $setupLength = '3';

    /**
     * edit for field type
     *
     * @var string
     */
    protected $editFormType = 'select';

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        $options = parent::getAdminColumnOptions();
        $options .= '<argument name="options" xsi:type="options" model="Magento\Config\Model\Config\Source\Locale\Country"/>';
        return $options;
    }

    /**
     * get additional options for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $underscore = $this->getUnderscore();
        $options[] = '\'values\' => array_merge([\'\' => \'\'], $this->'.$underscore.'countryOptions->toOptionArray()),';
        return $options;
    }
}
