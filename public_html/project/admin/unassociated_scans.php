<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: View scans not associated with any user
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Access denied", "danger");
    die(header("Location: $BASE_PATH/home.php"));
}

$params = [];
$query = "SELECT s.id, s.url, s.status, s.category, s.created
          FROM MaliciousScans s
          LEFT JOIN UserScans us ON s.id = us.scan_id
          WHERE us.id IS NULL";

// Optional: filter by status
if (!empty($_GET["status"])) {
    $query .= " AND s.status = :status";
    $params[":status"] = $_GET["status"];
}

// Limit
$limit = (int)($_GET["limit"] ?? 10);
$limit = max(1, min($limit, 100));
$query .= " ORDER BY s.created DESC LIMIT $limit";

$db = getDB();
$scans = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Unassociated scan fetch failed: " . $e->getMessage());
    flash("Failed to load scans", "danger");
}
?>

<div class="container-fluid">
    <h3>Unassociated Scans</h3>
    <form method="GET" class="mb-3 bg-light p-3 rounded">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="Clean" <?php echo se($_GET, 'status') === 'Clean' ? 'selected' : ''; ?>>Clean</option>
                    <option value="Suspicious" <?php echo se($_GET, 'status') === 'Suspicious' ? 'selected' : ''; ?>>Suspicious</option>
                    <option value="Malicious" <?php echo se($_GET, 'status') === 'Malicious' ? 'selected' : ''; ?>>Malicious</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Result Limit</label>
                <input type="number" name="limit" class="form-control" min="1" max="100"
                       value="<?php se($_GET, 'limit', 10); ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <?php if (empty($scans)) : ?>
        <div class="alert alert-info">No unassociated scans found</div>
    <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scans as $s) : ?>
                    <tr>
                        <td><?php se($s, "url"); ?></td>
                        <td><?php se($s, "status"); ?></td>
                        <td><?php se($s, "category"); ?></td>
                        <td><?php se($s, "created"); ?></td>
                        <td>
                            <a href="view_scan.php?id=<?php se($s, "id"); ?>" class="btn btn-sm btn-outline-info">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>