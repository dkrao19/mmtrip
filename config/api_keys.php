<?php
require_once __DIR__ . '/env.php';

/* =====================
   AMADEUS CONFIG
===================== */

define('AMADEUS_CLIENT_ID',     env('AMADEUS_CLIENT_ID', 'sslUAAuR5BlPxkfPqkzP3gPOtN5mj78j'));
define('AMADEUS_CLIENT_SECRET', env('AMADEUS_CLIENT_SECRET', 'RcSbHNtGIG8urjb7'));

/*
  IMPORTANT:
  Allowed values: 'production' or 'test'
*/
define('AMADEUS_ENV', env('AMADEUS_ENV', 'test'));


// Razorpay
define('RAZORPAY_KEY_ID', 'rzp_test_RxqMhVRnhmIUp1');
define('RAZORPAY_KEY_SECRET', 'm2RLZtM4QzmH20WKX0t7LlDX');

/* HOTELBEDS */
define('HOTELBEDS_API_KEY', '69869e95a8edad028a63d1915a34bbb8');
define('HOTELBEDS_SECRET', 'd95bd6ab53');

/* EXPEDIA RAPID */
define('EXPEDIA_API_KEY', 'xxxx');
define('EXPEDIA_SECRET', 'xxxx');

/* HOTEL PROVIDER SWITCH */
define('HOTEL_PROVIDER', 'HOTELBEDS'); // or EXPEDIA