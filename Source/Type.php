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
namespace Umc\Base\Source;

use Umc\Base\Config\Type as TypeConfig;

class Type extends AbstractSource
{
    /**
     * type config
     *
     * @var TypeConfig
     */
    protected $typeConfig;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @var null|string
     */
    protected $emptyLabel;

    /**
     * options
     *
     * @var null|array
     */
    protected $options;

    /**
     * @param TypeConfig $typeConfig
     * @param string $entityCode
     * @param null $emptyLabel
     */
    public function __construct(
        TypeConfig $typeConfig,
        $entityCode,
        $emptyLabel = null
    ) {
        $this->typeConfig = $typeConfig;
        $this->entityCode = $entityCode;
        if ($emptyLabel !== null) {
            $this->emptyLabel = $emptyLabel;
        }
    }

    /**
     * get list of types as options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        if ($this->options === null) {
            $groups = $this->typeConfig->getGroups($this->entityCode);
            $types = $this->typeConfig->getTypes($this->entityCode);
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
                    'label' => __($this->emptyLabel)
                ]
            );
        }
        return $options;
    }
}
