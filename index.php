<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
require_once("database.php");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM mails WHERE user_id = ? OR user_email = ?");
$stmt->bind_param("ss", $_SESSION['user_id'], $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Mailify | Home</title>
    <style>
        #logout-box {
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            display: none;
        }
    </style>
</head>
<body>
    <header class="w-full h-fit p-3 lg:p-8 flex justify-between border bg-indigo-600">
        <div class="flex row items-center text-white gap-2 font-bold">
            <img src="./assets/MailifyLogo.png" alt="Mailify Logo" class="w-7 h-7">
            <h1>Mailify</h1>
        </div>
        <nav>
            <ul class="flex row gap-8">
                <li class="flex justify-center items-center">
                    <span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                            <path d="M200-280q-17 0-28.5-11.5T160-320v-80H80v-160h80v-80q0-17 11.5-28.5T200-680h640q17 0 28.5 11.5T880-640v320q0 17-11.5 28.5T840-280H200Z"/>
                        </svg>
                    </span>
            </li>
                <!-- <li class="flex justify-center items-center">
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                            <path d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/>
                        </svg>
                    </button>
                    <?php //if ($notifNum > 0) : ?>
                    <div class="absolute cursor-default bg-red-600 md:w-5 md:h-5 w-3 h-3 text-xs rounded-full lg:translate-x-3 lg:-translate-y-2 translate-x-4 -translate-y-3 flex justify-center items-center text-white"><?php echo $notifNum ?></div>
                    <?php //endif; ?> 
                </li> -->
                <div class="flex row flex justify-center items-center">
                    <li><button class="md:w-7 md:h-7 w-5 h-5 bg-gray-400 rounded-full text-white"><span><?php echo strtoupper(substr($_SESSION['username'] ,0, 1)) ?></span></button></li>
                    <li><button onclick="toggleLogout()"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff"><path d="M480-360 280-560h400L480-360Z"/></svg></button></li>
                    <div class="lg-10 bg-white rounded-lg absolute top-16 p-2 right-10 shadow-lg" id="logout-box">
                        <form action="logout.php" method="get">
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </ul>
        </nav>
    </header>
    <main class="flex flex-col justify-center items-center gap-5">
        <div class="w-full p-8 flex justify-between items-center lg:text-xl">
            <h1 class="lg:text-2xl ">Welcome, <span class="font-bold"><?php echo $_SESSION['username'] ?></span></h1>
            <div class="flex justify-center items-center gap-4">
                <p><?php echo date("M d, Y", time()) ?></p>
            </div>
        </div>
        <div class="w-full md:p-8 p-3 flex flex-col justify-center rounded-lg items-center gap-5">
            <table class="w-full rounded-lg shadow-lg">
                <tr class="w-full lg:text-lg text-sm bg-gray-200 ">
                    <th>Mail Id</th>
                    <th>Date Received</th>
                    <th>Time Received</th>
                    <th>Status</th>
                </tr>
                <?php

                    foreach ($result as $index => $row) {
                        $date = new dateTime($row['receive_time']);
                        $time = new dateTime($row['receive_time']);
                        echo "<tr class='w-full text-center". ($index % 2 === 0 ? " bg-gray-100" : " bg-white") . "'>";
                        echo "<td class='p-2'>" . $row['id'] . "</td>";
                        echo "<td class='p-2'>" . $date->format("M d, Y") . "</td>";
                        echo "<td class='p-2'>" . $time->format("h:i a") . "</td>";
                        echo "<td class='p-2'>" . $row['status'] . "</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </main>
    <script>
        function toggleLogout() {
            let logoutBox = document.getElementById("logout-box");
            if (logoutBox.style.display === "block") {
                logoutBox.style.display = "none";
            } else {
                logoutBox.style.display = "block";
            }
        }
    </script>
</body>
</html>