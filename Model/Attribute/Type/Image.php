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

class Image extends AbstractType implements TypeInterface
{
    const NAME = 'image';

    /**
     * @return string
     */
    public function getAdditionalFormConfig()
    {
        $entity = $this->getAttribute()->getEntity();
        $module = $entity->getModule();
        $namespace = $module->getNamespace();
        $namespaceLower = $module->getNamespace(true);
        $moduleName = $module->getModuleName();
        $moduleNameLower = $module->getModuleName(true);
        $entityName = $entity->getNameSingular();
        $config =  parent::getAdditionalFormConfig();
        $config .= '<item name="elementTmpl" xsi:type="string">ui/form/element/uploader/uploader</item>';
        $config .= '<item name="previewTmpl" xsi:type="string">'.
            $namespace.'_'.$moduleName.'/'.$this->getUploadType().'-preview</item>';
        $config .= '<item name="uploaderConfig" xsi:type="array">';
        $config .= '<item name="url" xsi:type="url" path="'.
            $namespaceLower.'_'.$moduleNameLower.'/'.$entityName.'_'.$this->getUploadType().
            '/upload/field/'.$this->getAttribute()->getCode().'"/>';
        $config .='</item>';
        return $config;
    }

    /**
     * @return string
     */
    protected function getUploadType()
    {
        return static::NAME;
    }

    /**
     * @return string
     */
    public function getAdditionalFormConfigV2()
    {
        return '<elementTmpl>ui/form/element/uploader/uploader</elementTmpl>';
    }

    /**
     * @return string
     */
    public function getUiFormOptionsV2()
    {
        $entity = $this->getAttribute()->getEntity();
        $module = $entity->getModule();
        $namespace = $module->getNamespace();
        $namespaceLower = $module->getNamespace(true);
        $moduleName = $module->getModuleName();
        $moduleNameLower = $module->getModuleName(true);
        $entityName = $entity->getNameSingular();

        $config = '<formElements><fileUploader><settings><uploaderConfig>';
        $config .= '<param xsi:type="url" name="url" path="'.
            $namespaceLower.'_'.$moduleNameLower. '/'.$entityName .'_'.
            $this->getUploadType().'/upload/field/'. $this->getAttribute()->getCode().'"/>';
        $config .= '</uploaderConfig>';
        $config .= '<previewTmpl>'.$namespace.'_'.$moduleName.'/'.$this->getUploadType().'-preview</previewTmpl>';
        $config .= '</settings></fileUploader></formElements>';
        return $config;
    }
}
