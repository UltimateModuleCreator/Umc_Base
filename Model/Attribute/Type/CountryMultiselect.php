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
namespace Umc\Base\Model\Attribute\Type;

use Umc\Base\Api\Data\Attribute\TypeInterface;

class CountryMultiselect extends Country implements TypeInterface
{
    const NAME = 'country_multiselect';

    /**
     * @var string
     */
    protected $uiFormOptionV2Tag = 'multiselect';

    /**
     * @var string
     */
    const OPTION_SEPARATOR = "\n";

    /**
     * get default value
     *
     * @return mixed|string
     */
    public function getDefaultValue()
    {
        $value = $this->getAttribute()->getData(self::DEFAULT_VALUE);
        $parts = explode(self::OPTION_SEPARATOR, $value);
        return implode(',', array_map('trim', $parts));
    }
}
