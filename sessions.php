<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get sessions where you are actively requesting tutoring
$stmt_learner = $conn->prepare("SELECT s.*, u.name as tutor_name FROM sessions s JOIN users u ON s.tutor_id = u.user_id WHERE s.learner_id = ? ORDER BY s.date DESC");
$stmt_learner->bind_param("i", $user_id);
$stmt_learner->execute();
$learner_sessions = $stmt_learner->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_learner->close();

// Get sessions where you are requested to be the tutor
$stmt_tutor = $conn->prepare("SELECT s.*, u.name as learner_name FROM sessions s JOIN users u ON s.learner_id = u.user_id WHERE s.tutor_id = ? ORDER BY s.date DESC");
$stmt_tutor->bind_param("i", $user_id);
$stmt_tutor->execute();
$tutor_sessions = $stmt_tutor->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_tutor->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Sessions - BrainBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="skills.php">My Skills</a>
            <a href="match.php">Find Tutors</a>
            <a href="sessions.php" class="active">My Sessions</a>
            <a href="logout.php" style="margin-left:auto; color: #e74c3c;">Logout</a>
        </div>
        
        <?php if(isset($_SESSION['session_msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['session_msg']); unset($_SESSION['session_msg']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['session_error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['session_error']); unset($_SESSION['session_error']); ?></div>
        <?php endif; ?>

        <h2>My Tutoring Requests (As Learner)</h2>
        <?php if(count($learner_sessions) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Assigned Tutor</th>
                        <th>Subject</th>
                        <th>Scheduled Time</th>
                        <th>Current Status</th>
                        <th>Action Protocol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($learner_sessions as $session): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session['tutor_name']); ?></td>
                        <td><?php echo htmlspecialchars($session['skill']); ?></td>
                        <td><?php echo htmlspecialchars($session['date'] . ' @ ' . $session['time']); ?></td>
                        <td><span class="badge badge-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                        <td>
                            <?php if($session['status'] == 'accepted'): ?>
                                <form action="session_process.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="completed">
                                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                    <button type="submit" style="background:#34495e; padding:5px 10px; font-size:0.85rem;">Mark as Done</button>
                                </form>
                            <?php elseif($session['status'] == 'completed'): ?>
                                <a href="feedback.php?session_id=<?php echo $session['session_id']; ?>" style="font-size:0.85rem; color:#3498db; text-decoration:underline;">Leave Rating & Review</a>
                            <?php else: ?>
                                Awaiting Changes
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't requested any tutorship sessions yet via the Match module.</p>
        <?php endif; ?>

        <h2 style="margin-top: 3rem;">Incoming Student Requests (As Tutor)</h2>
        <?php if(count($tutor_sessions) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Interested Learner</th>
                        <th>Target Skill</th>
                        <th>Target Time</th>
                        <th>Handling Status</th>
                        <th>Action Gateway</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tutor_sessions as $session): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($session['learner_name']); ?></td>
                        <td><?php echo htmlspecialchars($session['skill']); ?></td>
                        <td><?php echo htmlspecialchars($session['date'] . ' @ ' . $session['time']); ?></td>
                        <td><span class="badge badge-<?php echo $session['status']; ?>"><?php echo ucfirst($session['status']); ?></span></td>
                        <td>
                            <?php if($session['status'] == 'pending'): ?>
                                <form action="session_process.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="accepted">
                                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                    <button type="submit" style="background:#2ecc71; padding:5px 10px; font-size:0.85rem;">Accept Student</button>
                                </form>
                                <form action="session_process.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="rejected">
                                    <input type="hidden" name="session_id" value="<?php echo $session['session_id']; ?>">
                                    <button type="submit" style="background:#e74c3c; padding:5px 10px; font-size:0.85rem;">Decline</button>
                                </form>
                            <?php else: ?>
                                Complete/Resolved
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No learners have requested you for tutoring slots yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
