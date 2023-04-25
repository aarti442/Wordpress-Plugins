<?php
require_once 'src/BeforeValidException.php';
require_once 'src/ExpiredException.php';
require_once 'src/SignatureInvalidException.php';
require_once 'src/JWT.php';

use \Firebase\JWT\JWT;
error_reporting(E_ALL);

$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA51moE9Itp4XIqBJ33wvomGzhIWUcq2kPTPj45w2qsdmR25yI
GS3qFrdKnB2pMwUd96jK1LAcF8yKZKbe9DdqdAPwzUoxOuy3G0Ha/S8uDVOHTdNl
yckBDJn0rFjc8wYRryWhKIp9cckWZkzWwBM79iA/A5o8cokRNzP+4st0dQ/wFR+b
VXJealiShNoLoxzM8xkN9ZGk0m9uaZHWkKuusj5Qn2vZY7MEaB9+hhMjtcRCGLEB
lJ3vmYYw7wD8k+NJItpD/jdJBrrGGEOPblQ+gdkCVTxOkaQ47Hq25wlCp6EWu+Nz
EKF7wynS5f2iMllNgIbossc8lz29zyouwbxa2QIDAQABAoIBACfoTlcE9X0lVcSw
Ut815ayNc5RYJcbnu4zykbUBpYVCW6e/a7a0NeIvQf6GG07CvjWfd0WTD3WHggP0
yRbljEZw+5PeDXn1pWxdQtJT1iTUWM2y/qb9NmIfGJa2SX7eeCR3YEJnCVaccnG5
JX5CkyBU1angbDbxr/eOz5P9tpwOjMvzXkqEfdEI4O/+aujNUjxv9qjLu+EAxJi4
chbH+GpThtf1Clo88e6PMuD0PbUFSLvT5CZ4klhb96wfJ8O5KC4gOH3UFpdHZGhC
R5KPyYy9uitoqF/y7rllSq9Bp2aTvYOafoWU3+zOPXtu/ORZniefsXui+tUYuXLz
2HnUJAECgYEA9UkFHq9Z0TtQNGvHOCWQKdLf1Cq0IeEtYEZfPQP3o4bGIHhUO5Mv
8U4uOyHjLuxyRKfd3O7Cp+Pul25QL6Mf3Yl224xLX6F0CJgyPRZ/Tc9ZRZ4rGmXt
jLl2OBO7HlaJWLaaxEoDp82djOTXkkGUtxJ4P4IrwQDSyaH6sRcSpVkCgYEA8XTN
yIjGl5xv1R9FKvZmUj5zK/Y+cFWVK77yrX6d6khjZXKdjcDEYOXJ1CI+KYU0a+CK
UVz94rcDAN/ZIjN6E2nqZxADxRZtZu/Zh1Af2MuD5+VP2CaoJF45O8L4e/J07Hrm
rEh/letmOT18jucKr0QN9NyKCR27FOILEXoZMYECgYEAtAYXv+Gq5inGP/MqkEbh
fBDNPobpFkSSbSq5E6spwddU6tfl2qO4eD6NJ9FDUFkxtLoP1+tU6qwbnQDV0WDE
LnCJz7E0UMfKxdQOa09rt8wLFVF1CTbuFm64GYhBuY6B156OEKrR7AK5M4NxxGJl
p8figZokSs8z5dEAkaEBimECgYA3i0sbantkvRPszvi2XWBEYyFTZ54NcWKTYaWp
y4rALk7PM4KS7Vk/gRzgs8/g9UeN2WV4OhAda0RBmzzz5Sub8618b+6uE+6u01YT
DpeE46xAWDv52UqssxAvczZ+LNRA4jTWRQc0kaY+XKqepP+MOfeL8H/AgRpcP3OU
XaYWAQKBgQDEdvNXbynm3WeEYlVQRfE/8c8bW+0oo1GmJda+ply4dQVyJL9tFIvd
8e2KAqhweVVS7cOuOhbO96mb+5sWdTZg6FHM/g5nW621NyAWr05Ewf0DvxYEFfFU
wwzsYhdOlYajFniwuMIE9XlekjVm/dpvOmjSYU8viij31R4udXmOZA==
-----END RSA PRIVATE KEY-----
EOD;

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA51moE9Itp4XIqBJ33wvo
mGzhIWUcq2kPTPj45w2qsdmR25yIGS3qFrdKnB2pMwUd96jK1LAcF8yKZKbe9Ddq
dAPwzUoxOuy3G0Ha/S8uDVOHTdNlyckBDJn0rFjc8wYRryWhKIp9cckWZkzWwBM7
9iA/A5o8cokRNzP+4st0dQ/wFR+bVXJealiShNoLoxzM8xkN9ZGk0m9uaZHWkKuu
sj5Qn2vZY7MEaB9+hhMjtcRCGLEBlJ3vmYYw7wD8k+NJItpD/jdJBrrGGEOPblQ+
gdkCVTxOkaQ47Hq25wlCp6EWu+NzEKF7wynS5f2iMllNgIbossc8lz29zyouwbxa
2QIDAQAB
-----END PUBLIC KEY-----
EOD;

$payload = array(
    "iss" => "3MVG9YDQS5WtC11ogAMGNnLhs5ONZ5lv7BTGu3ibLxoIBNHMWmHNQm80d1d7fH4SkijafMTJ5HJu2mYTmePYt",
    "aud" => "https://login.salesforce.com",
    "exp" => 1855964846,
    "sub" => "sales@biztechconsultancy.com"
);


$jwt = JWT::encode($payload, $privateKey, 'RS256');
echo "Encode:\n" . print_r($jwt, true) . "\n";

$decoded = JWT::decode($jwt, $publicKey, array('RS256'));

/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

$decoded_array = (array) $decoded;
echo "Decode:\n" . print_r($decoded_array, true) . "\n";
?>