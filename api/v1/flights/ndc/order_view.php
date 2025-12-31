<?php
function ndcOrderView($orderId){
  $xml = "<OrderViewRQ OrderID='$orderId'/>";
  return ndcCall($xml);
}
