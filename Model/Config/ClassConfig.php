<?php
namespace Umc\Base\Model\Config;

use Umc\Base\Model\Config;

class ClassConfig extends Config
{
    /**
     * root note of config
     *
     * @var string
     */
    protected $rootNode = 'classes';

    /**
     * get class data from xml map
     *
     * @param $className
     * @return array
     */
    public function getClassData($className)
    {
        $data = $this->getConfig('class/'.$className);
        if (is_null($data)) {
            $data = [
                'id'    => $className,
                'class' => $className,
                'alias' => isset($data['alias']) ? $data['alias'] : null
            ];
        }
        return $data;
    }
}