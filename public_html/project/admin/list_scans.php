<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    header("Location: " . get_url("home.php"));
    exit;
}

// Build query with filters
$params = [];
$query = "SELECT id, url, status, category, domain, created, is_api FROM MaliciousScans WHERE 1=1";

// Search filter
if (!empty(trim($_GET["search"] ?? ""))) {
    $query .= " AND (url LIKE :search OR domain LIKE :search)";
    $params[":search"] = "%" . trim($_GET["search"]) . "%";
}

// Status filter
$valid_statuses = ["Clean", "Suspicious", "Malicious"];
if (isset($_GET["status"]) && in_array($_GET["status"], $valid_statuses)) {
    $query .= " AND status = :status";
    $params[":status"] = $_GET["status"];
}

// Results limit
$limit = max(1, min((int)($_GET["limit"] ?? 10), 100));
$query .= " ORDER BY created DESC LIMIT " . $limit;

$db = getDB();
$scans = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("List scans failed: " . $e->getMessage());
    flash("Error loading scans", "danger");
}
?>

<div class="container-fluid">
    <h3>Recent Scans</h3>

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
                    <?php foreach ($valid_statuses as $status): ?>
                        <option value="<?= $status ?>" <?= se($_GET, 'status') === $status ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Results Limit</label>
                <input type="number" name="limit" class="form-control" min="1" max="100" 
                       value="<?php se($_GET, 'limit', 10); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <?php if (empty($scans)) : ?>
        <div class="alert alert-warning">No scans found</div>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Domain</th>
                    <th>Created</th>
                    <th>API?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scans as $scan): ?>
                    <tr>
                        <td><?php se($scan, 'id'); ?></td>
                        <td><?php se($scan, 'url'); ?></td>
                        <td><?php se($scan, 'status'); ?></td>
                        <td><?php se($scan, 'category'); ?></td>
                        <td><?php se($scan, 'domain'); ?></td>
                        <td><?php se($scan, 'created'); ?></td>
                        <td><?php echo $scan["is_api"] ? "Yes" : "No"; ?></td>
                        <td>
                            <a href="<?= get_url("admin/edit_scan.php?id=" . se($scan, 'id', null, false)) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="<?= get_url("admin/delete_scan.php?id=" . se($scan, 'id', null, false)) ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                            <?php if (is_logged_in()): ?>
                                <form method="POST" action="<?= get_url('associate_scan.php'); ?>" style="display:inline-block;">
                                    <input type="hidden" name="scan_id" value="<?php se($scan, 'id'); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Save</button>
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

