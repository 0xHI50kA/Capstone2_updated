<?php
session_start();

// Questions array
$questions = [
    "Do you have a persistent cough that has lasted for more than two weeks?",
    "Do you experience coughing at night that disrupts your sleep?",
    "Do you cough up blood or blood-streaked mucus?",
    "Is your cough accompanied by shortness of breath?",
    "Do you have a fever along with your cough?",
    "Do you experience wheezing while coughing?",
    "Do you cough after exposure to allergens like dust or pollen?",
    "Does your cough worsen after physical activity?",
    "Have you recently experienced unexplained weight loss with a cough?",
    "Do you feel chest pain or tightness when you cough?"
];

// Track current question
if (!isset($_SESSION['questionIndex'])) {
    $_SESSION['questionIndex'] = 0;
    $_SESSION['answers'] = [];
}

// Store answer from previous step
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['response'])) {
    $_SESSION['answers'][] = $_POST['response'];
    $_SESSION['questionIndex']++;
}

// If all questions answered, redirect
if ($_SESSION['questionIndex'] >= count($questions)) {
    header("Location: resultcough.php");
    exit();
}

// Current question
$currentIndex = $_SESSION['questionIndex'];
$currentQuestion = $questions[$currentIndex];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cough Symptom Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 40px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        button {
            margin: 10px;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
        }
        .yes { background-color: #28a745; color: white; }
        .no { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2>🩺 Cough Symptom Checker</h2>
    <p><strong>Question <?= $currentIndex + 1 ?> of <?= count($questions) ?>:</strong></p>
    <p><?= $currentQuestion ?></p>

    <form method="POST">
        <button type="submit" name="response" value="Yes" class="yes">Yes</button>
        <button type="submit" name="response" value="No" class="no">No</button>
    </form>
</div>
</body>
</html>