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
namespace Umc\Base\Model\Core;

use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Config\Restriction as RestrictionConfig;
use Umc\Base\Model\Config\SaveAttributes as SaveAttributesConfig;
use Umc\Base\Model\Umc;

/**
 * @method string getLicense()
 * @method bool getQualified()
 * @method bool getUnderscore()
 * @method $this setUnderscore()
 * @method bool getAnnotation()
 */
class Settings extends AbstractModel implements ModelInterface
{
    protected $directoryList;

    /**
     * constructor
     *
     * @param ManagerInterface $eventManager
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param RestrictionConfig $restrictionConfig
     * @param Escaper $escaper
     * @param DirectoryList $directoryList
     * @param array $data
     */
    public function __construct(
        ManagerInterface $eventManager,
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        RestrictionConfig $restrictionConfig,
        Escaper $escaper,
        DirectoryList $directoryList,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($eventManager, $saveAttributesConfig, $formConfig, $restrictionConfig, $escaper, $data);
    }

    /**
     * var dir name
     *
     * @var string
     */
    const VAR_DIR_NAME = 'umc';

    /**
     * folder for saved modules configuration
     *
     * @var string
     */
    const MODULES_DIR_NAME = 'modules';

    /**
     * entity code
     *
     * @var string
     */
    protected $entityCode = 'umc_settings';

    /**
     * get root path
     *
     * @return string
     */
    public function getXmlRootPath()
    {
        $base = $this->directoryList->getPath('var');
        return $base.'/'.self::VAR_DIR_NAME;
    }

    /**
     * get placeholders
     *
     * @return array|null
     */
    public function getPlaceholders()
    {
        if (is_null($this->placeholders)) {
            $this->placeholders = [
                '{{_}}' => $this->getUnderscoreValue(),
                '{{Y}}' => date('Y'),
            ];
        }
        return $this->placeholders;
    }

    /**
     * get prefix for protected methods
     *
     * @return string
     */
    public function getUnderscoreValue()
    {
        if ($this->getUnderscore()) {
            return '_';
        }
        return '';
    }
}
