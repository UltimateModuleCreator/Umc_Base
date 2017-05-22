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
namespace Umc\Base\Generator;

use Umc\Base\Api\Data\ModuleInterface;

interface GeneratorInterface
{
    /**
     * file separator
     *
     * @var string
     */
    const FILE_SEPARATOR = '::';

    /**
     * default module name
     *
     * @var string
     */
    const DEFAULT_MODULE = 'Umc_Base';

    /**
     * source folder
     *
     * @var string
     */
    const SOURCE_FOLDER = 'source';

    /**
     * @param ModuleInterface $module
     * @return array
     */
    public function generate(ModuleInterface $module);

    /**
     * set file config
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);
}
