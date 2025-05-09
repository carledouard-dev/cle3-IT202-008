<?php
// UCID: cle3 Date: 04/15/2025
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/form_helpers.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    header("Location: " . get_url("home.php"));
    exit;
}

$id = se($_GET, "id", null, false);
$scan = [];

if ($id === null) {
    flash("Invalid scan ID", "danger");
    header("Location: " . get_url("admin/list_scans.php"));
    exit;
}

$db = getDB();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["url"])) {
    $allowed_fields = ["url", "status", "category", "domain", "domain_age"];
    $update_data = array_intersect_key($_POST, array_flip($allowed_fields));

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
        header("Location: " . get_url("admin/list_scans.php"));
        exit;
    } catch (PDOException $e) {
        error_log("Update failed: " . $e->getMessage());
        flash("Error updating scan", "danger");
    }
}

// Fetch scan data (either first time or after failed update)
try {
    $stmt = $db->prepare("SELECT url, status, category, domain, domain_age FROM MaliciousScans WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $scan = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$scan) {
        flash("Scan not found", "danger");
        header("Location: " . get_url("admin/list_scans.php"));
        exit;
    }
} catch (PDOException $e) {
    error_log("Fetch failed: " . $e->getMessage());
    flash("Error loading scan data", "danger");
    header("Location: " . get_url("admin/list_scans.php"));
    exit;
}

// Build form
$form = [
    [
        "type" => "url",
        "id" => "url",
        "name" => "url",
        "label" => "URL",
        "value" => $scan["url"] ?? "",
        "rules" => ["required" => true]
    ],
    [
        "type" => "select",
        "id" => "status",
        "name" => "status",
        "label" => "Status",
        "options" => [
            "Clean" => "Clean",
            "Suspicious" => "Suspicious",
            "Malicious" => "Malicious"
        ],
        "value" => $scan["status"] ?? "",
        "rules" => ["required" => true]
    ],
    [
        "type" => "text",
        "id" => "category",
        "name" => "category",
        "label" => "Category",
        "value" => $scan["category"] ?? "",
        "rules" => ["required" => true]
    ],
    [
        "type" => "text",
        "id" => "domain",
        "name" => "domain",
        "label" => "Domain",
        "value" => $scan["domain"] ?? "",
        "rules" => ["required" => false]
    ],
    [
        "type" => "datetime-local",
        "id" => "domain_age",
        "name" => "domain_age",
        "label" => "Domain Age",
        "value" => isset($scan["domain_age"]) ? date("Y-m-d\\TH:i", strtotime($scan["domain_age"])) : "",
        "rules" => ["required" => false]
    ]
];
?>

<div class="container-fluid">
    <h3>Edit Scan</h3>
    <form method="POST">
        <?php foreach ($form as $field): ?>
            <div class="mb-3">
                <?php render_input($field); ?>
            </div>
        <?php endforeach; ?>
        <?php render_button(["text" => "Update", "type" => "submit", "class" => "btn btn-primary"]); ?>
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>