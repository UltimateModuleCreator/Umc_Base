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
namespace Umc\Base\Model\Core\Relation\Type;

use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Config\Type as TypeConfig;
use Umc\Base\Model\Core\Relation;
use Umc\Base\Model\Core\Relation\Type\TypeInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var TypeConfig
     */
    protected $typeConfig;

    /**
     * constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param TypeConfig $typeConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        TypeConfig $typeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->typeConfig    = $typeConfig;
    }

    /**
     * create attribute type instance
     *
     * @param Relation $relation
     * @return TypeInterface
     * @throws \Exception
     */
    public function create(Relation $relation)
    {
        $type = $relation->getType();
        $typeConfig = $this->typeConfig->getConfig('entity/'.$relation->getEntityCode().'/type/'.$type);
        if (!isset($typeConfig['model'])) {
            throw new \Exception('Relation type "'.$type.'" does not exist');
        }
        /** @var \Umc\Base\Model\Core\Relation\Type\TypeInterface $typeInstance */
        $typeInstance = $this->objectManager->create($typeConfig['model']);
        if (false == $typeInstance instanceof TypeInterface) {
            throw new \Exception(
                'Relation type instance is not instance on \Umc\Base\Model\Core\Relation\Type\TypeInterface'
            );
        }
        $typeInstance->setRelation($relation);
        return $typeInstance;
    }
}
