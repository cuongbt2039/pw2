<?php

require_once('Mysql.php');
require_once('UserItemStatus.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'];
    $item_id = $_POST['item_id'];
    echo createUserItem($uid, $item_id, PENDING);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'];
    if ($user_id != -1) {
        echo getItemBought($user_id);
    } else {
        echo 0;
    }
}

function getItemBought($user_id) {
    $conn = connector();
    $paid = PAID;
    $cancel   = CANCEL;
    $pending  = PENDING;
    $sql = "SELECT * FROM orders "
            . "where `pingback_status` in ('$paid', '$cancel')"
            . "and `order_status` = '$pending' "
            . "and user_id = " . $user_id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $list_item = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $status = $row["pingback_status"];
            $sqlu = "UPDATE orders SET `order_status` = '$status' WHERE id=" . $row["id"];
            if ($conn->query($sqlu) === TRUE) {
                $item = getItem($conn, $row["item_id"]);
                if($item){
                    $item["order_status"] = $status;
                    $list_item [] = $item;
                }
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }
    mysqli_close($conn);
    return json_encode($list_item);
}

function createUserItem($uid, $item_id, $order_status) {
    $sqlu = "INSERT INTO `orders` (user_id, item_id, order_status) VALUES "
            . "($uid, $item_id," . "'$order_status')";
    $conn = connector();
    mysqli_query($conn, $sqlu);
    return mysqli_insert_id($conn);
}

function getItem($conn, $id) {
    $sql = "SELECT id, name FROM items where id = " . $id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        $item = null;
    }
    return $item;
}
