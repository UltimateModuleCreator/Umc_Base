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

class SiblingRelation extends AbstractRelation
{
    /**
     * @var string
     */
    const RELATION_TYPE_SIBLING = 'sibling';

    /**
     * @return $this
     */
    public function processEntities()
    {
        $relation = $this->relation;
        $entities = $relation->getEntities();
        $entities[0]->addRelatedEntity(self::RELATION_TYPE_SIBLING, $entities[1]);
        $entities[1]->addRelatedEntity(self::RELATION_TYPE_SIBLING, $entities[0]);
        return parent::processEntities();
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        $placeholders = [];
        $relation = $this->relation;
        $reversed = $relation->getReversed();
        $relation->setReversed(false);
        $entities = $relation->getEntities();
        $relation->setReversed($reversed);
        $parts = [
            $relation->getModule()->getNamespace(true),
            $relation->getModule()->getModuleName(true),
            $entities[0]->getNameSingular(false),
            $entities[1]->getNameSingular(false)
        ];
        $placeholders['{{relation_table}}']   = implode('_', $parts);
        return $placeholders;
    }

    /**
     * @return array
     */
    public function getUninstallLines()
    {
        $placeholders = $this->getPlaceholders();
        $lines = parent::getUninstallLines();
        $lines[] = 'DROP TABLE '.$placeholders['{{relation_table}}'].';';
        return $lines;
    }
}
