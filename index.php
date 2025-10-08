<?php
session_start();
require "db.php"; // ito na yung database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Find user by username or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        // Success
        $_SESSION["logged_in"] = true;
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        
        header("Location: dashboard.php");
        exit();
    } else {
        // Failed
        $_SESSION["error"] = "Invalid username/email or password.";
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>ATIERA — Secure Login</title>
<link rel="icon" href="logo2.png">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

  <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6">ATIERA Payroll Login</h1>

    <?php if (!empty($_SESSION["error"])): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <?= $_SESSION["error"]; unset($_SESSION["error"]); ?>
      </div>
    <?php endif; ?>

    <form action="index.php" method="POST" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Username or Email</label>
        <input type="text" id="username" name="username" required class="w-full border rounded px-3 py-2 mt-1">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required class="w-full border rounded px-3 py-2 mt-1">
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700">Sign In</button>
    </form>
  </div>

</body>
</html>
