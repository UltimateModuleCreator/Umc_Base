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

class Textarea extends AbstractType
{
    /**
     * get edit form type
     *
     * @return string
     */
    public function getEditFormType()
    {
        if ($this->getAttribute()->getData('editor')) {
            return 'editor';
        }
        return parent::getEditFormType();
    }

    /**
     * get addition option for edit form
     *
     * @return array
     */
    public function getAdditionalEditFormOptions()
    {
        if ($this->getAttribute()->getData('editor')) {
            $underscore = $this->getUnderscore();
            return [
                '\'config\'    => $this->'.$underscore.'wysiwygConfig->getConfig()'
            ];
        }
        return parent::getAdditionalEditFormOptions();
    }

    /**
     * attribute can be searched full text
     *
     * @var bool
     */
    protected $fullText = true;
}
