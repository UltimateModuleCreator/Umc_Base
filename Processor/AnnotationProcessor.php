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

class AnnotationProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @param ModelInterface $mainModel
     * @param string $method
     * @param string $rawContent
     * @return array
     */
    public function process(ModelInterface $mainModel, $method, $rawContent = '')
    {
        $annotations = [];
        foreach ($this->getModelsToProcess($mainModel) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $method)) {
                $name = $model->filterContent($method['id']);
                $params = (isset($method['params'])) ? $method['params'] : '';
                $params = $model->filterContent($params);
                $method['return'] = $model->filterContent($method['return']);
                $annotations[$name] = ' * @method '.$method['return'].' '.$name.'('.$params.')';
            }
        }
        return $annotations;
    }
}
