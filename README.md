davamigo/simple-http-client
===========================

This is a simple PHP HTTP client library implemented with CURL that helps making HTTP requests.

Install
-------

The recommended way to add this library to your PHP projects is through [composer](https://getcomposer.org/).

Add the package and the repository to your **composer.json** file:
```javascript
{
    "require": {
        "davamigo/simple-http-client": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:davamigo/simple-http-client.git"
        }
    ]
}
```

Then update the dependencies:
```bash
$ php composer.phar update
```

Example
-------

```php
<?php

require_once __DIR__ . 'vendor/autoload.php';

// Use davamigo/simple-http-client library
use Davamigo\HttpClient\CurlHttpClient\CurlHttpClient;

// Create the HTTP client
$client = new CurlHttpClient();

// https/api.github.com/ requires a valid user agent
$client->setUserAgent('Mozilla/5.0');

// Create the request
$request = $client->get('https://api.github.com/repos/davamigo/simple-http-client');

// Send the request and get the response
$response = $client->send($request);

// If unsuccessful response...
if (!$response->isSuccessful()) {
    echo '<p>Status: ' . $response->getStatusCode() . '</p>';
    echo '<p>Phrase: ' . $response->getReasonPhrase() . '</p>';
    echo '<p>Body: ' . $response->getBody(true) . '</p>';
    exit(1);
}

$body = $response->getBody(true);
var_dump($body);
```
