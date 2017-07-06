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
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Block\Adminhtml;

use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;

/**
 * @method string getIncrement()
 * @method bool getIsTemplate()
 */

/**
 * @api
 */
class Relation extends AbstractTab
{

    /**
     * @var RelationInterface|null
     */
    protected $relation;

    /**
     * @return Relation
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setData('html_id_prefix', 'relation_'.$this->getIncrement().'_');
        $this->getForm()->addFieldNameSuffix('relation['.$this->getIncrement().']');
        if ($this->getRelation()) {
            $this->getForm()->setValues($this->getRelation()->getData());
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
     * @param RelationInterface|null $relation
     * @return Relation
     */
    public function setRelation(RelationInterface $relation = null)
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * get entity instance
     *
     * @return null|RelationInterface
     */
    public function getRelation()
    {
        return $this->relation;
    }
}
