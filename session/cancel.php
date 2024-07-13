<?php 
if (!session_id()) {
    session_start();
}
$_SESSION['error_message'] = 'Payment cancelled';
header('location: ../index.php');
exit();
?>