<?php
/*
 | Normalize Amadeus flight search response
 | Always return same structure to frontend
*/

function normalizeAmadeus(array $raw): array
{
    $results = [];

    if (empty($raw['data']) || !is_array($raw['data'])) {
        return [];
    }

    foreach ($raw['data'] as $offer) {

        if (empty($offer['itineraries'][0]['segments'][0])) {
            continue;
        }

        $segment = $offer['itineraries'][0]['segments'][0];

        $results[] = [
            'id'             => $offer['id'],
            'airline'        => $segment['carrierCode'],
            'flight_number'  => $segment['number'],
            'origin'         => $segment['departure']['iataCode'],
            'destination'    => $segment['arrival']['iataCode'],
            'departure_time' => $segment['departure']['at'],
            'arrival_time'   => $segment['arrival']['at'],
            'price'          => (float) $offer['price']['grandTotal'],
            'currency'       => $offer['price']['currency'],
            'raw'            => $offer
        ];
    }

    return $results; // ALWAYS array
}

