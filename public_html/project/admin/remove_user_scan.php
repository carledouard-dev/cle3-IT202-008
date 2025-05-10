<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Admin removes a scan association from a user
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "danger");
    die(header("Location: $BASE_PATH/home.php"));
}

$scan_id = se($_POST, "scan_id", null, false);
$username = se($_POST, "username", null, false);

if (!$scan_id || !$username) {
    flash("Missing scan ID or username", "danger");
    die(header("Location: all_user_scans.php"));
}

$db = getDB();
try {
    // Get user ID by username
    $stmt = $db->prepare("SELECT id FROM Users WHERE username = :uname");
    $stmt->execute([":uname" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $uid = $user["id"];
        // Delete the association
        $stmt = $db->prepare("DELETE FROM UserScans WHERE user_id = :uid AND scan_id = :sid");
        $stmt->execute([":uid" => $uid, ":sid" => $scan_id]);

        if ($stmt->rowCount() > 0) {
            flash("Association removed for user $username", "success");
        } else {
            flash("No matching association found", "warning");
        }
    } else {
        flash("User not found", "danger");
    }
} catch (PDOException $e) {
    error_log("Admin association removal failed: " . $e->getMessage());
    flash("Error processing removal", "danger");
}

die(header("Location: all_user_scans.php"));