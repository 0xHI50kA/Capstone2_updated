<?php
ob_start();
session_start();
if (empty($_SESSION['name'])) {
    header('location:index.php');
    exit;
}

include('header2.php');
include('includes/connection.php');

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $result = mysqli_query($connection, "SELECT image FROM news WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $image = $row['image'];

    if ($image && file_exists("uploads/$image")) {
        unlink("uploads/$image");
    }

    mysqli_query($connection, "DELETE FROM news WHERE id = $id");
    header("Location: crud.php?deleted=1");
    exit;
}

// Fetch news
$newsItems = [];
$result = mysqli_query($connection, "SELECT * FROM news ORDER BY id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $newsItems[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Health News</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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
            padding: 4px 18px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
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
    <h1>Health News</h1>



    <div class="news-container">
        <a href="updateNews.php" class="button">➕ Add News</a>
        <br><br>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($newsItems)): ?>
                <?php foreach ($newsItems as $news): ?>
                    <tr>
                        <td style="width: 150px;"><?php echo htmlspecialchars($news['title']); ?></td>
                        <td><img class="img" src="uploads/<?php echo htmlspecialchars($news['image']); ?>" alt="News Image"></td>
                        <td><?php echo htmlspecialchars(substr($news['content'], 0, 80)); ?>...</td>
                        <td style="width: 70px;">
                            <a href="editNews.php?id=<?php echo $news['id']; ?>" class="button">Edit</a>
                            <button class="button delete" onclick="showModal(<?php echo $news['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No news available.</td></tr>
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
            window.location.href = `crud.php?delete=${id}`;
        }, 1500);
    }

    // Optional: hide toast if using GET redirect
    setTimeout(() => {
        const toast = document.querySelector('.success-toast');
        if (toast) toast.style.display = 'none';
    }, 4000);
</script>

</body>
</html>

<?php 
include('footer.php');
ob_end_flush();
?>
