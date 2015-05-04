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

class Text extends AbstractType
{
    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = 'text';

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
    protected $setupLength = '255';

    /**
     * edit for field type
     *
     * @var string
     */
    protected $editFormType = 'text';
}
