<?php
/**
 * Amadeus Adapter
 * Handles auth + flight search
 */

require_once dirname(__DIR__, 4) . '/config/api_keys.php';

/**
 * Get OAuth token from Amadeus
 */
function getAmadeusToken(): string
{
    static $token = null;
    static $expiry = 0;

    if ($token && time() < $expiry) {
        return $token;
    }

    $url = (AMADEUS_ENV === 'production')
        ? 'https://api.amadeus.com/v1/security/oauth2/token'
        : 'https://test.api.amadeus.com/v1/security/oauth2/token';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type'    => 'client_credentials',
            'client_id'     => AMADEUS_CLIENT_ID,
            'client_secret' => AMADEUS_CLIENT_SECRET
        ])
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    $json = json_decode($response, true);

    if (empty($json['access_token'])) {
        throw new Exception('Amadeus auth failed: ' . $response);
    }

    $token  = $json['access_token'];
    $expiry = time() + $json['expires_in'] - 60;

    return $token;
}

/**
 * Flight search (GET based – stable)
 */
function amadeusSearch(array $p): array
{
    $token = getAmadeusToken();

    $url = (AMADEUS_ENV === 'production')
        ? 'https://api.amadeus.com/v2/shopping/flight-offers'
        : 'https://test.api.amadeus.com/v2/shopping/flight-offers';

    $query = [
        'originLocationCode'      => $p['from'],
        'destinationLocationCode' => $p['to'],
        'departureDate'           => $p['date'],
        'adults'                  => 1,
        'currencyCode'            => 'INR',
        'max'                     => 20
    ];

    if (!empty($p['return_date'])) {
        $query['returnDate'] = $p['return_date'];
    }

    $ch = curl_init($url . '?' . http_build_query($query));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ]
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    return json_decode($response, true) ?? [];
}
