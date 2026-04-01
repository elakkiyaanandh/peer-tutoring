<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get all existing skills for this user
$stmt = $conn->prepare("SELECT skill_id, skill_name, type FROM skills WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Skills - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="skills.php" class="active">My Skills</a>
            <a href="match.php">Find Tutors</a>
            <a href="sessions.php">My Sessions</a>
            <a href="logout.php" style="margin-left:auto; color: #e74c3c;">Logout</a>
        </div>
        
        <h2>My Knowledge Profile</h2>
        
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 2rem;">
            <h3>Register a New Attribute</h3>
            <form action="skills_process.php" method="POST" style="flex-direction: row; gap: 10px; align-items: flex-end;">
                <input type="hidden" name="action" value="add">
                <div style="flex: 2;">
                    <label for="skill_name">Subject or Skill Name</label>
                    <input type="text" id="skill_name" name="skill_name" required placeholder="e.g. Advanced Calculus" style="width: 100%; margin-bottom:0;">
                </div>
                <div style="flex: 1;">
                    <label for="type">Target Role</label>
                    <select id="type" name="type" style="width: 100%; margin-bottom:0; padding:0.75rem;">
                        <option value="teach">I want to Teach</option>
                        <option value="learn">I want to Learn</option>
                    </select>
                </div>
                <div>
                    <button type="submit" style="margin-bottom:0;">Add to Profile</button>
                </div>
            </form>
        </div>

        <h3>Your Current Registered Skills</h3>
        <?php if(count($skills) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject Focus</th>
                        <th>Type of Focus</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($skills as $skill): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                        <td>
                            <span class="badge <?php echo $skill['type'] == 'teach' ? 'badge-accepted' : 'badge-pending'; ?>">
                                <?php echo ucfirst($skill['type']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="skills_process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="skill_id" value="<?php echo $skill['skill_id']; ?>">
                                <button type="submit" style="background:#e74c3c; padding:5px 10px; font-size:0.85rem;">Remove Profile</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't defined any skills yet! Please use the form above.</p>
        <?php endif; ?>
    </div>
</body>
</html>
