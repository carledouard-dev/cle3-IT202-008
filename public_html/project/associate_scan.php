<?php
// UCID: cle3 | Date: 2025-04-15 | Associates a scan to the current user
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to save scans", "warning");
    header("Location: " . get_url("login.php"));
    exit;
}

$scan_id = se($_POST, "scan_id", null, false);
if (!$scan_id) {
    flash("Invalid scan ID", "danger");
    header("Location: " . get_url("admin/list_scans.php"));
    exit;
}

$db = getDB();
try {
    // Make sure the scan exists
    $stmt = $db->prepare("SELECT 1 FROM MaliciousScans WHERE id = :id");
    $stmt->execute([":id" => $scan_id]);
    if (!$stmt->fetch()) {
        flash("Scan not found", "danger");
        header("Location: " . get_url("admin/list_scans.php"));
        exit;
    }

    // Save scan to the current user
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

header("Location: " . get_url("my_scans.php"));
exit;
?>