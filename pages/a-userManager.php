<?php
$pageTitle = "User Manager";
include "panel.php";

// Function to search users
function searchUsers($searchTerm)
{
    global $conn; // Use the mysqli connection variable
    $sql = "SELECT * FROM users WHERE username LIKE ? OR first_name LIKE ? OR last_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTermParam = "%" . $searchTerm . "%";
    $stmt->bind_param("sss", $searchTermParam, $searchTermParam, $searchTermParam);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to create a new user
function createUser($username, $password, $first_name, $middle_name, $last_name, $gender, $role)
{
    global $conn; // Use the mysqli connection variable
    $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, password_hash($password, PASSWORD_DEFAULT), $first_name, $middle_name, $last_name, $gender, $role);
    $stmt->execute();
    $stmt->close();
}

// Function to update a user
function updateUser($user_id, $username, $password, $first_name, $middle_name, $last_name, $gender, $role)
{
    global $conn;

    if ($password) {
        $sql = "UPDATE users SET username = ?, password = ?, first_name = ?, middle_name = ?, last_name = ?, gender = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $username, $password, $first_name, $middle_name, $last_name, $gender, $role, $user_id);
    } else {
        $sql = "UPDATE users SET username = ?, first_name = ?, middle_name = ?, last_name = ?, gender = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $username, $first_name, $middle_name, $last_name, $gender, $role, $user_id);
    }

    $stmt->execute();
    $stmt->close();
}

// Function to delete a user
function deleteUser($user_id)
{
    global $conn; // Use the mysqli connection variable
    $sql = "DELETE FROM users WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Function to get user details by ID
function getUserById($user_id)
{
    global $conn; // Use the mysqli connection variable
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind the user_id as an integer
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set
    return $result->fetch_assoc(); // Fetch the associative array
}

// Handle search
$users = [];
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $users = searchUsers($searchTerm);
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        createUser($_POST['username'], $_POST['password'], $_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['gender'], $_POST['role']);
        // Clear form after adding a user
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to clear form
        exit;
    } elseif ($_POST['action'] == 'update') {
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash password only if provided
        updateUser($_POST['user_id'], $_POST['username'], $password, $_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['gender'], $_POST['role']);
    } elseif ($_POST['action'] == 'delete') {
        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            echo "User ID: " . $_POST['user_id']; // Debugging step
            deleteUser($_POST['user_id']);
            header("Location: " . $_SERVER['PHP_SELF']);
        } else {
            echo "No user_id provided."; // Debugging step for missing user_id
        }
        exit;
    }
}

// Handle user selection
$user_details = null;
if (isset($_POST['user_id'])) {
    $user_details = getUserById($_POST['user_id']);
}

// Function to get user activity logs
function getUserActivityLogs()
{
    global $conn; // Use the mysqli connection variable
    $sql = "
        SELECT u.username, l.login_time 
        FROM user_activity_log l
        JOIN users u ON l.user_id = u.user_id 
        ORDER BY l.login_time DESC"; // Adjust query as needed
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC); // Fetch all logs
}

// Fetch the logs
$logs = getUserActivityLogs();

?>

<script>
    function hideResults() {
        setTimeout(function() {
            document.getElementById('searchResults').style.display = 'none';
        }, 100); // Add a small delay to allow the user to click the result before it hides
    }

    function showResults() {
        if (document.getElementById('searchTerm').value.length > 0) {
            document.getElementById('searchResults').style.display = 'block';
        }
    }

    function searchUsers() {
        const searchTerm = document.getElementById('searchTerm').value;

        // If the search term is empty, hide the results
        if (searchTerm.length === 0) {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        // Create an AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../backend/search_users.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status === 200) {
                const results = JSON.parse(this.responseText);
                displayResults(results);
            }
        };
        xhr.send('searchTerm=' + encodeURIComponent(searchTerm));
    }

    function displayResults(users) {
        const resultsDiv = document.getElementById('searchResults');
        resultsDiv.innerHTML = ''; // Clear previous results
        if (users.length > 0) {
            users.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.textContent = `${user.first_name} ${user.last_name} (${user.username})(${user.role})`;
                userDiv.onclick = function() {
                    populateForm(user); // Populate form when user is clicked
                    resultsDiv.style.display = 'none'; // Hide results after selection
                };
                resultsDiv.appendChild(userDiv);
            });
            resultsDiv.style.display = 'block'; // Show results
        } else {
            resultsDiv.innerHTML = '<div>No results found</div>';
            resultsDiv.style.display = 'block'; // Show results
        }
    }

    function populateForm(user) {
        document.getElementById('user_id').value = user.user_id;
        document.getElementById('first_name').value = user.first_name;
        document.getElementById('middle_name').value = user.middle_name;
        document.getElementById('last_name').value = user.last_name;
        document.getElementById('gender').value = user.gender;
        document.getElementById('username').value = user.username;
        document.getElementById('password').value = ''; // Reset password
        document.getElementById('confirm_password').value = ''; // Reset password
        document.getElementById('role').value = user.role || ''; // Ensure role is set correctly

        // Enable buttons
        document.getElementById('user_id').disabled = false;
        document.getElementById('updateBtn').disabled = false;
        document.getElementById('deleteBtn').disabled = false;
    }

    function enableFormAdd() {
        document.querySelectorAll('#userForm input, #userForm select').forEach(input => {
            input.disabled = false;
        });
        document.getElementById('submitBtn').value = "add"; // Change action value
        document.getElementById('addBtn').textContent = "Clear Form"
    }

    function enableFormUpdate() {
        document.querySelectorAll('#userForm input, #userForm select').forEach(input => {
            input.disabled = false;
        });
        document.getElementById('password').disabled = true; // Enable password fields for adding a user
        document.getElementById('confirm_password').disabled = true; // Enable confirm password fields for adding a user
        document.getElementById('submitBtn').value = "update"; // Change action value
        document.getElementById('addBtn').textContent = "Clear Form"
    }

    function clearForm() {
        document.getElementById('userForm').reset();
        document.querySelectorAll('#userForm input, #userForm select').forEach(input => {
            input.disabled = false; // Enable fields for new user
        });

        // Disable update and delete buttons
        document.getElementById('updateBtn').disabled = true;
        document.getElementById('deleteBtn').disabled = true;
    }
</script>

<main>
    <div class="mainUserManager">
        <div class="containerUserManager">
            <form id="searchForm">
                <input type="text" id="searchTerm" placeholder="Search" required oninput="searchUsers()" onblur="hideResults()" onfocus="showResults()" autocomplete="off">
                <div id="searchResults" style="display: none;"></div>
            </form>
            <div class="separator">
                <!-- User Form -->
                <div class="form">
                    <h3>User Information</h3>
                    <form id="userForm" method="POST" action="">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo isset($user_details['user_id']) ? $user_details['user_id'] : ''; ?>">
                        <span>
                            <div class="design"></div>
                        </span>
                        <div class="form-separator">
                            <div class="separate">
                                <img src="../images/team.png" alt="">
                                <br>
                                <select id="gender" name="gender" required disabled>
                                    <option value="" disabled selected>Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select><br>

                                <select id="role" name="role" required disabled>
                                    <option value="" disabled selected>Role</option>
                                    <option value="student">Student</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="separate">
                                <div class="form-group">
                                    <input type="text" id="first_name" name="first_name" placeholder=" " autocomplete="off" required disabled>
                                    <label for="first_name">First Name:</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" id="middle_name" name="middle_name" placeholder=" " autocomplete="off" disabled>
                                    <label for="middle_name">Middle Name:</label>
                                </div>

                                <div class="form-group">
                                    <input type="text" id="last_name" name="last_name" placeholder=" " autocomplete="off" required disabled>
                                    <label for="last_name">Last Name:</label>
                                </div>


                                <div class="form-group">
                                    <input type="text" id="username" name="username" placeholder=" " autocomplete="off" required disabled>
                                    <label for="username">Username:</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="password" id="password" name="password" placeholder=" " autocomplete="off" required disabled>
                            <label for="password">Password:</label>
                        </div>

                        <div class="form-group">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder=" " autocomplete="off" required disabled>
                            <label for="confirm_password">Confirm Password:</label>
                        </div>

                        <div class="submit-buttons">
                            <div>
                                <button type="submit" name="action" id="submitBtn" value="add">Submit</button>
                            </div>
                            <div>
                                <button type="submit" name="action" value="delete" id="deleteBtn" disabled>Delete User</button>
                                <button type="button" id="updateBtn" onclick="enableFormUpdate()" disabled>Update User</button>
                                <button type="button" id="addBtn" onclick="clearForm(), enableFormAdd()">Add User</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="logs">
                    <h2>User Activity Logs</h2>
                    <div>
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>Login Time</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="2">No activity logs found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['username']); ?></td>
                                            <td><?php echo htmlspecialchars($log['login_time']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include "closing.php";
?>