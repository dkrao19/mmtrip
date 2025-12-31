<?php
function applyMarkup($netPrice,$type){
  // Example logic
  $platform = 500; // flat
  $percent  = 5;   // %

  return round($netPrice + $platform + ($netPrice*$percent/100));
}
