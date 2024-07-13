<?php
if (isset($_SESSION['error_message'])) { ?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
            <strong>Error: </strong> <?php echo $_SESSION['error_message']; ?>
        </div>
    </div>
<?php 
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) { ?>
    <div class="col-md-12">
        <div class="alert alert-success" role="alert">
            <strong>Success: </strong> <?php echo $_SESSION['success_message']; ?>
        </div>
    </div>
<?php 
    unset($_SESSION['success_message']);
}
?>