<?php
include 'db.php';

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['id'])){
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $department = $_POST['department'];
    $position   = $_POST['position'];
    $status     = $_POST['status'];

    $sql = "UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, department=?, position=?, status=? WHERE id=?";   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $department, $position, $status, $id);
    $stmt->execute();

    header("Location: employees.php");
    exit;
}
elseif(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        ?>
        <form method="POST" action="edit_employee.php">
          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm">First Name</label>
              <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" class="w-full border px-2 py-1 rounded">
            </div>
            <div>
              <label class="block text-sm">Last Name</label>
              <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" class="w-full border px-2 py-1 rounded">
            </div>
          </div>
          <div class="mt-2">
            <label class="block text-sm">Email</label>
            <input type="email" name="email" value="<?php echo $row['email']; ?>" class="w-full border px-2 py-1 rounded">
          </div>
          <div class="mt-2">
            <label class="block text-sm">Phone</label>
            <input type="text" name="phone" value="<?php echo $row['phone']; ?>" class="w-full border px-2 py-1 rounded">
          </div>
          <div class="mt-2">
            <label class="block text-sm">Department</label>
            <input type="text" name="department" value="<?php echo $row['department']; ?>" class="w-full border px-2 py-1 rounded">
          </div>
          <div class="mt-2">
            <label class="block text-sm">Position</label>
            <input type="text" name="position" value="<?php echo $row['position']; ?>" class="w-full border px-2 py-1 rounded">
          </div>
          <div class="mt-2">
            <label class="block text-sm">Status</label>
            <select name="status" class="w-full border px-2 py-1 rounded">
              <option value="ACTIVE" <?php if($row['status']=="ACTIVE") echo "selected"; ?>>Active</option>
              <option value="INACTIVE" <?php if($row['status']=="INACTIVE") echo "selected"; ?>>Inactive</option>
            </select>
          </div>
          <div class="flex justify-end mt-4">
            <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg">Save Changes</button>
          </div>
        </form>
        <?php
    }
}
?>
