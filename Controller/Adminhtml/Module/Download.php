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
namespace Umc\Base\Controller\Adminhtml\Module;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context; 
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\Decoder;
use Umc\Base\Model\Core\Settings;
use Umc\Base\Model\Downloader;

class Download extends Action
{
 
    /**
     * file factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * downloader reference
     *
     * @var \Umc\Base\Model\Downloader
     */
    protected $downloader;

    /**
     * file system reference
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * url decoder
     *
     * @var \Magento\Framework\Url\Decoder
     */
    protected $decoder;
    protected $_handle = null;

    /**
     * constructor
     *
     * @param FileFactory $fileFactory
     * @param Decoder $decoder
     * @param Downloader $downloader
     * @param Filesystem $filesystem
     * @param Context $context
     */
    public function __construct(
        FileFactory $fileFactory,
        Decoder $decoder,
        Downloader $downloader,
        Filesystem $filesystem,
        Context $context
    ) {
        $this->fileFactory           = $fileFactory;
        $this->decoder               = $decoder;
        $this->downloader            = $downloader;
        $this->filesystem            = $filesystem;
        parent::__construct($context);
    }
 
	
    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');
        $packageName = $this->decoder->decode($id);
        $downloader = $this->downloader->getDownloader($type);
        $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $file = $downloader->getRelativePath($packageName);
        $relativeFile = Settings::VAR_DIR_NAME . '/' .$file;
        $absoluteFile = $rootDir->getAbsolutePath($relativeFile);
        if ($rootDir->isFile($relativeFile) && $rootDir->isReadable($relativeFile)){
            $fileName = basename($absoluteFile);
            $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                $rootDir->stat($relativeFile)['size']
            );
			
			
			$contentType = 'application/octet-stream';

			$this->getResponse()->setHttpResponseCode(
				200
			)->setHeader(
				'Pragma',
				'public',
				true
			)->setHeader(
				'Cache-Control',
				'must-revalidate, post-check=0, pre-check=0',
				true
			)->setHeader(
				'Content-type',
				$contentType,
				true
			);
            $this->getResponse()->setHeader('Content-Length', $rootDir->stat($relativeFile)['size']);
			
			 
			$this->getResponse()->clearBody();
			$this->getResponse()->sendHeaders();
			
			
			$handle = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->openFile($relativeFile);
			
			while (true == ($buffer = $handle->read(1024))) {
				echo $buffer;
			}
		
		 
			exit();
        } else {
            $result = $this->resultRedirectFactory->create();
            $result->setPath('umc/module/index');
            $this->messageManager->addError(__('The requested file does not exist or is not readable'));
            return $result;
        }
    }
}
