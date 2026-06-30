<?php
require 'config/db.php';

$positions = $conn->query("SELECT * FROM positions WHERE posStat=1")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Election Results</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Election Results</h2>

<?php foreach ($positions as $p): ?>
    <h3><?= htmlspecialchars($p['posName']) ?></h3>
    <?php
    $stmt = $conn->prepare("
        SELECT c.candID, c.candFName, c.candMName, c.candLName, COUNT(v.candID) AS totalVotes
        FROM candidates c
        LEFT JOIN votes v ON c.candID = v.candID
        WHERE c.posID = ? AND c.candStat = 1
        GROUP BY c.candID
        ORDER BY totalVotes DESC
    ");
    $stmt->execute([$p['posID']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAllVotes = array_sum(array_column($rows, 'totalVotes'));
    ?>
    <table border="1" cellpadding="5">
    <tr><th>Candidate</th><th>Total Votes</th><th>Voting %</th></tr>
    <?php foreach ($rows as $r):
        $pct = $totalAllVotes > 0 ? round(($r['totalVotes'] / $totalAllVotes) * 100, 2) : 0;
    ?>
        <tr>
            <td><?= htmlspecialchars($r['candLName'] . ', ' . $r['candFName'] . ' ' . $r['candMName']) ?></td>
            <td><?= $r['totalVotes'] ?></td>
            <td><?= $pct ?>%</td>
        </tr>
    <?php endforeach; ?>
    </table>
    <br>
<?php endforeach; ?>
</body>
</html>
