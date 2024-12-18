<!-- <?php 

session_start();
require_once "database.php";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUsername = $_SESSION['username'];
    $currentEmail = $_SESSION['email'];
    $currentPass = hash("sha256", $_POST['password']);
    $newPassword = $_POST('newpassword');
    $stmt = $conn->prepare("SELECT * FROM users WHERE  email = ?");
    $stmt->bind_param("s", $currentEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($currentUsername != $_POST['username']) {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE email = ?");
            $stmt->bind_param("ss", $_POST['username'], $currentEmail);
            $stmt->execute();
            $_SESSION['username'] = $_POST['username'];
        }
        if ($currentPass.$row['password']){
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $newPassword , $currentEmail);
            $stmt->execute();
        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Mailify | Update Account</title>
</head>
<body>
    <div class="w-screen h-screen flexx justify-center items-center">>
        <h1>Update Account Information</h1>
        <form action="" method="POST" class="shadow-lg w-1/2  flex flex-col gap-2 p-4">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $_SESSION['username']; ?>
            <label for="password">Current Password</label>
            <input type="password" name="password" id="password" /> 
            <label for="newpassword">New Password</label>
            <input type="password" name="newpassword" id="password" /> 
            <input type="submit" value="Update" />
            <p class="text-red-600"><?php echo $error ?></p>
        </form>
    </div>
</body>
</html> -->