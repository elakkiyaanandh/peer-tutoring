<?php
// feedback_process.php
session_start();
require 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $session_id = $_POST['session_id'];
    $rating = $_POST['rating']; // Rating from 1 to 5
    $comments = trim($_POST['comments']);

    // Check if feedback already provided
    $check_stmt = $conn->prepare("SELECT feedback_id FROM feedback WHERE session_id = ?");
    $check_stmt->bind_param("i", $session_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    // Prevent duplicate feedback
    if ($check_stmt->num_rows > 0) {
        $_SESSION['feedback_error'] = "Feedback already submitted for this session.";
    } else {
        // Insert new feedback linked cleanly to a session
        $stmt = $conn->prepare("INSERT INTO feedback (session_id, rating, comments) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $session_id, $rating, $comments);
        
        if ($stmt->execute()) {
            $_SESSION['feedback_msg'] = "Thank you for your feedback!";
        } else {
            $_SESSION['feedback_error'] = "Failed to submit feedback.";
        }
        $stmt->close();
    }
    
    $check_stmt->close();
    
    // Redirect back to feedback page or dashboard
    header("Location: feedback.php");
    exit();
}
?>
