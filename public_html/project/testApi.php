<?php
require(__DIR__ . "/../../partials/nav.php");
// UCID: cle3 Date: 04/14/2025
$result = [];
if (isset($_GET["symbol"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["url" => $_GET["symbol"]];
    $endpoint = "https://malicious-scanner.p.rapidapi.com/rapid/url";
    $isRapidAPI = true;
    $rapidAPIHost = "malicious-scanner.p.rapidapi.com";
    //$result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    $result = ["status" => 200, "response" => '{"success":true,"data":{"status":"Suspicious","message":"A link has been flagged by multiple anti-malware engines.","scan":[],"finishScan":true,"category":"Phishing","sub_status":[{"sub_status":"Multi-Engine Links","level":3,"status":"Suspicious","category":"Phishing","message":"A link has been flagged by multiple anti-malware engines.","description":"This metric indicates that a link in the email has been flagged by multiple anti-malware engines. Links flagged by multiple engines are considered suspicious and may lead to phishing websites or other forms of cyber threats.","highlight":"<b>Reason</b>: Multi-Engine (PHISHING, MALWARE, SPAM, UNTRUSTED)"}],"highlight":"<b>Reason</b>: Multi-Engine (PHISHING, MALWARE, SPAM, UNTRUSTED)","url":"https://saledelivery.zone/?cp=fmlcqhob","name":"https://vryjm.page.link/jS6a","domain":"vryjm.page.link","type":"redirect","malware_type":"https://vryjm.page.link/jS6a","is_captcha":false,"is_anti_bot":false,"is_new_domain":false,"is_top_domain":true,"domain_age":"2017-02-09T00:00:00.000Z","original_url":"https://vryjm.page.link/jS6a","redirect_url":"https://saledelivery.zone/?cp=fmlcqhob","_id":"66c2041aa0483a893ed74dbb"}}'];
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        error_log("Response raw: " . var_export($result["response"], true));
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
<div class="container-fluid">
    <h1>Stock Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Stock" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
                    <?php var_export($stock); ?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");