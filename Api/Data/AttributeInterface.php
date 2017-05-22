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

use Umc\Base\Api\Data\Attribute\TypeInterface;

/**
 * @api
 */
interface AttributeInterface extends ModelInterface
{
    /**
     * @var string
     */
    const ENTITY_CODE       = 'umc_attribute';

    /**
     * @var string
     */
    const NOTE              = 'note';

    /**
     * @var string
     */
    const TYPE              = 'type';

    /**
     * @var string
     */
    const IS_NAME           = 'is_name';

    /**
     * @var string
     */
    const INLINE_EDIT       = 'inline_edit';

    /**
     * @var string
     */
    const ADMIN_GRID_FILTER = 'admin_grid_filter';

    /**
     * @var string
     */
    const CODE              = 'code';

    /**
     * @var string
     */
    const LABEL             = 'label';

    /**
     * @var string
     */
    const ADMIN_GRID        = 'admin_grid';

    /*
     * @var string
     */
    const REQUIRED          = 'required';

    /**
     * @var string
     */
    const OPTIONS           = 'options';

    /**
     * @var int
     */
    const POSITION          = 'position';

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function getRequired();

    /**
     * @param bool $required
     * @return AttributeInterface
     */
    public function setRequired($required);

    /**
     * @return string
     */
    public function getColumnSetup();

    /**
     * @param EntityInterface $entity
     * @return AttributeInterface
     */
    public function setEntity(EntityInterface $entity);

    /**
     * @return bool
     */
    public function getIsName();

    /**
     * @param string $isName
     * @return AttributeInterface
     */
    public function setIsName($isName);

    /**
     * @param string $code
     * @return AttributeInterface
     */
    public function setCode($code);

    /**
     * @param string $label
     * @return AttributeInterface
     */
    public function setLabel($label);

    /**
     * @param string $type
     * @return AttributeInterface
     */
    public function setType($type);

    /**
     * @return bool
     */
    public function getAdminGridFilter();

    /**
     * @param string $adminGridFilter
     * @return AttributeInterface
     */
    public function setAdminGridFilter($adminGridFilter);

    /**
     * @return bool
     */
    public function getInlineEdit();

    /**
     * @return bool
     */
    public function getAdminGrid();

    /**
     * @param string $adminGrid
     * @return AttributeInterface
     */
    public function setAdminGrid($adminGrid);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param string $note
     * @return AttributeInterface
     */
    public function setNote($note);

    /**
     * @return EntityInterface
     */
    public function getEntity();

    /**
     * @param bool|false $toUpper
     * @return mixed
     */
    public function getCodeCamelCase($toUpper = false);

    /**
     * @return TypeInterface
     */
    public function getTypeInstance();

    /**
     * @return bool
     */
    public function getFullText();

    /**
     * @return string
     */
    public function getDefaultValueProcessed();

    /**
     * @return string
     */
    public function getAdditionalFormConfig();

    /**
     * @return string
     */
    public function getAdditionalFormConfigV2();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param string $position
     * @return int AttributeInterface
     */
    public function setPosition($position);

    /**
     * @param string $options
     * @return AttributeInterface
     */
    public function setOptions($options);

    /**
     * @return string
     */
    public function getOptions();
}
