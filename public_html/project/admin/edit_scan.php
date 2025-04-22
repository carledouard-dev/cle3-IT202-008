<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

$id = se($_GET, "id", null, false);
$scan = [];

if ($id === null) {
    flash("Invalid scan ID", "danger");
    die(header("Location: list_scans.php"));
}

// Handle form submission
if (isset($_POST["url"])) {
    $allowed_fields = ["url", "status", "category", "domain", "domain_age"];
    $update_data = array_intersect_key($_POST, array_flip($allowed_fields));
    
    $db = getDB();
    $query = "UPDATE MaliciousScans SET ";
    $params = [];
    foreach ($update_data as $col => $val) {
        $query .= "$col = :$col, ";
        $params[":$col"] = $val;
    }
    $query = rtrim($query, ", ") . " WHERE id = :id";
    $params[":id"] = $id;

    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Scan updated!", "success");
    } catch (PDOException $e) {
        error_log("Update failed: " . $e->getMessage());
        flash("Error updating scan", "danger");
    }
}

// Fetch current scan data
$db = getDB();
$stmt = $db->prepare("SELECT url, status, category, domain, domain_age FROM MaliciousScans WHERE id = :id");
try {
    $stmt->execute([":id" => $id]);
    $scan = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch failed: " . $e->getMessage());
    flash("Error loading scan data", "danger");
}
?>

<div class="container-fluid">
    <h3>Edit Scan</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="url">URL</label>
            <input type="url" name="url" id="url" required 
                   value="<?php se($scan, "url"); ?>">
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="Clean" <?php echo se($scan, "status") === "Clean" ? "selected" : ""; ?>>Clean</option>
                <option value="Suspicious" <?php echo se($scan, "status") === "Suspicious" ? "selected" : ""; ?>>Suspicious</option>
                <option value="Malicious" <?php echo se($scan, "status") === "Malicious" ? "selected" : ""; ?>>Malicious</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" required
                   value="<?php se($scan, "category"); ?>">
        </div>
        <input type="submit" value="Update" class="btn btn-primary">
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>