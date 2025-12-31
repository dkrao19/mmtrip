<?php
function ndcCreateOrder($offerId,$passengers){
  $xml = "
  <OrderCreateRQ>
    <Offer OfferID='$offerId'/>
    <Passengers>".buildPassengersXML($passengers)."</Passengers>
  </OrderCreateRQ>";

  return ndcCall($xml);
}
