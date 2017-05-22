<?php
namespace Umc\Base\Writer;

use Magento\Framework\App\Filesystem\DirectoryList;

class Filesystem
{
    /**
     * var dir name
     *
     * @var string
     */
    const VAR_DIR_NAME = 'umc';

    /**
     * @var string
     */
    const MODULES_DIR_NAME = 'modules';

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    /**
     * @return string
     */
    public function getXmlRootPath()
    {
        $base = $this->directoryList->getPath('var');
        return $base.'/'.self::VAR_DIR_NAME;
    }
}
