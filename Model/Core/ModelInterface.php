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
namespace Umc\Base\Model\Core;

interface ModelInterface
{
    /**
     * get entity code
     *
     * @return string
     */
    public function getEntityCode();

    /**
     * add data to entity
     *
     * @param $data
     * @return $this
     */
    public function addData(array $data);

    /**
     * get entity parent
     *
     * @return ModelInterface
     */
    public function getParent();

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders();

    /**
     * filter text
     *
     * @param string $content
     * @return string
     */
    public function filterContent($content);
}
