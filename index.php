<!DOCTYPE html>
<html>
<head>
  <title>User Form</title>
  <style>
    /* CSS styles */
    body {
      font-family: Arial, sans-serif;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    th {
      background-color: #f2f2f2;
    }
    
    .form-container {
      max-width: 500px;
      margin: 20px auto;
      padding: 20px;
      border: 1px solid #ddd;
    }
    
    .form-container input[type=text], .form-container input[type=file], .form-container select {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    
    .form-container button {
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
    }
    
    .form-container button:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <?php
    // PHP code
    $users = []; // Array to store user data
    
    // Function to generate unique ID
    function generateId() {
      return '_' . uniqid();
    }
    
    // Function to save user data
    function saveUser($id, $name, $image, $address, $gender) {
      global $users;
      
      // Check if user already exists
      $existingUserIndex = findUserIndex($id);
      
      if ($existingUserIndex !== false) {
        $users[$existingUserIndex]['name'] = $name;
        $users[$existingUserIndex]['image'] = $image;
        $users[$existingUserIndex]['address'] = $address;
        $users[$existingUserIndex]['gender'] = $gender;
      } else {
        $newUser = [
          'id' => generateId(),
          'name' => $name,
          'image' => $image,
          'address' => $address,
          'gender' => $gender
        ];
        
        $users[] = $newUser;
      }
      
      // Save user data to session
      $_SESSION['users'] = $users;
    }
    
    // Function to find user index by ID
    function findUserIndex($id) {
      global $users;
      
      foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
          return $index;
        }
      }
      
      return false;
    }
    
    // Function to delete user by ID
    function deleteUser($id) {
      global $users;
      
      $userIndex = findUserIndex($id);
      
      if ($userIndex !== false) {
        array_splice($users, $userIndex, 1);
        
        // Save user data to session
        $_SESSION['users'] = $users;
      }
    }
    
    // Initialize session and retrieve user data if available
    session_start();
    
    if (isset($_SESSION['users'])) {
      $users = $_SESSION['users'];
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $userId = $_POST['userId'];
      $name = $_POST['name'];
      $image = '';
      $address = $_POST['address'];
      $gender = $_POST['gender'];
      
      // Handle image upload
      if (isset($_FILES['image'])) {
        $imageFile = $_FILES['image'];
        $imageFileName = $imageFile['name'];
        $imageTmpName = $imageFile['tmp_name'];
        $imageError = $imageFile['error'];
        
        // Check for upload errors
        if ($imageError === UPLOAD_ERR_OK) {
          // Move the uploaded file to a permanent location
          $imageUploadPath = 'uploads/' . $imageFileName;
          move_uploaded_file($imageTmpName, $imageUploadPath);
          $image = $imageUploadPath;
        }
      }
      
      saveUser($userId, $name, $image, $address, $gender);
    }
  ?>
  
  <div class="form-container">
    <h2>User Form</h2>
    
    <form id="userForm" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="userId" id="userId">
      
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" required>
      
      <label for="image">Image:</label>
      <input type="file" name="image" id="image" accept="image/*">
      
      <label for="address">Address:</label>
      <input type="text" name="address" id="address">
      
      <label for="gender">Gender:</label>
      <select name="gender" id="gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
      </select>
      
      <button type="submit">Save</button>
    </form>
  </div>
  
  <div id="userListContainer">
    <h2>User List</h2>
    <table id="userList">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Image</th>
          <th>Address</th>
          <th>Gender</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          // Generate HTML for each user
          foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . $user['id'] . '</td>';
            echo '<td>' . $user['name'] . '</td>';
            echo '<td><img src="' . $user['image'] . '"></td>';
            echo '<td>' . $user['address'] . '</td>';
            echo '<td>' . $user['gender'] . '</td>';
            echo '<td>';
            echo '<button onclick="editUser(\'' . $user['id'] . '\')">Edit</button>';
            echo '<button onclick="deleteUser(\'' . $user['id'] . '\')">Delete</button>';
            echo '</td>';
            echo '</tr>';
          }
        ?>
      </tbody>
    </table>
  </div>
  
  <script>
    // JavaScript code
    // Function to populate form fields for editing user
    function editUser(userId) {
      var form = document.getElementById('userForm');
      var userIdField = document.getElementById('userId');
      var nameField = document.getElementById('name');
      var imageField = document.getElementById('image');
      var addressField = document.getElementById('address');
      var genderField = document.getElementById('gender');
      
      // Find the user in the table
      var userList = document.getElementById('userList');
      var rows = userList.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      
      for (var i = 0; i < rows.length; i++) {
        var idCell = rows[i].getElementsByTagName('td')[0];
        
        if (idCell.textContent === userId) {
          var nameCell = rows[i].getElementsByTagName('td')[1];
          var imageCell = rows[i].getElementsByTagName('td')[2];
          var addressCell = rows[i].getElementsByTagName('td')[3];
          var genderCell = rows[i].getElementsByTagName('td')[4];
          
          // Populate form fields with user data
          userIdField.value = userId;
          nameField.value = nameCell.textContent;
          addressField.value = addressCell.textContent;
          genderField.value = genderCell.textContent;
          
          break;
        }
      }
    }
    
    // Function to delete user
    function deleteUser(userId) {
      // Confirm deletion
      if (confirm('Are you sure you want to delete this user?')) {
        // Submit the delete form
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        var userIdField = document.createElement('input');
        userIdField.type = 'hidden';
        userIdField.name = 'userId';
        userIdField.value = userId;
        
        form.appendChild(userIdField);
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html>
