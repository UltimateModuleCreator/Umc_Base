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
namespace Umc\Base\Api\Data\Entity;

use Umc\Base\Api\Data\EntityInterface;

/**
 * @api
 */
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
     * @param EntityInterface $entity
     * @return $this
     */
    public function setEntity(EntityInterface $entity);

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
     * @param string $type
     * @return bool
     */
    public function getHasAttributeType($type);

    /**
     * check if has a type attribute
     *
     * @param string $type
     * @return bool
     */
    public function getHasAttributeTypeRequired($type);
}
