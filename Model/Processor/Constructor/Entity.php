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
namespace Umc\Base\Model\Processor\Constructor;

use Umc\Base\Model\Core\AbstractModel;
use Umc\Base\Model\Processor\Constructor\GlobalConstructor;

class Entity extends GlobalConstructor
{
    /**
     * get models to process
     *
     * @return AbstractModel[]
     */
    protected function getModelsToProcess()
    {
        return $this->getModel()->getChildModels();
    }
}
