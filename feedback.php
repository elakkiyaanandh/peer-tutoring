<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Retrieve session ID targeted for feedback
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Feedback - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-container">
        <div class="nav" style="justify-content: center;">
            <a href="dashboard.php" style="font-size: 0.9em; font-weight: normal;">< Dashboard</a>
            <a href="sessions.php" style="font-size: 0.9em; font-weight: normal;">My Sessions</a>
        </div>
        
        <h2 style="text-align: center;">Session Feedback</h2>
        
        <?php if(isset($_SESSION['feedback_msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['feedback_msg']); unset($_SESSION['feedback_msg']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['feedback_error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['feedback_error']); unset($_SESSION['feedback_error']); ?></div>
        <?php endif; ?>

        <form action="feedback_process.php" method="POST">
            <!-- Hidden field bridging frontend feedback to the correct backend session record -->
            <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
            
            <label for="rating">Tutor Rating (1 to 5)</label>
            <select id="rating" name="rating" required>
                <option value="" disabled selected>Select Rating...</option>
                <option value="5">5 - Excellent Mastery</option>
                <option value="4">4 - Good Understanding</option>
                <option value="3">3 - Average Communication</option>
                <option value="2">2 - Poor Experience</option>
                <option value="1">1 - Terrible Session</option>
            </select>

            <label for="comments">Specific Comments</label>
            <textarea id="comments" name="comments" rows="4" placeholder="How did the tutoring session help you? Could anything be improved?" required></textarea>

            <button type="submit">Publish Constructive Feedback</button>
        </form>
        <p style="margin-top: 1rem; text-align: center;"><a href="sessions.php">Return to Active Sessions</a></p>
    </div>
</body>
</html>
