<?php
namespace Umc\Base\Processor;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\ClassConfig;
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Provider\Processor\ProviderInterface;

class ImplementProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * reference to class config
     *
     * @var \Umc\Base\Config\ClassConfig
     */
    protected $classConfig;

    /**
     * @param Dependency $dependencyValidator
     * @param ProviderInterface $modelProvider
     * @param ClassConfig $classConfig
     */
    public function __construct(
        Dependency $dependencyValidator,
        ProviderInterface $modelProvider,
        ClassConfig $classConfig
    ) {
        $this->classConfig = $classConfig;
        parent::__construct($dependencyValidator, $modelProvider);
    }

    /**
     * @param ModelInterface $mainModel
     * @param array $implement
     * @param string $rawContent
     * @return array
     */
    public function process(ModelInterface $mainModel, $implement, $rawContent = '')
    {
        $classData = $this->classConfig->getClassData($implement['id']);
        $implements = [];
        foreach ($this->getModelsToProcess($mainModel) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $implement)) {
                $implements[$model->filterContent($classData['id'])] = [
                    'id' => $model->filterContent($classData['id']),
                    'class' => $model->filterContent($classData['class']),
                    'alias' => $model->filterContent($classData['alias']),
                ];
            }
        }
        return $implements;
    }
}
