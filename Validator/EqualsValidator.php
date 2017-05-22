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

class EqualsValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var string
     */
    protected $fieldOne;

    /**
     * @var string
     */
    protected $fieldTwo;

    /**
     * @param string $fieldOne
     * @param string $fieldTwo
     * @param string $errorMessage
     */
    public function __construct(
        $fieldOne,
        $fieldTwo,
        $errorMessage
    ) {
        $this->fieldOne = $fieldOne;
        $this->fieldTwo = $fieldTwo;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param ModelInterface $model
     * @return array
     */
    public function validate(ModelInterface $model)
    {
        $errors = [];
        if ($model->getDataUsingMethod($this->fieldOne) === $model->getDataUsingMethod($this->fieldTwo)) {
            $errors[$model->getValidationErrorKey($this->fieldTwo)][] = __($this->errorMessage, [$this->fieldOne, $this->fieldTwo]);
        }
        return $errors;
    }
}
