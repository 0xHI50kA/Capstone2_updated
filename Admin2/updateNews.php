<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header2.php');

// Database connection
$host = "localhost";
$dbname = "hms_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            image VARCHAR(255) NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $sql = "INSERT INTO news (title, content, image) VALUES (:title, :content, :image)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":image", $imageName);

        if ($stmt->execute()) {
            echo "<script>
                setTimeout(function(){ window.location.href = 'crud.php'; }, 00);
            </script>";
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
    <title>News Reports</title>
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

        /* Modal */
        #confirmModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 99999;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            min-width: 300px;
        }

        #modalForm button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #modalForm .confirm {
            background-color: green;
            color: white;
        }

        #modalForm .cancel {
            background-color: gray;
            color: white;
        }

        #successMessage {
            display: none;
            color: green;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <h1>Add News Report</h1>
    <div class="news-container">
        <?php if (!empty($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>
        <?php if (!empty($successMessage)) echo "<p class='success'>$successMessage</p>"; ?>

        <form id="newsForm" method="POST" action="" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="8" required></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>

            <div class="image-preview">
                <img id="preview" alt="Image Preview">
            </div>
            <div style="display: flex; gap: 10px;">
    <button type="button" class="btn-primary" onclick="openModal()">Submit</button>
    <a href="crud.php" class="btn-secondary" style="text-decoration: none; padding: 12px 20px; background-color: #6c757d; color: white; border-radius: 5px; text-align: center;">Back</a>
</div>
                
        </form>
    </div>
</div>

<!-- Submission Confirmation Modal -->
<div id="confirmModal">
    <div class="modal-content">
        <h2 id="modalTitle">📌 Confirm Submission</h2>
        <p id="modalMessage">Are you sure you want to add this news post?</p>

        <div id="modalForm">
            <button type="button" class="confirm" onclick="confirmSubmit()">Yes, Submit</button>
            <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
        </div>

        <div id="successMessage">✅ News added successfully!</div>
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

    function openModal() {
        document.getElementById('confirmModal').style.display = 'flex';
        document.getElementById('modalForm').style.display = 'block';
        document.getElementById('successMessage').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function confirmSubmit() {
        // Hide confirm buttons and show success message
        document.getElementById('modalForm').style.display = 'none';
        document.getElementById('modalMessage').style.display = 'none';
        document.getElementById('modalTitle').innerText = '✅ Added Succcessfully!';
        document.getElementById('successMessage').style.display = 'block';

        // Submit form after delay
        setTimeout(() => {
            document.getElementById('newsForm').submit();
        }, 1500);
    }
</script>

</body>
<?php include('footer.php'); ?>
</html>
