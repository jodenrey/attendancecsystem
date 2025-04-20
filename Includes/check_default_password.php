<?php
// Check if user is using the default password
if (isset($_SESSION['default_password']) && $_SESSION['default_password'] === true) {
    // Only allow access to change password page
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage != 'changePassword.php') {
        // Redirect to change password page
        echo "<script type='text/javascript'>
            alert('Please change your default password before continuing.');
            window.location = 'changePassword.php';
        </script>";
        exit();
    }
}
?>
