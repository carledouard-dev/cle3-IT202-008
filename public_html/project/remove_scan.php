<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Removes a scan-user association (soft unlink)
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to remove scans", "warning");
    die(header("Location: $BASE_PATH/login.php"));
}

$scan_id = se($_POST, "scan_id", null, false);
if (!$scan_id) {
    flash("Missing scan ID", "danger");
    die(header("Location: $BASE_PATH/my_scans.php"));
}

$db = getDB();
try {
    $stmt = $db->prepare("DELETE FROM UserScans WHERE user_id = :uid AND scan_id = :sid");
    $stmt->execute([
        ":uid" => get_user_id(),
        ":sid" => $scan_id
    ]);

    if ($stmt->rowCount() > 0) {
        flash("Scan removed from your account", "success");
    } else {
        flash("Scan not associated or already removed", "warning");
    }
} catch (PDOException $e) {
    error_log("Remove scan failed: " . $e->getMessage());
    flash("Error removing scan", "danger");
}

die(header("Location: $BASE_PATH/my_scans.php"));