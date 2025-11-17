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
CREATE TABLE IF NOT EXISTS pharmacies (
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
    $getImage = $conn->prepare("SELECT image_path FROM pharmacies WHERE id = ?");
    $getImage->bind_param("i", $id);
    $getImage->execute();
    $result = $getImage->get_result();
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }
    $getImage->close();

    $delete = $conn->prepare("DELETE FROM pharmacies WHERE id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();
    $delete->close();

    header("Location: crudLocator2.php?deleted=1");
    exit;
}

// 3. FETCH DATA
$data = [];
$result = $conn->query("SELECT * FROM pharmacies ORDER BY id DESC");
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
    <title>Manage Medical Clinics</title>
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

        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <h1>Manage Pharmacies</h1>

    <div class="news-container">
        <a href="updateLocator2.php" class="button">➕ Add Healthcare</a>
        <label for="categorySelect"><strong>View Category:</strong></label>
        <select id="categorySelect" onchange="navigateCategory()" style="padding: 8px; border-radius: 6px; font-size: 14px;">
            <option value="crudLocator.php">🦷 Dental Clinics</option>
            <option value="crudLocator1.php">🩺 Medical Clinics</option>
            <option value="crudLocator2.php" selected>💊 Pharmacies</option>
            <option value="crudLocator3.php">🏥 Hospitals</option>
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
                                <a style="width:63px;" href="editLocator2.php?id=<?php echo $row['id']; ?>" class="button">Edit</a>
                                <button class="button delete" onclick="showModal(<?php echo $row['id']; ?>)">Delete</button>
                                <!-- <a href="?delete=<?php echo $row['id']; ?>" class="button delete" onclick="return confirm('Delete this clinic?')">Delete</a> -->
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

<!-- Delete Confirmation Modal -->
<div id="confirmModal">
    <div class="modal-content">
        <h2>❗ Confirm Deletion</h2>
        <p>Are you sure you want to delete this news post?</p>
        <div id="modalForm">
            <input type="hidden" id="deleteId">
            <button type="button" class="button delete" onclick="performDelete()">Yes, Delete</button>
            <button type="button" class="button" onclick="closeModal()">Cancel</button>
        </div>
        <div id="successMessage" style="display: none; color: green; font-weight: bold; margin-top: 15px;">
            ✅ Deleted successfully!
        </div>
    </div>
</div>

<script>
    function showModal(id) {
        document.getElementById('deleteId').value = id;
        document.getElementById('confirmModal').style.display = 'flex';
        document.getElementById('modalForm').style.display = 'block';
        document.getElementById('successMessage').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function performDelete() {
        const id = document.getElementById('deleteId').value;
        // Hide buttons
        document.getElementById('modalForm').style.display = 'none';
        // Show success
        document.getElementById('successMessage').style.display = 'block';

        // Wait a bit and redirect
        setTimeout(() => {
            window.location.href = `crudLocator2.php?delete=${id}`;
        }, 1500);
    }

    // Optional: hide toast if using GET redirect
    setTimeout(() => {
        const toast = document.querySelector('.success-toast');
        if (toast) toast.style.display = 'none';
    }, 4000);
</script>

<?php include('footer.php'); ob_end_flush(); ?>
<script>
    function navigateCategory() {
    const selectedPage = document.getElementById("categorySelect").value;
    window.location.href = selectedPage;
}
</script>
</body>
</html>
