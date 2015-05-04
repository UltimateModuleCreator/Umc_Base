<?php
namespace Umc\Base\Model\Processor\Implement;

use Umc\Base\Model\Config\ClassConfig;
use Umc\Base\Model\Processor\AbstractProcessor;

class GlobalImplement extends AbstractProcessor
{
    /**
     * reference to class config
     *
     * @var \Umc\Base\Model\Config\ClassConfig
     */
    protected $classConfig;

    /**
     * constructor
     *
     * @param ClassConfig $classConfig
     */
    public function __construct(
        ClassConfig $classConfig
    )
    {
        $this->classConfig = $classConfig;
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
        return [
            $this->getModel()->filterContent($classData['id']) => [
                'id' => $this->getModel()->filterContent($classData['id']),
                'class' => $this->getModel()->filterContent($classData['class']),
                'alias' => $this->getModel()->filterContent($classData['alias']),
            ]
        ];
    }
}
