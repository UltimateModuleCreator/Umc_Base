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
namespace Umc\Base\Model\Help\Condition;

class ModuleCondition implements ConditionInterface
{
    /**
     * @param array $dependGroupLabels
     * @return string
     */
    public function getConditionText(array $dependGroupLabels)
    {
        $returnText = '';
        if (count($dependGroupLabels) > 1) {
            $returnText .= __('The following condition are met:');
        } else {
            $returnText .= __('The following condition is met:');
        }
        $prefix = ' ';
        $suffix = '';
        $implode = __(' OR ');
        if (count($dependGroupLabels) > 1) {
            $prefix = ' ( ';
            $suffix = ' )';
            $implode = $suffix.$implode.$prefix;
        }
        $returnText .= $prefix.' '.implode($implode, $dependGroupLabels).$suffix;
        return $returnText;
    }
}
