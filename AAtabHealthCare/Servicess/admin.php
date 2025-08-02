<?php
include 'connection.php';

// ✅ Create the table if it doesn't exist
$createTableSQL = "
CREATE TABLE IF NOT EXISTS immunization_section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heading VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
)";
$conn->query($createTableSQL);

// ✅ Insert logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $heading = $_POST['heading'];
    $content = $_POST['content'];

    // Image Upload Handling
    $targetDir = "uploads1/";
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;
    // Create folder if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Optional: check file type
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Save path to DB
            $sql = "INSERT INTO immunization_section (heading, content, image) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $heading, $content, $targetFile);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>✅ Content added with image successfully.</p>";
            } else {
                echo "<p style='color:red;'>❌ DB Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color:red;'>❌ Failed to upload image.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Invalid image type. Only JPG, PNG, or GIF allowed.</p>";
    }
}
?>

<!-- ✅ Admin Form -->
<h2>Add Immunization Content</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <label><strong>Heading:</strong></label><br>
    <input type="text" name="heading" required><br><br>

    <label><strong>Content:</strong></label><br>
    <textarea name="content" rows="5" required></textarea><br><br>

    <label><strong>Upload Image:</strong></label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <input type="submit" value="Add Content">
</form>
