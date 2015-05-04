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
use Umc\Base\Model\Core\Entity as EntityModel;

/**
 * @method string getIncrement()
 * @method bool getIsTemplate()
 */
class Entity extends AbstractTab
{
    /**
     * entity instance
     *
     * @var null|\Umc\Base\Model\Core\Entity
     */
    protected $entity;

    /**
     * prepare form
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setHtmlIdPrefix('entity_'.$this->getIncrement().'_');
        $this->getForm()->addFieldNameSuffix('entity['.$this->getIncrement().']');
        if ($this->getEntity()) {
            $this->getForm()->setValues($this->getEntity()->getData());
        } else {
            $this->getForm()->setValues(
                $this->_scopeConfig->getValue('umc/'.$this->model->getEntityCode())
            );
        }
        return $this;
    }

    /**
     * set the entity instance
     *
     * @param \Umc\Base\Model\Core\Entity $entity
     * @return $this
     */
    public function setEntity(EntityModel $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * get entity instance
     *
     * @return null|\Umc\Base\Model\Core\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * get entity attributes
     *
     * @return \Umc\Base\Model\Core\Attribute[]
     */
    public function getAttributes()
    {
        if ($this->getEntity()) {
            return $this->getEntity()->getAttributes();
        }
        return [];
    }
}
