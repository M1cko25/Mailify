<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "login");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables with current user data (you would typically get this from your database)
$user = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "No user found with ID = $id";
    }
} else {
    echo "Invalid or missing ID.";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate user_id first
    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        $error_message = "Invalid user ID";
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "UPDATE users SET 
            first_name = ?, 
            last_name = ?, 
            phone_number = ?, 
            email = ? 
            WHERE id = ?");
        
        if ($stmt) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "ssssi", 
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['phone_number'],
                $_POST['email'],
                $_POST['user_id']
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $success_message = "Profile updated successfully!";
                    // Refresh user data
                    $user = [
                        'id' => $_POST['user_id'],
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'phone_number' => $_POST['phone_number'],
                        'email' => $_POST['email']
                    ];
                } 
            } else {
                $error_message = "Error updating profile: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Error preparing statement: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 0 0 48%;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .save-button {
            background-color: #6c5ce7;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .save-button:hover {
            background-color: #5b4bc4;
        }

        .save-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store initial form values
            const form = document.querySelector('form');
            const initialFormData = new FormData(form);
            const submitButton = document.querySelector('.save-button');
            
            // Start with disabled button
            submitButton.disabled = true;

            // Convert initial form data to an object for easier comparison
            const initialValues = {};
            for (let pair of initialFormData.entries()) {
                initialValues[pair[0]] = pair[1];
            }

            // Check for changes on any input
            form.addEventListener('input', function(e) {
                const currentFormData = new FormData(form);
                let hasChanges = false;

                // Compare current values with initial values
                for (let pair of currentFormData.entries()) {
                    if (initialValues[pair[0]] !== pair[1]) {
                        hasChanges = true;
                        break;
                    }
                }

                // Enable/disable button based on changes
                submitButton.disabled = !hasChanges;
            });
        });
    </script>
</head>
<body>
    <div class="profile-container">
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h2>Contact Details</h2>
        <form method="POST" action="">
            <input type="hidden" name="user_id" value="<?php echo $user['id'] ?? ''; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo $user['first_name'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo $user['last_name'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" 
                           value="<?php echo $user['phone_number'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo $user['email'] ?? ''; ?>" required>
                </div>
            </div>

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </div>
</body>
</html>
