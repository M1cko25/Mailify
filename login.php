<?php
session_start();
require_once("database.php");

if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
$errorMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Wrong password";
        }
    } else {
        $errorMessage = "You're not registered, Please register first";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailify | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center w-full bg-gradient-to-r from-indigo-800 to-blue-900 dark:bg-gradient-to-r dark:from-gray-900 dark:to-gray-950 p-4">
        <div class="bg-white dark:bg-gray-900 shadow-2xl rounded-xl px-8 py-6 max-w-md w-full transform transition-all duration-300 hover:scale-[1.01] animate-fade-in">
            <h1 class="text-3xl font-bold text-center mb-8 text-indigo-800 dark:text-gray-200">Welcome Back!</h1>
            <form action="" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-indigo-900 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        name="email"
                        class="shadow-sm rounded-lg w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-800 dark:focus:ring-blue-900 transition-all duration-300" 
                        placeholder="your@email.com" 
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-indigo-900 dark:text-gray-300 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="shadow-sm rounded-lg w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-800 dark:focus:ring-blue-900 transition-all duration-300" 
                        placeholder="Enter your password" 
                        required
                    >
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            class="h-4 w-4 rounded border-gray-300 text-indigo-800 focus:ring-indigo-800 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-900 transition-colors duration-300" 
                            checked
                        >
                        <label for="remember" class="ml-2 block text-sm text-indigo-900 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>
                    <a href="signup.php" 
                        class="text-sm text-indigo-800 hover:text-blue-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-300">
                        Create Account
                    </a>
                </div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-800 transform transition-all duration-300 hover:scale-[1.02] dark:ring-offset-gray-900"
                    onclick="handleLogin(event)"
                >
                    Login
                </button>
                <?php if ($errorMessage) : ?>
                    <p class="text-red-500 text-sm mt-2 w-full text-center"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>

    <script>

        function handleLogin(event) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            if (!email || !password) {
                event.preventDefault();
                return;
            }
            if (!isValidEmail(email)) {
                event.preventDefault();
                return;
            }
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Add ripple effect to button
        document.querySelector('button[type="submit"]').addEventListener('mousedown', function(e) {
            const button = e.currentTarget;
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size/2;
            const y = e.clientY - rect.top - size/2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                top: ${y}px;
                left: ${x}px;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });

        
    </script>

    <style>
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>