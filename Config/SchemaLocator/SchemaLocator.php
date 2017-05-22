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
namespace Umc\Base\Config\SchemaLocator;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir\Reader;

class SchemaLocator implements SchemaLocatorInterface
{

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $perFileSchema = null;

    /**
     * constructor
     *
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param string $fileSchema
     * @param null $mergedSchema
     */
    public function __construct(
        Reader $moduleReader,
        $fileSchema,
        $mergedSchema = null
    ) {
        if ($mergedSchema === null) {
            $mergedSchema = $fileSchema;
        }
        $this->schema = $moduleReader->getModuleDir('etc', 'Umc_Base') . '/umc/'.$mergedSchema.'.xsd';
        $this->perFileSchema = $moduleReader->getModuleDir('etc', 'Umc_Base') . '/umc/'.$fileSchema.'.xsd';
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
