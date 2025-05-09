<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Displays a single scan’s details
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view scans", "warning");
    header("Location: " . get_url("login.php"));
    exit;
}

$id = se($_GET, "id", null, false);
if (!$id || !is_numeric($id)) {
    flash("Invalid scan ID", "danger");
    header("Location: " . get_url("my_scans.php"));
    exit;
}

$db = getDB();
$scan = [];

try {
    if (has_role("Admin")) {
        $stmt = $db->prepare("SELECT * FROM MaliciousScans WHERE id = :id LIMIT 1");
        $params = [":id" => $id];
    } else {
        $stmt = $db->prepare("SELECT s.* FROM MaliciousScans s 
                              JOIN UserScans us ON s.id = us.scan_id 
                              WHERE s.id = :id AND us.user_id = :uid LIMIT 1");
        $params = [
            ":id" => $id,
            ":uid" => get_user_id()
        ];
    }

    $stmt->execute($params);
    $scan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$scan) {
        flash("Scan not found or not accessible", "warning");
        header("Location: " . get_url("my_scans.php"));
        exit;
    }
} catch (PDOException $e) {
    error_log("Scan fetch error: " . $e->getMessage());
    flash("Unexpected error loading scan", "danger");
    header("Location: " . get_url("my_scans.php"));
    exit;
}
?>

<div class="container-fluid">
    <h3>Scan Details</h3>
    <table class="table table-bordered">
        <?php foreach ($scan as $key => $value) : ?>
            <tr>
                <th><?php se($key); ?></th>
                <td><?php se($value); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="mt-3">
        <?php if (has_role("Admin")) : ?>
            <a href="<?php echo get_url("admin/edit_scan.php?id=" . se($scan, 'id', '', false)); ?>" class="btn btn-outline-primary">Edit</a>
            <a href="<?php echo get_url("admin/delete_scan.php?id=" . se($scan, 'id', '', false)); ?>" class="btn btn-outline-danger">Delete</a>
            <a href="<?php echo get_url("admin/list_scans.php"); ?>" class="btn btn-secondary">Back to List</a>
        <?php else : ?>
            <a href="<?php echo get_url("my_scans.php"); ?>" class="btn btn-secondary">Back to My Scans</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>