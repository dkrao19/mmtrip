<?php
return [
  'flights' => [
    'amadeus' => ['enabled'=>true,'priority'=>1],
    'tbo'     => ['enabled'=>true,'priority'=>2],
    'ndc'     => ['enabled'=>false,'priority'=>3]
  ],
  'hotels' => [
    'hotelbeds' => ['enabled'=>true],
    'expedia'   => ['enabled'=>false]
  ],
  'ndc' => [
  'enabled' => true,
  'endpoint' => 'https://ndc.airline.com',
  'api_key' => 'NDC_API_KEY',
  'airlines' => ['EK','LH','SQ']
]
];
