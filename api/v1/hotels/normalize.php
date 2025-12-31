<?php
function normalizeHotel($provider,$hotel){
  return [
    'provider'=>$provider,
    'hotel_name'=>$hotel['name'] ?? '',
    'price'=>$hotel['price']['net'] ?? 0,
    'currency'=>$hotel['currency'] ?? 'USD',
    'rating'=>$hotel['categoryCode'] ?? ''
  ];
}
