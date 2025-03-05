<?php
// Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    // Strip the port number if present
    $domain = explode(":", $domain)[0];
}
$localWorks = true; // Some people have issues with localhost for the cookie params
// If you're one of those people, make this false

// This is an extra condition added to "resolve" the localhost issue for the session cookie
if (($localWorks && $domain == "localhost") || $domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60, // Cookie lifetime (1 hour), not session lifetime
        "path" => "/project", // Match your project folder (case sensitive)
        "domain" => $domain, // Use the domain without the port number
        "secure" => true, // Ensure the cookie is only sent over HTTPS
        "httponly" => true, // Prevent JavaScript access to the cookie
        "samesite" => "lax" // Prevent CSRF attacks
    ]);
}

// Start the session
session_start();

// Include functions here so we can have it on every page that uses the nav bar
// That way we don't need to include so many other files on each page
// nav will pull in functions, and functions will pull in db
require(__DIR__ . "/../lib/functions.php");
?>

<nav>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>