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

class Yesno extends AbstractType
{
    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = 'options';

    /**
     * setup script constant name
     *
     * @var string
     */
    protected $sqlTypeConst = 'TYPE_INTEGER';

    /**
     * setup script length
     *
     * @var string
     */
    protected $setupLength = 'null';

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
        $options .= '<argument name="options" xsi:type="options" model="Magento\Config\Model\Config\Source\Yesno"/>';
        return $options;
    }

    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $underscore = $this->getUnderscore();
        $options[] = '\'values\' => $this->'.$underscore.'booleanOptions->toOptionArray(),';
        return $options;
    }
}
