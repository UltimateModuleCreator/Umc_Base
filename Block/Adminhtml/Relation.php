<?php
/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_UMC2.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright 2015 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Umc\Base\Model\Config\Type;
use Umc\Base\Model\Core\Relation as RelationModel;

class Relation extends Template
{
    /**
     * @var \Umc\Base\Model\Config\Type
     */
    protected $typeConfig;
    protected $relationInstance;

    /**
     * constructor
     *
     * @param Type $typeConfig
     * @param RelationModel $relationInstance
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Type $typeConfig,
        RelationModel $relationInstance,
        Context $context,
        array $data = []
    )
    {
        $this->relationInstance = $relationInstance;
        $this->typeConfig       = $typeConfig;
        parent::__construct($context, $data);
    }
    /**
     * @return array
     */
    public function getTypes()
    {
        $types = $this->typeConfig->getTypes($this->relationInstance->getEntityCode());
        $options = [];
        foreach ($types as $key => $settings) {
            $options[$key] = __($settings['label']);
        }
        return $options;
    }
}
