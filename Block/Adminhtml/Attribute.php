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
namespace Umc\Base\Block\Adminhtml;

use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Umc\Base\Model\Core\Attribute as AttributeModel;

/**
 * @method int getEntityId()
 * @method string getIncrement()
 * @method bool getIsTemplate()
 * @method Attribute setEntityId()
 */
class Attribute extends AbstractTab
{
    /**
     * attribute instance
     *
     * @var \Umc\Base\Model\Core\Attribute
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
        $this->getForm()->setHtmlIdPrefix('attribute_'.$this->getEntityId().'_'.$this->getIncrement().'_');
        $this->getForm()->addFieldNameSuffix('entity['.$this->getEntityId().'][attributes]['.$this->getIncrement().']');

        if ($this->getAttributeInstance()) {
            $this->getForm()->setValues($this->getAttributeInstance()->getData());
        } else {
            $this->getForm()->setValues($this->_scopeConfig->getValue('umc/'.$this->model->getEntityCode()));
        }
        return $this;
    }

    /**
     * get the attribute instance
     *
     * @return \Umc\Base\Model\Core\Attribute
     */
    public function getAttributeInstance()
    {
        return $this->attribute;
    }

    /**
     * set the attribute instance
     *
     * @param \Umc\Base\Model\Core\Attribute $attribute
     * @return $this
     */
    public function setAttributeInstance(AttributeModel $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }
}
