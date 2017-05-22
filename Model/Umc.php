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

use Magento\Framework\DataObject;

class Umc extends DataObject
{
    /**
     * indentation
     *
     * @var string
     */
    protected $padding = '    '; //4 spaces

    /**
     * end of line
     *
     * @var string
     */
    protected $eol = "\n"; //new line

    /**
     * set indentation
     *
     * @param string $padding
     * @return $this
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * get indentation
     *
     * @param int $count
     * @return string
     */
    public function getPadding($count = 1)
    {
        return str_repeat($this->padding, $count);
    }

    /**
     * set end of line
     *
     * @param string $eol
     * @return $this
     */
    public function setEol($eol)
    {
        $this->eol = $eol;
        return $this;
    }

    /**
     * get end of line
     *
     * @return string
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * set data by path
     *
     * @param string $path
     * @param mixed $value
     * @return $this
     */
    public function setDataByPath($path, $value)
    {
        $data = &$this->_data;

        if (!is_array($path)) {
            $path = explode('/', $path);
        }
        $key = array_pop($path);
        foreach ($path as $k) {
            if (!isset($data[$k])) {
                $data[$k] = [];
            }
            $data = &$data[$k];
        }
        $data[$key ? $key : count($data)] = $value;
        return $this;
    }
}
