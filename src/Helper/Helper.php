<?php
/**
 * Helper
 *
 * @author Carlos Gartner <contato@carlosgartner.com.br>
 */

namespace Cagartner\Picpay\Helper;

use Cagartner\Picpay\Payment\PagSeguro;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\RefundRepository;
use function core;

/**
 * Class Helper
 * @package Cagartner\Picpay\Helper
 */
class Helper
{
    /**
     *
     */
    const MODULE_VERSION = '1.0.0';

    /**
     *
     */
    const STATUS_PAYED = 3;

    /**
     *
     */
    const STATUS_AVALAIBLE = 4;

    /**
     *
     */
    const STATUS_REFUNDED = 6;

    /**
     *
     */
    const STATUS_CANCELED = 7;

    /**
     *
     */
    const PAYMENT_STATUS = [
        1 => 'pending_payment',
        2 => 'pending_payment',
        3 => 'processing',
        4 => 'processing',
        5 => 'fraud',
        6 => 'closed', // refunded
        7 => 'canceled',
        8 => 'closed',
        9 => 'fraud',
    ];

    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var RefundRepository
     */
    protected $refundRepository;

    /**
     * Helper constructor.
     * @param OrderRepository $orderRepository
     * @param InvoiceRepository $invoiceRepository
     * @param RefundRepository $refundRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        RefundRepository $refundRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->refundRepository = $refundRepository;

    }

    /**
     * @param $response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrder($response)
    {
        if (core()->getConfigData(PagSeguro::CONFIG_DEBUG)) {
            Log::debug($response->reference);
            Log::debug($response->status);
        }

        /** @var \Webkul\Sales\Models\Order $order */
        if ($order = $this->orderRepository->findOneByField(['cart_id' => $response->reference])) {
            $this->orderRepository->update(['status' => self::PAYMENT_STATUS[$response->status]], $order->id);

            // If order is paid or available create the invoice
            if ($response->status === self::STATUS_PAYED || $response->status === self::STATUS_AVALAIBLE) {
                if ($order->canInvoice() && !$order->invoices->count()) {
                    $this->invoiceRepository->create($this->prepareInvoiceData($order));
                }
            }

            // Create refunds
            if ($response->status === self::STATUS_REFUNDED) {
                if ($order->canRefund()) {
                    $this->refundRepository->create($this->prepareRefundData($order));
                }
            }

            if ($response->status === self::STATUS_CANCELED) {
                if ($order->canCancel()) {
                    $this->orderRepository->cancel($order->id);
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareInvoiceData(Order $order)
    {
        $invoiceData = [
            "order_id" => $order->id,
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * @param \Webkul\Sales\Models\Order $order
     * @return array
     */
    protected function prepareRefundData(\Webkul\Sales\Models\Order $order)
    {
        $refundData = [
            "order_id" => $order->id,
            'adjustment_refund'      => $order->sub_tota,
            'base_adjustment_refund' => $order->base_sub_total,
            'adjustment_fee'         => 0,
            'base_adjustment_fee'    => 0,
            'shipping_amount'        => $order->shipping_invoiced,
            'base_shipping_amount'   => $order->base_shipping_invoiced,
        ];

        foreach ($order->items as $item) {
            $refundData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $refundData;
    }
}