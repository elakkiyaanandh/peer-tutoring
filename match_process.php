<?php
// match_process.php
session_start();
require 'db.php';

// This file handles logic to find matching tutors for the logged-in user.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Complex JOIN Query: Find users who teach what the logged-in user wants to learn
// l_skills represents the 'learn' skills of the logged-in user
// t_skills represents the 'teach' skills of potential tutors
$query = "
    SELECT t_users.user_id AS tutor_id, t_users.name AS tutor_name, t_skills.skill_name 
    FROM skills l_skills
    JOIN skills t_skills ON l_skills.skill_name = t_skills.skill_name AND t_skills.type = 'teach'
    JOIN users t_users ON t_skills.user_id = t_users.user_id
    WHERE l_skills.user_id = ? AND l_skills.type = 'learn' AND t_users.user_id != ?
";

$stmt = $conn->prepare($query);
// Bind current user id to both l_skills.user_id and ensuring they don't match with themselves
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$matches = [];
// Store data to be used in match.php frontend
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

$stmt->close();
?>
