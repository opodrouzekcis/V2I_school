<?php
// Zabbix API URL
$url = "https://czabbix/api_jsonrpc.php";

// API Token
$apiToken = "API_KLIC";

// JSON-RPC request
function sendRequest($url, $payload) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Načtení dat pomocí API
$hostItemPairs = [
    ["hostid" => "10982", "itemid" => "146463", "location" => "NH/Normalfertigung", "sensor_name" => "cissensorhe04"],
    ["hostid" => "10983", "itemid" => "146466", "location" => "NH/Krimpstationen", "sensor_name" => "cissensorhe05"],
    ["hostid" => "10984", "itemid" => "146469", "location" => "NH/MeisterBüro", "sensor_name" => "cissensorhe06"],
    ["hostid" => "10985", "itemid" => "146472", "location" => "NH/Rampe U3", "sensor_name" => "cissensorhe07"],
    ["hostid" => "10986", "itemid" => "146475", "location" => "U1/Wind_1", "sensor_name" => "cissensorhe08"],
    ["hostid" => "10986", "itemid" => "146476", "location" => "U1/Wind_2", "sensor_name" => "cissensorhe08"],
];

$itemResponses = [];
foreach ($hostItemPairs as $pair) {
    $itemPayload = [
        "jsonrpc" => "2.0",
        "method" => "item.get",
        "params" => [
            "output" => "extend", // Načti veškeré údaje
            "itemids" => $pair['itemid'], // Item ID
            "hostids" => $pair['hostid']  // Host ID
        ],
        "auth" => $apiToken, 
        "id" => 1
    ];
    $response = sendRequest($url, $itemPayload);
    if (isset($response['result']) && count($response['result']) > 0) {
        $response['result'][0]['location'] = $pair['location'];
        $response['result'][0]['sensor_name'] = $pair['sensor_name'];
    }
    $itemResponses[] = $response;
}

// Výstup - HTML
?>

<!DOCTYPE html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktuální teploty v CiS systems s.r.o.</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f3f3f3;
            margin: 0;
            padding: 20px;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            margin-top: 20px;
        }
        .logo {
            width: 150px;
            height: auto;
        }
        h1 {
            flex-grow: 1;
            font-size: 2em;
            margin: 0;
            text-align: center;
        }
        .tiles {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .tile {
            background-color: white;
            color: #333;
            padding: 20px;
            width: 200px;
            height: 200px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .tile:hover {
            transform: scale(1.05);
        }
        .tile:nth-child(even) {
            background-color: #f7f7f7;
        }
        .temperature-value {
            font-size: 2em;
            font-weight: bold;
            color: #d9534f;
            margin: 10px 0;
        }
    </style>
    <script>
        setInterval(function() {
            location.reload();
        }, 60000); // 1 minuta
    </script>
</head>
<body>
    <header>
        <img src="https://cis.de/wp-content/uploads/2020/03/BG-Logo-Positiv.svg" alt="CiS Logo" class="logo">
        <h1>Aktuální teploty v CiS systems s.r.o.</h1>
    </header>
    <div class="tiles">
        <?php
        foreach ($itemResponses as $itemResponse) {
            if (isset($itemResponse['result']) && count($itemResponse['result']) > 0) {
                foreach ($itemResponse['result'] as $item) {
                    echo "<div class='tile'>";
                    echo "<strong>LOKALITA:</strong> {$item['location']}<br>";
                    echo "<strong>TEPLOTA:</strong> <span class='temperature-value'>" . (number_format($item['lastvalue'], 1) ?? "N/A") . "&deg;C</span><br>";
                    echo "<strong>SENSOR:</strong> {$item['sensor_name']}<br>";
                    echo "</div>";
                }
            } else {
                echo "<div class='tile'>Error: " . htmlspecialchars(json_encode($itemResponse['error'] ?? "Unknown error")) . "</div>";
            }
        }
        ?>
    </div>
</body>
</html>

