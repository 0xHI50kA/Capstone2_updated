<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "healthcare";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ Define symptom tables
$symptomTables = [
    "cough",
    "fatigue",
    "fever",
    "headache",
    "highblood_pressure",
    "muscle_pain",
    "nausea",
    "shortness_of_breath",
    "sore_throat",
    "weightloss"
];

// ✅ Create all tables if not exist
foreach ($symptomTables as $table) {
    $createSQL = "
    CREATE TABLE IF NOT EXISTS `$table` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        symptom_name VARCHAR(255) NOT NULL,
        level VARCHAR(100) NOT NULL,
        AIresponse TEXT NOT NULL,
        suggestions TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($createSQL);
}

// ✅ Get selected table
$currentTable = isset($_GET['table']) ? $_GET['table'] : $symptomTables[0];
if (!in_array($currentTable, $symptomTables)) $currentTable = $symptomTables[0];

$message = "";

// ✅ Add record
if (isset($_POST['add_entry'])) {
    $symptom_name = trim($_POST['symptom_name']);
    $level = trim($_POST['level']);
    $AIresponse = trim($_POST['AIresponse']);
    $suggestions = trim($_POST['suggestions']);

    if ($symptom_name && $level && $AIresponse && $suggestions) {
        $stmt = $conn->prepare("INSERT INTO `$currentTable` (symptom_name, level, AIresponse, suggestions) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $symptom_name, $level, $AIresponse, $suggestions);
        $stmt->execute();
        $stmt->close();
        $message = "<p style='color:green;'>✅ Entry added successfully to '$currentTable'.</p>";
    } else {
        $message = "<p style='color:red;'>❌ Please fill all fields.</p>";
    }
}

// ✅ Delete record
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM `$currentTable` WHERE id = $id");
    $message = "<p style='color:orange;'>🗑️ Entry deleted successfully.</p>";
}

// ✅ Edit (load data)
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM `$currentTable` WHERE id = $id");
    $editData = $result->fetch_assoc();
}

// ✅ Update record
if (isset($_POST['update_entry'])) {
    $id = intval($_POST['id']);
    $symptom_name = trim($_POST['symptom_name']);
    $level = trim($_POST['level']);
    $AIresponse = trim($_POST['AIresponse']);
    $suggestions = trim($_POST['suggestions']);

    $stmt = $conn->prepare("UPDATE `$currentTable` SET symptom_name=?, level=?, AIresponse=?, suggestions=? WHERE id=?");
    $stmt->bind_param("ssssi", $symptom_name, $level, $AIresponse, $suggestions, $id);
    $stmt->execute();
    $stmt->close();
    $message = "<p style='color:blue;'>✏️ Entry updated successfully.</p>";
}

// ✅ Fetch all entries from current table
$allResults = $conn->query("SELECT * FROM `$currentTable` ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - AI Symptom Data Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f2f7fc; }
        h2 { color: #333; }
        form { margin-bottom: 25px; padding: 20px; border: 1px solid #ccc; border-radius: 8px; width: 650px; background: #fff; }
        input[type=text], textarea, select { width: 97%; padding: 8px; margin: 5px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 8px 15px; border: none; background: #007bff; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; vertical-align: top; }
        th { background: #007bff; color: white; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .actions { white-space: nowrap; }
        /* .topnav {
            margin-bottom: 25px;
            padding: 10px 15px;
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 8px;
            display: inline-block;
        }

        .topnav form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topnav label {
            font-weight: bold;
        } */
    </style>
</head>
<body>

<h2>🧠 Admin: AI Symptom Response Management</h2>

    <form method="GET">
        <label for="table">Select Symptom Table:</label>
        <select name="table" id="table" onchange="this.form.submit()">
            <?php foreach ($symptomTables as $tbl): ?>
                <option value="<?= $tbl ?>" <?= $tbl == $currentTable ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $tbl)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

<?php if ($message) echo $message; ?>

<form method="POST">
    <h3><?= $editData ? "✏️ Edit Entry" : "➕ Add New Entry" ?> (<?= ucfirst(str_replace('_', ' ', $currentTable)) ?>)</h3>
    <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
    <?php endif; ?>

    <input type="text" name="symptom_name" placeholder="Symptom Name" value="<?= $editData['symptom_name'] ?? '' ?>" required>
    <input type="text" name="level" placeholder="Level (e.g., Mild, Moderate, Severe)" value="<?= $editData['level'] ?? '' ?>" required>
    <textarea name="AIresponse" placeholder="AI Response" required><?= $editData['AIresponse'] ?? '' ?></textarea>
    <textarea name="suggestions" placeholder="Suggestions" required><?= $editData['suggestions'] ?? '' ?></textarea>

    <?php if ($editData): ?>
        <button type="submit" name="update_entry">Update</button>
        <a href="?table=<?= $currentTable ?>" style="margin-left:10px; color:red;">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add_entry">Save</button>
    <?php endif; ?>
</form>

<h3>📋 Entries in "<?= ucfirst(str_replace('_', ' ', $currentTable)) ?>"</h3>

<table>
    <tr>
        <th>ID</th>
        <th>Symptom Name</th>
        <th>Level</th>
        <th>AI Response</th>
        <th>Suggestions</th>
        <th class="actions">Actions</th>
    </tr>
    <?php if ($allResults->num_rows > 0): ?>
        <?php while ($row = $allResults->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><strong><?= htmlspecialchars($row['symptom_name']) ?></strong></td>
                <td><?= htmlspecialchars($row['level']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['AIresponse'])) ?></td>
                <td><?= nl2br(htmlspecialchars($row['suggestions'])) ?></td>
                <td class="actions">
                    <a href="?table=<?= $currentTable ?>&edit=<?= $row['id'] ?>">✏️ Edit</a> |
                    <a href="?table=<?= $currentTable ?>&delete=<?= $row['id'] ?>" onclick="return confirm('Delete this entry?')">🗑️ Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">No entries found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
