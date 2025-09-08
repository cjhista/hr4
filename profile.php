<!-- dito nyo cacall kung sino user admin employee etc -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// For now, hardcode user info (later you can pull from DB or session)
$userId   = 1; 
$userName = "Charles"; 
?>

<!-- Right: User Info -->
<div class="relative">
  <button id="userDropdownToggle" class="flex items-center gap-2 focus:outline-none">
    <!-- Profile Picture -->
    <img src="user_profile.php?id=<?php echo $userId; ?>&type=avatar" 
         alt="profile picture" 
         class="w-8 h-8 rounded-full border object-cover" />
    
    <!-- User Name -->
    <span class="text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($userName); ?></span>
    
    <!-- Dropdown Icon -->
    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-600"></i>
  </button>

  <!-- Dropdown -->
  <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow-lg hidden z-20">
    <a href="user_profile.php?id=<?php echo $userId; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
  </div>
</div>
