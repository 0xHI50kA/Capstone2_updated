<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header2.php');

// PDO Database connection
$host = "localhost";
$dbname = "hms_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pharmacies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            map_embed TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $mapEmbed = $_POST["map_embed"];

    $targetDir = "uploads4/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $sql = "INSERT INTO pharmacies (name, image_path, map_embed) VALUES (:name, :image, :embed)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":image", $targetFile);
        $stmt->bindParam(":embed", $mapEmbed);

        if ($stmt->execute()) {
            echo "<script>setTimeout(() => { window.location.href = 'crudLocator2.php'; }, 0);</script>";
        } else {
            $errorMessage = "Failed to save pharmacy.";
        }
    } else {
        $errorMessage = "❌ Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Pharmacy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0; padding: 0;
        }

        .page-wrapper {
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 90%;
            max-width: 800px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            width: 100%;
        }

        .btn-primary {
            padding: 12px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #218838;
        }

        .image-preview {
            text-align: center;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 250px;
            display: none;
            border-radius: 8px;
        }

        .error, .success {
            text-align: center;
            font-weight: bold;
            color: red;
        }

        #confirmModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .modal-content button {
            margin: 5px;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .confirm { background: green; color: white; }
        .cancel { background: gray; color: white; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="container">
        <h1>Add Pharmacy</h1>

        <?php if (!empty($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>

        <form id="pharmacyForm" method="POST" enctype="multipart/form-data">
            <label>Pharmacy Name:</label>
            <input type="text" name="name" required>

            <label>Pharmacy Image:</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)" required>

            <label>Google Maps Embed Code:</label>
            <textarea name="map_embed" rows="4" placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>' required></textarea>

            <div class="image-preview">
                <img id="preview" alt="Preview Image">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-primary" onclick="openModal()">Submit</button>
                <a href="crudLocator2.php" style="text-decoration:none; background:#6c757d; color:white; padding:12px 20px; border-radius:6px;">Back</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="confirmModal">
    <div class="modal-content">
        <h2>📌 Confirm Submission</h2>
        <p>Are you sure you want to save this pharmacy?</p>
        <button class="confirm" onclick="confirmSubmit()">Yes, Submit</button>
        <button class="cancel" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const preview = document.getElementById("preview");
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function openModal() {
        document.getElementById("confirmModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("confirmModal").style.display = "none";
    }

    function confirmSubmit() {
        document.getElementById("pharmacyForm").submit();
    }
</script>

<?php include('footer.php'); ?>
</body>
</html>
