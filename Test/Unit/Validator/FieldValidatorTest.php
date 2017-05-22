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
namespace Umc\Base\Test\Unit\Validator;

use Magento\Framework\Phrase;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\Form;
use Umc\Base\Config\Restriction;
use Umc\Base\Validator\FieldValidator;

class FieldValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldValidator
     */
    protected $fieldValidator;

    /**
     * @var Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formConfig;

    /**
     * @var array
     */
    protected $formConfigSource;

    /**
     * @var array
     */
    protected $restrictionConfigSource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Restriction
     */
    protected $restrictionConfig;

    /**
     * set up tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formConfig               = $this->getMock(Form::class, [], [], '', false);
        $this->formConfigSource         = $this->getFormConfig();
        $this->restrictionConfigSource  = $this->getRestrictionConfig();
        $this->restrictionConfig        = $this->getMock(Restriction::class, [], [], '', false);

        $this->formConfig->method('getConfig')->willReturn($this->formConfigSource);
        $this->restrictionConfig->method('getRestrictions')
            ->willReturn($this->restrictionConfigSource);
        $this->restrictionConfig->method('getReservedKeywords')->willReturn(['public', 'protected', 'private']);
        $this->restrictionConfig->method('getMagicRestrictedValues')->willReturn(['getData']);

        $this->fieldValidator = new FieldValidator($this->formConfig, $this->restrictionConfig);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->formConfig               = null;
        $this->formConfigSource         = null;
        $this->restrictionConfigSource  = null;
        $this->restrictionConfig        = null;
        $this->fieldValidator           = null;
        parent::tearDown();
    }

    protected function getFormConfig()
    {
        return [
            'general' => [
                'id'    => 'general',
                'sort'  => 10,
                'collapsible' => true,
                'label' => 'Module',
                'field' => [
                    'namespace' => [
                        'id' => 'namespace',
                        'type' => 'text',
                        'required' => true,
                        'sort' => 10,
                        'system' => true,
                        'label' => 'Module namespace',
                        'tooltip' => 'tooltip goes here'
                    ],
                ]
            ],
        ];
    }

    /**
     * get restriction config
     * @return array
     */
    protected function getRestrictionConfig()
    {
        return [
            'namespace' => [
                'id' => 'namespace',
                'val' => [
                    'Magento' => [
                        'id' => 'Magento',
                        'translate' => true,
                        'real_val' => 'Magento',
                        'message' => "Don't use Magento as namespace. Be creative."
                    ],
                ]
            ]
        ];
    }

    /**
     * @tests FieldValidator::validate()
     * validation should pass
     */
    public function testValidateOk()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|ModelInterface $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('getEntityCode')->willReturn('umc_module');
        $model->method('getDataUsingMethod')->willReturn('Demo');
        $this->assertEquals([], $this->fieldValidator->validate($model));

    }

    /**
     * @tests FieldValidator::validate()
     * validation should fail due to incorrect namespace
     */
    public function testValidateRestriction()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|ModelInterface $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('getEntityCode')->willReturn('umc_module');
        $model->method('getDataUsingMethod')->willReturn('Magento');
        $model->method('getValidationErrorKey')->willReturn('dummy');
        $message = $this->restrictionConfigSource['namespace']['val']['Magento']['message'];
        $expected = [
            'dummy' => [$message]
        ];
        $this->assertEquals($expected, $this->fieldValidator->validate($model));
    }

    /**
     * @tests FieldValidator::validate()
     * validation should fail due to required field
     */
    public function testValidateRequired()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|ModelInterface $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('getEntityCode')->willReturn('umc_module');
        $model->method('getDataUsingMethod')->willReturn('');
        $model->method('getValidationErrorKey')->willReturn('dummy');
        $this->formConfig->method('getBoolValue')->willReturn(true);
        $expected = [
            'dummy' => [
                new Phrase('Field %1 is required', ['Module namespace'])
            ]
        ];
        $this->assertEquals($expected, $this->fieldValidator->validate($model));
    }
}
