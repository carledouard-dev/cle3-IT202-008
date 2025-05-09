<?php
// UCID: cle3 | Date: 2025-05-08 | Improved to conserve API calls and redirect cleanly
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../partials/form_helpers.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

$scan = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = se($_POST, "action", "", false);
    $url = se($_POST, "url", "", false);

    if (!empty($url)) {
        if ($action === "fetch") {
            // API call only when confirmed
            if (isset($_POST["confirm_fetch"])) {
                $api = get_url('testApi.php') . '?symbol=' . urlencode($url);
                $response = file_get_contents($api);
                $result = json_decode($response, true);

                if ($result && isset($result["data"])) {
                    $scan = [
                        "api_id" => $result["data"]["_id"] ?? null,
                        "status" => $result["data"]["status"] ?? "Unknown",
                        "category" => $result["data"]["category"] ?? "Unknown",
                        "url" => $result["data"]["url"],
                        "domain" => $result["data"]["domain"] ?? "",
                        "domain_age" => $result["data"]["domain_age"] ?? null,
                        "is_api" => 1
                    ];
                    flash("Scan fetched successfully via API", "success");
                } else {
                    flash("API fetch failed", "warning");
                }
            } else {
                flash("Please confirm API fetch to avoid wasting calls", "warning");
            }
        } elseif ($action === "create") {
            $scan = [
                "url" => $url,
                "status" => $_POST["status"] ?? "Unknown",
                "category" => $_POST["category"] ?? "Unknown",
                "domain" => $_POST["domain"] ?? "",
                "domain_age" => !empty($_POST["domain_age"]) ? $_POST["domain_age"] : null,
                "is_api" => 0
            ];
        }

        // Save if scan data is ready
        if (!empty($scan)) {
            $db = getDB();
            $cols = array_keys($scan);
            $query = "INSERT INTO MaliciousScans (" . implode(",", $cols) . ") VALUES (:" . implode(",:", $cols) . ")";
            try {
                $stmt = $db->prepare($query);
                $stmt->execute($scan);
                flash("Scan saved! ID: " . $db->lastInsertId(), "success");
                die(header("Location: " . get_url("admin/list_scans.php")));
            } catch (PDOException $e) {
                error_log("Insert error: " . $e->getMessage());
                flash("Error saving scan", "danger");
            }
        }
    } else {
        flash("URL is required", "danger");
    }
}
?>

<div class="container-fluid">
    <h3>Scan a URL</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" href="#" onclick="switchTab('fetch')">Fetch via API</a></li>
        <li class="nav-item"><a class="nav-link" href="#" onclick="switchTab('create')">Manual Entry</a></li>
    </ul>

    <!-- API Scan Tab -->
    <div id="fetch" class="tab-target mt-3">
        <form method="POST">
            <?php render_input(["type" => "url", "name" => "url", "label" => "URL to Scan", "placeholder" => "https://example.com", "required" => true]); ?>
            <input type="hidden" name="action" value="fetch">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm_fetch" id="confirm_fetch">
                <label class="form-check-label" for="confirm_fetch">
                    No Manual Entry 
                </label>
            </div>
            <?php render_button(["text" => "Fetch Scan (API)", "class" => "btn btn-warning mt-2"]); ?>
        </form>
    </div>

    <!-- Manual Entry Tab -->
    <div id="create" class="tab-target mt-3" style="display:none;">
        <form method="POST">
            <?php
            render_input(["type" => "url", "name" => "url", "label" => "URL", "required" => true]);
            render_input(["type" => "text", "name" => "status", "label" => "Status", "placeholder" => "Clean, Suspicious, Malicious", "required" => true]);
            render_input(["type" => "text", "name" => "category", "label" => "Category", "placeholder" => "Phishing, Malware, etc", "required" => true]);
            render_input(["type" => "text", "name" => "domain", "label" => "Domain"]);
            render_input(["type" => "date", "name" => "domain_age", "label" => "Domain Age"]);
            ?>
            <input type="hidden" name="action" value="create">
            <?php render_button(["text" => "Save Manually", "class" => "btn btn-primary"]); ?>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll(".tab-target").forEach(el => {
            el.style.display = (el.id === tab) ? "block" : "none";
        });
    }
</script>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>

