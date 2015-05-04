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
namespace Umc\Base\Model\Config;

use Umc\Base\Model\Config;

class Type extends Config
{
    /**
     * root node
     *
     * @var string
     */
    protected $rootNode = 'types';
    /**
     * get types
     *
     * @param $code
     * @return array
     */
    public function getTypes($code)
    {
        return $this->getElements($code.'/type');
    }

    /**
     * get groups
     *
     * @param $code
     * @return array
     */
    public function getGroups($code)
    {
        return $this->getElements($code.'/group');
    }

    /**
     * get elements
     *
     * @param string $path
     * @return array
     */
    protected function getElements($path)
    {
        $configElements = $this->getConfig('entity/'.$path, true, []);
        $elements = [];
        foreach ($configElements as $element) {
            $elements[$element['id']] = $element;
        }
        return $elements;
    }
}
