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

class ParentRelation extends AbstractRelation
{
    /**
     * @var string
     */
    const RELATION_TYPE_PARENT = 'parent';

    /**
     * @return $this
     */
    public function processEntities()
    {
        $relation = $this->relation;
        $reversed = $relation->getReversed();
        $relation->setReversed(false);
        $entities = $relation->getEntities();
        $entities[0]->addRelatedEntity(ChildRelation::RELATION_TYPE_CHILD, $entities[1]);
        $entities[1]->addRelatedEntity(self::RELATION_TYPE_PARENT, $entities[0]);
        $entities[0]->setIsParent(true);
        $relation->setReversed($reversed);
        return parent::processEntities();
    }
}
