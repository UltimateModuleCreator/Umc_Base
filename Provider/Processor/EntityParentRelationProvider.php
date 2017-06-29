<?php
namespace Umc\Base\Provider\Processor;

use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Api\Data\ParentRelationInterface;
use Umc\Base\Api\Data\ParentRelationInterfaceFactory;
use Umc\Base\Model\Relation\Type\ParentRelation;

class EntityParentRelationProvider extends SelfProvider implements ProviderInterface
{
    /**
     * @var ParentRelationInterfaceFactory
     */
    protected $parentRelationInterfaceFactory;

    /**
     * EntityParentRelationProvider constructor.
     * @param ParentRelationInterfaceFactory $parentRelationInterfaceFactory
     */
    public function __construct(
        ParentRelationInterfaceFactory $parentRelationInterfaceFactory
    ) {
        $this->parentRelationInterfaceFactory = $parentRelationInterfaceFactory;
    }

    /**
     * @param ModelInterface|EntityInterface $model
     * @return ParentRelationInterface[]|ModelInterface[]
     */
    public function getModels(ModelInterface $model)
    {
        $parentRelations = [];
        $module = $model->getModule();
        $relations = $module->getRelations();
        foreach ($relations as $relation) {
            if ($relation->getType() == ParentRelation::RELATION_TYPE_PARENT) {
                $entities = $relation->getEntities();
                if (
                    $entities[0]->getNameSingular() == $model->getNameSingular() ||
                    $entities[1]->getNameSingular() == $model->getNameSingular()
                ) {
                    $parentRelations[] = $this->parentRelationInterfaceFactory->create([
                        'entity' => $model,
                        'relation' => $relation
                    ]);
                }
            }
        }
        return $parentRelations;
    }
}
