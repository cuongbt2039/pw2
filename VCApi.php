<?php

require_once('paymentwall.php');
require_once('Mysql.php');
require_once('UserItemStatus.php');

Paymentwall_Base::setApiType(Paymentwall_Base::API_VC);
Paymentwall_Base::setAppKey('a9bd5cbb12bb9f763ff7bf3a6ad508b9'); // available in your Paymentwall merchant area
Paymentwall_Base::setSecretKey('ad8a0fac4b51b5d2d29e1a4efecd1657'); // available in your Paymentwall merchant area\
handlePingBack();

function handlePingBack() {
    $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
    if ($pingback->validate()) {
        $virtualCurrency = $pingback->getVirtualCurrencyAmount();
        $user_id = $pingback->getUserId();
        if ($pingback->isDeliverable()) {
            //saveVC($virtualCurrency, 'Paid', $user_id);
            $currency = $pingback->getParameter('currency_code');
            $price = $pingback->getParameter('price');
            saveToFile($currency, $price);
        } else if ($pingback->isCancelable()) {
            saveVC($virtualCurrency, 'Cancel', $user_id);
        }
        echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
    } else {
        echo $pingback->getErrorSummary();
    }
}

function saveVC($virtualCurrency, $status, $user_id) {
    $sqlu = "INSERT INTO `cash_orders` (cash, pingback_status, order_status, user_id) VALUES "
            . "('$virtualCurrency', '$status', 'Pending', $user_id)";
    $conn = connector();
    mysqli_query($conn, $sqlu);
    return mysqli_insert_id($conn);
}

function saveToFile($currency, $price) {
    $myfile = fopen("output.txt", "w") or die("Unable to open file!");
    $txt = "\nCurrency: ". $currency;
    $txt .= "\nPrice: ". $price;
    fwrite($myfile, $txt);
    fclose($myfile);
}