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
namespace Umc\Base\Model\Help;

use Umc\Base\Config\Form as FormConfig;

class Fields extends AbstractHelp implements HelpInterface
{
    /**
     * @var FormConfig
     */
    protected $formConfig;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var array
     */
    protected $entities;

    /**
     * @param array $columns
     * @param FormConfig $formConfig
     * @param array $entities
     */
    public function __construct(
        array $columns,
        FormConfig $formConfig,
        array $entities
    ) {
        $this->formConfig = $formConfig;
        $this->entities = $entities;
        parent::__construct($columns);
    }

    /**
     * @return array
     */
    public function getRows()
    {
        if ($this->rows === null) {
            $this->rows = [];
            foreach ($this->entities as $entity) {
                $entityCode = $entity['key'];
                $entityLabel = $entity['label'];
                $config = $this->formConfig->getConfig('form/' . $entityCode, true, []);
                if (isset($config['fieldset'])) {
                    $this->rows[] = ['__colspan' => $entityLabel];
                    foreach ($config['fieldset'] as $fieldset) {
                        $this->rows[] = ['__colspan' => $fieldset['label']];
                        foreach ($fieldset['field'] as $field) {
                            if ($field['type'] != 'hidden') {
                                $this->rows[] = [
                                    'field' => $field['label'],
                                    'type' => $field['type'],
                                    'description' => isset($field['tooltip']) ? $field['tooltip'] : ''
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $this->rows;
    }
}
