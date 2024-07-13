<?php
if (!session_id()) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('location: index.php');
    exit();
}

require_once 'MoneyCollect.php';
$moneyCollect = new MoneyCollect();

$customer_name = trim($_POST['customer_name']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$amount = (float) trim($_POST['amount']);

if (empty($customer_name) || empty($phone) || empty($email) || empty($amount)) {
    $_SESSION['error_message'] = 'All fields are required';
    header('location: index.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = 'Invalid email address';
    header('location: index.php');
    exit();
}

$create_session = $moneyCollect->create_session($customer_name, $phone, $email, $amount);
if ($create_session == NULL) {
    $_SESSION['error_message'] = 'Error communicating to provider';
    header('location: index.php');
    exit();
}

$decodeSessionData = json_decode($create_session, true);
if ($decodeSessionData['code'] == "success" AND $decodeSessionData['msg'] == "success") {
    $checkOutUrl = $decodeSessionData['data']['url'] ?? NULL;
    if ($checkOutUrl == NULL) {
        $moneyCollect->logs($create_session);
        $_SESSION['error_message'] = 'Error generating payment link';
        header('location: index.php');
        exit();
    }

    if (filter_var($checkOutUrl, FILTER_VALIDATE_URL)) {
        $_SESSION['payment_id'] = $decodeSessionData['data']['id'];
        header("location: $checkOutUrl");
        exit;
    }
} else {
    $moneyCollect->logs($create_session);
    $_SESSION['error_message'] = 'Error communicating to provider - Reason unknown';
    header('location: index.php');
    exit();
}

?>