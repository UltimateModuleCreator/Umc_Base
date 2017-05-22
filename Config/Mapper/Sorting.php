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
namespace Umc\Base\Config\Mapper;

use Magento\Config\Model\Config\Structure\MapperInterface;
use Umc\Base\Model\UmcFactory;

class Sorting implements MapperInterface
{
    /**
     * umc object factory
     *
     * @var UmcFactory
     */
    protected $umcFactory;

    /**
     * sort field
     *
     * @var string
     */
    protected $sortField;

    /**
     * paths to sort
     *
     * @var array
     */
    protected $paths;

    /**
     * constructor
     *
     * @param UmcFactory $umcFactory
     * @param array $paths
     * @param string $sortField
     */
    public function __construct(
        UmcFactory $umcFactory,
        array $paths = [],
        $sortField = 'sort'
    ) {
        $this->umcFactory = $umcFactory;
        $this->paths      = $paths;
        $this->sortField  = $sortField;
    }

    /**
     * map elements
     *
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        /** @var \Umc\Base\Model\Umc $object */
        $object = $this->umcFactory->create();
        $object->setData($data);
        foreach ($this->paths as $path => $elements) {
            $_data = $object->getData($path);
            if (is_array($_data)) {
                foreach ($elements as $element) {
                    foreach ($_data as $key => &$values) {
                        if (isset($values[$element])) {
                            uasort($values[$element], [$this, 'cmp']);
                            $object->setDataByPath($path.'/'.$key.'/'.$element, $values[$element]);
                        }
                    }
                }
            }
        }
        return $object->getData();
    }

    /**
     * compare
     *
     * @param array $elementA
     * @param array $elementB
     * @return int
     */
    protected function cmp($elementA, $elementB)
    {
        $sortIndexA = 0;
        if (isset($elementA[$this->sortField])) {
            $sortIndexA = intval($elementA[$this->sortField]);
        }
        $sortIndexB = 0;
        if (isset($elementB[$this->sortField])) {
            $sortIndexB = intval($elementB[$this->sortField]);
        }
        if ($sortIndexA == $sortIndexB) {
            return 0;
        }
        return $sortIndexA < $sortIndexB ? -1 : 1;
    }
}
