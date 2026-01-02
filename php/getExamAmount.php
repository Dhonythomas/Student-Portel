<?php
header("Content-Type: application/json");
require 'connect.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate
if (empty($data['examCode'])) {
    echo json_encode(['amount' => 600]);
    exit;
}


$examCode  = mysqli_real_escape_string($conn, trim($data['examCode']));

// Fetch amount from exam_fees table (adjust table/column names if needed)
$sql = "SELECT amount FROM exam_fees WHERE exam_id='$examCode';";
$result = mysqli_query($conn, $sql);

$amount = 600; // Default amount
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $amount = floatval($row['amount']);
}

mysqli_close($conn);

// Return JSON
echo json_encode(['amount' => $amount]);
?>
