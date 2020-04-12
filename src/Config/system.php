<?php
return [
    [
        'key' => 'sales.paymentmethods.picpay',
        'name' => 'PicPay',
        'sort' => 110,
        'fields' => [
            [
                'name' => 'title',
                'title' => 'Título',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'Descrição',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'picpay_token',
                'title' => 'Picpay Token (x-picpay-token)',
                'type' => 'text',
                'validation' => 'required',
                'info' => 'Token gerado na sua conta Picpay, Pegue seu token aqui: https://lojista.picpay.com/ecommerce-token'
            ], [
                'name' => 'seller_token',
                'title' => 'Seller Token (x-seller-token)',
                'type' => 'text',
                'validation' => 'required',
                'info' => 'Token gerado na sua conta Picpay, Pegue seu token aqui: https://lojista.picpay.com/ecommerce-token'
            ],[
                'name' => 'debug',
                'title' => 'Debug log?',
                'type' => 'boolean',
                'validation' => 'required'
            ], [
                'name' => 'active',
                'title' => 'admin::app.admin.system.status',
                'type' => 'boolean',
                'validation' => 'required'
            ]
        ]
    ]
];