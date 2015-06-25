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
namespace Umc\Base\Model\Processor\Annotation\Entity;

use Umc\Base\Model\Processor\ProcessorInterface;
use Umc\Base\Model\Processor\Annotation\Entity as EntityAnnotation;

class Relation extends EntityAnnotation implements ProcessorInterface
{
    /**
     * get models to process
     *
     * @return \Umc\Base\Model\Core\AbstractModel[]
     */
    protected function getModelsToProcess()
    {
        $relations = [];
        /** @var \Umc\Base\Model\Core\Entity $model */
        $model = $this->getModel();
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
