<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1>Home</h1>
<?php

//Keep error log for Milestone1 evidence
//Do not delete
error_log("Session: ". var_export($_SESSION, true));
if(isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])){
 echo "Welcome, " . $_SESSION["user"]["email"]; 
}
else{
  echo "You're not logged in";
}
if(is_logged_in()){
    echo "Welcome " . get_user_email();
}
else{
    echo "You're not logged in";
}
?>