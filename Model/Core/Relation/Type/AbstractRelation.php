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
namespace Umc\Base\Model\Core\Relation\Type;

use Umc\Base\Model\Core\Relation;
use Umc\Base\Model\Umc;

class AbstractRelation extends Umc implements TypeInterface
{
    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @param Relation $relation
     * @return $this|mixed
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;
        $this->processEntities();
        return $this;
    }

    /**
     * @return $this
     */
    public function processEntities()
    {
        return $this;
    }
}