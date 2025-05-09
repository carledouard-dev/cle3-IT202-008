<?php
// UCID: cle3 | Date: 2025-05-08 | Desc: Admin assigns scans to users
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Access denied", "danger");
    die(header("Location: $BASE_PATH/home.php"));
}

$db = getDB();
$users = [];
$scans = [];
$errors = [];

$username = trim($_POST["username"] ?? $_GET["username"] ?? "");
$scan_input = trim($_POST["scan"] ?? $_GET["scan"] ?? "");

// Fetch matches
if ($username || $scan_input) {
    if ($username) {
        $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :u LIMIT 25");
        $stmt->execute([":u" => "%$username%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($scan_input) {
        $stmt = $db->prepare("SELECT id, url FROM MaliciousScans WHERE url LIKE 😒 LIMIT 25");
        $stmt->execute([":s" => "%$scan_input%"]);
        $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Assign associations
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["assign"])) {
    $selected_users = $_POST["user_ids"] ?? [];
    $selected_scans = $_POST["scan_ids"] ?? [];

    foreach ($selected_users as $uid) {
        foreach ($selected_scans as $sid) {
            try {
                $stmt = $db->prepare("INSERT INTO UserScans (user_id, scan_id) VALUES (:uid, :sid)");
                $stmt->execute([":uid" => $uid, ":sid" => $sid]);
            } catch (PDOException $e) {
                if ($e->errorInfo[1] !== 1062) {
                    error_log("Assignment failed: " . $e->getMessage());
                    $errors[] = "Error assigning scan ID $sid to user ID $uid";
                }
            }
        }
    }

    if (empty($errors)) {
        flash("Assignments completed successfully", "success");
    } else {
        flash("Some associations failed", "warning");
    }
}
?>

<div class="container-fluid">
    <h3>Assign Scans to Users</h3>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="username" class="form-control" placeholder="Search by username"
                   value="<?php se($_GET, "username"); ?>">
        </div>
        <div class="col-md-5">
            <input type="text" name="scan" class="form-control" placeholder="Search by scan URL"
                   value="<?php se($_GET, "scan"); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <form method="POST">
        <input type="hidden" name="assign" value="1" />
        <div class="row">
            <div class="col-md-6">
                <h5>Users</h5>
                <?php foreach ($users as $u) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="user_ids[]" value="<?php se($u, "id"); ?>" id="user_<?php se($u, "id"); ?>">
                        <label class="form-check-label" for="user_<?php se($u, "id"); ?>">
                            <?php se($u, "username"); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-6">
                <h5>Scans</h5>
                <?php foreach ($scans as $s) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="scan_ids[]" value="<?php se($s, "id"); ?>" id="scan_<?php se($s, "id"); ?>">
                        <label class="form-check-label" for="scan_<?php se($s, "id"); ?>">
                            <?php se($s, "url"); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-success">Assign Selected</button>
        </div>
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>