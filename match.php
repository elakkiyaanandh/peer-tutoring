<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];

// Powerful Matching Algorithm directly connecting learning desires to available teachers
$query = "
    SELECT t_users.user_id AS tutor_id, t_users.name AS tutor_name, t_skills.skill_name 
    FROM skills l_skills
    JOIN skills t_skills ON l_skills.skill_name = t_skills.skill_name AND t_skills.type = 'teach'
    JOIN users t_users ON t_skills.user_id = t_users.user_id
    WHERE l_skills.user_id = ? AND l_skills.type = 'learn' AND t_users.user_id != ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Tutors - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="skills.php">My Skills</a>
            <a href="match.php" class="active">Find Tutors</a>
            <a href="sessions.php">My Sessions</a>
            <a href="logout.php" style="margin-left:auto; color: #e74c3c;">Logout</a>
        </div>
        
        <h2>Find Network Tutors</h2>
        <p>Based specifically on the skills you declared you want to learn, here are perfectly matched tutors:</p>

        <?php if(isset($_SESSION['session_msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['session_msg']); unset($_SESSION['session_msg']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['session_error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['session_error']); unset($_SESSION['session_error']); ?></div>
        <?php endif; ?>

        <?php if(count($matches) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tutor Identity</th>
                        <th>Subject Matches</th>
                        <th>Book a Session Request</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($matches as $match): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($match['tutor_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($match['skill_name']); ?></td>
                        <td>
                            <form action="session_process.php" method="POST" style="flex-direction: row; gap: 5px; align-items: center;">
                                <input type="hidden" name="action" value="request">
                                <input type="hidden" name="tutor_id" value="<?php echo $match['tutor_id']; ?>">
                                <input type="hidden" name="skill" value="<?php echo htmlspecialchars($match['skill_name']); ?>">
                                <input type="date" name="date" required style="padding: 5px; margin-bottom: 0;">
                                <input type="time" name="time" required style="padding: 5px; margin-bottom: 0;">
                                <button type="submit" style="padding: 5px 10px; margin-bottom: 0;">Request Session</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; text-align:center;">
                <p>No ideal matches discovered yet on the network. Make sure you have added specific skills you want to 'Learn' via the My Skills page, or wait for new instructors to register!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
