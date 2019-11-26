<?php

if(! defined('ENVIRONMENT') )
{
$domain = strtolower($_SERVER['HTTP_HOST']);

switch($domain) {
case 'www.framework.com' :
define('ENVIRONMENT', 'production');
break;

case 'frs-demo.com':
define('ENVIRONMENT', 'development');
break;
}
}
?>