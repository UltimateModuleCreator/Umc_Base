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

class Decimal extends Int
{
    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = 'number';

    /**
     * setup script constant name
     *
     * @var string
     */
    protected $sqlTypeConst = 'TYPE_DECIMAL';

    /**
     * setup script length
     *
     * @var string
     */
    protected $setupLength = '\'12,4\'';
}
