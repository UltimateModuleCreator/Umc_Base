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
namespace Umc\Base\Generator;

use Umc\Base\Api\Data\ModelInterface;

class CsvGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * remove empty lines
     * sort alphabetically
     *
     * @param string $content
     * @return string
     */
    protected function postProcess($content)
    {
        $parts = explode($this->getEol(), $content);
        asort($parts);
        $parts = array_unique($parts);
        //remove empty lines
        $parts = array_diff($parts, ['', '"",""']);
        return implode($this->getEol(), $parts).$this->getEol();
    }
}
