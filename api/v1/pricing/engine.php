<?php
function applyPricing($net){
  $platformMarkup = 500;
  $agentMarkup = $_SESSION['agent_markup'] ?? 0;
  $corporateDiscount = $_SESSION['corp_discount'] ?? 0;
  $coupon = $_SESSION['coupon'] ?? 0;
  $loyalty = $_SESSION['loyalty_points'] ?? 0;

  return max(0,
    $net
    + $platformMarkup
    + $agentMarkup
    - $corporateDiscount
    - $coupon
    - $loyalty
  );
}
