<?php
namespace Umc\Base\Model\Processor;

use Umc\Base\Model\Config\ClassConfig;
use Umc\Base\Model\Provider\Processor\ProviderInterface;

class ImplementProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * reference to class config
     *
     * @var \Umc\Base\Model\Config\ClassConfig
     */
    protected $classConfig;

    /**
     * @param ClassConfig $classConfig
     * @param ProviderInterface $modelProvider
     */
    public function __construct(
        ClassConfig $classConfig,
        ProviderInterface $modelProvider
    )
    {
        $this->classConfig   = $classConfig;
        parent::__construct($modelProvider);
    }

    /**
     * process element
     *
     * @param $implement
     * @param string $rawContent
     * @return array|mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function process($implement, $rawContent = '')
    {
        $classData = $this->classConfig->getClassData($implement['id']);
        $implements = [];
        foreach ($this->getModelsToProcess() as $model) {
            $implements[$model->filterContent($classData['id'])] = [
                'id' => $model->filterContent($classData['id']),
                'class' => $model->filterContent($classData['class']),
                'alias' => $model->filterContent($classData['alias']),
            ];
        }
        return $implements;
    }

    /**
     * @return \Umc\Base\Model\Core\AbstractModel[]
     * @throws \Exception
     */
    public function getModelsToProcess()
    {
        return $this->modelProvider->setMainModel($this->getModel())->getModels();
    }
}
