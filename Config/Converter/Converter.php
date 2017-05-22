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
namespace Umc\Base\Config\Converter;

use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\ObjectManager\Config\Mapper\ArgumentParser;

class Converter implements ConverterInterface
{
    /**
     * mapper list
     *
     * @var \Magento\Config\Model\Config\Structure\MapperInterface[]
     */
    protected $mapperList;

    /**
     * nodes with ids
     *
     * @var array
     */
    protected $idNodes;

    /**
     * @param InterpreterInterface $argumentInterpreter
     * @param ArgumentParser $argumentParser
     * @param array $mapperList
     * @param array $idNodes
     */
    public function __construct(
        InterpreterInterface $argumentInterpreter,
        ArgumentParser $argumentParser,
        array $mapperList = [],
        array $idNodes = []
    ) {
        $this->argumentInterpreter  = $argumentInterpreter;
        $this->mapperList           = $mapperList;
        $this->idNodes              = $idNodes;
        $this->argumentParser       = $argumentParser;
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
        foreach ($this->mapperList as $mapper) {
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
            $isArgumentsNode = false;
            $child = $children->item($i);
            $childName = $child->nodeName;
            switch ($child->nodeType) {
                case XML_COMMENT_NODE:
                    continue 2;
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
                    if ($child->nodeName == 'arguments') {
                        $arguments = [];
                        /** @var \DOMNode $argumentNode */
                        foreach ($child->childNodes as $argumentNode) {
                            if ($argumentNode->nodeType != XML_ELEMENT_NODE) {
                                continue;
                            }
                            $argumentName = $argumentNode->attributes->getNamedItem('name')->nodeValue;
                            $argumentData = $this->argumentParser->parse($argumentNode);
                            $arguments[$argumentName] = $this->argumentInterpreter->evaluate(
                                $argumentData
                            );
                        }
                        $convertedChild = $arguments;
                        $isArgumentsNode = true;
                    } else {
                        $convertedChild = $this->convertDOMDocument($child);
                    }
                    break;
            }
            if ($isArgumentsNode) {
                $result = array_merge($result, $convertedChild);
            } elseif (in_array($childName, $processedSubLists)) {
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
                    $result[$childName][] = $convertedChild;
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
