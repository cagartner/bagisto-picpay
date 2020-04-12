<?php

const PICPAY_CONTROLER = 'Cagartner\Picpay\Http\Controllers\PicpayController@';

Route::group(['middleware' => ['web']], function () {
    Route::prefix('picpay')->group(function () {
        Route::get('/pay', PICPAY_CONTROLER . 'pay')->name('picpay.pay');
        Route::post('/notify', PICPAY_CONTROLER . 'notify')->name('picpay.notify');
        Route::get('/success', PICPAY_CONTROLER . 'success')->name('picpay.success');
        Route::get('/cancel', PICPAY_CONTROLER . 'cancel')->name('picpay.cancel');
    });
});
