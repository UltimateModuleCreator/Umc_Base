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
namespace Umc\Base\Model\Processor;

use Umc\Base\Model\Config\ClassConfig;
use Umc\Base\Model\Core\AbstractModel;
use Umc\Base\Model\Core\Module;
use Umc\Base\Model\Provider\Processor\ProviderInterface;

class AnnotationProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * reference to the config class
     *
     * @var ClassConfig
     */
    protected $classConfig;

    /**
     * @param ClassConfig $classConfig
     * @param ProviderInterface $modelProvider
     */
    public function __construct(
        ClassConfig $classConfig,
        ProviderInterface $modelProvider
    )
    {
        $this->classConfig = $classConfig;
        parent::__construct($modelProvider);
    }

    /**
     * process element
     *
     * @param $method
     * @param string $rawContent
     * @return array|mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function process($method, $rawContent = '')
    {
        $annotations = [];
        foreach ($this->getModelsToProcess() as $model) {
            if ($model->validateDepend($method)) {
                $name = $model->filterContent($method['id']);
                $params = (isset($method['params'])) ? $method['params'] : '';
                $params = $model->filterContent($params);
                $method['return'] = $model->filterContent($method['return']);
                $annotations[$name] = ' * @method '.$method['return'].' '.$name.'('.$params.')';
            }
        }
        return $annotations;
    }

    /**
     * get models to process
     *
     * @return AbstractModel[]
     */
    protected  function getModelsToProcess()
    {
        return $this->modelProvider->setMainModel($this->getModel())->getModels();
    }
}
