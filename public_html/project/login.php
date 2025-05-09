<?php
/* UCID: cle3 | Date: 2025-04-07 | Desc: Handles user login */  
require(__DIR__ . "/../../partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="user">Email or Username</label>
        <input type="text" name="user" required 
        value="<?php echo htmlspecialchars($_POST['user'] ?? ''); ?>" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        const user = form.user.value.trim();
        const password = form.password.value;

        if (!user) {
            flash("Email or username is required", "danger");
            return false;
        }

        if (!password || password.length < 8) {
            flash("Password must be at least 8 characters", "danger");
            return false;
        }

        return true;
    }
</script>
<?php
if (isset($_POST["user"]) && isset($_POST["password"])) {
    $user = se($_POST, "user", "", false);
    $password = se($_POST, "password", "", false);

    $hasError = false;
    if (empty($user)) {
        flash("Email or username must not be empty", "danger");
        $hasError = true;
    }

    if (empty($password)) {
        flash("Password must not be empty", "danger");
        $hasError = true;
    }

    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }

    if (!$hasError) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password FROM Users 
        WHERE email = :user OR username = :user");
        try {
            $r = $stmt->execute([":user" => $user]);
            if ($r) {
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userData) {
                    $hash = $userData["password"];
                    unset($userData["password"]);
                    if (password_verify($password, $hash)) {
                        $_SESSION["user"] = $userData;

                        try {
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                            JOIN UserRoles on Roles.id = UserRoles.role_id 
                            WHERE UserRoles.user_id = :user_id AND Roles.is_active = 1 AND UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $userData["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {
                            error_log(var_export($e, true));
                        }

                        $_SESSION["user"]["roles"] = $roles ?? [];

                        flash("Welcome, " . get_username(), "success");
                        die(header("Location: home.php"));
                    } else {
                        flash("Invalid password", "danger");
                    }
                } else {
                    flash("Account not found", "danger");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>