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
namespace Umc\Base\Model\Core\Entity\Type;

use Umc\Base\Model\Core\Entity;

interface TypeInterface
{

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders();

    /**
     * set the entity
     *
     * @param Entity $entity
     * @return $this
     */
    public function setEntity(Entity $entity);

    /**
     * check if entity has file attributes
     *
     * @return bool
     */
    public function getHasFile();

    /**
     * check if entity has image attributes
     *
     * @return bool
     */
    public function getHasImage();

    /**
     * check if entity has date attributes
     *
     * @return bool
     */
    public function getHasDate();

    /**
     * check if entity has multi select attributes
     *
     * @return bool
     */
    public function getHasMulti();

    /**
     * check if has a type attribute
     *
     * @param $type
     * @return bool
     */
    public function getHasAttributeType($type);
}
