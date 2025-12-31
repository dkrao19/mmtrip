<?php
header('Content-Type: application/json');
session_start();

/*
 We use selected_offer or return_offer already stored
*/
$offer = $_SESSION['selected_offer'] ?? null;
if(!$offer || !isset($offer['raw'])){
  echo json_encode(['error'=>'NO_OFFER']);
  exit;
}

$raw = $offer['raw'];
$rules = [];
$baggage = [];

foreach($raw['travelerPricings'] as $tp){
  foreach($tp['fareDetailsBySegment'] as $seg){

    // Baggage
    if(isset($seg['includedCheckedBags'])){
      $baggage[] = [
        'type'=>'Checked',
        'value'=>($seg['includedCheckedBags']['weight'] ?? '') . ' ' .
                 ($seg['includedCheckedBags']['weightUnit'] ?? '')
      ];
    }

    if(isset($seg['includedCabinBags'])){
      $baggage[] = [
        'type'=>'Cabin',
        'value'=>($seg['includedCabinBags']['weight'] ?? '') . ' ' .
                 ($seg['includedCabinBags']['weightUnit'] ?? '')
      ];
    }

    // Fare rules from amenities
    if(isset($seg['amenities'])){
      foreach($seg['amenities'] as $a){
        $rules[] = [
          'label'=>$a['description'],
          'chargeable'=>$a['isChargeable'] ? 'Chargeable' : 'Included'
        ];
      }
    }
  }
}

echo json_encode([
  'baggage'=>$baggage,
  'rules'=>$rules
]);
