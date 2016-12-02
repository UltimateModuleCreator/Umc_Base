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
namespace Umc\Base\Model\Config\Converter;

use Magento\Framework\Config\ConverterInterface;
use Umc\Base\Model\Config\Mapper\FactoryInterface;

class Converter implements ConverterInterface
{
    /**
     * mapper list
     *
     * @var array
     */
    protected $mapperList;

    /**
     * nodes with ids
     *
     * @var array
     */
    protected $idNodes;

    /**
     * constructor
     *
     * @param FactoryInterface $mapperFactory
     * @param array $mapperList
     * @param array $idNodes
     */
    public function __construct(
        FactoryInterface $mapperFactory,
        array $mapperList = [],
        array $idNodes = []
    )
    {
        $this->mapperFactory = $mapperFactory;
        $this->mapperList    = $mapperList;
        $this->idNodes       = $idNodes;
    }

    /**
     * Convert dom document
     *
     * @param \DOMNode $source
     * @return array
     */
    public function convert($source)
    {
        $result = $this->convertDOMDocument($source);
        foreach ($this->mapperList as $type) {
            /** @var $mapper \Magento\Config\Model\Config\Structure\MapperInterface */
            $mapper = $this->mapperFactory->create($type);
            $result = $mapper->map($result);
        }
        return $result;
    }

    /**
     * Retrieve \DOMDocument as array
     *
     * @param \DOMNode $root
     * @return array|null
     */
    protected function convertDOMDocument(\DOMNode $root)
    {
        $result = $this->processAttributes($root);
        $children = $root->childNodes;

        $processedSubLists = [];
        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);
            $childName = $child->nodeName;

            switch ($child->nodeType) {
                case XML_COMMENT_NODE:
                    continue 2;
                    break;

                case XML_TEXT_NODE:
                    if ($children->length && trim($child->nodeValue, "\n ") === '') {
                        continue 2;
                    }
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                case XML_CDATA_SECTION_NODE:
                    $childName = 'value';
                    $convertedChild = $child->nodeValue;
                    break;

                default:
                    $convertedChild = $this->convertDOMDocument($child);
                    break;
            }
            if (in_array($childName, $processedSubLists)) {
                $result = $this->addProcessedNode($convertedChild, $result, $childName);
            } else if (array_key_exists($childName, $result)) {
                if (in_array($childName, $this->idNodes)) {
                    $key = $convertedChild['id'];
                } else {
                    $key = null;
                }
                if ($key) {
                    $result[$childName][$key] = $convertedChild;
                } else {
					try{
						$result[$childName][] = $convertedChild;
					}catch(Exception $e){
						print_r($result[$childName]);
						exit();
					}
                    
                }
                $processedSubLists[] = $childName;
            } else {
                if (in_array($childName, $this->idNodes)) {
                    $key = $convertedChild['id'];
                    $result[$childName][$key] = $convertedChild;
                } else {
                    $result[$childName] = $convertedChild;
                }
            }
        }
        if (count($result) == 1 && array_key_exists('value', $result)) {
            $result = $result['value'];
        }
        if ($result == []) {
            $result = null;
        }
        return $result;
    }

    /**
     * Add converted child with processed name
     *
     * @param array $convertedChild
     * @param array $result
     * @param string $childName
     * @return array
     */
    protected function addProcessedNode($convertedChild, $result, $childName)
    {
        $identifier = 'id';
        if (is_array($convertedChild) && array_key_exists($identifier, $convertedChild)) {
            $result[$childName][$convertedChild[$identifier]] = $convertedChild;
        } else {
            $result[$childName][] = $convertedChild;
        }
        return $result;
    }

    /**
     * Process element attributes
     *
     * @param \DOMNode $root
     * @return array
     */
    protected function processAttributes(\DOMNode $root)
    {
        $result = [];

        if ($root->hasAttributes()) {
            $attributes = $root->attributes;
            foreach ($attributes as $attribute) {
                $result[$attribute->name] = $attribute->value;
            }
            return $result;
        }
        return $result;
    }
}
