<?php
session_start();

// ✅ Database connection (update credentials if needed)
$host = "localhost";
$port = "3307"; // kung XAMPP mo ay nasa 3307
$user = "root"; // palitan kung may ibang MySQL user ka sa hosting
$pass = "";     // password ng MySQL
$db   = "hr4_db";

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// ✅ Login logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username !== "" && $password !== "") {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // check hashed password
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["role"] = $row["role"];

                // log success
                $log = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, status) VALUES (?, ?, 'success')");
                $log->bind_param("is", $row["id"], $client_ip);
                $log->execute();
                $log->close();

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid username or password.";

                // log failed
                $log = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, status) VALUES (?, ?, 'failed')");
                $uid = $row["id"];
                $log->bind_param("is", $uid, $client_ip);
                $log->execute();
                $log->close();
            }
        } else {
            $error = "Invalid username or password.";
            // log failed without user_id
            $log = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, status) VALUES (NULL, ?, 'failed')");
            $log->bind_param("s", $client_ip);
            $log->execute();
            $log->close();
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
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
<style>
:root {
  --gold: #fbbf24;
  --muted: #64748b;
}
body {
  background: linear-gradient(to right, #0f172a, #1e293b);
  color: #fff;
  font-family: 'Inter', sans-serif;
}
.card {
  background: white;
  color: black;
  border-radius: 1rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.dark .card {
  background: #1e293b;
  color: white;
}
.btn {
  width: 100%;
  background-color: var(--gold);
  color: black;
  font-weight: 600;
  padding: 10px;
  border-radius: 8px;
  transition: 0.3s;
}
.btn:hover { background-color: #facc15; }
.alert-error {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
}
.dark .alert-error {
  background-color: #7f1d1d;
  color: #fecaca;
}
.field { position: relative; }
.input {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: transparent;
  color: inherit;
  outline: none;
}
.float-label {
  position: absolute;
  left: 14px;
  top: 14px;
  font-size: 0.875rem;
  color: #64748b;
  pointer-events: none;
  transition: all 0.2s ease;
}
.input:focus + .float-label,
.input:not(:placeholder-shown) + .float-label {
  top: -8px;
  left: 10px;
  font-size: 0.75rem;
  color: var(--gold);
  background: inherit;
  padding: 0 4px;
}
#lockMessage {
  text-align: center;
  color: var(--gold);
  margin-top: 10px;
}
</style>
</head>

<body class="grid md:grid-cols-2 gap-0 place-items-center p-6 md:p-10 transition-colors duration-300">
<section class="hidden md:flex w-full h-full items-center justify-center">
  <div class="max-w-lg text-white px-6 reveal">
    <img src="logo.png" alt="ATIERA" class="w-56 mb-6 drop-shadow-xl">
    <h1 class="text-4xl font-extrabold">ATIERA <span class="text-yellow-400">Payroll</span> Management</h1>
    <p class="mt-4 text-white/90 text-lg">Secure • Fast • Intuitive</p>
  </div>
</section>

<main class="w-full max-w-md md:ml-auto">
  <div class="card p-6 sm:p-8 reveal">
    <div class="flex items-center justify-between mb-4">
      <div class="md:hidden flex items-center gap-3">
        <img src="logo.png" alt="ATIERA" class="h-10 w-auto">
        <div>
          <div class="text-sm font-semibold">ATIERA Finance Suite</div>
          <div class="text-[10px] text-[color:var(--muted)] caption">Blue • White • <span class="text-yellow-400 font-medium">Gold</span></div>
        </div>
      </div>
      <div class="flex items-center gap-2 ml-auto">
        <button id="modeBtn" class="px-3 py-2 rounded-lg border border-slate-200 text-sm hover:bg-white/60 dark:hover:bg-slate-800">🌓</button>
      </div>
    </div>

    <h3 class="text-lg sm:text-xl font-semibold mb-1">Sign in</h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Use your administrator credentials to continue.</p>

    <?php if ($error): ?>
      <div class="alert-error mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="loginForm" method="POST" class="space-y-4" novalidate>
      <div class="field">
        <input id="username" name="username" type="text" class="input peer" placeholder=" " required>
        <label for="username" class="float-label">Username or Email</label>
      </div>
      <div class="field">
        <input id="password" name="password" type="password" class="input peer" placeholder=" " required>
        <label for="password" class="float-label">Password</label>
      </div>
      <button id="submitBtn" type="submit" class="btn">Sign In</button>
      <p id="lockMessage"></p>
      <p class="text-xs text-center text-slate-500 dark:text-slate-400">© 2025 ATIERA BSIT 4101 CLUSTER 1</p>
    </form>
  </div>
</main>

<script>
// 🌙 Dark mode toggle
let dark = localStorage.getItem("darkMode") === "true";
const body = document.body;
if (dark) body.classList.add("dark");
document.getElementById("modeBtn").onclick = () => {
  dark = !dark;
  body.classList.toggle("dark");
  localStorage.setItem("darkMode", dark);
};

// 🔒 Lock timer logic
let attempts = 0;
const form = document.getElementById("loginForm");
const lockMsg = document.getElementById("lockMessage");
form.addEventListener("submit", () => { attempts++; if (attempts >= 3) lock(); });
function lock() {
  let seconds = 15;
  form.querySelector("button").disabled = true;
  const timer = setInterval(() => {
    lockMsg.textContent = `Too many attempts. Try again in ${seconds}s`;
    seconds--;
    if (seconds < 0) {
      clearInterval(timer);
      attempts = 0;
      lockMsg.textContent = "";
      form.querySelector("button").disabled = false;
    }
  }, 1000);
}
</script>
</body>
</html>
