EasyChaincoin-PHP
===============

A simple class for making calls to Bitcoin's API using PHP.

Getting Started
---------------
1. Include easychaincoin.php into your PHP script:

    ```php
    require_once('easychaincoin.php');
    ```
2. Initialize Bitcoin connection/object:

    ```php
    $chaincoin = new Chaincoin('username','password');
    ```

    Optionally, you can specify a host, port. Default is HTTP on localhost port 11995.

    ```php
    $chaincoin = new Chaincoin('username','password','localhost','11995');
    ```

    If you wish to make an SSL connection you can set an optional CA certificate or leave blank
    ```php
    $chaincoin->setSSL('/full/path/to/mycertificate.cert');
    ````

3. Make calls to bitcoind as methods for your object. Examples:

    ```php
    $chaincoin->getinfo();
    
    $chaincoin->getrawtransaction('0e3e2357e806b6cdb1f70b54c3a3a17b6714ee1f0e68bebb44a74b1efd512098',1);
    
    $chaincoin->getblock('000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f');
    ```

Additional Info
---------------
* When a call fails for any reason, it will return false and put the error message in `$chaincoin->error`

* The HTTP status code can be found in $bitcoin->status and will either be a valid HTTP status code or will be 0 if cURL was unable to connect.

* The full response (not usually needed) is stored in `$bitcoin->response` while the raw JSON is stored in `$chaincoin->raw_response`
