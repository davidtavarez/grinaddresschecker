<?php
if (!array_key_exists('wallet', $_POST))
{
    http_response_code(400);
    exit("missing arguments");
}

$url = rtrim(trim($_POST['wallet'], '/'));

if (strpos($url, '.onion') === false)
{
    http_response_code(400);
    exit("invalid arguments");
}

$address = "{$url}/v2/foreign";
$params = json_encode(array(
    "jsonrpc" => "2.0",
    "id" => uniqid() ,
    "method" => "check_version"
));

$ch = curl_init();
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Cache-Control: no-cache',
    'Pragma: no-cache',
    'Content-Type: application/json',
    'Content-Length: ' . strlen($params)
));
curl_setopt($ch, CURLOPT_URL, $address);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TCP_NODELAY, false);
curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);

$output = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpcode);
if ($httpcode !== 200)
{
    exit("not reachable");
}
exit("reachable");
