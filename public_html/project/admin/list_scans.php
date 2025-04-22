<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

// Build query with filters
$params = [];
$query = "SELECT id, url, status, category, domain, created, is_api FROM MaliciousScans WHERE 1=1";

// Search filter
if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
    $query .= " AND (url LIKE :search OR domain LIKE :search)";
    $params[":search"] = "%" . trim($_GET["search"]) . "%";
}

// Status filter
if (isset($_GET["status"]) && in_array($_GET["status"], ["Clean", "Suspicious", "Malicious"])) {
    $query .= " AND status = :status";
    $params[":status"] = $_GET["status"];
}

// Limit (1-100 range enforced)
$limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
$limit = max(1, min(100, $limit));
$query .= " ORDER BY created DESC LIMIT " . $limit;

// Execute query
$db = getDB();
$stmt = $db->prepare($query);
$scans = [];
try {
    $stmt->execute($params);
    $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("List scans failed: " . $e->getMessage());
    flash("Error loading scans", "danger");
}
?>

<div class="container-fluid">
    <h3>Recent Scans</h3>
    
    <!-- Filter Form -->
    <form method="GET" class="mb-3 bg-light p-3 rounded">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       value="<?php se($_GET, 'search'); ?>" 
                       placeholder="URL or domain">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Clean" <?php echo se($_GET, 'status') === 'Clean' ? 'selected' : ''; ?>>Clean</option>
                    <option value="Suspicious" <?php echo se($_GET, 'status') === 'Suspicious' ? 'selected' : ''; ?>>Suspicious</option>
                    <option value="Malicious" <?php echo se($_GET, 'status') === 'Malicious' ? 'selected' : ''; ?>>Malicious</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Results Limit</label>
                <input type="number" name="limit" class="form-control" 
                       min="1" max="100" value="<?php se($_GET, 'limit', 10); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <?php if (empty($scans)) : ?>
        <div class="alert alert-warning">No scans found</div>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <?php foreach ($scans[0] as $col => $val) : ?>
                        <th><?php se($col); ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scans as $scan) : ?>
                    <tr>
                        <?php foreach ($scan as $val) : ?>
                            <td><?php se($val); ?></td>
                        <?php endforeach; ?>
                        <td>
                            <a href="edit_scan.php?id=<?php se($scan, 'id'); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="delete_scan.php?id=<?php se($scan, 'id'); ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                            <?php if (is_logged_in()) : ?>
                                <form method="POST" action="associate_scan.php" style="display:inline-block; margin-left:5px;">
                                    <input type="hidden" name="scan_id" value="<?php se($scan, 'id'); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Save to My Scans</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>