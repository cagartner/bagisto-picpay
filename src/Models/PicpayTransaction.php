<?php

namespace Cagartner\Picpay\Models;

use Cagartner\Picpay\Contracts\PicpayTransaction as PicpayTransactionContract;
use Illuminate\Database\Eloquent\Model;
use Webkul\Checkout\Models\Cart;

/**
 * Class PicpayTransaction
 * @package Cagartner\Picpay\Models
 * @property integer $id
 * @property integer $reference_id
 * @property string $payment_url
 * @property string $qr_code
 * @property string $expires_at
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property Cart $cart
 */
class PicpayTransaction extends Model implements PicpayTransactionContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart()
    {
        return $this->hasOne(Cart::class, 'id', 'reference_id');
    }
}
