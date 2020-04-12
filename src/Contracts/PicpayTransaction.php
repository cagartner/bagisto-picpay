<?php


namespace Cagartner\Picpay\Contracts;

use Webkul\Checkout\Models\Cart;

/**
 * Class PicpayTransaction
 * @package Cagartner\Picpay\Contracts
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
interface PicpayTransaction
{

}