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
namespace Umc\Base\Model;

use Magento\Framework\Escaper;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ParentRelationInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\SaveAttributes as SaveAttributesConfig;

class ParentRelation extends AbstractModel implements ParentRelationInterface
{
    /**
     * @var array
     */
    protected $placeholders;

    /**
     * @var RelationInterface
     */
    protected $relation;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * ParentRelation constructor.
     * @param SaveAttributesConfig $saveAttributesConfig
     * @param FormConfig $formConfig
     * @param Escaper $escaper
     * @param EntityInterface $entity
     * @param RelationInterface $relation
     * @param array $data
     */
    public function __construct(
        SaveAttributesConfig $saveAttributesConfig,
        FormConfig $formConfig,
        Escaper $escaper,
        EntityInterface $entity,
        RelationInterface $relation,
        array $data = []
    ) {
        $this->relation = $relation;
        $this->entity = $entity;
        parent::__construct($saveAttributesConfig, $formConfig, $escaper, $data);
    }

    /**
     * @return array
     */
    public function getPlaceholders()
    {
        if ($this->placeholders === null) {
            $this->placeholders = [
                '{{relation_column_code}}'      => $this->getRelationColumnCode(),
                '{{relationColumnCamelCase}}'   => $this->getRelationCodeCamelCase(),
                '{{RelationColumnCamelCase}}'   => $this->getRelationCodeCamelCase(true),
                '{{RELATION_COLUMN}}'           => strtoupper($this->getRelationColumnCode()),
                '{{relation_label}}'            => $this->relation->getTitle(),
                '{{RelatedEntity}}'             => $this->getRelatedEntity()->getNameSingular(true),
                '{{form_validation_v1}}'        => $this->getFormValidationV1(),
                '{{form_validation_v2}}'        => $this->getFormValidationV2(),
                '{{grid_editor_v1}}'            => $this->getGridEditorOptionsV1(),
                '{{grid_editor_v2}}'            => $this->getGridEditorOptionsV2(),
            ];
            $this->placeholders = array_merge($this->entity->getPlaceholders(), $this->placeholders);
        }
        return $this->placeholders;
    }

    /**
     * @return string
     */
    protected function getRelationColumnCode()
    {
        $relatedEntity = $this->getRelatedEntity();
        $relationCode = $this->relation->getCode();
        $code = ($relationCode) ? $relationCode . '_' : '';
        $code .= $relatedEntity->getNameSingular().'_id';
        return $code;
    }

    /**
     * @return EntityInterface
     */
    protected function getRelatedEntity()
    {
        $relationEntities = $this->relation->getEntities();
        if ($this->entity->getNameSingular() == $relationEntities[0]->getNameSingular()) {
            return $relationEntities[1];
        }
        return $relationEntities[0];
    }

    /**
     * @return string
     */
    protected function getRelationLabel()
    {
        return $this->relation->getTitle();
    }

    /**
     * @return int
     */
    public function getUiVersion()
    {
        return $this->entity->getModule()->getUiVersion();
    }

    /**
     * @return string
     */
    protected function getFormValidationV1()
    {
        $content = '';
        if ($this->relation->getRequired()) {
            $content .= '<item name="validation" xsi:type="array">';
            $content .= '<item name="required-entry" xsi:type="boolean">true</item>';
            $content .= '</item>';
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function getFormValidationV2()
    {
        $content = '';
        if ($this->relation->getRequired()) {
            $content .= '<validation>';
            $content .= '<rule name="required-entry" xsi:type="boolean">true</rule>';
            $content .= '</validation>';
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function getGridEditorOptionsV1()
    {
        $content = '';
        if ($this->entity->getInlineEdit()) {
            $content .= '<item name="editor" xsi:type="array">';
            $content .= '<item name="editorType" xsi:type="string">select</item>';
            if ($this->relation->getRequired()) {
                $content .= '<item name="validation" xsi:type="array">';
                $content .= '<item name="required-entry" xsi:type="boolean">true</item>';
                $content .= '</item>';
            }
            $content .= '</item>';

        }
        return $content;
    }

    /**
     * @return string
     */
    protected function getGridEditorOptionsV2()
    {
        $content = '';
        if ($this->entity->getInlineEdit()) {
            $content .= '<editor>';
            $content .= '<editorType>select</editorType>';
            if ($this->relation->getRequired()) {
                $content .= '<validation>';
                $content .= '<rule name="required-entry" xsi:type="boolean">true</rule>';
                $content .= '</validation>';
            }
            $content .= '</editor>';
        }
        return $content;
    }

    /**
     * get camel case code
     *
     * @param bool $toUpper
     * @return string
     */
    protected function getRelationCodeCamelCase($toUpper = false)
    {
        $code = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getRelationColumnCode())));
        if ($toUpper) {
            return $code;
        }
        return lcfirst($code);
    }
}
