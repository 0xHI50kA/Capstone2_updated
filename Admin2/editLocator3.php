<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header3.php');
include '../AAtabHealthCare/servicess/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid request: No ID provided.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("❌ Record not found.");
}
$row = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mapEmbed = $_POST['map_embed'];
    $imagePath = $row['image_path'];

    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES["image"]["name"]);
        $targetDir = "uploads5/";
        $imagePath = $targetDir . $imageName;

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                echo "<p style='color:red;'>❌ Failed to upload new image.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Invalid image type.</p>";
        }
    }

    $stmt = $conn->prepare("UPDATE hospitals SET name=?, image_path=?, map_embed=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $imagePath, $mapEmbed, $id);

    if ($stmt->execute()) {
        echo "<script>
            setTimeout(function() {
                window.location.href = 'crudLocator3.php?updated=1';
            }, 1500);
        </script>";
    } else {
        echo "<p style='color:red;'>❌ Update failed: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hospital</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .page-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            min-height: 100vh;
        }

        .form-container {
            width: 90%;
            max-width: 800px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 5px;
            display: block;
        }

        button {
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #confirmModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
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
            border: none;
            border-radius: 5px;
        }

        .confirm {
            background: green;
            color: white;
        }

        .cancel {
            background: gray;
            color: white;
        }

        #successMessage {
            color: green;
            font-weight: bold;
            display: none;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="form-container">
        <h1>Edit Hospital</h1>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            <label for="name">Hospital Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

            <label for="image">Upload New Image:</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
            <div class="image-preview">
                <img id="preview" src="<?= htmlspecialchars($row['image_path']) ?>" alt="Current Image">
            </div>

            <label for="map_embed">Google Maps Embed Code:</label>
            <textarea name="map_embed" rows="4" required><?= htmlspecialchars($row['map_embed']) ?></textarea>

            <div>
                <button type="button" onclick="openModal()">Submit Update</button>
            </div>
        </form>
    </div>
</div>

<div id="confirmModal">
    <div class="modal-content">
        <h2>📌 Confirm Update</h2>
        <p>Are you sure you want to update this hospital?</p>
        <div>
            <button class="confirm" onclick="submitForm()">Yes, Update</button>
            <button class="cancel" onclick="closeModal()">Cancel</button>
        </div>
        <div id="successMessage">✅ Update successful!</div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById("preview");
    const reader = new FileReader();
    reader.onload = function () {
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function openModal() {
    document.getElementById("confirmModal").style.display = "flex";
    document.getElementById("successMessage").style.display = "none";
}

function closeModal() {
    document.getElementById("confirmModal").style.display = "none";
}

function submitForm() {
    document.getElementById("successMessage").style.display = "block";
    setTimeout(() => {
        document.getElementById("editForm").submit();
    }, 1200);
}
</script>

<?php include('footer.php'); ?>
</body>
</html>
