<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: An area to see scans
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    header("Location: " . get_url("login.php"));
    exit;
}

$db = getDB();
$params = [":uid" => get_user_id()];
$query = "SELECT s.id, s.url, s.status, s.category, s.created
          FROM MaliciousScans s
          JOIN UserScans us ON s.id = us.scan_id
          WHERE us.user_id = :uid";

// Filter by status
$valid_statuses = ["Clean", "Suspicious", "Malicious"];
if (!empty($_GET["status"]) && in_array($_GET["status"], $valid_statuses)) {
    $query .= " AND s.status = :status";
    $params[":status"] = $_GET["status"];
}

// Search
if (!empty(trim($_GET["search"] ?? ""))) {
    $query .= " AND (s.url LIKE :search OR s.category LIKE :search)";
    $params[":search"] = "%" . trim($_GET["search"]) . "%";
}

// Sorting
$sort_field = $_GET["sort"] ?? "created";
$sort_dir = strtolower($_GET["dir"] ?? "desc") === "asc" ? "ASC" : "DESC";
$allowed_sort_fields = ["url", "status", "category", "created"];
if (!in_array($sort_field, $allowed_sort_fields)) $sort_field = "created";
$query .= " ORDER BY s." . $sort_field . " " . $sort_dir;

// Limit
$limit = max(1, min((int)($_GET["limit"] ?? 10), 100));
$query .= " LIMIT $limit";

$scans = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to fetch user scans: " . $e->getMessage());
    flash("Error loading your scans", "danger");
}
?>

<div class="container-fluid">
    <h3>My Saved Scans</h3>

    <form method="GET" class="mb-3 bg-light p-3 rounded">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control"
                       value="<?php se($_GET, 'search'); ?>"
                       placeholder="URL or category">
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
            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="created">Created</option>
                    <option value="url">URL</option>
                    <option value="status">Status</option>
                    <option value="category">Category</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Direction</label>
                <select name="dir" class="form-select">
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <div class="mb-3">
        <strong>Total Results:</strong> <?= count($scans) ?>
    </div>

    <?php if (empty($scans)) : ?>
        <div class="alert alert-info">No scans match your search</div>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scans as $scan) : ?>
                    <tr>
                        <td><?php se($scan, 'url'); ?></td>
                        <td><?php se($scan, 'status'); ?></td>
                        <td><?php se($scan, 'category'); ?></td>
                        <td><?php se($scan, 'created'); ?></td>
                        <td>
                            <a href="<?= get_url("admin/view_scan.php?id=" . se($scan, 'id', '', false)); ?>" class="btn btn-sm btn-outline-info">View</a>
                            <form method="POST" action="<?= get_url('scans/remove_scan.php'); ?>" style="display:inline;">
                                <input type="hidden" name="scan_id" value="<?php se($scan, 'id'); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>

