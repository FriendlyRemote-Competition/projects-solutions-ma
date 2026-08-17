<?php
session_start();
$usersFile = __DIR__ . "/users.json";
$message = "";
$messageType = "";

// Load users
function getUsers($file){
    if (!file_exists($file)) {
        file_put_contents($file, "[]");
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?? [];
}

// Save users
function saveUsers($file, $users){
    file_put_contents(
        $file,
        json_encode($users)
    );
}

// Registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $username = trim($_POST["register_username"] ?? "");
    $password = $_POST["register_password"] ?? "";

    // Empty fields
    if ($username === "" || $password === "") {
        $message = "Please fill in all registration fields.";
        $messageType = "error";
    } else {
        $users = getUsers($usersFile);
        // Check if username already exists
        $userExists = false;
        foreach ($users as $user) {
            if ($user["username"] === $username) {
                $userExists = true;
                break;
            }
        }
        if ($userExists) {
            $message = "Username already exists.";
            $messageType = "error";
        } else {
            // Hash password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            // Add new user
            $users[] = [
                "username" => $username,
                "password" => $hashedPassword
            ];
            saveUsers($usersFile, $users);
            $message = "Registration successful. You can now log in.";
            $messageType = "success";
        }
    }
}

// Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $username = trim($_POST["login_username"] ?? "");
    $password = $_POST["login_password"] ?? "";

    // Empty fields
    if ($username === "" || $password === "") {
        $message = "Please enter your username and password.";
        $messageType = "error";
    } else {
        $users = getUsers($usersFile);
        $loginSuccessful = false;
        foreach ($users as $user) {
            if (
                $user["username"] === $username
                && password_verify($password, $user["password"])
            ) {
                $_SESSION["username"] = $user["username"];
                $loginSuccessful = true;
                break;
            }
        }

        if ($loginSuccessful) {
            $message = "Login successful. Welcome, " . $_SESSION["username"] . "!";
            $messageType = "success";
        } else {
            $message = "Invalid username or password.";
            $messageType = "error";
        }
    }
}

// Logout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
$isLoggedIn = isset($_SESSION["username"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Login System</h1>

    <?php if ($message !== ""): ?>
        <div class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <!-- Logged in -->
        <div class="welcome">
            <h2>
                Welcome,
                <?= htmlspecialchars($_SESSION["username"]) ?>!
            </h2>
            <p>
                You are successfully logged in.
            </p>

            <form method="POST">
                <button type="submit" name="logout" class="logout">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Registration -->
        <form method="POST" class="form">
            <h2>Register</h2>
            <label for="register_username">Username</label>
            <input type="text" id="register_username" name="register_username" placeholder="Enter username">
            <label for="register_password">Password</label>
            <input type="password" id="register_password" name="register_password" placeholder="Enter password">
            <button type="submit" name="register">
                Register
            </button>
        </form>
        <div class="divider"></div>

        <!-- Login -->
        <form method="POST" class="form">
            <h2>Login</h2>
            <label for="login_username"> Username </label>
            <input type="text" id="login_username" name="login_username" placeholder="Enter username">
            <label for="login_password">Password</label>
            <input type="password" id="login_password" name="login_password" placeholder="Enter password">
            <button type="submit" name="login">
                Login
            </button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>