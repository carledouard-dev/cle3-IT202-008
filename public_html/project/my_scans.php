<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Lists all scans saved by the logged-in user
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: $BASE_PATH/login.php"));
}

$db = getDB();
$query = "SELECT s.id, s.url, s.status, s.category, s.created
          FROM MaliciousScans s
          JOIN UserScans us ON s.id = us.scan_id
          WHERE us.user_id = :uid
          ORDER BY us.created DESC";
$scans = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":uid" => get_user_id()]);
    $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to fetch user scans: " . $e->getMessage());
    flash("Error loading your scans", "danger");
}
?>

<div class="container-fluid">
    <h3>My Saved Scans</h3>

    <?php if (empty($scans)) : ?>
        <div class="alert alert-info">You haven't saved any scans yet</div>
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
                            <a href="<?php echo get_url('admin/view_scan.php?id=' . se($scan, 'id', '', false)); ?>" class="btn btn-sm btn-info">View</a>
                            <form method="POST" action="<?php echo get_url('scans/remove_scan.php'); ?>" style="display:inline;">
                                <input type="hidden" name="scan_id" value="<?php se($scan, 'id'); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>