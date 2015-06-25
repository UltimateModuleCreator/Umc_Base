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
namespace Umc\Base\Model\Core;

use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Config\Restriction as RestrictionConfig;
use Umc\Base\Model\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Core\Relation\Type\Factory as RelationTypeFactory;

class Relation extends AbstractModel implements ModelInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_relation';
    /**
     * @var Relation\Type\TypeInterface
     */
    protected $typeInstance;

    /**
     * @var Module
     */
    protected $module;
    protected $entityOne;
    protected $entityTwo;
    protected $type;
    protected $reversed = false;
    protected $placeholders = [];

    /**
     * constructor
     *
     * @param RelationTypeFactory $typeFactory
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        RelationTypeFactory $typeFactory,
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        parent::__construct($eventManager, $saveAttributesConfig, $formConfig, $restrictionConfig, $escaper, $data);
    }
    /**
     * @param $flag
     * @return $this
     */
    public function setReversed($flag)
    {
        $this->reversed = !!$flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReversed()
    {
        return $this->reversed;
    }

    /**
     * @param Module $module
     * @return $this
     */
    public function setModule(Module $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return $this|ModelInterface|Module
     */
    public function getParent()
    {
        return $this->getModule();
    }

    public function setEntities($entityOne, $entityTwo, $type)
    {
        $this->entityOne = $entityOne;
        $this->entityTwo = $entityTwo;
        $this->type      = $type;
        //trigger associations
        $this->getTypeInstance();
        return $this;
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
     * @return Entity[]
     */
    public function getEntities()
    {
        if ($this->getReversed()) {
            return [$this->entityTwo, $this->entityOne];
        }
        return [$this->entityOne, $this->entityTwo];
    }

    /**
     * get type instance
     *
     * @return Relation\Type\TypeInterface
     */
    public function getTypeInstance()
    {
        if (is_null($this->typeInstance)) {
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
        $rootName = 'relation',
        $addOpenTag = false,
        $addCdata = false
    ) {
        $xml = '';
        if ($rootName) {
            $xml .= '<'.$rootName.'>';
        }
        //make sure the entities are not reversed
        $reversed = $this->getReversed();
        $this->setReversed(false);
        $entities = $this->getEntities();
        $xml .= '<'.$entities[0]->getNameSingular().'_'.$entities[1]->getNameSingular().'>';
        $xml .= $this->getType();
        $xml .= '</'.$entities[0]->getNameSingular().'_'.$entities[1]->getNameSingular().'>';
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
            $this->placeholders[$reversed] = $placeholders;
        }
        return $this->placeholders[$reversed];
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

    /**
     * return array
     */
    public function getUninstallLines()
    {
        return $this->getTypeInstance()->getUninstallLines();
    }
}
