<?php
require_once __DIR__."/../../config/database.php";

$refunds = $pdo->query("
  SELECT * FROM refunds
  WHERE status='PENDING'
  AND retry_count < 3
");

foreach($refunds as $r){
  try{
    if($r['payment_mode'] === 'RAZORPAY'){
      // call Razorpay refund API
      $gatewayRefundId = 'rzp_refund_xxx';
    }
    elseif($r['payment_mode'] === 'STRIPE'){
      // call Stripe refund API
      $gatewayRefundId = 'stripe_refund_xxx';
    }
    else{
      // WALLET refund
      $pdo->prepare("
        UPDATE wallets SET balance = balance + ?
        WHERE user_id = (
          SELECT user_id FROM bookings WHERE id=?
        )
      ")->execute([$r['refund_amount'],$r['booking_id']]);

      $gatewayRefundId = 'WALLET';
    }

    $pdo->prepare("
      UPDATE refunds
      SET status='SUCCESS', gateway_refund_id=?
      WHERE id=?
    ")->execute([$gatewayRefundId,$r['id']]);

  }catch(Exception $e){
    $pdo->prepare("
      UPDATE refunds
      SET status='FAILED',
          retry_count = retry_count + 1,
          last_error = ?
      WHERE id=?
    ")->execute([$e->getMessage(),$r['id']]);
  }
}
