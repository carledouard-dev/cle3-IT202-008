<?php
/* UCID: cle3 | Date: 2025-04-07 | Desc: Handles user logout */  
session_start();
require(__DIR__ . "/../../lib/functions.php");
reset_session();

flash("Successfully logged out", "success");
header("Location: login.php");