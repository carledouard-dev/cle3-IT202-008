<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page", "warning");
    die(header("Location: $BASE_PATH/login.php"));
}

$db = getDB();
$query = "SELECT s.id, s.url, s.status, s.category, s.created 
          FROM MaliciousScans s JOIN UserScans us ON s.id = us.scan_id
          WHERE us.user_id = :uid ORDER BY us.created DESC";
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
        <p>You haven't saved any scans yet</p>
    <?php else : ?>
        <table class="table">
            <!-- Same table structure as list_scans.php -->
        </table>
    <?php endif; ?>
</div>