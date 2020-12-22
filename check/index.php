<?php
function validate_url($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    $encoded_path = array_map('urlencode', explode('/', $path));
    $url = str_replace($path, implode('/', $encoded_path) , $url);

    return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
}

if (array_key_exists('wallet', $_POST) === false)
{
    http_response_code(400);
    exit("missing arguments");
}

$url = rtrim(trim($_POST['wallet'], '/'));

if (validate_url($url === false))
{
    http_response_code(400);
    exit("invalid arguments");
}

if (strpos($url, "grinplusplus.com") !== false)
{
    $url = str_replace(".grinplusplus.com", ".onion", $url);
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
    http_response_code(404);
    exit("not reachable");
}
else
{
    exit("reachable");
}
