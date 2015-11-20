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
namespace Umc\Base\Model\Core\Attribute\Type;

class CountryMultiselect extends AbstractType
{
    /**
     * @var string
     */
    const OPTION_SEPARATOR = "\n";

    /**
     * get additional options for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        $options = parent::getAdditionalEditFormOptions();
        $underscore = $this->getUnderscore();
        $options[] = '\'values\' => $this->'.$underscore.'countryOptions->toOptionArray(),';
        return $options;
    }

    /**
     * get default value
     *
     * @return mixed|string
     */
    public function getDefaultValue()
    {
        $value = $this->getAttribute()->getData('default_value');
        $parts = explode(self::OPTION_SEPARATOR, $value);
        return implode(',', array_map('trim', $parts));
    }
}
