<?php
namespace Umc\Base\Config;

class ClassConfig extends Config
{
    /**
     * get class data from xml map
     *
     * @param string $className
     * @return array
     */
    public function getClassData($className)
    {
        $data = $this->getConfig('class/'.$className);
        if ($data === null) {
            $data = [
                'id'    => $className,
                'class' => $className,
                'alias' => null
            ];
        }
        return $data;
    }
}
