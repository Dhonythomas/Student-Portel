<?php
header('Content-Type: application/json');
require 'connect.php';

// --- Read POST JSON data ---
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['student_id']) || empty($data['student_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Student ID is required"]);
    exit;
}

$student_id = mysqli_real_escape_string($conn, $data['student_id']);

// --- SQL Query ---
// Ensure your 'college_fee' table actually has a column named 'fee_structure_path'
$sql = "
    SELECT 
        studentfeecollege.id AS student_fee_id,
        studentfeecollege.student_id,
        studentfeecollege.department,
        studentfeecollege.status,
        college_fee.fee_id,
        college_fee.fee_title,
        college_fee.amount,
        college_fee.last_date,
        college_fee.fee_structure_path
    FROM studentfeecollege
    JOIN college_fee 
      ON studentfeecollege.feeId = college_fee.fee_id
    WHERE studentfeecollege.student_id = '$student_id'
      AND studentfeecollege.status = 'pending'
      AND college_fee.active_flag = TRUE
";

$result = mysqli_query($conn, $sql);

$fees = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fees[] = [
            "feeid" => $row['fee_id'],
            "feeTitle" => $row['fee_title'],
            "amount" => $row['amount'],
            "lastdate" => $row['last_date'],
            "studentid" => $row['student_id'],
            "department" => $row['department'],
            "fee_structure_path" => $row['fee_structure_path'] ? $row['fee_structure_path'] : ""
        ];
    }
}

echo json_encode($fees);

mysqli_close($conn);
?>
