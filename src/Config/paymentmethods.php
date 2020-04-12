<?php
return [
    'picpay'  => [
        'code'              => 'picpay',
        'title'             => 'PicPay',
        'description'       => 'Pague sua compra com PicPay',
        'class'             => \Cagartner\Picpay\Payment\Picpay::class,
        'active'            => true,
        'type'              => 'redirect',
        'debug'              => false,
        'sort'              => 100,
    ],
];