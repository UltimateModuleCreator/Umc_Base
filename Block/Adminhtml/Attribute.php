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

use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Umc\Base\Api\Data\AttributeInterface;

/**
 * @api
 */
class Attribute extends AbstractTab
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
     * @var string
     */
    const IS_TEMPLATE = 'is_template';

    /**
     * attribute instance
     *
     * @var AttributeInterface
     */
    protected $attribute;

    /**
     * prepare the form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setData('html_id_prefix', 'attribute_'.$this->getEntityId().'_'.$this->getIncrement().'_');
        $this->getForm()->addFieldNameSuffix('entity['.$this->getEntityId().'][attributes]['.$this->getIncrement().']');

        if ($this->getAttributeInstance()) {
            $this->getForm()->setValues($this->getAttributeInstance()->getData());
        } else {
            $this->getForm()->setValues($this->_scopeConfig->getValue('umc/'.$this->entityCode));
        }
        return $this;
    }

    /**
     * get the attribute instance
     *
     * @return AttributeInterface
     */
    public function getAttributeInstance()
    {
        return $this->attribute;
    }

    /**
     * set the attribute instance
     *
     * @param AttributeInterface $attribute
     * @return $this
     */
    public function setAttributeInstance(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @return mixed
     */
    public function getIncrement()
    {
        return $this->getData(self::INCREMENT);
    }

    /**
     * @return mixed
     */
    public function getIsTemplate()
    {
        return $this->getData(self::IS_TEMPLATE);
    }

    /**
     * @param string $entityId
     * @return AttributeInterface | Attribute
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }
}
