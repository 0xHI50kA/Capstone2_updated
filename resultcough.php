<?php
session_start();

$answers = $_SESSION['answers'] ?? [];

$yesCount = count(array_filter($answers, fn($a) => $a === 'Yes'));
$noCount = count(array_filter($answers, fn($a) => $a === 'No'));

// Determine response
if ($yesCount >= 8) {
    $conditionTitle = "⚠️ Critical Respiratory Warning";
    $conditionDetail = "Your responses indicate a high likelihood of a serious respiratory condition such as tuberculosis, pneumonia, or chronic bronchitis.";
    $suggestions = [
        "Seek immediate medical attention for proper diagnosis and treatment.",
        "Avoid strenuous activities that may worsen your symptoms.",
        "Wear a mask in public and avoid exposure to infection sources.",
        "Keep track of additional symptoms like chest pain, high fever, or weight loss.",
        "Do not self-medicate without professional guidance."
    ];
} elseif ($yesCount >= 5) {
    $conditionTitle = "🔶 Possible Respiratory Conditions";
    $conditionDetail = "Your symptoms may indicate early signs of asthma, respiratory inflammation, or allergen-triggered coughs.";
    $suggestions = [
        "Avoid exposure to allergens (e.g., dust, pollen, smoke) that may trigger or worsen symptoms.",
        "Ensure proper hydration to help relieve airway discomfort.",
        "Practice controlled breathing techniques to ease shortness of breath.",
        "Consider using an air purifier to improve indoor air quality.",
        "Monitor your symptoms for changes or worsening conditions."
    ];
} elseif ($yesCount >= 2) {
    $conditionTitle = "🟡 Mild Respiratory Concerns";
    $conditionDetail = "You show some signs of respiratory irritation, possibly due to temporary factors like dry air, mild infection, or physical activity.";
    $suggestions = [
        "Rest and drink warm fluids.",
        "Avoid dusty or smoky environments.",
        "Use steam inhalation or saline sprays to soothe airways.",
        "Keep a symptom diary to monitor progression.",
        "See a healthcare provider if symptoms persist for more than a week."
    ];
} else {
    $conditionTitle = "✅ No Significant Respiratory Issues";
    $conditionDetail = "Based on your responses, there is no strong indication of a respiratory issue at this time.";
    $suggestions = [
        "Maintain a healthy lifestyle to prevent illness.",
        "Stay hydrated and avoid smoking or secondhand smoke.",
        "Wash your hands frequently to avoid infections.",
        "Get enough rest and boost your immunity.",
        "Re-evaluate if new symptoms appear later."
    ];
}

// Reset session
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cough Symptom Checker Result</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6fc;
            padding: 40px;
        }
        .result-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0056b3;
        }
        .condition {
            margin: 20px 0;
            font-size: 18px;
        }
        .suggestions {
            margin-top: 30px;
        }
        .suggestions h3 {
            color: red;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        ul li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        ul li::before {
            content: "⚕️";
            position: absolute;
            left: 0;
        }
        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="result-box">
    <h2>🧾 Cough Symptom Checker Result</h2>
    <div class="condition">
        <p><strong>As your AI Symptom Checker:</strong></p>
        <p><strong>Diagnosis:</strong> <?= htmlspecialchars($conditionTitle) ?></p>
        <p><?= htmlspecialchars($conditionDetail) ?></p>
    </div>

    <div class="suggestions">
        <h3>WHAT TO DO NOW:</h3>
        <ul>
            <?php foreach ($suggestions as $tip): ?>
                <li><?= htmlspecialchars($tip) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <a class="btn" href="coughai.php">🔁 Retake Symptom Checker</a>
</div>
</body>
</html>
