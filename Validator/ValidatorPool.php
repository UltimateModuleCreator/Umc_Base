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

use Umc\Base\Api\Data\ModelInterface;

class ValidatorPool implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    protected $validators;

    /**
     * @param array $validators
     */
    public function __construct(
        array $validators
    ) {
        $this->validators = $validators;
    }

    /**
     * @param ModelInterface $model
     * @return array
     * @throws \Exception
     */
    public function validate(ModelInterface $model)
    {
        $errors = [];
        foreach ($this->validators as $validator) {
            if (!($validator instanceof ValidatorInterface)) {
                throw new \Exception("Validator should be an instance of ".ValidatorInterface::class);
            }
            $errors = array_merge($errors, $validator->validate($model));
        }
        return $errors;
    }
}
