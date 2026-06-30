<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['voterID'])) {
    header("Location: vote_login.php");
    exit;
}
$voterID = $_SESSION['voterID'];

// Re-check voter eligibility (in case state changed)
$stmt = $conn->prepare("SELECT * FROM voters WHERE voterID=?");
$stmt->execute([$voterID]);
$voter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$voter || !$voter['voterStat'] || $voter['voted']) {
    session_destroy();
    die("You are not eligible to vote. <a href='vote_login.php'>Back to login</a>");
}

$error = '';

if (isset($_POST['submitVote'])) {
    $positions = $conn->query("SELECT * FROM positions WHERE posStat=1")->fetchAll(PDO::FETCH_ASSOC);
    $valid = true;

    // Validate: number of selected candidates per position <= numOfPositions
    foreach ($positions as $p) {
        $selected = $_POST['pos_' . $p['posID']] ?? [];
        if (count($selected) > $p['numOfPositions']) {
            $valid = false;
            $error = "Too many candidates selected for " . $p['posName'];
            break;
        }
    }

    if ($valid) {
        $conn->beginTransaction();
        foreach ($positions as $p) {
            $selected = $_POST['pos_' . $p['posID']] ?? [];
            foreach ($selected as $candID) {
                $stmt = $conn->prepare("INSERT INTO votes (posID, voterID, candID) VALUES (?, ?, ?)");
                $stmt->execute([$p['posID'], $voterID, $candID]);
            }
        }
        $stmt = $conn->prepare("UPDATE voters SET voted=1 WHERE voterID=?");
        $stmt->execute([$voterID]);
        $conn->commit();

        session_destroy();
        echo "<p>Vote submitted successfully. Thank you for voting!</p>";
        echo "<a href='index.php'>Back to Menu</a>";
        exit;
    }
}

$positions = $conn->query("SELECT * FROM positions WHERE posStat=1")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Vote</title></head>
<body>
<h2>Cast Your Vote</h2>
<p>Voter: <?= htmlspecialchars($voter['voterFName'] . ' ' . $voter['voterLName']) ?></p>
<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>

<form method="post">
<?php foreach ($positions as $p):
    $candStmt = $conn->prepare("SELECT * FROM candidates WHERE posID=? AND candStat=1");
    $candStmt->execute([$p['posID']]);
    $cands = $candStmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3><?= htmlspecialchars($p['posName']) ?> (choose up to <?= $p['numOfPositions'] ?>)</h3>
    <?php foreach ($cands as $c): ?>
        <label>
            <input type="checkbox" name="pos_<?= $p['posID'] ?>[]" value="<?= $c['candID'] ?>">
            <?= htmlspecialchars($c['candLName'] . ', ' . $c['candFName'] . ' ' . $c['candMName']) ?>
        </label><br>
    <?php endforeach; ?>
    <br>
<?php endforeach; ?>
    <button type="submit" name="submitVote">Submit Vote</button>
</form>
</body>
</html>
