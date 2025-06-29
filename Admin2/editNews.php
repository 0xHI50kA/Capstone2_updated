<?php
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header2.php');
include('includes/connection.php'); // uses $connection (MySQLi)

// Check if ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request: No ID specified.");
}

$id = intval($_GET['id']);
$message = "";

// Fetch current data
$query = mysqli_query($connection, "SELECT * FROM news WHERE id = $id");
if (!$query || mysqli_num_rows($query) === 0) {
    die("News not found.");
}
$news = mysqli_fetch_assoc($query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $content = mysqli_real_escape_string($connection, $_POST['content']);
    $image = $news['image']; // Default to old image

    // Check if new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetPath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $imageName;
        } else {
            $message = "Image upload failed. Keeping old image.";
        }
    }

    // Update DB
    $update = mysqli_query($connection, "UPDATE news SET title='$title', content='$content', image='$image' WHERE id=$id");
    if ($update) {
        $message = "News updated successfully. Redirecting...";

        // Refresh data
        $query = mysqli_query($connection, "SELECT * FROM news WHERE id = $id");
        $news = mysqli_fetch_assoc($query);

        // Redirect after a second
        echo "<script>
            setTimeout(function() {
                window.location.href = 'crud.php';
            }, 00);
        </script>";
    } else {
        $message = "Failed to update news.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit News</title>
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
    <h1>Edit News</h1>

    <div class="news-container">
        <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

        <form id="newsForm" method="POST" action="" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="8" required><?php echo htmlspecialchars($news['content']); ?></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">

            <div class="image-preview">
                <img id="preview" src="uploads/<?php echo htmlspecialchars($news['image']); ?>" alt="Image Preview" style="display: block;">
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
        <p id="modalMessage">Are you sure you want to update this news post?</p>

        <div id="modalForm">
            <button type="button" class="confirm" onclick="confirmSubmit()">Yes, Update</button>
            <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
        </div>

        <div id="successMessage">✅ News edited successfully!</div>
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
        document.getElementById('modalMessage').style.display = 'block';
        document.getElementById('modalTitle').innerText = '📌 Confirm Update';
        document.getElementById('successMessage').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function confirmSubmit() {
        document.getElementById('modalForm').style.display = 'none';
        document.getElementById('modalMessage').style.display = 'none';
        document.getElementById('modalTitle').innerText = '✅ Update Submitted!';
        document.getElementById('successMessage').style.display = 'block';

        setTimeout(() => {
            document.getElementById('newsForm').submit();
        }, 1500);
    }
</script>
</body>
</html>

<?php include('footer.php'); ?>
