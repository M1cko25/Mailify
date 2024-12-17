<?php
require 'vendor/autoload.php'; // Adjust the path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
session_start();
require_once("database.php");
if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $mailbox = $_POST['mailID'];
            $sql = $conn->prepare("SELECT * FROM users WHERE MailBox = ?");
            $sql->bind_param("s", $mailbox);
            $sql->execute();
            $result = $sql->get_result();
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $userId = $row['id'];
                $username = $row['username'];
                $email = $row['email'];
                $receive_date = date("Y-m-d");
                $receive_time = date("H:i:s", time());
                $status = "Mail Received";
        
                $stmt = $conn->prepare("INSERT INTO `mails`(`receive_date`, `receive_time`, `status`, `user_id`, `user_email`) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param("sssis", $receive_date, $receive_time, $status, $userId, $email);
                    if ($stmt->execute()) {
                        echo "Mail received successfully!";
                        try {
                            // Server settings
                            $mail->isSMTP();                                            // Send using SMTP
                            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server
                            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                            $mail->Username   = 'lutiva.micojake.bscs2022@gmail.com';               // SMTP username
                            $mail->Password   = 'ttil yutm tbrm qvqg';                  // SMTP password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
                            $mail->Port       = 587;                                    // TCP port to connect to
                        
                            // Recipients   
                            $mail->setFrom("mailify748@gmail.com", "Mailify");       // Sender's email
                            $mail->addAddress($email, $username); // Add a recipient
                        
                            // Content
                            $mail->isHTML(true);                                        // Set email format to HTML
                            $mail->Subject = 'Mail Received';
                            $mail->Body    = 'You received a mail at '. $receive_date . ' ' . $receive_time . "<br>Check it now.";
                            $mail->AltBody = 'Mail Received';
                        
                            $mail->send();
                            echo 'Message has been sent successfully';
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                    } else {
                        echo "Error executing statement: " . $stmt->error;
                    }
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "No user found with this mailbox ID";
            }
}

?>