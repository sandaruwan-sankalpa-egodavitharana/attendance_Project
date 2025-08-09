<?php
// connect.php - Database Connection and Basic Operations

// Database configuration
$servername = "localhost"; // Replace with your database server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "univotech_attendance"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Handle POST requests for different actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? sanitize_input($_POST['action']) : '';

    switch ($action) {
        case 'register_user':
            $fullname = sanitize_input($_POST['fullname']);
            $email = sanitize_input($_POST['email']);
            $username = sanitize_input($_POST['username']);
            $password = password_hash(sanitize_input($_POST['password']), PASSWORD_DEFAULT); // Hash password
            $role = sanitize_input($_POST['role']);

            $sql = "INSERT INTO users (fullname, email, username, password, role) VALUES ('$fullname', '$email', '$username', '$password', '$role')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully. Redirecting to login...";
                header("Location: login.html"); // Redirect to login page after successful registration
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            break;

        case 'login_user':
            $username = sanitize_input($_POST['username']);
            $password = sanitize_input($_POST['password']);
            $role = sanitize_input($_POST['role']);

            $sql = "SELECT * FROM users WHERE username='$username' AND role='$role'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Start session and set session variables
                    session_start();
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    echo "Login successful. Redirecting to dashboard...";
                    header("Location: dashboard.html"); // Redirect to dashboard
                    exit();
                } else {
                    echo "Invalid password.";
                }
            } else {
                echo "No user found with that username and role.";
            }
            break;

        case 'add_class':
            $class_name = sanitize_input($_POST['class_name']);
            $class_code = sanitize_input($_POST['class_code']);
            $teacher_in_charge = sanitize_input($_POST['teacher_in_charge']);

            $sql = "INSERT INTO classes (class_name, class_code, teacher_in_charge) VALUES ('$class_name', '$class_code', '$teacher_in_charge')";

            if ($conn->query($sql) === TRUE) {
                echo "New class added successfully.";
                // In a real application, you might redirect back to classes.html or update the table dynamically
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            break;

        case 'add_teacher':
            $teacher_name = sanitize_input($_POST['teacher_name']);
            $teacher_id = sanitize_input($_POST['teacher_id']);
            $teacher_email = sanitize_input($_POST['teacher_email']);
            $teacher_department = sanitize_input($_POST['teacher_department']);

            $sql = "INSERT INTO teachers (teacher_id, name, email, department) VALUES ('$teacher_id', '$teacher_name', '$teacher_email', '$teacher_department')";

            if ($conn->query($sql) === TRUE) {
                echo "New teacher added successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            break;

        case 'add_student':
            $student_name = sanitize_input($_POST['student_name']);
            $student_id = sanitize_input($_POST['student_id']);
            $student_email = sanitize_input($_POST['student_email']);
            $student_class = sanitize_input($_POST['student_class']);

            $sql = "INSERT INTO students (student_id, name, email, class) VALUES ('$student_id', '$student_name', '$student_email', '$student_class')";

            if ($conn->query($sql) === TRUE) {
                echo "New student added successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            break;

        // Add cases for update and delete operations here if needed
        // For example:
        /*
        case 'update_class':
            // ... update logic ...
            break;
        case 'delete_class':
            // ... delete logic ...
            break;
        */

        default:
            echo "Invalid action.";
            break;
    }
}

// Close connection
$conn->close();
?>
