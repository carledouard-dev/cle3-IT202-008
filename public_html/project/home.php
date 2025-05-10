<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
/* UCID: cle3 | Date: 2025-04-07 | Desc: Handles user home page */  
if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}
?>
<h2>Welcome, <?php echo htmlspecialchars(get_username()); ?>!</h2>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>