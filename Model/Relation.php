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
namespace Umc\Base\Model;

use Magento\Framework\Escaper;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Api\Data\Relation\TypeInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Relation\Type\Factory as RelationTypeFactory;

class Relation extends AbstractModel implements RelationInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = RelationInterface::ENTITY_CODE;

    /**
     * @var null|TypeInterface
     */
    protected $typeInstance;

    /**
     * @var ModuleInterface
     */
    protected $module;

    /**
     * @var EntityInterface
     */
    protected $entityOne;

    /**
     * @var EntityInterface
     */
    protected $entityTwo;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $reversed = false;

    /**
     * @var RelationTypeFactory
     */
    protected $typeFactory;

    /**
     * constructor
     *
     * @param RelationTypeFactory $typeFactory
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        RelationTypeFactory $typeFactory,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($saveAttributesConfig, $formConfig, $escaper, $data);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setReversed($flag)
    {
        $this->reversed = !!$flag;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @return bool
     */
    public function getReversed()
    {
        return $this->reversed;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->getData(self::REQUIRED);
    }

    /**
     * @param bool $required
     * @return RelationInterface
     */
    public function setRequired($required)
    {
        return $this->setData(self::REQUIRED, $required);
    }

    /**
     * @return bool
     */
    public function isSelfRelation()
    {
        $entities = $this->getEntities();
        return ($entities[0]->getIndex() == $entities[1]->getIndex());
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @param string $title
     * @return RelationInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @param ModuleInterface $module
     * @return $this
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return ModuleInterface
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return ModelInterface|ModuleInterface
     */
    public function getParent()
    {
        return $this->getModule();
    }

    /**
     * @param string $type
     * @return RelationInterface
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->typeInstance = null;
        //trigger associations
        $this->getTypeInstance();
        return $this;
    }

    /**
     * @param EntityInterface $entityOne
     * @param EntityInterface $entityTwo
     * @return RelationInterface|Relation
     */
    public function setEntities(EntityInterface $entityOne, EntityInterface $entityTwo)
    {
        $this->entityOne = $entityOne;
        $this->entityTwo = $entityTwo;
        return $this;
    }

    /**
     * @param int $index
     * @return null|EntityInterface
     */
    public function getEntity($index)
    {
        if ($index == 0) {
            return $this->entityOne;
        } elseif ($index == 1) {
            return $this->entityTwo;
        }
        return null;
    }

    /**
     * get relation type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * get related entities
     *
     * @return EntityInterface[]
     */
    public function getEntities()
    {
        if ($this->getReversed()) {
            return [$this->entityTwo, $this->entityOne];
        }
        return [$this->entityOne, $this->entityTwo];
    }

    /**
     * @return string
     */
    public function getUniqueKey()
    {
        $entities = $this->getEntities();
        $keyArray = [$entities[0]->getIndex(), $entities[1]->getIndex()];
        asort($keyArray);
        $keyArray[] = $this->getCode();
        return implode('-', $keyArray);
    }

    /**
     * get type instance
     *
     * @return TypeInterface
     */
    public function getTypeInstance()
    {
        if ($this->typeInstance === null) {
            $this->typeInstance = $this->typeFactory->create($this);
        }
        return $this->typeInstance;
    }

    /**
     * save as XML
     *
     * @param array $arrAttributes
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml(
        array $arrAttributes = [],
        $rootName = null,
        $addOpenTag = false,
        $addCdata = false
    ) {
        $xml = '';
        if ($rootName === null) {
            $rootName = $this->getEntityCode();
        }
        if ($rootName) {
            $xml .= '<'.$rootName.'>';
        }
        //make sure the entities are not reversed
        $reversed = $this->getReversed();
        $this->setReversed(false);
        $entities = $this->getEntities();
        $prefix = $addCdata ? '<![CDATA[' : '';
        $suffix = $addCdata ? ']]>' : '';
        $addOpenTag = false;
        $xml .= '<entity_one>'.$prefix.$entities[0]->getIndex().$suffix.'</entity_one>';
        $xml .= '<entity_two>'.$prefix.$entities[1]->getIndex().$suffix.'</entity_two>';
        $xml .= '<type>'.$prefix.$this->getType().'</type>';
        $xml .= parent::toXml($arrAttributes, '', $addOpenTag, $addCdata);
        //set back reverse
        $this->setReversed($reversed);
        if ($rootName) {
            $xml .= '</'.$rootName.'>';
        }
        return $xml;
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        $reversed = $this->getReversed();
        if (!isset($this->placeholders[(int)$reversed])) {
            $placeholders = [];
            $entities = $this->getEntities();
            $placeholders = array_merge($placeholders, $this->getTypeInstance()->getPlaceholders());
            $placeholders = array_merge($placeholders, $entities[0]->getPlaceholders());
            $placeholders = array_merge($placeholders, $entities[1]->getPlaceholdersAsSibling());
            $this->placeholders[(int)$reversed] = $placeholders;
        }
        return $this->placeholders[(int)$reversed];
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, strlen('getE1')) == 'getE1') {
            $entities = $this->getEntities();
            $key = $this->_underscore(substr($method, strlen('getE1')));
            return $entities[0]->getDataUsingMethod($key);
        }
        if (substr($method, 0, strlen('getE2')) == 'getE2') {
            $entities = $this->getEntities();
            $key = $this->_underscore(substr($method, strlen('getE2')));
            return $entities[1]->getDataUsingMethod($key);
        }
        return parent::__call($method, $args);
    }
}
