<?php
return [
    'config' => [
        'form' => [
            'umc_entity' => [
                'id' => 'umc_entity',
                'fieldset' => [
                    'name_settings' => [
                        'id'    => 'name_settings',
                        'sort'  => 10,
                        'collapsible' => true,
                        'translate' => 'label',
                        'label' => 'Name Settings',
                        'field' => [
                            'label_singular' => [
                                'id' => 'label_singular',
                                'type' => 'text',
                                'required' => true,
                                'sort' => 10,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Entity Label Singular',
                                'tooltip' => 'tooltip goes here',
                            ],
                            'label_plural' => [
                                'id' => 'label_plural',
                                'type' => 'text',
                                'required' => true,
                                'sort' => 20,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Entity Label Plural',
                                'tooltip' => 'tooltip goes here',
                            ],
                            'name_singular' => [
                                'id' => 'name_singular',
                                'type' => 'text',
                                'required' => true,
                                'sort' => 20,
                                'system' => true,
                                'translate' => 'label tooltip',
                                'label' => 'Entity Name Singular',
                                'tooltip' => 'tooltip goes here',
                            ],
                        ]
                    ],
                ]
            ]
        ]
    ]
];
