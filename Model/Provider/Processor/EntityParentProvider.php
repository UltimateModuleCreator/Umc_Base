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
namespace Umc\Base\Model\Provider\Processor;

use Umc\Base\Model\Core\AbstractModel;
use Umc\Base\Model\Core\Relation\Type\ParentRelation;

class EntityParentProvider extends SelfProvider implements ProviderInterface
{
    /**
     * @return \Umc\Base\Model\Core\AbstractModel[]|\Umc\Base\Model\Core\Relation[]
     */
    public function getModels()
    {
        /** @var AbstractModel|\Umc\Base\Model\Core\Entity $model */
        $model = $this->mainModel;
        return $model->getRelatedEntities(ParentRelation::RELATION_TYPE_PARENT);
    }
}
