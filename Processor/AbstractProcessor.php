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
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Provider\Processor\ProviderInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @var ProviderInterface
     */
    protected $modelProvider;

    /**
     * @var Dependency
     */
    protected $dependencyValidator;

    /**
     * @param Dependency $dependencyValidator
     * @param ProviderInterface $modelProvider
     */
    public function __construct(
        Dependency $dependencyValidator,
        ProviderInterface $modelProvider
    ) {
        $this->dependencyValidator = $dependencyValidator;
        $this->modelProvider = $modelProvider;
    }

    /**
     * @param ModelInterface $model
     * @param string $element
     * @param string $rawContent
     * @return mixed
     */
    abstract public function process(ModelInterface $model, $element, $rawContent = '');

    /**
     * @param ModelInterface $model
     * @return \Umc\Base\Api\Data\ModelInterface[]
     */
    protected function getModelsToProcess(ModelInterface $model)
    {
        return $this->modelProvider->getModels($model);
    }
}
