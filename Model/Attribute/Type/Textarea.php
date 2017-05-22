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

class Textarea extends AbstractType implements TypeInterface
{
    const NAME = 'textarea';

    /**
     * @return mixed|string
     */
    public function getFormElement()
    {
        if ($this->getAttribute()->getData('editor')) {
            return 'wysiwyg';
        }
        return parent::getFormElement();
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfig()
    {
        $config =  parent::getAdditionalFormConfig();
        if ($this->getAttribute()->getData('editor')) {
            $config .= '<item name="additionalClasses" xsi:type="string">admin__field-wide</item>';
            $config .= '<item name="wysiwyg" xsi:type="boolean">true</item>';
        }
        return $config;
    }
}
