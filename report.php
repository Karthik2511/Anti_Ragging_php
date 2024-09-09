<?php
// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables for error/success messages
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $mysqli = new mysqli("localhost", "root", "", "anti_ragging_db");

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Collect form data
    $college_name = $_POST['college_name'];
    $accused_name = $_POST['accused_name'];
    $file_name = $_FILES['evidence']['name']; // Original file name
    $file_tmp = $_FILES['evidence']['tmp_name']; // Temporary file path

    // Validate form data
    if (empty($college_name) || empty($accused_name) || empty($file_name)) {
        $message = "All fields are required!";
    } else {
        // Define the upload directory
        $upload_dir = "uploads/";

        // Ensure the directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the uploads directory if it doesn't exist
        }

        // Generate a unique file name to avoid overwriting existing files
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION); // Get file extension
        $new_file_name = uniqid() . "." . $file_extension; // Generate unique name
        $upload_file = $upload_dir . $new_file_name; // Full path of the uploaded file

        // Move the uploaded file to the server
        if (move_uploaded_file($file_tmp, $upload_file)) {
            // Insert the report into the database (approved = 0 by default)
            $stmt = $mysqli->prepare("INSERT INTO reports (college_name, accused_name, evidence, approved) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $college_name, $accused_name, $upload_file);
            
            if ($stmt->execute()) {
                $message = "Report submitted successfully!";
            } else {
                $message = "Failed to submit report!";
            }

            $stmt->close();
        } else {
            $message = "Failed to upload the file.";
        }
    }

    // Close the database connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Report an Incident</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <form action="report.php" method="POST" enctype="multipart/form-data">
            <label for="college_name">College Name:</label>
            <input type="text" id="college_name" name="college_name" required>

            <label for="accused_name">Accused Student's Name:</label>
            <input type="text" id="accused_name" name="accused_name" required>

            <label for="evidence">Upload Evidence (Image/Video):</label>
            <input type="file" id="evidence" name="evidence" accept="image/*,video/*" required>

            <button type="submit">Submit</button>
        </form>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
