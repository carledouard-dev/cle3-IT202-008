<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Admin view of all user-scan associations
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

// Filters
$params = [];
$query = "SELECT u.username, s.id AS scan_id, s.url, s.status, s.category, us.created
          FROM UserScans us
          JOIN Users u ON us.user_id = u.id
          JOIN MaliciousScans s ON us.scan_id = s.id
          WHERE 1=1";

// Username filter
if (!empty(trim($_GET["username"] ?? ""))) {
    $query .= " AND u.username LIKE :username";
    $params[":username"] = "%" . trim($_GET["username"]) . "%";
}

// Status filter
if (!empty($_GET["status"])) {
    $query .= " AND s.status = :status";
    $params[":status"] = $_GET["status"];
}

// Result limit
$limit = (int)($_GET["limit"] ?? 10);
$limit = max(1, min($limit, 100));
$query .= " ORDER BY us.created DESC LIMIT $limit";

$db = getDB();
$associations = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $associations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch failed: " . $e->getMessage());
    flash("Error loading associations", "danger");
}
?>

<div class="container-fluid">
    <h3>All User Scan Associations</h3>

    <!-- Filter Form -->
    <form method="GET" class="mb-3 bg-light p-3 rounded">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       value="<?php se($_GET, 'username'); ?>" placeholder="Search by username">
            </div>
            <div class="col-md-3">
                <label class="form-label">Scan Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="Clean" <?php echo se($_GET, 'status') === 'Clean' ? 'selected' : ''; ?>>Clean</option>
                    <option value="Suspicious" <?php echo se($_GET, 'status') === 'Suspicious' ? 'selected' : ''; ?>>Suspicious</option>
                    <option value="Malicious" <?php echo se($_GET, 'status') === 'Malicious' ? 'selected' : ''; ?>>Malicious</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Result Limit</label>
                <input type="number" name="limit" class="form-control" min="1" max="100"
                       value="<?php se($_GET, 'limit', 10); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <?php if (empty($associations)) : ?>
        <div class="alert alert-warning">No results found</div>
    <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Saved On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $row) : ?>
                    <tr>
                        <td><?php se($row, "username"); ?></td>
                        <td><?php se($row, "url"); ?></td>
                        <td><?php se($row, "status"); ?></td>
                        <td><?php se($row, "category"); ?></td>
                        <td><?php se($row, "created"); ?></td>
                        <td>
                            <form method="POST" action="remove_user_scan.php" onsubmit="return confirm('Remove this user’s scan association?')">
                                <input type="hidden" name="scan_id" value="<?php se($row, 'scan_id'); ?>">
                                <input type="hidden" name="username" value="<?php se($row, 'username'); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>

