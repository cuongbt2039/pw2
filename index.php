<?php

require_once('paymentwall.php');
require_once('Mysql.php');
require_once('UserItemStatus.php');

Paymentwall_Base::setApiType(initPW());
Paymentwall_Base::setAppKey('afaf91e202d15fb421367a38fc7946f6'); // available in your Paymentwall merchant area
Paymentwall_Base::setSecretKey('52dd1bb226d8c0fb9dac04da7d82119f'); // available in your Paymentwall merchant area\
handlePingBack();

function handlePingBack() {
    $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
    $type_api = $_POST["type"];
    echo "type_api: " . $type_api;
    echo "initPW: " . initPW();
    switch ($type_api) {
        case "good_api":
            GoodApiPingBack($pingback);
        case "vc_api":
            VCApiPingBack($pingback);
        default :
            GoodApiPingBack($pingback);
    }
}

function initPW() {
    $type_api = $_POST["type"];
    switch ($type_api) {
        case "good_api":
            return Paymentwall_Base::API_GOODS;
        case "vc_api":
            return Paymentwall_Base::API_VC;
        default :
            return Paymentwall_Base::API_GOODS;
    }
}

function GoodApiPingBack($pingback) {
    if ($pingback->validate()) {
        echo 'OK';
        $productId = $pingback->getProduct()->getId();

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

function VCApiPingBack($pingback) {
    if ($pingback->validate()) {
        $virtualCurrency = $pingback->getVirtualCurrencyAmount();
        if ($pingback->isDeliverable()) {
            // deliver the virtual currency
        } else if ($pingback->isCancelable()) {
            // withdraw the virtual currency
        }
        echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
    } else {
        echo $pingback->getErrorSummary();
    }
}
