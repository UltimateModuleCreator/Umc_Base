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
interface EntityInterface extends ModelInterface
{
    const ENTITY_CODE = 'umc_entity';

    /**
     * Field constants
     */
    const LABEL_SINGULAR        = 'label_singular';
    const LABEL_PLURAL          = 'label_plural';
    const NAME_SINGULAR         = 'name_singular';
    const NAME_PLURAL           = 'name_plural';
    const TYPE                  = 'type';
    const IS_TREE               = 'is_tree';
    const ADD_CREATED_TO_GRID   = 'add_created_to_grid';
    const ADD_UPDATED_TO_GRID   = 'add_updated_to_grid';
    const INLINE_EDIT           = 'inline_edit';
    const SEARCH                = 'search';

    /**
     * @return AttributeInterface[]
     */
    public function getAttributes();

    /**
     * @param string $key
     * @param null $value
     * @return EntityInterface[]
     */
    public function setData($key, $value = null);

    /**
     * @param ModuleInterface $module
     * @return EntityInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * @param string $type
     * @return bool
     */
    public function getHasAttributeType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param bool|false $ucFirst
     * @return mixed
     */
    public function getNameSingular($ucFirst = false);

    /**
     * get entity label singular
     *
     * @param bool $ucFirst
     * @return string
     */
    public function getLabelSingular($ucFirst = false);

    /**
     * @return null|AttributeInterface
     */
    public function getNameAttribute();

    /**
     * @return bool
     */
    public function getIsTree();

    /**
     * @param AttributeInterface $attribute
     * @return EntityInterface
     */
    public function addAttribute(AttributeInterface $attribute);

    /**
     * @param bool $isTree
     * @return EntityInterface
     */
    public function setIsTree($isTree);

    /**
     * @return bool
     */
    public function getInlineEdit();

    /**
     * @param bool $inlineEdit
     * @return EntityInterface
     */
    public function setInlineEdit($inlineEdit);

    /**
     * @param string $labelSingular
     * @return EntityInterface
     */
    public function setLabelSingular($labelSingular);

    /**
     * @return string
     */
    public function getLabelPlural();

    /**
     * @param string $labelPlural
     * @return EntityInterface
     */
    public function setLabelPlural($labelPlural);

    /**
     * @param string $nameSingular
     * @return EntityInterface
     */
    public function setNameSingular($nameSingular);

    /**
     * @return string
     */
    public function getNamePlural();

    /**
     * @param string $namePlural
     * @return EntityInterface
     */
    public function setNamePlural($namePlural);

    /**
     * @param string $type
     * @return EntityInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getAddCreatedToGrid();

    /**
     * @param string $addCreatedToGrid
     * @return EntityInterface
     */
    public function setAddCreatedToGrid($addCreatedToGrid);

    /**
     * @return string
     */
    public function getAddUpdatedToGrid();

    /**
     * @param string $addUpdatedToGrid
     * @return EntityInterface
     */
    public function setAddUpdatedToGrid($addUpdatedToGrid);

    /**
     * @return string
     */
    public function getSearch();

    /**
     * @param string $search
     * @return EntityInterface
     */
    public function setSearch($search);
}
