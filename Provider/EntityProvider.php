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
namespace Umc\Base\Provider;

use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Api\Data\ModelInterface;

class EntityProvider extends GlobalProvider implements ProviderInterface
{
    /**
     * @param ModelInterface $model
     * @return \Umc\Base\Api\Data\EntityInterface[]
     */
    public function getModels(ModelInterface $model)
    {
        /** @var ModelInterface|ModuleInterface $model */
        return $model->getEntities();
    }
}
