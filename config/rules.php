<?php

if (preg_match('/(?:www\.|)(m\.)([\da-z\.-]+)\.([a-z\.]{2,6})/i', $_SERVER['HTTP_HOST'])) {
    $rules = require(__DIR__ . '/rules.mobile.php');
} else {
    $rules = require(__DIR__ . '/rules.desktop.php');
}
return $rules;
