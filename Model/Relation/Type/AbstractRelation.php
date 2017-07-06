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
namespace Umc\Base\Model\Relation\Type;

use Umc\Base\Api\Data\Relation\TypeInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Model\Relation;
use Umc\Base\Model\Umc;

abstract class AbstractRelation extends Umc implements TypeInterface
{
    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @param RelationInterface $relation
     * @return $this|mixed
     */
    public function setRelation(RelationInterface $relation)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        return [];
    }
}
