<?php
// UCID: cle3 Date: 2025-04-22
// Admin page to create/fetch URL scans via testApi
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("Permission denied", "warning");
    die(header("Location: $BASE_PATH/home.php"));
}

// Handle form submission
if (isset($_POST["action"])) {
    $url = se($_POST, "url", "", false);
    $scan = [];
    
    if (!empty($url)) {
        if ($_POST["action"] === "fetch") {
            // Fetch from testApi instead of malicious scanner
            $testApiUrl = get_url('testApi.php') . '?symbol=' . urlencode($url);
            $response = file_get_contents($testApiUrl);
            $result = json_decode($response, true);
            
            if ($result && isset($result['data'])) {
                $scan = [
                    'api_id' => $result['data']['_id'] ?? null,
                    'status' => $result['data']['status'],
                    'category' => $result['data']['category'],
                    'url' => $result['data']['url'],
                    'domain' => $result['data']['domain'],
                    'domain_age' => $result['data']['domain_age'] ?? null,
                    'is_api' => 1
                ];
                flash("Scan fetched successfully via testApi", "success");
            } else {
                flash("Failed to fetch scan data", "warning");
            }
        } elseif ($_POST["action"] === "create") {
            // Manual entry remains unchanged
            $allowed = ["url", "status", "category", "domain", "domain_age"];
            $scan = array_intersect_key($_POST, array_flip($allowed));
            $scan["is_api"] = 0;
        }
        
        // Existing DB insertion logic remains the same
        if (!empty($scan)) {
            $db = getDB();
            $query = "INSERT INTO MaliciousScans (";
            $query .= implode(",", array_keys($scan)) . ") VALUES (";
            $query .= ":" . implode(",:", array_keys($scan)) . ")";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->execute($scan);
                flash("Scan saved! ID: " . $db->lastInsertId(), "success");
            } catch (PDOException $e) {
                error_log("DB Error: " . $e->getMessage());
                flash("Error saving scan", "danger");
            }
        }
    } else {
        flash("URL is required", "warning");
    }
}
?>

<!-- REST OF THE FILE REMAINS EXACTLY THE SAME -->
<div class="container-fluid">
    <h3>URL Scan Tool</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" onclick="switchTab('fetch')">API Scan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="switchTab('create')">Manual Entry</a>
        </li>
    </ul>
    
    <!-- API Scan Form -->
    <div id="fetch" class="tab-target">
        <form method="POST">
            <div class="mb-3">
                <label for="url">URL to Scan</label>
                <input type="url" name="url" id="url" required 
                       placeholder="https://example.com" 
                       value="<?php echo ($_POST['action'] ?? '') === 'fetch' ? se($_POST['url'] ?? '') : ''; ?>">
            </div>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Scan URL" class="btn btn-primary">
        </form>
    </div>
    
    <!-- Manual Entry Form -->
    <div id="create" class="tab-target" style="display:none;">
        <form method="POST">
            <div class="mb-3">
                <label for="manual_url">URL</label>
                <input type="url" name="url" id="manual_url" required 
                       placeholder="https://example.com"
                       value="<?php echo ($_POST['action'] ?? '') === 'create' ? se($_POST['url'] ?? '') : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="manual_status">Status</label>
                <select name="status" id="manual_status" required>
                    <option value="Clean" <?php echo ($_POST['status'] ?? '') === 'Clean' ? 'selected' : ''; ?>>Clean</option>
                    <option value="Suspicious" <?php echo ($_POST['status'] ?? '') === 'Suspicious' ? 'selected' : ''; ?>>Suspicious</option>
                    <option value="Malicious" <?php echo ($_POST['status'] ?? '') === 'Malicious' ? 'selected' : ''; ?>>Malicious</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="manual_category">Threat Category</label>
                <input type="text" name="category" id="manual_category" required 
                       placeholder="Phishing, Malware, etc"
                       value="<?php echo ($_POST['action'] ?? '') === 'create' ? se($_POST['category'] ?? '') : ''; ?>">
            </div>
            <input type="hidden" name="action" value="create">
            <input type="submit" value="Save Scan" class="btn btn-primary">
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