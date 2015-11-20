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
                        ]
                    ],
                ]
            ],
            'umc_module' => [
                'id' => 'umc_module',
                'fieldset' => [
                    'general' => [
                        'id'    => 'general',
                        'sort'  => 10,
                        'collapsible' => true,
                        'translate' => 'label',
                        'label' => 'Module',
                        'field' => [
                            'namespace' => [
                                'id' => 'namespace',
                                'type' => 'text',
                                'required' => true,
                                'sort' => 10,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Module namespace',
                                'tooltip' => 'tooltip goes here',
                            ],
                        ]
                    ],
                ]
            ]
        ]
    ]
];
