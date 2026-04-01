<?php
// skills_process.php
session_start();
require 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the action is to add a new skill
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $skill_name = trim($_POST['skill_name']);
        $type = $_POST['type']; // Either 'teach' or 'learn'

        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $skill_name, $type);
        $stmt->execute();
        $stmt->close();
    } 
    // Check if the action is to delete an existing skill
    elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $skill_id = $_POST['skill_id'];
        
        // Ensure the person deleting the skill actually owns it
        $stmt = $conn->prepare("DELETE FROM skills WHERE skill_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $skill_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect back to the skills page
    header("Location: skills.php");
    exit();
}
?>
