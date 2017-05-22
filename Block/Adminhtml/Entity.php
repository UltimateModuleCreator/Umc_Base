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
namespace Umc\Base\Block\Adminhtml;

use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Block\Adminhtml\AttributeFactory as AttributeBlockFactory;

/**
 * @method string getIncrement()
 * @method bool getIsTemplate()
 */
/**
 * @api
 */
class Entity extends AbstractTab
{
    /**
     * @var string
     */
    const ENTITY_ID = 'entity_id';

    /**
     * @var string
     */
    const INCREMENT = 'increment';

    /**
     * @var AttributeBlockFactory
     */
    protected $attributeBlockFactory;

    /**
     * @var string
     */
    protected $attributeBlockTemplate;

    /**
     * entity instance
     *
     * @var null|\Umc\Base\Api\Data\EntityInterface
     */
    protected $entity;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FormConfig $formConfig
     * @param string $entityCode
     * @param AttributeFactory $attributeBlockFactory
     * @param string $attributeBlockTemplate
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FormConfig $formConfig,
        $entityCode,
        AttributeBlockFactory $attributeBlockFactory,
        $attributeBlockTemplate,
        array $data = []
    ) {
        $this->attributeBlockFactory  = $attributeBlockFactory;
        $this->attributeBlockTemplate = $attributeBlockTemplate;
        parent::__construct($context, $registry, $formFactory, $formConfig, $entityCode, $data);
    }

    /**
     * prepare form
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setData('html_id_prefix', 'entity_'.$this->getIncrement().'_');
        $this->getForm()->addFieldNameSuffix('entity['.$this->getIncrement().']');
        if ($this->getEntity()) {
            $this->getForm()->setValues($this->getEntity()->getData());
        } else {
            $this->getForm()->setValues(
                $this->_scopeConfig->getValue('umc/'.$this->entityCode)
            );
        }
        return $this;
    }

    /**
     * set the entity instance
     *
     * @param EntityInterface|null $entity
     * @return $this
     */
    public function setEntity(EntityInterface $entity = null)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * get entity instance
     *
     * @return null|EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * get entity attributes
     *
     * @return AttributeInterface[]
     */
    public function getAttributes()
    {
        if ($this->getEntity()) {
            return $this->getEntity()->getAttributes();
        }
        return [];
    }

    /**
     * @param string $increment
     * @param string $entityId
     * @param AttributeInterface|null $attribute
     * @return \Umc\Base\Block\Adminhtml\Attribute
     */
    public function getAttributeBlock($increment, $entityId, AttributeInterface $attribute = null)
    {
        $attributeBlock = $this->attributeBlockFactory->create();
        $attributeBlock->setTemplate($this->attributeBlockTemplate);
        $attributeBlock->setData(self::ENTITY_ID, $entityId);
        $attributeBlock->setAttributeInstance($attribute);
        $attributeBlock->setData(self::INCREMENT, $increment);
        return $attributeBlock;
    }
}
