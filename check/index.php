<?php
if (!array_key_exists('wallet', $_POST)) {
	http_response_code(404);
	exit("missing arguments");
}

$wallet = trim($_POST['wallet']);

$url = $wallet;
if (filter_var($wallet, FILTER_VALIDATE_URL) === FALSE) {
	$url = "http://{$wallet}.grinplusplus.com/";
}
$url = rtrim($url, "/");  // remove trailing slash

$address = "{$url}/v2/foreign";
$params = json_encode(array("jsonrpc" => "2.0", "id" => uniqid(), "method" => "check_version"));

$ch = curl_init();
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($params)
	)
);
curl_setopt($ch, CURLOPT_URL, $address);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9050");
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$output = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpcode);
if ($httpcode !== 200) {
	exit("not reachable");
}
exit("reachable");
