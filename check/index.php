<?php
require_once __DIR__ . "/bech32.php";
require_once __DIR__ . "/base32.php";

use Base32\Base32;

const TOR_PROXY = "127.0.0.1:9050";

$_POST['wallet'] = "grin1mj6h5dslvn9y8dkg9l0xktrhruzq34g3tsq8hy937fymcnhutlxqkuf6xx";

if (array_key_exists('wallet', $_POST) === false) {
    http_response_code(400);
    exit("missing arguments");
}

$wallet = trim($_POST['wallet']);

if (strlen($wallet) !== 63) {
    http_response_code(400);
    exit("invalid arguments");
} elseif (substr($wallet, 0, 5) !== "grin1") {
    http_response_code(400);
    exit("invalid arguments");
}

// let's find the public key...
$data = decodeRaw($wallet)[1]; // bech32 decode
$decoded = convertBits($data, count($data), 5, 8, false);
$pkb = pack("C*", ...$decoded); // binary public key
// let's get the checksum
$checksum = hash("sha3-256", b".onion checksum" . $pkb . "\x03", true);
$u = unpack("C*", $checksum);
$cut = $checksum[0] . $checksum[1]; // cut a bit...
$wallet = strtolower(Base32::encode($pkb . $cut . "\x03")); // money!
if (strlen($wallet) === 56) {
    $wallet = "http://{$wallet}.onion";
}

$address = "{$wallet}/v2/foreign";

$params = json_encode([
    "jsonrpc" => "2.0",
    "id" => uniqid(),
    "method" => "check_version",
]);
$headers = [
    'Cache-Control: no-cache',
    'Pragma: no-cache',
    'Content-Type: application/json',
    'Content-Length: ' . strlen($params),
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, $address);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_PROXY, TOR_PROXY);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
curl_setopt($ch, CURLOPT_TIMEOUT, 40);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TCP_NODELAY, false);
curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);

$output = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);
http_response_code($httpcode);

if ($httpcode !== 200) {
    http_response_code(404);
    exit("not reachable");
} else {
    exit("reachable");
}
