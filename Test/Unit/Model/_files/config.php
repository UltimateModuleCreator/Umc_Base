<?php
return [
    'config' => [
        'form' => [
            'umc_settings' => [
                'id' => 'umc_settings',
                'fieldset' => [
                    'settings' => [
                        'id'    => 'settings',
                        'sort'  => 10,
                        'collapsible' => true,
                        'translate' => 'label',
                        'label' => 'Module settings',
                        'field' => [
                            'qualified' => [
                                'id' => 'qualified',
                                'type' => 'select',
                                'required' => true,
                                'sort' => 10,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Fully qualified class names',
                                'tooltip' => 'tooltip goes here',
                                'source' => 'Magento\Config\Model\Config\Source\Yesno'
                            ],
                            'underscore' => [
                                'id' => 'underscore',
                                'type' => 'select',
                                'required' => true,
                                'sort' => 20,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Use underscore',
                                'tooltip' => 'tooltip goes here',
                                'source' => 'Magento\Config\Model\Config\Source\Yesno'
                            ],
                            'disabled' => [
                                'id' => 'disabled',
                                'type' => 'text',
                                'required' => true,
                                'sort' => 30,
                                'system' => true,
                                'translate' => 'label',
                                'label' => 'Disabled field',
                                'disabled' => true
                            ]
                        ]
                    ],
                    'disabled' => [
                        'id'    => 'disabled',
                        'sort'  => 20,
                        'collapsible' => true,
                        'translate' => 'label',
                        'label' => 'Disabled fieldset',
                        'disabled' => 'true',
                        'field' => [
                            'some_field' => [
                                'id' => 'some_field',
                                'type' => 'select',
                                'required' => true,
                                'sort' => 10,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Some field',
                                'tooltip' => 'tooltip goes here',
                                'source' => 'Magento\Config\Model\Config\Source\Yesno'
                            ],
                        ]
                    ]
                ]
            ]
        ]
    ]
];
