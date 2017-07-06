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
namespace Umc\Base\Api\Data;

use Umc\Base\Api\Data\Relation\TypeInterface;

/**
 * @api
 */
interface RelationInterface extends ModelInterface
{
    const ENTITY_CODE = 'umc_relation';

    const CODE        = 'code';

    const TITLE       = 'title';

    const REQUIRED    = 'required';

    /**
     * @param ModuleInterface $module
     * @return RelationInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * @param bool $reversed
     * @return RelationInterface
     */
    public function setReversed($reversed);

    /**
     * @return bool
     */
    public function getReversed();

    /**
     * @return bool
     */
    public function getRequired();

    /**
     * @param bool $required
     * @return RelationInterface
     */
    public function setRequired($required);

    /**
     * @param string $type
     * @return RelationInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param EntityInterface $entityOne
     * @param EntityInterface $entityTwo
     * @return RelationInterface
     */
    public function setEntities(EntityInterface $entityOne, EntityInterface $entityTwo);

    /**
     * @return EntityInterface[]
     */
    public function getEntities();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return RelationInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getUniqueKey();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return TypeInterface
     */
    public function getTypeInstance();

    /**
     * @return bool
     */
    public function isSelfRelation();
}
