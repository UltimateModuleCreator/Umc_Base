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
namespace Umc\Base\Model\Provider;

use Umc\Base\Model\Core\Relation\Type\SiblingRelation;

class SiblingsProvider extends GlobalProvider implements ProviderInterface
{
    /**
     * @return \Umc\Base\Model\Core\AbstractModel[]|\Umc\Base\Model\Core\Relation[]
     */
    public function getModels()
    {
        $relations = [];
        $module = $this->module;
        foreach ($module->getRelations() as $relation) {
            if ($relation->getType() == SiblingRelation::RELATION_TYPE_SIBLING) {
                $relation->setReversed(false);
                $relations[] = $relation;
                $clone = clone $relation;
                $clone->setReversed(true);
                $relations[] = $clone;
            }
        }
        return $relations;
    }
}
