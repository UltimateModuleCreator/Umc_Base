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
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Validator;

use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModuleInterface;

class ModuleValidator
{
    /**
     * @var ValidatorPool[]
     */
    protected $validatorPools;

    /**
     * @param array $validatorPools
     */
    public function __construct(array $validatorPools)
    {
        $this->validatorPools = $validatorPools;
    }

    /**
     * @param ModuleInterface $module
     * @return array
     */
    public function validate(ModuleInterface $module)
    {
        $moduleValidatorPool = $this->getValidatorPool(ModuleInterface::ENTITY_CODE);
        $entityValidatorPool = $this->getValidatorPool(EntityInterface::ENTITY_CODE);
        $attributeValidatorPool = $this->getValidatorPool(AttributeInterface::ENTITY_CODE);
        $errors = [];
        if ($moduleValidatorPool) {
            $errors = array_merge($errors, $moduleValidatorPool->validate($module));
        }

        foreach ($module->getEntities() as $entity) {
            if ($entityValidatorPool) {
                $errors = array_merge($errors, $entityValidatorPool->validate($entity));
            }
            if ($attributeValidatorPool) {
                foreach ($entity->getAttributes() as $attribute) {
                    $errors = array_merge($errors, $attributeValidatorPool->validate($attribute));
                }
            }
        }
        return $errors;
    }

    /**
     * @param string $entityCode
     * @return bool|ValidatorPool
     */
    protected function getValidatorPool($entityCode)
    {
        if (isset($this->validatorPools[$entityCode])) {
            return $this->validatorPools[$entityCode];
        }
        return false;
    }
}
