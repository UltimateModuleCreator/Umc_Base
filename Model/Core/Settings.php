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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @method string getLicense()
 * @method bool getQualified()
 * @method bool getUnderscore()
 * @method bool getAnnotation()
 */
class Settings extends AbstractModel implements ModelInterface
{
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
        return DirectoryList::VAR_DIR.'/'.self::VAR_DIR_NAME;
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
