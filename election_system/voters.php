<?php
include 'config.php';

// Add voter
if (isset($_POST['add'])) {
    $voterID = mysqli_real_escape_string($conn, $_POST['voterID']);
    $voterPass = mysqli_real_escape_string($conn, $_POST['voterPass']);
    $voterFName = mysqli_real_escape_string($conn, $_POST['voterFName']);
    $voterMName = mysqli_real_escape_string($conn, $_POST['voterMName']);
    $voterLName = mysqli_real_escape_string($conn, $_POST['voterLName']);
    $sql = "INSERT INTO voters (voterID, voterPass, voterFName, voterMName, voterLName, voterStat, voted)
            VALUES ('$voterID', '$voterPass', '$voterFName', '$voterMName', '$voterLName', 1, 0)";
    mysqli_query($conn, $sql);
    header("Location: voters.php");
    exit();
}

// Update voter
if (isset($_POST['update'])) {
    $voterID = mysqli_real_escape_string($conn, $_POST['voterID']);
    $voterPass = mysqli_real_escape_string($conn, $_POST['voterPass']);
    $voterFName = mysqli_real_escape_string($conn, $_POST['voterFName']);
    $voterMName = mysqli_real_escape_string($conn, $_POST['voterMName']);
    $voterLName = mysqli_real_escape_string($conn, $_POST['voterLName']);
    $sql = "UPDATE voters SET voterPass='$voterPass', voterFName='$voterFName',
            voterMName='$voterMName', voterLName='$voterLName' WHERE voterID='$voterID'";
    mysqli_query($conn, $sql);
    header("Location: voters.php");
    exit();
}

// Deactivate voter
if (isset($_GET['deactivate'])) {
    $voterID = mysqli_real_escape_string($conn, $_GET['deactivate']);
    mysqli_query($conn, "UPDATE voters SET voterStat=0 WHERE voterID='$voterID'");
    header("Location: voters.php");
    exit();
}

// Activate voter
if (isset($_GET['activate'])) {
    $voterID = mysqli_real_escape_string($conn, $_GET['activate']);
    mysqli_query($conn, "UPDATE voters SET voterStat=1 WHERE voterID='$voterID'");
    header("Location: voters.php");
    exit();
}

// Load record for editing
$editRow = null;
if (isset($_GET['edit'])) {
    $voterID = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM voters WHERE voterID='$voterID'");
    $editRow = mysqli_fetch_assoc($result);
}

$voters = mysqli_query($conn, "SELECT * FROM voters ORDER BY voterID");
?>
<!DOCTYPE html>
<html>
<head>
<title>Voters Management</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Voters Management</h2>

<h3><?php echo $editRow ? "Update Voter" : "Add Voter"; ?></h3>
<form method="post" action="voters.php">
    Voter ID: <input type="text" name="voterID" value="<?php echo $editRow ? htmlspecialchars($editRow['voterID']) : ''; ?>" <?php echo $editRow ? 'readonly' : 'required'; ?>><br><br>
    Password: <input type="text" name="voterPass" value="<?php echo $editRow ? htmlspecialchars($editRow['voterPass']) : ''; ?>" required><br><br>
    First Name: <input type="text" name="voterFName" value="<?php echo $editRow ? htmlspecialchars($editRow['voterFName']) : ''; ?>" required><br><br>
    Middle Name: <input type="text" name="voterMName" value="<?php echo $editRow ? htmlspecialchars($editRow['voterMName']) : ''; ?>"><br><br>
    Last Name: <input type="text" name="voterLName" value="<?php echo $editRow ? htmlspecialchars($editRow['voterLName']) : ''; ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
        <a href="voters.php">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<h3>Voters List</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Voter ID</th>
        <th>Full Name</th>
        <th>Status</th>
        <th>Voted</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($voters)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['voterID']); ?></td>
        <td><?php echo htmlspecialchars($row['voterFName'] . ' ' . $row['voterMName'] . ' ' . $row['voterLName']); ?></td>
        <td><?php echo $row['voterStat'] ? 'Active' : 'Inactive'; ?></td>
        <td><?php echo $row['voted'] ? 'Yes' : 'No'; ?></td>
        <td>
            <a href="voters.php?edit=<?php echo urlencode($row['voterID']); ?>">Edit</a>
            <?php if ($row['voterStat']): ?>
                | <a href="voters.php?deactivate=<?php echo urlencode($row['voterID']); ?>" onclick="return confirm('Deactivate this voter?')">Deactivate</a>
            <?php else: ?>
                | <a href="voters.php?activate=<?php echo urlencode($row['voterID']); ?>">Activate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
