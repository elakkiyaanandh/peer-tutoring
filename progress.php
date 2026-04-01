<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];

// 1. Total sessions learned (Completed as learner)
$stmt = $conn->prepare("SELECT COUNT(*) as learned_count FROM sessions WHERE learner_id = ? AND status = 'completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$learned_count = $stmt->get_result()->fetch_assoc()['learned_count'];
$stmt->close();

// 2. Total sessions taught (Completed as tutor)
$stmt = $conn->prepare("SELECT COUNT(*) as taught_count FROM sessions WHERE tutor_id = ? AND status = 'completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$taught_count = $stmt->get_result()->fetch_assoc()['taught_count'];
$stmt->close();

// 3. Average rating & Total reviews received as a tutor
$stmt = $conn->prepare("
    SELECT AVG(f.rating) as avg_rating, COUNT(f.feedback_id) as total_reviews
    FROM feedback f
    JOIN sessions s ON f.session_id = s.session_id
    WHERE s.tutor_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rating_data = $stmt->get_result()->fetch_assoc();
$avg_rating = $rating_data['avg_rating'] ? number_format($rating_data['avg_rating'], 1) : "N/A";
$total_reviews = $rating_data['total_reviews'];
$stmt->close();

// 4. Detailed feedback from learners
$stmt = $conn->prepare("
    SELECT f.rating, f.comments, f.created_at, u.name as learner_name, s.skill
    FROM feedback f
    JOIN sessions s ON f.session_id = s.session_id
    JOIN users u ON s.learner_id = u.user_id
    WHERE s.tutor_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feedbacks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Progress - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 2rem; }
        .stat-card { background: #f9f9f9; padding: 25px 20px; border-radius: 8px; text-align: center; border: 1px solid #eee; }
        .stat-card h3 { color: #7f8c8d; font-size: 1rem; margin-bottom: 10px; font-weight: 600; }
        .stat-card .value { font-size: 2.5rem; font-weight: bold; color: #2c3e50; }
        .star-rating { color: #f39c12; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="skills.php">My Skills</a>
            <a href="match.php">Find Tutors</a>
            <a href="sessions.php">My Sessions</a>
            <a href="progress.php" class="active">Progress Tracker</a>
            <a href="logout.php" style="margin-left:auto; color: #e74c3c;">Logout</a>
        </div>
        
        <h2>My Progress & Statistics</h2>
        <p style="margin-bottom: 2rem;">Track your accomplishments and overall impact on the BrainBridge network.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Sessions Learned</h3>
                <div class="value" style="color: #3498db;"><?php echo $learned_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Sessions Taught</h3>
                <div class="value" style="color: #2ecc71;"><?php echo $taught_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Your Tutor Rating</h3>
                <div class="value star-rating"><?php echo $avg_rating; ?> <?php echo ($avg_rating !== 'N/A') ? '★' : ''; ?></div>
                <div style="font-size: 0.85rem; color: #7f8c8d; margin-top: 5px;"><?php echo $total_reviews; ?> total reviews</div>
            </div>
        </div>

        <h3>Detailed Student Feedback</h3>
        <?php if(count($feedbacks) > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($feedbacks as $fb): ?>
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e1e8ed; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong style="font-size: 1.1em; color: #2c3e50;"><?php echo htmlspecialchars($fb['learner_name']); ?> - <span style="font-weight: normal; color: #7f8c8d;"><?php echo htmlspecialchars($fb['skill']); ?></span></strong>
                            <span class="star-rating" style="font-size: 1.2em;"><?php echo $fb['rating']; ?> ★</span>
                        </div>
                        <p style="color: #444; font-style: italic; line-height: 1.5; font-size: 1.05em;">"<?php echo htmlspecialchars($fb['comments']); ?>"</p>
                        <div style="text-align: right; font-size: 0.8em; color: #95a5a6; margin-top: 15px;">
                            Reviewed on <?php echo date('M d, Y', strtotime($fb['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="padding: 20px; background: #f9f9f9; border-radius: 8px; text-align: center; color: #7f8c8d;">
                You haven't received any feedback as an instructor yet. Complete some teaching requests to earn ratings!
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
