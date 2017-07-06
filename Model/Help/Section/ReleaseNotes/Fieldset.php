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
namespace Umc\Base\Model\Help\Section\ReleaseNotes;

use Umc\Base\Model\Help\HelpInterface;

class Fieldset implements HelpInterface
{
    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $rows;

    /**
     * Fieldset constructor.
     * @param array $columns
     * @param array $rows
     */
    public function __construct(
        array $columns,
        array $rows
    ) {
        $this->columns = $columns;
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }
}
