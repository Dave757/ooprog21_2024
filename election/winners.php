<?php
require 'config/db.php';

$positions = $conn->query("SELECT * FROM positions WHERE posStat=1")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Election Winners</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Election Winners</h2>

<table border="1" cellpadding="5">
<tr><th>Elective Position</th><th>Winner</th><th>Total Votes</th></tr>
<?php foreach ($positions as $p):
    $stmt = $conn->prepare("
        SELECT c.candFName, c.candMName, c.candLName, COUNT(v.candID) AS totalVotes
        FROM candidates c
        LEFT JOIN votes v ON c.candID = v.candID
        WHERE c.posID = ? AND c.candStat = 1
        GROUP BY c.candID
        ORDER BY totalVotes DESC
        LIMIT " . (int)$p['numOfPositions']
    );
    $stmt->execute([$p['posID']]);
    $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($winners as $i => $w): ?>
    <tr>
        <td><?= $i === 0 ? htmlspecialchars($p['posName']) : '' ?></td>
        <td><?= htmlspecialchars($w['candLName'] . ', ' . $w['candFName'] . ' ' . $w['candMName']) ?></td>
        <td><?= $w['totalVotes'] ?></td>
    </tr>
    <?php endforeach;
endforeach; ?>
</table>
</body>
</html>
