<?php
return [
    'config' => [
        'entity' => [
            'umc_module' => [
                'id' =>' umc_module',
                'restriction' => [
                    'namespace' => [
                        'id' => 'namespace',
                        'val' => [
                            'Magento' => [
                                'id' => 'Magento',
                                'translate' => true,
                                'real_val' => 'Magento',
                                'message' => "Don't use Magento as namespace. Be creative."
                            ]
                        ]
                    ]
                ]
            ],
            'umc_entity' => [
                'id' =>' umc_entity',
                'restriction' => [
                    'name_singular' => [
                        'id' => 'name_singular',
                        'val' => [
                            'resource' => [
                                'id' => 'resource',
                                'translate' => true,
                                'real_val' => 'resource',
                                'message' => "You cannot use this value here. It will conflict with the Magento folder structure."
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];