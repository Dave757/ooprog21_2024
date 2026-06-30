<?php
session_start();
include 'config.php';

$error = "";

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: vote.php");
    exit();
}

// Handle login
if (isset($_POST['login'])) {
    $voterID = mysqli_real_escape_string($conn, $_POST['voterID']);
    $voterPass = mysqli_real_escape_string($conn, $_POST['voterPass']);

    $result = mysqli_query($conn, "SELECT * FROM voters WHERE voterID='$voterID' AND voterPass='$voterPass'");

    if (mysqli_num_rows($result) == 1) {
        $voter = mysqli_fetch_assoc($result);
        if ($voter['voterStat'] == 0) {
            $error = "Your account is inactive. You cannot vote.";
        } elseif ($voter['voted'] == 1) {
            $error = "You have already voted.";
        } else {
            $_SESSION['voterID'] = $voter['voterID'];
            $_SESSION['voterName'] = $voter['voterFName'] . ' ' . $voter['voterLName'];
        }
    } else {
        $error = "Invalid Voter ID or Password.";
    }
}

// Handle vote submission
if (isset($_POST['submitVote']) && isset($_SESSION['voterID'])) {
    $voterID = $_SESSION['voterID'];

    // Re-check voter hasn't already voted (race condition safety)
    $check = mysqli_query($conn, "SELECT voted FROM voters WHERE voterID='$voterID'");
    $checkRow = mysqli_fetch_assoc($check);

    if ($checkRow['voted'] == 1) {
        $error = "You have already voted.";
    } else {
        $positionsResult = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1");
        $valid = true;

        while ($pos = mysqli_fetch_assoc($positionsResult)) {
            $posID = $pos['posID'];
            $fieldName = "pos_" . $posID;

            if (isset($_POST[$fieldName])) {
                $selectedCands = $_POST[$fieldName]; // array of candIDs
                if (count($selectedCands) > $pos['numOfPositions']) {
                    $valid = false;
                    $error = "Too many candidates selected for " . htmlspecialchars($pos['posName']);
                    break;
                }
            }
        }

        if ($valid) {
            $positionsResult = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1");
            while ($pos = mysqli_fetch_assoc($positionsResult)) {
                $posID = $pos['posID'];
                $fieldName = "pos_" . $posID;

                if (isset($_POST[$fieldName])) {
                    foreach ($_POST[$fieldName] as $candID) {
                        $candID = (int)$candID;
                        mysqli_query($conn, "INSERT INTO votes (posID, voterID, candID) VALUES ($posID, '$voterID', $candID)");
                    }
                }
            }

            mysqli_query($conn, "UPDATE voters SET voted=1 WHERE voterID='$voterID'");

            session_unset();
            session_destroy();
            echo "<p>Your vote has been recorded successfully. Thank you for voting!</p>";
            echo "<p><a href='vote.php'>Back to Login</a> | <a href='index.php'>Back to Menu</a></p>";
            exit();
        }
    }
}

$positions = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1 ORDER BY posName");
?>
<!DOCTYPE html>
<html>
<head>
<title>Vote</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Voting</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if (!isset($_SESSION['voterID'])): ?>

    <h3>Voter Login</h3>
    <form method="post" action="vote.php">
        Voter ID: <input type="text" name="voterID" required><br><br>
        Password: <input type="password" name="voterPass" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>

<?php else: ?>

    <p>Logged in as: <?php echo htmlspecialchars($_SESSION['voterName']); ?> | <a href="vote.php?logout=1">Logout</a></p>

    <form method="post" action="vote.php">
        <?php
        mysqli_data_seek($positions, 0);
        while ($pos = mysqli_fetch_assoc($positions)):
            $posID = $pos['posID'];
            $candidates = mysqli_query($conn, "SELECT * FROM candidates WHERE posID=$posID AND candStat=1 ORDER BY candLName");
        ?>
        <h3><?php echo htmlspecialchars($pos['posName']); ?> (Vote up to <?php echo $pos['numOfPositions']; ?>)</h3>
        <?php while ($cand = mysqli_fetch_assoc($candidates)): ?>
            <label>
                <input type="checkbox" name="pos_<?php echo $posID; ?>[]" value="<?php echo $cand['candID']; ?>">
                <?php echo htmlspecialchars($cand['candFName'] . ' ' . $cand['candMName'] . ' ' . $cand['candLName']); ?>
            </label><br>
        <?php endwhile; ?>
        <br>
        <?php endwhile; ?>

        <button type="submit" name="submitVote" onclick="return confirm('Submit your vote? This cannot be undone.')">Submit Vote</button>
    </form>

<?php endif; ?>

</body>
</html>
