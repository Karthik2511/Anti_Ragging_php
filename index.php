<?php
// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "anti_ragging_db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch approved incidents from the database
$sql = "SELECT college_name, accused_name, evidence FROM reports WHERE approved = 1";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Ragging Campaign</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Anti-Ragging Campaign</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="report.php">Report Incident</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="report-section container">
        <h2>Recent Reports</h2>
        <div class="report-table">
            <div class="table-header">
                <span>College Name</span>
                <span>Accused Student</span>
                <span>Evidence</span>
            </div>
            <?php if ($result->num_rows > 0) { ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <div class="table-row">
                        <span><?php echo htmlspecialchars($row['college_name']); ?></span>
                        <span><?php echo htmlspecialchars($row['accused_name']); ?></span>
                        <span>
                            <?php 
                            $evidence = $row['evidence'];
                            $file_extension = strtolower(pathinfo($evidence, PATHINFO_EXTENSION));
                            
                            // Check if the uploaded file is an image
                            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                echo '<img src="' . htmlspecialchars($evidence) . '" alt="Evidence" style="width:100px;height:100px;">';
                            } else {
                                echo '<a href="' . htmlspecialchars($evidence) . '" target="_blank">View File</a>';
                            }
                            ?>
                        </span>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="table-row">
                    <span colspan="3">No incidents reported yet.</span>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; 2024 Anti-Ragging Campaign. All rights reserved.</p>
    </div>
</footer>

</body>
</html>

<?php
$mysqli->close();
?>
