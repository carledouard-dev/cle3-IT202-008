<?php
require_once(__DIR__ . "/../../../lib/db.php"); ?>

<?php
// don't edit - this
$expected_fields = ["task", "due", "assigned"];
$diff = array_diff($expected_fields, array_keys($_GET));

if (empty($diff)) {

    // data variables, don't edit
    $task = $_GET["task"];
    $due = $_GET["due"]; //hint: must be a valid MySQL date format
    $assigned = $_GET["assigned"]; // Must be "self" or a valid format (not empty or equivalent)

    $is_valid = true;
    // TODO Validate the incoming data for correct format based on the SQL table definition.
    // When not valid, provide a user-friendly message of what specifically was wrong and set $is_valid to false.
    // Assigned should check for "self" if a valid format/value isn't provided.
    // Start validations
    $is_valid = true;

    // Validate task
    if (empty($task)) {
        echo "Task cannot be empty.<br>";
        $is_valid = false;
    } elseif (strlen($task) > 255) { // Assuming task is a VARCHAR(255) in the database
        echo "Task must be 255 characters or fewer.<br>";
        $is_valid = false;
    }

    // Validate due date
    if (empty($due)) {
        echo "Due date cannot be empty.<br>";
        $is_valid = false;
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due)) { // Validate MySQL date format (YYYY-MM-DD)
        echo "Due date must be in the format YYYY-MM-DD.<br>";
        $is_valid = false;
    } elseif (strtotime($due) === false) { // Validate that the date is valid
        echo "Due date is not a valid date.<br>";
        $is_valid = false;
    }

    // Validate assigned
    if (empty($assigned)) {
        echo "Assigned cannot be empty. Defaulting to 'self'.<br>";
        $assigned = "self"; // Default to "self" if empty
    } elseif (strlen($assigned) > 60) { // Assuming assigned is a VARCHAR(60) in the database
        echo "Assigned must be 60 characters or fewer. Defaulting to 'self'.<br>";
        $assigned = "self"; // Default to "self" if too long
    }
    // End validations

    /// UCID: cle3
    // Date: 03/09/2025

    if ($is_valid) {
        /*
        Design a query to insert the incoming data to the proper columns.
        Ensure valid and proper PDO named placeholders are used.
        https://phpdelusions.net/pdo
        */
        $query = 'INSERT INTO M4_Todos (task, due, assigned) VALUES (:task, :due, :assigned)'; // edit this
        $params = [':task' => $task, ':due' => $due, ':assigned' => $assigned]; // Apply the proper PDO placeholder to variable mapping here
        try {
            $db = getDB();
            $stmt = $db->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
                echo "Inserted new Todo with id " . $db->lastInsertId();
            } else {
                echo "Failed to insert";
            }
        } catch (PDOException $e) {
            // extra credit
            // check if the exception was related to a unique constraint
            // provide an appropriate user-friendly message for this scenario
            // Otherwise show the default message below

            if ((string)$e->getCode() === '23000') { //this is a universal unique constraint error exception code
                 echo "This already exists. Please provide a unique input.";
            }
            else{
                echo "Error occurred during insertion; please check the logs in terminal";
            }
            error_log("Insert Error: " . var_export($e, true)); // shows in the terminal
        }
    } else {
        error_log("Creation input wasn't valid");
    }
}
?>
<html>

<body>
    <?php require_once(__DIR__ . "/../nav.php"); ?>
    <section>
        <h2>Create ToDo </h2>
        <form>
            <!-- design the form with proper labels and input fields with the correct types based on the SQL table.
             Wrap each label/input pair in a div tag.
             For "Assigned" ensure the default value is "self". -->
            <div>
                <label for="task">Task:</label>
                <input type="text" id="task" name="task" required maxlength="255" />
            </div>
            <div>
                <label for="due">Due Date:</label>
                <input type="date" id="due" name="due" required />
            </div>
            <div>
                <label for="assigned">Assigned:</label>
                <input type="text" id="assigned" name="assigned" value="self" maxlength="60" />
            </div>
            <div>
                <input type="submit" value="Create ToDo" />
            </div>
        </form>
    </section>
</body>
</body>

</html>