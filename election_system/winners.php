<?php
include 'config.php';

$positions = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1 ORDER BY posName");
?>
<!DOCTYPE html>
<html>
<head>
<title>Election Winners</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Election Winners</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Elective Position</th>
        <th>Winner</th>
        <th>Total Votes</th>
    </tr>
    <?php while ($pos = mysqli_fetch_assoc($positions)):
        $posID = $pos['posID'];
        $limit = (int)$pos['numOfPositions'];

        $candVotes = mysqli_query($conn, "
            SELECT c.candFName, c.candMName, c.candLName, COUNT(v.candID) AS votes
            FROM candidates c
            LEFT JOIN votes v ON c.candID = v.candID AND v.posID = $posID
            WHERE c.posID = $posID AND c.candStat = 1
            GROUP BY c.candID
            ORDER BY votes DESC
            LIMIT $limit
        ");

        while ($c = mysqli_fetch_assoc($candVotes)):
    ?>
    <tr>
        <td><?php echo htmlspecialchars($pos['posName']); ?></td>
        <td><?php echo htmlspecialchars($c['candFName'] . ' ' . $c['candMName'] . ' ' . $c['candLName']); ?></td>
        <td><?php echo $c['votes']; ?></td>
    </tr>
    <?php
        endwhile;
    endwhile;
    ?>
</table>

</body>
</html>
