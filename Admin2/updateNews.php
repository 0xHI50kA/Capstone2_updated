<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header2.php');
include('includes/connection.php');

// Database connection
$host = "localhost";
$dbname = "hms_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the news table if not exists
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            image VARCHAR(255) NOT NULL
        );
    ";
    $pdo->exec($createTableSQL);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];

    // File upload handling
    $targetDir = "uploads/"; // Directory to save images

    // Create 'uploads' folder if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Generate a unique filename to prevent overwrites
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    // Validate file upload
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Insert data into the database
        $sql = "INSERT INTO news (title, content, image) VALUES (:title, :content, :image)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":image", $imageName);

        if ($stmt->execute()) {
            $successMessage = "News report added successfully!";
        } else {
            $errorMessage = "Failed to add news report.";
        }
    } else {
        $errorMessage = "Error: Could not move the uploaded file.";
    }
}
?>

    
     <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Reports</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .page-wrapper {
            margin-top: -20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        .news-container {
            margin-top: 9px;
            width: 90%;
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-top: 70px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .image-preview {
            text-align: center;
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 5px;
            display: none; /* Hidden until an image is selected */
        }
    </style>
</head>

<body>

    <div class="page-wrapper">
        <h1>Add News Report</h1>
        <div class="news-container">
            <?php if (!empty($errorMessage)) echo "<p style='color: red;'>$errorMessage</p>"; ?>
            <?php if (!empty($successMessage)) echo "<p style='color: green;'>$successMessage</p>"; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div>
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="8" required></textarea>
                </div>

                <div>
                    <label for="image">Upload Image:</label>
                    <input type="file" id="image" name="image" accept="image/*"  onchange="previewImage(event)">
                </div>

                <!-- Image Preview -->
                <div class="image-preview">
                    <img id="preview" alt="Image Preview">
                </div>

                <div>
                    <button type="submit">Submit Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById("preview");
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function () {
                    preview.src = reader.result;
                    preview.style.display = "block";
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = "none";
            }
        }
    </script>

</body>
 <?php 
 include('footer.php');
?>