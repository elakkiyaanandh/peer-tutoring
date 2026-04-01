<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="skills.php">My Skills</a>
            <a href="match.php">Find Tutors</a>
            <a href="sessions.php">My Sessions</a>
            <a href="logout.php" style="margin-left:auto; color: #e74c3c;">Logout</a>
        </div>
        
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <p>This is your BrainBridge dashboard. From here, you can manage the subjects you want to teach or discover, find matching peers, and manage your tutoring sessions.</p>
        
        <div style="display: flex; gap: 20px; margin-top: 2rem;">
            <div style="flex: 1; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                <h3>Your Profile Skills</h3>
                <p>Manage what you can teach and what you want to learn successfully.</p>
                <a href="skills.php" style="display:inline-block; margin-top:10px; background:#3498db; color:white; padding:8px 15px; border-radius:4px; font-weight: bold;">Manage Skills</a>
            </div>
            <div style="flex: 1; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                <h3>Find Network Matches</h3>
                <p>Discover expert tutors who can teach the skills you want to precisely learn.</p>
                <a href="match.php" style="display:inline-block; margin-top:10px; background:#2ecc71; color:white; padding:8px 15px; border-radius:4px; font-weight: bold;">Find Matches</a>
            </div>
        </div>
    </div>
</body>
</html>
