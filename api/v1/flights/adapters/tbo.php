<?php
function tboSearch($payload){
  return tboRequest('/AirSearch', $payload);
}

function tboBook($payload){
  return tboRequest('/AirBook', $payload);
}
