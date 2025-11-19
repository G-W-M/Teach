<?php
require_once "../../database/conf.php";

echo "==== UNIT TEST: CERTIFICATE GENERATION ====\n";

$tutor_id = 3;
$type = "High Performance";

$stmt = $conn->prepare("
    INSERT INTO certificates (tutor_id, certificate_type)
    VALUES (?, ?)
");
$stmt->bind_param("is", $tutor_id, $type);
$result = $stmt->execute();

echo $result
    ? "✔ Certificate Insert Test Passed\n"
    : "✘ Certificate Insert Test Failed\n";

$q = $conn->query("SELECT certificate_id FROM certificates WHERE tutor_id=$tutor_id ORDER BY certificate_id DESC LIMIT 1");

echo $q->num_rows > 0
    ? "✔ Certificate Retrieval Passed\n"
    : "✘ Certificate Retrieval Failed\n";

echo "==============================\n";
