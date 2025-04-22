<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to save scans", "warning");
    die(header("Location: $BASE_PATH/login.php"));
}

$scan_id = se($_POST, "scan_id", null, false);
if ($scan_id === null) {
    flash("Invalid scan ID", "danger");
    die(header("Location: list_scans.php"));
}

$db = getDB();
try {
    // Verify scan exists first
    $stmt = $db->prepare("SELECT 1 FROM MaliciousScans WHERE id = :id");
    $stmt->execute([":id" => $scan_id]);
    if (!$stmt->fetch()) {
        flash("Scan not found", "danger");
        die(header("Location: list_scans.php"));
    }

    // Create association
    $stmt = $db->prepare("INSERT INTO UserScans (user_id, scan_id) VALUES (:uid, :sid)");
    $stmt->execute([
        ":uid" => get_user_id(),
        ":sid" => $scan_id
    ]);
    flash("Scan saved to your account!", "success");
} catch (PDOException $e) {
    error_log("Association failed: " . $e->getMessage());
    if ($e->errorInfo[1] === 1062) { // Duplicate entry
        flash("This scan is already in your collection", "warning");
    } else {
        flash("Error saving scan", "danger");
    }
}

die(header("Location: list_scans.php"));