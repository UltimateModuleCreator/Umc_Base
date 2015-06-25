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
namespace Umc\Base\Model\Generator\Xml;

use Umc\Base\Model\Generator\XmlGenerator;
use Umc\Base\Model\Generator\GeneratorInterface;

class SiblingsGenerator extends XmlGenerator implements GeneratorInterface
{
    /**
     * default scope
     *
     * @var string
     */
    protected $defaultScope = 'siblings';

    /**
     * get models for processors
     *
     * @return \Umc\Base\Model\Core\AbstractModel[]|\Umc\Base\Model\Core\Relation[]
     */
    protected function getModelsForProcessor()
    {
        $relations = [];
        $module = $this->module;
        foreach ($module->getRelations() as $relation) {
            if ($relation->getType() == \Umc\Base\Model\Core\Relation\Type\SiblingRelation::RELATION_TYPE_SIBLING) {
                $relation->setReversed(false);
                $relations[] = $relation;
                $clone = clone $relation;
                $clone->setReversed(true);
                $relations[] = $clone;
            }
        }
        return $relations;
    }

}
