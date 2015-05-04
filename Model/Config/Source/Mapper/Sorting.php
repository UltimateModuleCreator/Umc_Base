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
namespace Umc\Base\Model\Config\Source\Mapper;

use Umc\Base\Model\Config\Mapper\Sorting as AbstractSorting;

class Sorting extends AbstractSorting
{
    /**
     * map elements
     *
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        foreach ($data['source']['file'] as &$element) {
            $element = $this->processConfig($element);
        }
        uasort($data['source']['file'], [$this, 'cmp']);
        return $data;
    }

    /**
     * process config
     *
     * @param $data
     * @return mixed
     */
    protected function processConfig($data)
    {

        if (isset($data['code']['part'])) {
            uasort($data['code']['part'], [$this, 'cmp']);
        }
        if (isset($data['annotations']['annotation'])) {
            uasort($data['annotations']['annotation'], [$this, 'cmp']);
        }
        if (isset($data['members']['member'])) {
            uasort($data['members']['member'], [$this, 'cmp']);
        }
        if (isset($data['constructs']['construct'])) {
            uasort($data['constructs']['construct'], [$this, 'cmp']);
        }
        if (isset($data['implements']['implement'])) {
            uasort($data['implements']['implement'], [$this, 'cmp']);
        }
        return $data;
    }
}
