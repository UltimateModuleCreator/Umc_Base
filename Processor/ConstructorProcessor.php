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
namespace Umc\Base\Processor;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\ClassConfig;
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Provider\Processor\ProviderInterface;

class ConstructorProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @param ModelInterface $mainModel
     * @param string $element
     * @param string $rawContent
     * @return array
     */
    public function process(ModelInterface $mainModel, $element, $rawContent = '')
    {
        $constructs = [];
        foreach ($this->getModelsToProcess($mainModel) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $element)) {
                $value = $model->filterContent($element['value']);
                $constructs[$value] = $value;
            }
        }
        return $constructs;
    }
}
