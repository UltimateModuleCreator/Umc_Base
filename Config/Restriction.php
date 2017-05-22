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
namespace Umc\Base\Config;

class Restriction extends Config
{
    /**
     * reserved words
     *
     * @var array
     */
    protected $reservedKeywords = [
        '__halt_compiler', 'abstract', 'and', 'array',
        'as', 'break', 'callable', 'case',
        'catch', 'class', 'clone', 'const',
        'continue', 'declare', 'default', 'die',
        'do', 'echo', 'else', 'elseif',
        'empty', 'enddeclare', 'endfor', 'endforeach',
        'endif', 'endswitch', 'endwhile', 'eval',
        'exit', 'extends', 'final', 'for',
        'foreach', 'function', 'global', 'goto',
        'if', 'implements', 'include', 'include_once',
        'instanceof', 'insteadof', 'interface', 'isset',
        'list', 'namespace', 'new', 'or',
        'print', 'private', 'protected', 'public',
        'require', 'require_once', 'return', 'static',
        'switch', 'throw', 'trait', 'try',
        'unset', 'use', 'var', 'while',
        'xor'
    ];

    /**
     * get restrictions by entity
     *
     * @param string $entityCode
     * @return array|null|string
     */
    public function getRestrictions($entityCode)
    {
        return $this->getConfig('entity/'.$entityCode.'/restriction', true, []);
    }

    /**
     * get reserved words
     *
     * @return array
     */
    public function getReservedKeywords()
    {
        return $this->reservedKeywords;
    }

    /**
     * get restricted words via getters|setters
     * @param string $class
     * @return array
     */
    public function getMagicRestrictedValues($class)
    {
        try {
            $reflection = new \ReflectionClass($class);
            $methods = $reflection->getMethods();
            $restricted = [];
            foreach ($methods as $method) {
                if (in_array(substr($method->getName(), 0, 3), ['get', 'set', 'uns', 'has'])) {
                    $code = $this->_underscore(substr($method->getName(), 3));
                    $restricted[$code] = 1;
                }
            }
            return array_keys($restricted);
        } catch (\Exception $e) {
            return [];
        }
    }
}
