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
namespace Umc\Base\Model\Source\Entity;

use Umc\Base\Model\Config\Type as TypeConfig;
use Umc\Base\Model\Core\Entity;
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
     * entity model
     *
     * @var Entity
     */
    protected $entityModel;

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
     * @param Entity $entityModel
     */
    public function __construct(
        TypeConfig $typeConfig,
        Entity $entityModel
    ) {
        $this->typeConfig  = $typeConfig;
        $this->entityModel = $entityModel;
    }

    /**
     * get type options as array to use in a select
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        if (is_null($this->options)) {
            $types = $this->typeConfig->getTypes($this->entityModel->getEntityCode());
            $this->options = [];
            foreach ($types as $type) {
                $this->options[] = [
                    'value' => $type['id'],
                    'label' => $type['label']
                ];
            }
        }
        $options = $this->options;
        if ($withEmpty) {
            array_unshift(
                $options,
                [
                    'value' => '',
                    'label' => __('Select Entity Type')
                ]
            );
        }
        return $options;
    }
}
