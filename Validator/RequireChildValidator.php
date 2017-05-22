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

class RequireChildValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param string $errorMessage
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param ModelInterface $model
     * @return array
     */
    public function validate(ModelInterface $model)
    {
        $errors = [];
        if (count($model->getChildModels()) == 0) {
            $errors[''][] = $this->errorMessage;
        }
        return $errors;
    }
}
