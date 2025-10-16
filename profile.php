<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Change Name
    if (isset($_POST['change_name'])) {
        $newName = trim($_POST['full_name']);
        if ($newName) {
            $stmt = $conn->prepare("UPDATE users SET full_name=? WHERE id=?");
            $stmt->bind_param("si", $newName, $userId);
            if ($stmt->execute()) {
                $success = "Name updated successfully.";
                $user['full_name'] = $newName;
            } else {
                $error = "Failed to update name.";
            }
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if (password_verify($current, $user['password'])) {
            if ($new === $confirm) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $hashed, $userId);
                if ($stmt->execute()) {
                    $success = "Password updated successfully.";
                    $user['password'] = $hashed;
                } else {
                    $error = "Failed to update password.";
                }
            } else {
                $error = "New password and confirmation do not match.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }

    // Change Profile Image
    
        } else {if (isset($_POST['change_image']) && isset($_FILES['profile_image'])) {
        $file = $_FILES['profile_image'];
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFileName = "uploads/profile_{$userId}.".$ext;
            move_uploaded_file($file['tmp_name'], $newFileName);
            $stmt = $conn->prepare("UPDATE users SET profile_image=? WHERE id=?");
            $stmt->bind_param("si", $newFileName, $userId);
            if ($stmt->execute()) {
                $success = "Profile picture updated.";
                $user['profile_image'] = $newFileName;
            } else {
                $error = "Failed to update image.";
            }
            $error = "Invalid file type.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Profile Settings</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">
<?php include "sidebar.php"; ?>

<main class="flex-1 p-8">
<h1 class="text-2xl font-bold mb-6">Profile Settings</h1>

<?php if($success): ?>
<div class="bg-green-100 text-green-800 p-3 rounded mb-4"><?= $success ?></div>
<?php endif; ?>
<?php if($error): ?>
<div class="bg-red-100 text-red-800 p-3 rounded mb-4"><?= $error ?></div>
<?php endif; ?>

<section class="mb-6 bg-white p-6 rounded shadow">
<h2 class="font-semibold mb-4">Change Name</h2>
<form method="POST">
<input type="text" name="full_name" class="border p-2 rounded w-full mb-3" value="<?= htmlspecialchars($user['full_name']) ?>" required>
<button type="submit" name="change_name" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Name</button>
</form>
</section>

<section class="mb-6 bg-white p-6 rounded shadow">
<h2 class="font-semibold mb-4">Change Password</h2>
<form method="POST">
<input type="password" name="current_password" placeholder="Current Password" class="border p-2 rounded w-full mb-3" required>
<input type="password" name="new_password" placeholder="New Password" class="border p-2 rounded w-full mb-3" required>
<input type="password" name="confirm_password" placeholder="Confirm New Password" class="border p-2 rounded w-full mb-3" required>
<button type="submit" name="change_password" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Password</button>
</form>
</section>

<section class="mb-6 bg-white p-6 rounded shadow">
<h2 class="font-semibold mb-4">Change Profile Picture</h2>
<form method="POST" enctype="multipart/form-data">
<?php if($user['profile_image'] && file_exists($user['profile_image'])): ?>
<img src="<?= $user['profile_image'] ?>" alt="Profile Picture" class="w-32 h-32 rounded-full mb-3 object-cover">
<?php endif; ?>
<input type="file" name="profile_image" accept="image/*" class="mb-3">
<button type="submit" name="change_image" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload Image</button>
</form>
</section>
</main>
</body>
</html>
