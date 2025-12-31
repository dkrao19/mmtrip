<?php
require_once "../../config/database.php";
$config = require "../../config/payments.php";

$data = json_decode(file_get_contents("php://input"),true);

// choose gateway
if ($data['gateway']=='RAZORPAY' && $config['razorpay']['enabled']) {
  // create razorpay order
}

if ($data['gateway']=='STRIPE' && $config['stripe']['enabled']) {
  // create stripe intent
}

echo json_encode(["status"=>"initiated"]);
