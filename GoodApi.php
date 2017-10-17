<?php

require_once('paymentwall.php');
require_once('Mysql.php');
require_once('UserItemStatus.php');

Paymentwall_Base::setApiType(Paymentwall_Base::API_GOODS);
Paymentwall_Base::setAppKey('8ec04f96a62ec4c7a0f13ceccee638ac'); // available in your Paymentwall merchant area
Paymentwall_Base::setSecretKey('adb7497cc888e5c1547dded7aa9ca0d7'); // available in your Paymentwall merchant area\
handlePingBack();

function handlePingBack() {
    $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
    if ($pingback->validate()) {
        echo 'OK';
        $productId = $pingback->getProduct()->getId();
        $currency = $pingback->getParameter('currency_code');
        $price = $pingback->getParameter('price');
        saveToFile($currency, $price);
        
        if ($pingback->isDeliverable()) {
            $status = PAID;
        } else if ($pingback->isCancelable()) {
            $status = CANCEL;
        } else if ($pingback->isUnderReview()) {
            //only via brick api
            $status = UNDERREVIEW;
        }
        $conn = connector();
        $sqlu = "UPDATE orders SET `pingback_status` = '$status' WHERE id=" . $productId;
        $conn->query($sqlu);
    } else {
        echo $pingback->getErrorSummary();
    }
}

function saveToFile($currency, $price) {
    $myfile = fopen("output.txt", "w") or die("Unable to open file!");
    $txt = "\nCurrency: ". $currency;
    $txt .= "\nPrice: ". $price;
    fwrite($myfile, $txt);
    fclose($myfile);
}