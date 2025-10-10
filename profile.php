<?php
session_start();
include 'db.php'; // database connection

// Sample lang: kung naka-login si user
// ideally galing to sa session ng login
$userId = $_SESSION['user_id'] ?? 1; 

// Fetch current user data
$stmt = $conn->prepare("SELECT id, name, email, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];

    // Kung may bagong password
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Kung may profile picture
    $avatarPath = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $avatarPath = $targetDir . basename($_FILES["avatar"]["name"]);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatarPath);
    }

    if ($password) {
        $update = $conn->prepare("UPDATE users SET name=?, email=?, password=?, avatar=? WHERE id=?");
        $update->bind_param("ssssi", $name, $email, $password, $avatarPath, $userId);
    } else {
        $update = $conn->prepare("UPDATE users SET name=?, email=?, avatar=? WHERE id=?");
        $update->bind_param("sssi", $name, $email, $avatarPath, $userId);
    }

    if ($update->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10 bg-white shadow rounded-lg p-6">
  <h2 class="text-xl font-semibold mb-4">Profile Settings</h2>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
      <?php 
        echo $_SESSION['success']; 
        unset($_SESSION['success']); 
      ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <!-- Avatar -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
      <div class="flex items-center gap-4 mt-2">
        <img src="<?php echo $user['avatar'] ?: 'default.png'; ?>" 
             alt="Avatar" class="w-16 h-16 rounded-full object-cover border">
        <input type="file" name="avatar" accept="image/*" class="text-sm text-gray-600">
      </div>
    </div>

    <!-- Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Full Name</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
             class="mt-1 w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
             class="mt-1 w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
    </div>

    <!-- Password -->
    <div>
      <label class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
      <input type="password" name="password" 
             class="mt-1 w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
    </div>

    <!-- Submit -->
    <div class="pt-4">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Save Changes
      </button>
    </div>
  </form>
</div>

<script>
  lucide.createIcons();
</script>
</body>
</html>
