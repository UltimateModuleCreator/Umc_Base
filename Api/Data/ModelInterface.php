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

/**
 * @api
 */
interface ModelInterface
{
    /**
     * @var string
     */
    const INDEX = 'index';

    /**
     * get entity code
     *
     * @return string
     */
    public function getEntityCode();

    /**
     * add data to entity
     *
     * @param array $data
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
     * @return int
     */
    public function getIndex();

    /**
     * @param int $index
     * @return EntityInterface
     */
    public function setIndex($index);

    /**
     * filter text
     *
     * @param string $content
     * @return string
     */
    public function filterContent($content);

    /**
     * @param array $arrAttributes
     * @param null $rootName
     * @param bool|false $addOpenTag
     * @param bool|false $addCdata
     * @return string
     */
    public function toXml(
        array $arrAttributes = [],
        $rootName = null,
        $addOpenTag = false,
        $addCdata = false
    );

    /**
     * @param string $fieldId
     * @return string
     */
    public function getValidationErrorKey($fieldId);

    /**
     * @param string $key
     * @param null $index
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @return ModelInterface[]
     */
    public function getGrandChildModels();

    /**
     * @return ModelInterface[]
     */
    public function getChildModels();

    /**
     * @param string $field
     * @return mixed
     */
    public function getDataUsingMethod($field);

    /**
     * @param string $key
     * @param mixed $value
     * @return AttributeInterface
     */
    public function setData($key, $value = null);
}
