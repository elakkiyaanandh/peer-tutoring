<?php
// session_process.php
session_start();
require 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    // Booking a new session request
    if ($action == 'request') {
        $tutor_id = trim($_POST['tutor_id']);
        $skill = trim($_POST['skill']);
        $date = trim($_POST['date']);
        $time = trim($_POST['time']);

        // Learner (current user) booking a tutor
        $stmt = $conn->prepare("INSERT INTO sessions (learner_id, tutor_id, skill, date, time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iisss", $user_id, $tutor_id, $skill, $date, $time);
        
        if ($stmt->execute()) {
            $_SESSION['session_msg'] = "Session successfully requested.";
        } else {
            $_SESSION['session_error'] = "Error booking session.";
        }
        $stmt->close();
        header("Location: match.php"); // redirect back to booking page
        exit();
    } 
    // Accepting, rejecting, or completing an existing session
    elseif (in_array($action, ['accepted', 'rejected', 'completed'])) {
        $session_id = $_POST['session_id'];
        $status = $action;
        
        // Ensure that the logged-in user is actually a participant in the session before updating status
        $stmt = $conn->prepare("UPDATE sessions SET status = ? WHERE session_id = ? AND (tutor_id = ? OR learner_id = ?)");
        $stmt->bind_param("siii", $status, $session_id, $user_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['session_msg'] = "Session marked as $status.";
        } else {
            $_SESSION['session_error'] = "Error updating session.";
        }
        $stmt->close();
        header("Location: sessions.php");
        exit();
    }
}
?>
