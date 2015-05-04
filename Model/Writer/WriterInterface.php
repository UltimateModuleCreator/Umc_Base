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
namespace Umc\Base\Model\Writer;

interface WriterInterface
{
    /**
     * write file to disk
     *
     * @param string $file
     * @param string $content
     * @return $this
     */
    public function write($file, $content);

    /**
     * set path to write
     *
     * @param $path
     * @return $this
     */
    public function setPath($path);

    /**
     * get the write base path
     *
     * @return string
     */
    public function getPath();
}
