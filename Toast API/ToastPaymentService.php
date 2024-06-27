<?php

namespace App\Services\Toast;


use App\Models\Toast\ToastPayment;
use Illuminate\Support\Facades\Log;

/**
 * Class ToastPaymentService
 * @package App\Services
 */
class ToastPaymentService
{
    /**
     * Accepts an array of payments and handles the upsert operation.
     */
    public function savePayments($payments)
    {
        $upsertBody = [];
        foreach ($payments as $payment) {
            $uniqueKey = $payment->guid . '-' . $payment->checkGuid . '-' . $payment->refundStatus;
            $upsertBody[] = [
                'check_guid' => $payment->checkGuid,
                'order_guid' => $payment->orderGuid ?? "-",
                'entity_type' => $payment->entityType,
                'payment_guid' => $payment->guid,
                'payment_entity_type' => $payment->entityType,
                'original_processing_fee' => $payment->originalProcessingFee,
                'refund_status' => $payment->refundStatus,
                'type' => $payment->type,
                'paid_date' => $payment->paidDate,
                'payment_status' => $payment->paymentStatus,
                'amount' => $payment->amount,
                'tip_amount' => $payment->tipAmount,
                'amount_tendered' => $payment->amountTendered,
                'cardType' => $payment->cardType,
                'last_4_digits' => $payment->last4Digits,
                'unique_Key' => $uniqueKey
            ];
        }

        ToastPayment::upsert(
            $upsertBody,
            ['uniqueKey'],
            [
                'check_guid',
                'order_guid',
                'entity_type',
                'payment_guid',
                'payment_entity_type',
                'original_processing_fee',
                'refund_status',
                'type',
                'paid_date',
                'payment_status',
                'amount',
                'tip_amount',
                'amount_tendered',
                'cardType',
                'last_4_digits',
            ]
        );
    }
}
