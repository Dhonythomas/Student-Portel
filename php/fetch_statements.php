<?php
// --- DATABASE CONNECTION ---
include 'connect.php';

if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

header('Content-Type: application/json');

// --- MAIN LOGIC ---
$statementType = $_POST['statementType'] ?? '';

if ($statementType === 'college') {
    fetchCollegeStatements($conn);
} elseif ($statementType === 'exam') {
    fetchExamStatements($conn);
} else {
    echo json_encode(["error" => "Invalid statement type specified."]);
}

$conn->close();

// --- FUNCTION FOR COLLEGE STATEMENTS ---
function fetchCollegeStatements($conn) {
    $sql = "SELECT student_id, FeeTitle, department, semester, year, amount, upi_id FROM studentfeecollege WHERE status = 'paid'";
    $params = [];
    $types = "";

    if (isset($_POST['feeTitle']) && $_POST['feeTitle'] !== 'all') {
        $sql .= " AND FeeTitle = ?";
        $params[] = $_POST['feeTitle'];
        $types .= "s";
    }
    if (isset($_POST['department']) && $_POST['department'] !== 'all') {
        $sql .= " AND department = ?";
        $params[] = $_POST['department'];
        $types .= "s";
    }
    if (isset($_POST['semester']) && $_POST['semester'] !== 'all') {
        $sql .= " AND semester = ?";
        $params[] = (int)$_POST['semester'];
        $types .= "i";
    }
    if (!empty($_POST['year'])) {
        $sql .= " AND year = ?";
        $params[] = (int)$_POST['year'];
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($data);
    $stmt->close();
}

// --- FUNCTION FOR EXAM STATEMENTS ---
// --- FUNCTION FOR EXAM STATEMENTS ---
function fetchExamStatements($conn) {
    // Corrected the column names in the SELECT statement to match your table
    $sql = "SELECT studentid AS student_id, examid, examname, department, semester, amount, upi_id FROM studentfeeexam WHERE status = 'paid'";
    $params = [];
    $types = "";

    if (!empty($_POST['studentId'])) {
        $sql .= " AND studentid = ?";
        $params[] = $_POST['studentId'];
        $types .= "s";
    }
    if (isset($_POST['department']) && $_POST['department'] !== 'all') {
        $sql .= " AND department = ?";
        $params[] = $_POST['department'];
        $types .= "s";
    }
    if (isset($_POST['semester']) && $_POST['semester'] !== 'all') {
        $sql .= " AND semester = ?";
        $params[] = (int)$_POST['semester'];
        $types .= "i"; // Your correct fix is here!
    }

    $stmt = $conn->prepare($sql);
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        // Handle error, e.g., log it or send an error response
        echo json_encode(["error" => "Failed to prepare the SQL statement."]);
        return;
    }

    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($data);
    $stmt->close();
}
?>