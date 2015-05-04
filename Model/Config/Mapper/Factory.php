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
namespace Umc\Base\Model\Config\Mapper;

use Magento\Config\Model\Config\Structure\MapperInterface;
use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Config\Mapper\FactoryInterface;

class Factory implements FactoryInterface
{
    /**
     * object manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * type mapping
     *
     * @var array
     */
    protected $typeMap = [];

    /**
     * constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $typeMap
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $typeMap = []
    )
    {
        $this->objectManager = $objectManager;
        $this->typeMap = $typeMap;
    }

    /**
     * create objects
     *
     * @param $type
     * @return MapperInterface|mixed
     * @throws \Exception
     */
    public function create($type)
    {
        $className = $this->getMapperClassNameByType($type);
        /** @var MapperInterface $mapperInstance  */
        $mapperInstance = $this->objectManager->create($className);

        if (false == $mapperInstance instanceof MapperInterface) {
            throw new \Exception(
                'Mapper object is not instance of \Magento\Config\Model\Config\Structure\MapperInterface'
            );
        }
        return $mapperInstance;
    }

    /**
     * get mapper by type
     *
     * @param $type
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getMapperClassNameByType($type)
    {
        if (!isset($this->typeMap[$type])) {
            throw new \InvalidArgumentException('Invalid mapper type: ' . $type);
        }
        return $this->typeMap[$type];
    }
}
