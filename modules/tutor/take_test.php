<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../includes/session_check.php';
require_once '../../database/conf.php';
require_once '../../includes/logger.php';

if (!isset($_SESSION['user_id'])) die("You must be logged in.");
$user_id = $_SESSION['user_id'];

$unit_id = $_GET['unit'] ?? null;
if (!$unit_id) die("Invalid test. Please select a subject first.");

// Get unit details
$unit_stmt = $conn->prepare("SELECT unit_code, unit_name FROM units WHERE unit_id=?");
$unit_stmt->bind_param("i", $unit_id);
$unit_stmt->execute();
$unit = $unit_stmt->get_result()->fetch_assoc();
if (!$unit) die("Invalid subject selected.");

// Check if user already passed
$check_test = $conn->prepare("SELECT * FROM tutor_tests WHERE user_id=? AND unit_id=? AND passed=1");
$check_test->bind_param("ii", $user_id, $unit_id);
$check_test->execute();
if ($check_test->get_result()->num_rows > 0) {
    die("You have already passed this test for " . htmlspecialchars($unit['unit_name']));
}

// Fetch questions
$questions_stmt = $conn->prepare("SELECT * FROM test_questions WHERE unit_id=? ORDER BY RAND() LIMIT 10");
$questions_stmt->bind_param("i", $unit_id);
$questions_stmt->execute();
$questions = $questions_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);
if ($total_questions === 0) die("No questions available for this subject yet.");

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    foreach ($questions as $q) {
        $qid = $q['question_id'];
        $user_answer = $_POST['question_' . $qid] ?? null;
        if ($user_answer && (int)$user_answer === (int)$q['correct_option']) $score++;
    }

    $passing_score = ceil($total_questions * 0.7);
    $passed = $score >= $passing_score ? 1 : 0;

    $save_stmt = $conn->prepare("INSERT INTO tutor_tests (user_id, unit_id, score, total_questions, passed) VALUES (?,?,?,?,?)");
    $save_stmt->bind_param("iiiii", $user_id, $unit_id, $score, $total_questions, $passed);
    $save_stmt->execute();

    $_SESSION['test_results'] = [
        'score'=>$score,
        'total'=>$total_questions,
        'passed'=>$passed,
        'unit_name'=>$unit['unit_name'],
        'unit_code'=>$unit['unit_code'],
        'unit_id'=>$unit_id
    ];

    header("Location: test_results.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Take Test - <?= htmlspecialchars($unit['unit_name']) ?></title>
<link rel="stylesheet" href="../../assets/css/tutor.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif;}
body {
    height:100vh;
    width:100%;
    background: url('../img/bg.jpeg') no-repeat center center;
    background-size: cover;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
}
.overlay {
    position:absolute;
    top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.4);
    z-index:1;
}
.test-wrapper {
    position:relative;
    z-index:10;
    width:90%;
    max-width:700px;
    overflow-y:auto;
    max-height:90vh;
}
.test-box {
    background: rgba(255,255,255,0.95);
    padding:30px;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,0.2);
}
.test-box h2 {
    color:#003366;
    text-align:center;
    margin-bottom:20px;
}
fieldset {
    border:1px solid #ccc;
    border-radius:8px;
    padding:15px;
    margin-bottom:15px;
}
legend {
    font-weight:600;
}
input[type=radio] {
    margin-right:10px;
}
button {
    width:100%;
    background:#003366;
    color:#fff;
    border:none;
    padding:12px;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}
button:hover { background:#0059b3; }
.error { color:red; margin-bottom:10px; }
</style>
</head>
<body>
<div class="overlay"></div>
<div class="test-wrapper">
<div class="test-box">
<h2>Test: <?= htmlspecialchars($unit['unit_code'] . ' - ' . $unit['unit_name']) ?></h2>

<form method="POST">
    <?php foreach($questions as $idx => $q): ?>
        <fieldset>
            <legend>Q<?= $idx+1 ?>: <?= htmlspecialchars($q['question_text']) ?></legend>
            <?php for($i=1;$i<=4;$i++): ?>
                <label>
                    <input type="radio" name="question_<?= $q['question_id'] ?>" value="<?= $i ?>" required>
                    <?= htmlspecialchars($q['option_'.$i]) ?>
                </label><br>
            <?php endfor; ?>
        </fieldset>
    <?php endforeach; ?>
    <div style="display:flex; justify-content:space-between; margin-top:15px;">
    <a href="tutor_dash.php" 
       class="btn" 
       style="background:#e74c3c; padding:10px 20px; border-radius:8px; text-decoration:none; color:white;"
       onclick="return confirm('Are you sure you want to quit the test? Your answers will not be saved.')">
       Quit Test
    </a>

    <button type="submit" style="flex:1; margin-left:10px;">Submit Test</button>
</div>
</form>
</div>
</div>
</body>
</html>
