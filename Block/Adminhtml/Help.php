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
namespace Umc\Base\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Umc\Base\Model\Config\Help as HelpConfig;

class Help extends GenericForm
{
    /**
     * default column type
     *
     * @var string
     */
    const DEFAULT_COLUMN_TYPE = 'text';

    /**
     * object manager reference
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Umc\Base\Model\Config\Help
     */
    protected $helpConfig;

    /**
     * construct
     *
     * @param HelpConfig $helpConfig
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        HelpConfig $helpConfig,
        ObjectManagerInterface $objectManager,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->helpConfig    = $helpConfig;
        $this->objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * get fieldset values
     *
     * @param array $fieldset
     * @return array
     */
    public function getFiedsetValues(array $fieldset)
    {
        $shared = $this->helpConfig->getBoolValue($fieldset['source'], 'shared');
        if ($shared) {
            $class = $this->objectManager->get($fieldset['source']['class']);
        } else {
            $class = $this->objectManager->create($fieldset['source']['class']);
        }
        $method = $fieldset['source']['method'];
        if (is_callable([$class, $method])) {
            return $class->$method();
        }
        return [];
    }

    /**
     * get help sections
     *
     * @return array
     */
    public function getSections()
    {
        $sections = [];
        foreach ($this->helpConfig->getConfig('section') as $sectionId => $section) {
            $fieldsets = [];
            foreach ($section['fieldset'] as $fieldsetId => $fieldsetSettings) {
                $columns = [];
                foreach ($fieldsetSettings['columns']['column'] as $columnId => $columnSettings) {
                    $columns[$columnId] = [
                        'label' => $columnSettings['label'],
                        'key'   => $columnSettings['key']
                    ];
                }
                if (count($columns)) {
                    $description = (isset($fieldsetSettings['description']) ? $fieldsetSettings['description'] : '');
                    $fieldsets[$fieldsetId] = [
                        'label' => $fieldsetSettings['label'],
                        'description' => $description,
                        'fields' => $this->getFiedsetValues($fieldsetSettings),
                        'columns' => $columns
                    ];
                }
            }
            $sections[$sectionId] = [
                'label' => $section['label'],
                'description' => (isset($section['description']) ? $section['description'] : ''),
                'fieldsets' => $fieldsets,
            ];
        }
        return $sections;
    }

    /**
     * format value
     *
     * @param $field
     * @param $column
     * @return string
     */
    public function getFormatedValue($field, $column)
    {
        if (!isset($column['type'])) {
            $column['type'] = self::DEFAULT_COLUMN_TYPE;
        }
        if (!isset($column['key'])) {
            return '';
        }
        $key = $column['key'];
        $rawValue = $field[$key];
        switch($column['type']){
            case 'bool':
                $value = (bool)(string)$rawValue;
                if ($value == 1) {
                    return __('Yes');
                }
                return __('No');
                break;
            case 'text':
                //intentional fall through
            default:
                return $rawValue;
                break;
        }
    }
}
