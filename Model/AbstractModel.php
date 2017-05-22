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
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;

abstract class AbstractModel extends Umc implements ModelInterface
{
    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_model';

    /**
     * @var array|null
     */
    protected $placeholders;

    /**
     * save attributes config
     *
     * @var SaveAttributesConfig;
     */
    protected $saveAttributesConfig;

    /**
     * form config
     *
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * form dependencies
     *
     * @var array
     */
    protected $formDepends;

    /**
     * text escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * constructor
     *
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        array $data = []
    ) {
        $this->saveAttributesConfig = $saveAttributesConfig;
        $this->formConfig           = $formConfig;
        $this->escaper              = $escaper;
        parent::__construct($data);
    }

    /**
     * save as XML
     *
     * @param array $keys
     * @param null $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml(array $keys = [], $rootName = null, $addOpenTag = false, $addCdata = true)
    {
        if ($rootName === null) {
            $rootName = $this->getEntityCode();
        }
        if (empty($keys)) {
            $keys = $this->getXmlAttributes();
        }
        return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
    }

    /**
     * get parent entity
     *
     * @return $this
     */
    public function getParent()
    {
        return $this;
    }

    /**
     * get attributes to save
     *
     * @return array
     */
    public function getXmlAttributes()
    {
        return $this->saveAttributesConfig->getAttributes($this->getEntityCode());
    }

    /**
     * get entity code
     *
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     * set index
     *
     * @param int $index
     * @return $this
     */
    public function setIndex($index)
    {
        return $this->setData(self::INDEX, $index);
    }

    /**
     * get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->getData(self::INDEX);
    }

    /**
     * get validation error key
     *
     * @param string $field
     * @return mixed
     */
    public function getValidationErrorKey($field)
    {
        return $field;
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
     * filter content
     *
     * @param mixed $content
     * @return mixed
     */
    public function filterContent($content)
    {
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $content[$key] = $this->filterContent($value);
            }
            return $content;
        } else {
            $placeholders = $this->getPlaceholders();
            return str_replace(array_keys($placeholders), array_values($placeholders), $content);
        }
    }

    /**
     * get child models
     *
     * @return ModelInterface[]
     */
    public function getChildModels()
    {
        return [];
    }

    /**
     * get grand child models
     *
     * @return AbstractModel[]
     */
    public function getGrandChildModels()
    {
        return [];
    }
}
