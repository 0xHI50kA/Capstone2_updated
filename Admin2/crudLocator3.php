<?php
ob_start();
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header4.php');
include('../AAtabHealthCare/servicess/connection.php');

// 1. CREATE TABLE IF NOT EXISTS
$createTableSQL = "
CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    map_embed TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTableSQL);

// 2. HANDLE DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $getImage = $conn->prepare("SELECT image_path FROM hospitals WHERE id = ?");
    $getImage->bind_param("i", $id);
    $getImage->execute();
    $result = $getImage->get_result();
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }
    $getImage->close();

    $delete = $conn->prepare("DELETE FROM hospitals WHERE id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();
    $delete->close();

    header("Location: crudLocator3.php?deleted=1");
    exit;
}

// 3. FETCH DATA
$data = [];
$result = $conn->query("SELECT * FROM hospitals ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hospitals</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th { background-color: #f2f2f2; }

        .img {
            width: 120px;
            height: auto;
            object-fit: cover;
            border-radius: 4px;
        }

        .map-embed iframe {
            width: 120px;
            height: 120px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button.button, a.button {
            padding: 4px 12px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 3px;
            border: none;
            cursor: pointer;
        }

        a.button:hover, button.button:hover { background-color: #0056b3; }

        .button.delete { background-color: red; }
        .button.delete:hover { background-color: darkred; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <h1>Hospitals</h1>

    <div class="news-container">
        <a href="updateLocator3.php" class="button">➕ Add Healthcare</a>
        <label for="categorySelect"><strong>View Category:</strong></label>
        <select id="categorySelect" onchange="navigateCategory()" style="padding: 8px; border-radius: 6px; font-size: 14px;">
            <option value="crudLocator.php">🦷 Dental Clinics</option>
            <option value="crudLocator1.php">🩺 Medical Clinics</option>
            <option value="crudLocator2.php">💊 Pharmacies</option>
            <option value="crudLocator3.php" selected>🏥 Hospitals</option>
        </select>
        <br><br>
<table>
            <thead>
                <tr>
                    <th>Clinic Name</th>
                    <th>Image</th>
                    <th>Map Embed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><img class="img" src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Clinic Image"></td>
                            <td class="map-embed"><?php echo $row['map_embed']; ?></td>
                            <td style="width: 70px;">
                                <a style="width:63px;" href="editLocator3.php?id=<?php echo $row['id']; ?>" class="button">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="button delete" onclick="return confirm('Delete this clinic?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No medical clinics found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ob_end_flush(); ?>
<script>
    function navigateCategory() {
    const selectedPage = document.getElementById("categorySelect").value;
    window.location.href = selectedPage;
}
</script>
</body>
</html>
