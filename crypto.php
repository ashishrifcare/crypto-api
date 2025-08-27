<?php
// Enable CORS for all origins (for development use; restrict domain in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Update any crypto currency and mention below seperated by comma first check that coin name by hitting this url >>> https://api.coingecko.com/api/v3/coins/dogecoin >>> here change dogecoin and put your coin name if data found then put that name below inside variable coin_ids
$coin_ids = 'tether,bitcoin,ethereum,ripple,binancecoin,dogecoin,solana,shiba-inu';

$apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=$coin_ids&vs_currencies=usd&include_market_cap=true&include_24hr_vol=true&include_24hr_change=true";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!$data) {
    echo json_encode(['error' => 'Failed to fetch data'], JSON_PRETTY_PRINT);
    exit;
}

$results = [];
foreach (explode(',', $coin_ids) as $coin) {
    if (!isset($data[$coin])) {
        $results[$coin] = ['error' => 'No data found'];
        continue;
    }

    $results[$coin] = [
        'id' => $coin,
        'symbol' => strtoupper(substr($coin, 0, 3)),
        'name' => ucfirst($coin),
        'current_price' => $data[$coin]['usd'] ?? null,
        'market_cap' => $data[$coin]['usd_market_cap'] ?? null,
        'total_volume' => $data[$coin]['usd_24h_vol'] ?? null,
        'price_change_percentage_24h' => $data[$coin]['usd_24h_change'] ?? null,
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
