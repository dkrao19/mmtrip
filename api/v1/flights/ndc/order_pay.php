<?php
function ndcPayOrder($orderId,$amount){
  $xml = "
  <OrderPayRQ>
    <Order OrderID='$orderId'/>
    <Amount>$amount</Amount>
  </OrderPayRQ>";

  return ndcCall($xml);
}
