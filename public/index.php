<?php

require __DIR__.'/../vendor/autoload.php';

use fweber\Proxy\Host;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

$http = HttpClient::create();

$request = Request::createFromGlobals();
$base = $request->server->get('BASE');
$regex = "/".str_replace("/", "\/", $base)."\/([a-z]+)\/.*/";

preg_match($regex, $request->getRequestUri(), $matches);

if(count($matches) == 0) {
    throw new Exception(sprintf('no matches found for uri %s', $request->getRequestUri()));
}

$host = Host::fromConfig($matches[1]);

if(!$host) {
    throw new Exception(sprintf('no host found for id %s', $matches[1]));
}

$targetUri = str_replace($base.'/'.$host->id, '', $request->getRequestUri());
$targetHost = parse_url($host->target, PHP_URL_HOST);

$headers = [];

foreach ($request->headers->all() as $key => $value) {
    if($key == 'host') {
        continue;
    }

    $headers[$key] = $value[0];
}

$headers['host'] = $targetHost;

$response = $http->request($request->getMethod(), $host->target.$targetUri, [
    'headers' => $headers,
    'body' => $request->getContent(),
]);

foreach ($response->getHeaders() as $key => $header) {
    header($key.': '.$header[0]);
}

echo $response->getContent();
