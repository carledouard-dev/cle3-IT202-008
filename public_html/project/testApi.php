<?php
require(__DIR__ . "/../../partials/nav.php");
// UCID: cle3 Date: 04/14/2025
$result = [];
if (isset($_GET["symbol"])) {
    // Check cache first
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM MaliciousScans WHERE url = ? ORDER BY created DESC LIMIT 1");
    $stmt->execute([$_GET["symbol"]]);
    $cached = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cached) {
        // Only call API if no cache exists
        $data = ["url" => $_GET["symbol"]];
        $endpoint = "https://malicious-scanner.p.rapidapi.com/rapid/url";
        $isRapidAPI = true;
        $rapidAPIHost = "malicious-scanner.p.rapidapi.com";
        
        //$result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
        //example of cached data to save the quotas
        $result = ["status" => 200, "response" => '{"success":true,"data":{"status":"Suspicious","message":"A link has been flagged by multiple anti-malware engines.","scan":[],"finishScan":true,"category":"Phishing","sub_status":[{"sub_status":"Multi-Engine Links","level":3,"status":"Suspicious","category":"Phishing","message":"A link has been flagged by multiple anti-malware engines.","description":"This metric indicates that a link in the email has been flagged by multiple anti-malware engines. Links flagged by multiple engines are considered suspicious and may lead to phishing websites or other forms of cyber threats.","highlight":"<b>Reason</b>: Multi-Engine (PHISHING, MALWARE, SPAM, UNTRUSTED)"}],"highlight":"<b>Reason</b>: Multi-Engine (PHISHING, MALWARE, SPAM, UNTRUSTED)","url":"https://saledelivery.zone/?cp=fmlcqhob","name":"https://vryjm.page.link/jS6a","domain":"vryjm.page.link","type":"redirect","malware_type":"https://vryjm.page.link/jS6a","is_captcha":false,"is_anti_bot":false,"is_new_domain":false,"is_top_domain":true,"domain_age":"2017-02-09T00:00:00.000Z","original_url":"https://vryjm.page.link/jS6a","redirect_url":"https://saledelivery.zone/?cp=fmlcqhob","_id":"66c2041aa0483a893ed74dbb"}}'];
            error_log("Response: " . var_export($result, true));
            if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
                error_log("Response raw: " . var_export($result["response"], true));
                $result = json_decode($result["response"], true);
            } else {
                $result = [];
            }
        }
        
        if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
            $decoded = json_decode($result["response"], true);
            if ($decoded && isset($decoded['data'])) {
                // Format domain_age for MySQL if it exists
                $domain_age = null;
                if (isset($decoded['data']['domain_age'])) {
                    $date = new DateTime($decoded['data']['domain_age']);
                    $domain_age = $date->format('Y-m-d H:i:s');
                }
                
                // Cache new results
                $query = "INSERT INTO MaliciousScans 
                          (url, status, category, domain, domain_age, is_api)
                          VALUES (:url, :status, :category, :domain, :age, 1)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':url' => $decoded['data']['url'],
                    ':status' => $decoded['data']['status'],
                    ':category' => $decoded['data']['category'],
                    ':domain' => $decoded['data']['domain'],
                    ':age' => $domain_age
                ]);
            }
            $result = $decoded;
        }
    } else {
        $result = ['data' => $cached];
    }

    // Fallback to example data if empty
    if (empty($result)) {
        $result = json_decode('{"data":{"status":"Suspicious","category":"Phishing","url":"https://example.com","domain":"example.com"}}', true);
    }
?>

<div class="container-fluid">
    <h1>URL Scanner</h1>
    <form>
        <div class="mb-3">
            <label>URL</label>
            <input name="symbol" class="form-control" placeholder="Enter URL" required />
            <input type="submit" value="Scan URL" class="btn btn-primary mt-2" />
        </div>
    </form>

    <?php if (isset($result)) : ?>
        <div class="mt-3">
            <table class="table table-bordered">
                <?php if (isset($result['data'])) : ?>
                    <?php foreach (['url', 'status', 'category', 'domain'] as $field) : ?>
                        <tr>
                            <th><?= ucfirst($field) ?></th>
                            <td><?= se($result['data'], $field, 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
            <details class="mt-3">
                <summary>Raw Data</summary>
                <pre><?php var_export($result); ?></pre>
            </details>
        </div>
    <?php endif; ?>
</div>

<?php
require(__DIR__ . "/../../partials/flash.php");