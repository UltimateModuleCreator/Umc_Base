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
namespace Umc\Base\Model\Core\Attribute\Type;

use Magento\Framework\Escaper;
use Magento\Framework\Object;
use Umc\Base\Model\Core\Attribute;
use Umc\Base\Model\Umc;

class AbstractType extends Umc implements TypeInterface
{
    /**
     * string escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * constructor
     *
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Escaper $escaper,
        array $data = []
    )
    {
        $this->escaper = $escaper;
        parent::__construct($data);
    }
    /**
     * attribute is multiple select
     *
     * @var bool
     */
    protected $multi = false;

    /**
     * admin column type
     *
     * @var string
     */
    protected $adminColumnType = '';

    /**
     * setup script constant name
     *
     * @var string
     */
    protected $sqlTypeConst = '';

    /**
     * setup script length
     *
     * @var string
     */
    protected $setupLength = '';

    /**
     * edit for field type
     *
     * @var string
     */
    protected $editFormType = '';

    /**
     * attribute type has options
     *
     * @var bool
     */
    protected $hasOptions = false;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * set the attribute instance
     *
     * @param Attribute $attribute
     * @return $this|mixed
     */
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * get the attribute instance
     *
     * @return \Umc\Base\Model\Core\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * get placeholders
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isMulti()
    {
        return $this->multi;
    }

    /**
     * get admin column options
     *
     * @return string
     */
    public function getAdminColumnOptions()
    {
        return '<argument name="type" xsi:type="string">'.$this->getAdminColumnType().'</argument>';
    }

    /**
     * get admin column type
     *
     * @return string
     */
    public function getAdminColumnType()
    {
        return $this->adminColumnType;
    }

    /**
     * get sql type constant
     *
     * @return string
     */
    public function getSqlTypeConst()
    {
        return $this->sqlTypeConst;
    }

    /**
     * get sql setup length
     *
     * @return string
     */
    public function getSetupLength()
    {
        return $this->setupLength;
    }

    public function getEditFormField()
    {
        $lines = [];
        $tab = $this->getPadding();
        $eol = $this->getEol();
        $lines[] = '$fieldset->addField(';
        $lines[] = $tab.'\''.$this->getAttribute()->getCode().'\',';
        $lines[] = $tab.'\''.$this->getEditFormType().'\',';
        $lines[] = $tab.'[';
        $lines[] = $tab.$tab.'\'name\'  => \''.$this->getAttribute()->getCode().'\',';
        $lines[] = $tab.$tab.'\'label\' => __(\''.$this->getAttribute()->getLabel().'\'),';
        $lines[] = $tab.$tab.'\'title\' => __(\''.$this->getAttribute()->getLabel().'\'),';
        if ($this->getAttribute()->getRequired()) {
            $lines[] = $tab.$tab.'\'required\' => true,';
        }
        if ($note = $this->getAttribute()->getNote()) {
            $lines[] = $tab.$tab.'\'note\' => __(\''.$this->escaper->escapeJsQuote($note).'\'),';
        }
        foreach ($this->getAdditionalEditFormOptions() as $option) {
            $lines[] = $tab.$tab.$option;
        }
        $lines[] = $tab.']';
        $lines[] = ');';
        return $tab.$tab.implode($eol.$tab.$tab, $lines).$eol;
    }

    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        return [];
    }

    /**
     * get entity type for form
     *
     * @return string
     */
    public function getEditFormType()
    {
        return $this->editFormType;
    }

    /**
     * get entity
     *
     * @return \Umc\Base\Model\Core\Entity
     */
    public function getEntity()
    {
        return $this->getAttribute()->getEntity();
    }

    /**
     * get module
     *
     * @return \Umc\Base\Model\Core\Module
     */
    public function getModule()
    {
        return $this->getEntity()->getModule();
    }

    /**
     * get header class for admin grid
     *
     * @return string
     */
    public function getGridHeaderClass()
    {
        return 'col-'.str_replace('_', '-', $this->getAttribute()->getCode());
    }

    /**
     * get class for admin grid
     *
     * @return string
     */
    public function getGridColumnClass()
    {
        return $this->getGridHeaderClass();
    }

    /**
     * get underscore value for protected members
     *
     * @return string
     */
    public function getUnderscore()
    {
        return $this->getModule()->getSettings()->getUnderscoreValue();
    }

    /**
     * get default value
     *
     * @return mixed|string
     */
    public function getDefaultValue()
    {
        return $this->getAttribute()->getData('default_value');
    }

    /**
     * check if attribute has options
     *
     * @return bool
     */
    public function getHasOptions()
    {
        return $this->hasOptions;
    }
}

