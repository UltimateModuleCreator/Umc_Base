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
namespace Umc\Base\Model\Source\Attribute;

use Umc\Base\Model\Config\Type as TypeConfig;
use Umc\Base\Model\Core\Attribute;
use Umc\Base\Model\Source\AbstractSource;

class Type extends AbstractSource
{
    /**
     * type config
     *
     * @var TypeConfig
     */
    protected $typeConfig;

    /**
     * attribute model
     *
     * @var Attribute
     */
    protected $attributeModel;

    /**
     * options
     *
     * @var null|array
     */
    protected $options;

    /**
     * constructor
     *
     * @param TypeConfig $typeConfig
     * @param Attribute $attributeModel
     */
    public function __construct(
        TypeConfig $typeConfig,
        Attribute $attributeModel
    ) {
        $this->typeConfig     = $typeConfig;
        $this->attributeModel = $attributeModel;
    }

    /**
     * get list of types as options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        if (is_null($this->options)) {
            $groups = $this->typeConfig->getGroups($this->attributeModel->getEntityCode());
            $types = $this->typeConfig->getTypes($this->attributeModel->getEntityCode());
            $typesByGroup = [];
            foreach ($groups as $id => $group) {
                $typesByGroup[$id] = [];
            }
            $orphanTypes = [];

            foreach ($types as $type) {
                $element = [
                    'value' => $type['id'],
                    'label' => $type['label']
                ];
                if (isset($type['group']) && isset($typesByGroup[$type['group']])) {
                    $typesByGroup[$type['group']][] = $element;
                } else {
                    $orphanTypes[] = $element;
                }
            }
            $this->options = [];
            foreach ($typesByGroup as $id => $values) {
                $this->options[] = [
                    'label' => $groups[$id]['label'],
                    'value' => $values
                ];
            }
            foreach ($orphanTypes as $type) {
                $this->options[] = $type;
            }
        }
        $options = $this->options;
        if ($withEmpty) {
            array_unshift(
                $options,
                [
                    'value' => '',
                    'label' => __('Select Attribute Type')
                ]
            );
        }
        return $options;
    }
}
