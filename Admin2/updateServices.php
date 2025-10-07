<?php
ob_start();
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header3.php');
include('../AAtabHealthCare/servicess/connection.php');

// ✅ Step 1: Create table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS immunization_section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heading VARCHAR(255) NOT NULL,
    content TEXT NOT NULL
)";
$conn->query($createTableSQL);

// ✅ Step 2: Alter table to add new columns if not exist
$checkCol = $conn->query("SHOW COLUMNS FROM immunization_section LIKE 'image_admin_path'");
if ($checkCol->num_rows == 0) {
    $alterSQL = "
        ALTER TABLE immunization_section 
        ADD COLUMN image_admin_path VARCHAR(255) NOT NULL,
        ADD COLUMN image_user_path VARCHAR(255) NOT NULL
    ";
    $conn->query($alterSQL);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $heading = $_POST['heading'];
    $content = $_POST['content'];
    $imageName = basename($_FILES["image"]["name"]);

    // Admin path (for backend use)
    $adminDir = "uploads1/";
    $adminFullPath = $adminDir . $imageName;

    // User path (browser-accessible directory)
    $userDir = "../AAtabHealthCare/Servicess/uploads1/";
    $userFullPath = $userDir . $imageName;

    // URL to be saved in DB for user access
    $userURL = "/AtabsHealthCare10/AAtabHealthCare/Servicess/" . $userFullPath;

    // Create directories if they don't exist
    if (!file_exists($adminDir)) mkdir($adminDir, 0755, true);
    if (!file_exists($userDir)) mkdir($userDir, 0755, true);

    // Validate image type
    $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $adminFullPath)) {
            // Copy to browser-accessible directory
            if (copy($adminFullPath, $userFullPath)) {
                $sql = "INSERT INTO immunization_section (heading, content, image_admin_path, image_user_path) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $heading, $content, $adminFullPath, $userURL);

                if ($stmt->execute()) {
                    echo "<p style='color:green;'>✅ Content and image saved in both directories.</p>";
                } else {
                    echo "<p style='color:red;'>❌ DB Error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color:red;'>❌ Failed to copy image to user path.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to upload image to admin path.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Invalid image type. Only JPG, PNG, or GIF allowed.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Immunization Content</title>
    <style>
        * {
            margin: 0; padding: 0;
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

        .btn-primary {
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary:hover {
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
            display: none;
        }

        .success {
            color: green;
            font-weight: bold;
            text-align: center;
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <h1>Add Services</h1>

    <div class="news-container">
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form id="immunizationForm" method="POST" action="" enctype="multipart/form-data">
            <label for="heading">Heading:</label>
            <input type="text" id="heading" name="heading" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="8" required></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>

            <div class="image-preview">
                <img id="preview" alt="Image Preview" style="max-width: 100%; max-height: 300px; display: none;">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-primary">Submit</button>
                <a href="crudServices.php" class="btn-secondary" style="text-decoration: none; padding: 12px 20px; background-color: #6c757d; color: white; border-radius: 5px; text-align: center;">Back</a>
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
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
}
</script>


<?php include('footer.php'); ob_end_flush(); ?>
</body>
</html>
