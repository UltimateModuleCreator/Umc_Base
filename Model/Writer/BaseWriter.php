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

use Magento\Framework\Filesystem\Io\File as IoFile;
use Umc\Base\Model\Umc;

class BaseWriter extends Umc implements WriterInterface
{
    /**
     * io access
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $io;

    /**
     * base path to write
     *
     * @var string
     */
    protected $path;

    /**
     * constructor
     *
     * @param IoFile $io
     * @param array $data
     */
    public function __construct(
        IoFile $io,
        array $data = []
    )
    {
        $this->io = $io;
        parent::__construct($data);
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * get the base write path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * write file to disk
     *
     * @param string $file
     * @param string $content
     * @return $this
     * @throws \Exception
     */
    public function write($file, $content)
    {
        try {
            $io = $this->io;
            $io->mkdir(dirname($this->path.'/'.$file));
            $io->write($this->path.'/'.$file, $content, 0777);
        } catch (\Exception $e) {
            if ($e->getCode() != 0){//TODO: Why did I put this here? check what 0 means.
                throw $e;
            }
        }
        return $this;
    }
}
