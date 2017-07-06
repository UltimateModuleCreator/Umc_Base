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
namespace Umc\Base\Model\Help\Section;

use Umc\Base\Block\Adminhtml\Help\Section;
use Umc\Base\Block\Adminhtml\Help\Section\FieldsetFactory;
use Umc\Base\Model\Help\Section\ReleaseNotes\FieldsetFactory as ReleaseNotesFieldsetFactory;
use Umc\Base\Model\Help\SectionInterface;
use Umc\Base\Config\ReleaseNotes as ReleaseNotestConfig;
use Umc\Base\Block\Adminhtml\Help\Section\Fieldset;

class ReleaseNotes implements SectionInterface
{
    /**
     * @var FieldsetFactory
     */
    protected $fieldsetFactory;

    /**
     * @var ReleaseNotestConfig
     */
    protected $releaseNotesConfig;

    /**
     * @var ReleaseNotesFieldsetFactory
     */
    protected $releaseNotesFieldsetFactory;

    /**
     * @var Fieldset[]
     */
    protected $fieldsets;

    /**
     * ReleaseNotes constructor.
     * @param FieldsetFactory $fieldsetFactory
     * @param ReleaseNotestConfig $releaseNotesConfig
     * @param ReleaseNotesFieldsetFactory $releaseNotesFieldsetFactory
     */
    public function __construct(
        FieldsetFactory $fieldsetFactory,
        ReleaseNotestConfig $releaseNotesConfig,
        ReleaseNotesFieldsetFactory $releaseNotesFieldsetFactory

    )
    {
        $this->fieldsetFactory = $fieldsetFactory;
        $this->releaseNotesConfig = $releaseNotesConfig;
        $this->releaseNotesFieldsetFactory = $releaseNotesFieldsetFactory;
    }

    /**
     * @param Section $section
     * @return Fieldset[]
     */
    public function getFieldsets(Section $section)
    {
        if ($this->fieldsets === null) {
            $this->fieldsets = [];
            $releaseNotes = $this->releaseNotesConfig->getConfig('module');
            foreach ($releaseNotes as $moduleName => $settings) {
                $versions = array_reverse($settings['version'], true);
                $rows = [];
                foreach ($versions as $versionName => $versionSettings) {
                    $rows[] = ['__colspan' =>  $versionName . ': '. $versionSettings['date']];
                    foreach ($versionSettings['fix'] as $fixKey => $fixSettings) {
                        $rows[] = $fixSettings;
                    }
                }
                $source = $this->releaseNotesFieldsetFactory->create(
                    [
                        'columns' => $this->getColumns(),
                        'rows' => $rows
                    ]
                );
                $fieldsetData = [
                    'label' => $moduleName,
                    'source' => $source

                ];
                $this->fieldsets[] = $this->fieldsetFactory->create([
                    'fieldsetData' => $fieldsetData,
                    'section' => $section
                ]);
            }
        }
        return $this->fieldsets;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'label' => __('Title'),
                'key' => 'title',
            ],
            [
                'label' => __('Description'),
                'key' => 'description',
            ],
            [
                'label' => __('Type'),
                'key' => 'type',
            ],
        ];
    }
}
