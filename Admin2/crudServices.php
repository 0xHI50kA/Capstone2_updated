<?php
ob_start();
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header3.php');
include('../AAtabHealthCare/servicess/connection.php');

// Delete logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $getImage = $conn->prepare("SELECT image_admin_path FROM immunization_section WHERE id = ?");
    $getImage->bind_param("i", $id);
    $getImage->execute();
    $result = $getImage->get_result();
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['image_admin_path'])) {
            unlink($row['image_admin_path']);
        }
    }
    $getImage->close();

    $delete = $conn->prepare("DELETE FROM immunization_section WHERE id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();
    $delete->close();

    header("Location: crudServices.php?deleted=1");
    exit;
}

// Fetch all records
$data = [];
$result = $conn->query("SELECT * FROM immunization_section ORDER BY id DESC");
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
    <title>Manage Immunization Content</title>
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

        

        button.button {
            padding: 4px 10px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        a.button{
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
    <h1>Manage Services</h1>

    <div class="news-container">
        <a href="updateServices.php" class="button">➕ Add Services</a>
        <br><br>
        <table>
            <thead>
                <tr>
                    <th>Heading</th>
                    <th>Image</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td style="width: 150px;"><?php echo htmlspecialchars($row['heading']); ?></td>
                            <td><img class="img" src="<?php echo htmlspecialchars($row['image_admin_path']); ?>" alt="Immunization Image"></td>
                            <td><?php echo htmlspecialchars(substr($row['content'], 0, 80)); ?>...</td>
                            <td style="width: 70px;">
                                <a style="width:63px;" href="editServices.php?id=<?php echo $row['id']; ?>" class="button">Edit</a>
                                <button class="button delete" onclick="showModal(<?php echo $row['id']; ?>)">Delete</button>
                                <!-- <a style="width:63px;" href="?delete=<?php echo $row['id']; ?>" class="button delete" onclick="return confirm('Delete this record?')">Delete</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No immunization records available.</td></tr>
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
            window.location.href = `crudServices.php?delete=${id}`;
        }, 1500);
    }

    // Optional: hide toast if using GET redirect
    setTimeout(() => {
        const toast = document.querySelector('.success-toast');
        if (toast) toast.style.display = 'none';
    }, 4000);
</script>

<?php include('footer.php'); ob_end_flush(); ?>
</body>
</html>
