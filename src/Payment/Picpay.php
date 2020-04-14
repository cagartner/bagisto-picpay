<?php

namespace Cagartner\Picpay\Payment;

use Cagartner\Picpay\Helper\Helper;
use Cagartner\Picpay\Repositories\PicpayTransactionRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Payment\Payment\Payment;

/**
 * Class PagSeguro
 * @package Cagartner\Picpay\Payment
 */
class Picpay extends Payment
{
    /**
     *
     */
    const API_BASE_URI = 'https://appws.picpay.com/ecommerce/public/';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'picpay';

    /**
     * @var
     */
    protected $picpay_token;

    /**
     * @var
     */
    protected $seller_token;

    /**
     * @var PicpayTransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var array
     */
    protected $url = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * Picpay constructor.
     * @param PicpayTransactionRepository $transactionRepository
     */
    public function __construct(PicpayTransactionRepository $transactionRepository)
    {
        $this->picpay_token = $this->getConfigData('picpay_token');
        $this->seller_token = $this->getConfigData('seller_token');
        $this->transactionRepository = $transactionRepository;

        $this->client = new Client([
            'base_uri' => self::API_BASE_URI,
            'headers' => [
                'Content-Type' => 'application/json',
                'x-picpay-token' => $this->picpay_token,
            ],
            'decode_content' => false,
        ]);
    }

    /**
     * @throws Exception
     */
    public function paymentRequest()
    {
        if (!$this->picpay_token || !$this->seller_token) {
            throw new Exception('Picpay: Para usar essa opção de pagamento você precisa informar os token de pagamento!');
        }

        /** @var Cart $cart */
        $cart = $this->getCart();

        $billingAddress = $cart->getBillingAddressAttribute();
        $paymentData = [
            'referenceId' => $cart->id,
            'callbackUrl' => route('picpay.notify'),
            'returnUrl' => route('picpay.success'),
            'value' => $cart->id,
            'buyer' => [
                'firstName' => $cart->customer_first_name,
                'lastName' => $cart->customer_last_name,
                'document' => $cart->customer_first_name,
                'document' => '070.136.179-46',
                'email' => $cart->customer_email,
                'phone' => $billingAddress->phone,
            ]
        ];

        $paymentRequest = new Request('POST', 'payments', [], json_encode($paymentData));

        try {
            $response = $this->client->send($paymentRequest);
        } catch (GuzzleRequestException $e) {
            $code = -1;
            $message = 'Request Error';
            $errors = [];

            if ($response = $e->getResponse()) {
                $code = $response->getStatusCode();
                $body = json_decode((string) $response->getBody());
                $message = isset($body->message) ? $body->message : $message;
                $errors = isset($body->errors) ? $body->errors : $errors;

                throw new Exception($message, $code);
            }
        }

        if ($response->getStatusCode() === 200) {
            $responseContent = json_decode($response->getBody()->getContents());

            // Save transaction to db
            $this->createTransaction($responseContent);

            return $responseContent->paymentUrl;
        } else {
            throw new Exception('Erro ao criar transação na PicPay. Tente novamente mais tarde');
        }
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('picpay.pay');
    }

    /**
     * @param $response
     * @return \Cagartner\Picpay\Contracts\PicpayTransaction
     */
    protected function createTransaction($response) {
        $data = [
            'reference_id' => $response->referenceId,
            'payment_url' => $response->paymentUrl,
            'qr_code' => $response->qrcode->base64,
            'expires_at' => $response->expiresAt,
        ];

        return $this->transactionRepository->create($data);
    }
}