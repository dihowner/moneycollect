<?php 
if (!session_id()) {
    session_start();
}
require_once '../MoneyCollect.php';
$moneyCollect = new MoneyCollect();
$paymentId = $_SESSION['payment_id'];
$retrieve_session = $moneyCollect->retrieve_session($paymentId);
$decodeRetrieve = json_decode($retrieve_session, true);
$decodedData = $decodeRetrieve['data'];

$_SESSION['success_message'] = 'Your payment was successful. Thank You';
header('location: ../index.php');
exit;
// echo "<pre>";
//     print_r($decodeRetrieve);
// echo "</pre>";
// echo "<hr>";
?>

<table border="1">
    <thead>
        <tr>
            <td>ID </td>
            <td><?php echo $paymentId; ?></td>
        </tr>
        <tr>
            <td>Amount </td>
            <td><?php echo $moneyCollect->revert_from_usd($decodedData['amountTotal']); ?> USD</td>
        </tr>
        <tr>
            <td>Payment Reference or Order Number </td>
            <td><?php echo $decodedData['orderNo']; ?></td>
        </tr>
        <tr>
            <td>Payment ID </td>
            <td><?php echo $decodedData['paymentId']; ?></td>
        </tr>
        <tr>
            <td>Status </td>
            <td><?php echo $decodedData['status']; ?></td>
        </tr>
    </thead>
</table>