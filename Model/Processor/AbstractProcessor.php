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

use Umc\Base\Model\Core\AbstractModel;
use Umc\Base\Model\Core\Module;
use Umc\Base\Model\Processor\ProcessorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * current model
     *
     * @var AbstractModel
     */
    protected $model;

    /**
     * process element
     *
     * @param $element
     * @param string $rawContent
     * @return mixed
     */
    abstract public function process($element, $rawContent = '');

    /**
     * set model for processing
     *
     * @param AbstractModel $model
     * @return $this|mixed
     */
    public function setModel(AbstractModel $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * get model for processing
     *
     * @return AbstractModel
     * @throws \Exception
     */
    public function getModel()
    {
        if (is_null($this->model)) {
            throw new \Exception("Model not set for processor ". get_class($this));
        }
        return $this->model;
    }
}
