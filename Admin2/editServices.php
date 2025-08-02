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
$stmt = $conn->prepare("SELECT * FROM immunization_section WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("❌ Record not found.");
}
$row = $result->fetch_assoc();
$stmt->close();

// Form handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'];
    $content = $_POST['content'];
    $adminPath = $row['image_admin_path'];
    $userPath = $row['image_user_path'];

    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES["image"]["name"]);
        $targetDir = "uploads1/";
        $adminPath = $targetDir . $imageName;
        $userPath = "/AtabsHealthCare10/AAtabHealthCare/servicess/uploads1/" . $imageName;

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($adminPath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (file_exists($row['image_admin_path'])) {
                unlink($row['image_admin_path']);
            }

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $adminPath)) {
                echo "<p style='color:red;'>❌ Failed to upload new image.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Invalid image type.</p>";
        }
    }

    $stmt = $conn->prepare("UPDATE immunization_section SET heading=?, content=?, image_admin_path=?, image_user_path=? WHERE id=?");
    $stmt->bind_param("ssssi", $heading, $content, $adminPath, $userPath, $id);

    if ($stmt->execute()) {
        echo "<script>
            setTimeout(function() {
                window.location.href = 'crudServices.php?updated=1';
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
    <title>Edit Immunization</title>
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
        }

        .success {
            color: green;
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
    <h1>Edit Immunization Content</h1>
    <div class="news-container">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            <label for="heading">Heading:</label>
            <input type="text" name="heading" id="heading" value="<?= htmlspecialchars($row['heading']) ?>" required>

            <label for="content">Content:</label>
            <textarea name="content" id="content" rows="6" required><?= htmlspecialchars($row['content']) ?></textarea>

            <label for="image">Upload New Image:</label>
            <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">

            <div class="image-preview">
                <img id="preview" src="<?= htmlspecialchars($row['image_admin_path']) ?>" alt="Current Image">
            </div>

            <div>
                <button type="button" onclick="openModal()">Submit Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="confirmModal">
    <div class="modal-content">
        <h2 id="modalTitle">📌 Confirm Update</h2>
        <p>Are you sure you want to update this record?</p>
        <div id="modalForm">
            <button class="confirm" onclick="submitForm()">Yes, Update</button>
            <button class="cancel" onclick="closeModal()">Cancel</button>
        </div>
        <div id="successMessage">✅ Update successful!</div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById("preview");
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
}

function openModal() {
    document.getElementById('confirmModal').style.display = 'flex';
    document.getElementById('successMessage').style.display = 'none';
}

function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

function submitForm() {
    document.getElementById('modalForm').style.display = 'none';
    document.getElementById('successMessage').style.display = 'block';
    setTimeout(() => {
        document.getElementById('editForm').submit();
    }, 1200);
}
</script>
</body>
</html>
<?php include('footer.php'); ?>