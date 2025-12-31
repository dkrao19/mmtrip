<?php
function ndcOfferPrice($offerId){
  $xml = "
  <OfferPriceRQ>
    <Offer OfferID='$offerId'/>
  </OfferPriceRQ>";

  return ndcCall($xml);
}
