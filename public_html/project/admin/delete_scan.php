<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

$id = se($_GET, "id", null, false);
if ($id === null) {
    flash("Invalid scan ID", "danger");
    die(header("Location: list_scans.php"));
}

if (isset($_POST["confirm"])) {
    $db = getDB();
    try {
        $stmt = $db->prepare("DELETE FROM MaliciousScans WHERE id = :id");
        $stmt->execute([":id" => $id]);
        
        if ($stmt->rowCount() > 0) {
            flash("Scan deleted successfully", "success");
        } else {
            flash("Scan not found or already deleted", "warning");
        }
        die(header("Location: list_scans.php"));
    } catch (PDOException $e) {
        error_log("Delete failed: " . $e->getMessage());
        flash("Error deleting scan", "danger");
    }
}
?>

<div class="container-fluid">
    <h3>Delete Scan</h3>
    <p>Are you sure you want to delete this scan? This action cannot be undone.</p>
    <form method="POST">
        <input type="hidden" name="confirm" value="1">
        <button type="submit" class="btn btn-danger">Confirm Delete</button>
        <a href="list_scans.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>