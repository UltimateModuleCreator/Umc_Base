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
namespace Umc\Base\Model\Provider\Processor;

use Umc\Base\Model\Core\AbstractModel;

class EntityRelationProvider extends SelfProvider implements ProviderInterface
{
    /**
     * @return \Umc\Base\Model\Core\AbstractModel[]|\Umc\Base\Model\Core\Relation[]
     */
    public function getModels()
    {
        /** @var AbstractModel|\Umc\Base\Model\Core\Entity $model */
        $model = $this->mainModel;
        $relations = [];
        foreach ($model->getModule()->getRelations() as $relation) {
            $relation->setReversed(false);
            $entities = $relation->getEntities();
            if ($entities[0]->getIndex() == $model->getIndex()) {
                $relations[] = $relation;
            } elseif ($entities[1]->getIndex() == $model->getIndex()) {
                $relation->setReversed(true);
                $relations[] = $relation;
            }
        }
        return $relations;
    }
}
