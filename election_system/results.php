<?php
include 'config.php';

$positions = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1 ORDER BY posName");
?>
<!DOCTYPE html>
<html>
<head>
<title>Election Results</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Election Results</h2>

<?php while ($pos = mysqli_fetch_assoc($positions)):
    $posID = $pos['posID'];

    // Total votes cast for this position
    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM votes WHERE posID=$posID");
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalVotes = $totalRow['total'];

    // Votes per candidate
    $candVotes = mysqli_query($conn, "
        SELECT c.candID, c.candFName, c.candMName, c.candLName, COUNT(v.candID) AS votes
        FROM candidates c
        LEFT JOIN votes v ON c.candID = v.candID AND v.posID = $posID
        WHERE c.posID = $posID AND c.candStat = 1
        GROUP BY c.candID
        ORDER BY votes DESC
    ");
?>
    <h3><?php echo htmlspecialchars($pos['posName']); ?></h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Candidate</th>
            <th>Total Votes</th>
            <th>Voting %</th>
        </tr>
        <?php while ($c = mysqli_fetch_assoc($candVotes)):
            $pct = $totalVotes > 0 ? round(($c['votes'] / $totalVotes) * 100, 2) : 0;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($c['candFName'] . ' ' . $c['candMName'] . ' ' . $c['candLName']); ?></td>
            <td><?php echo $c['votes']; ?></td>
            <td><?php echo $pct; ?>%</td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
<?php endwhile; ?>

</body>
</html>
