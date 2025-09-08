<?php
$host = "localhost";
$user = "root";       
$pass = "";           
$db   = "hr4_system";       

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_employee'])) {
  $first_name   = $_POST['first_name'];
  $last_name    = $_POST['last_name'];
  $email        = $_POST['email'];
  $phone        = $_POST['phone'];
  $department   = $_POST['department'];
  $position     = $_POST['position'];
  $salary       = $_POST['salary'];
  $status       = $_POST['status'];

  $sql = "INSERT INTO employees (first_name, last_name, email, phone, department, position, salary, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssssis", $first_name, $last_name, $email, $phone, $department, $position, $salary, $status);

  if ($stmt->execute()) {
    echo "<script>alert('Employee added successfully!'); window.location.href='employee.php';</script>";
  } else {
    echo "<script>alert('Error: " . $stmt->error . "');</script>";
  }
}
?>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
    <h2 class="text-xl font-bold mb-4">Add New Employee</h2>

    <form method="POST" action="">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">First Name</label>
          <input type="text" name="first_name" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Last Name</label>
          <input type="text" name="last_name" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
      </div>

      <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
      </div>

      <div class="mt-3">
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
      </div>

      <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Department</label>
          <select name="department" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="">-- Select --</option>
            <option value="Hotel Operations">Hotel Operations</option>
            <option value="Kitchen">Kitchen</option>
            <option value="Housekeeping">Housekeeping</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Human Resources">Human Resources</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Position</label>
          <select name="position" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="">-- Select --</option>
            <option value="Front Desk Manager">Front Desk Manager</option>
            <option value="Head Chef">Head Chef</option>
            <option value="Housekeeping Supervisor">Housekeeping Supervisor</option>
            <option value="Waiter">Waiter</option>
            <option value="HR Assistant">HR Assistant</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Salary</label>
          <input type="number" name="salary" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="ACTIVE">Active</option>
            <option value="ON LEAVE">On Leave</option>
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg">Cancel</button>
        <button type="submit" name="save_employee" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Save</button>
      </div>
    </form>
  </div>
</div>
