<?php

require_once('paymentwall.php');
require_once('Mysql.php');
require_once('UserItemStatus.php');

Paymentwall_Base::setApiType(Paymentwall_Base::API_VC);
Paymentwall_Base::setAppKey('a9bd5cbb12bb9f763ff7bf3a6ad508b9'); // available in your Paymentwall merchant area
Paymentwall_Base::setSecretKey('ad8a0fac4b51b5d2d29e1a4efecd1657'); // available in your Paymentwall merchant area\

createWidget();

function createWidget(){
    $widget = new Paymentwall_Widget(
	'user40012', // id of the end-user who's making the payment
	'p1_1',      // widget code, e.g. p1; can be picked inside of your merchant account
	array(),     // array of products - leave blank for Virtual Currency API
	array('email' => 'user@hostname.com', 'price'=>2000, 'currency_code'=>'USD') // additional parameters
    );
    echo $widget->getHtmlCode();
}

