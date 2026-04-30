<?php
session_start();

// Initialize error variable
$login_error = null;

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: admin_dashboard.php");
    exit;
}

// Process the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_attempt'])) {
    
    // Include the separated database connection
    require_once 'db_connection.php';

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Use prepared statements for security (SQL Injection prevention)
    $stmt = $pdo->prepare("SELECT id, username, password, name FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Verify the password against the stored hash
        if (password_verify($password, $user['password'])) { 
            
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            
            // Redirect to the dashboard
            header("location: admin_dashboard.php"); 
            exit;
        } else {
            // User found but password incorrect
            $login_error = "Invalid username or password.";
        }
    } else {
        // User not found
        $login_error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - JRMSU SSG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        'primary': '#001f3f', 
                        'secondary': '#DAA520', 
                        'light': '#F0F8FF',
                        'accent': '#FFD700',
                    },
                },
            },
        };
    </script>
    <style>body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border-t-4 border-secondary">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary mb-4">
                <svg class="h-8 w-8 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-primary">JRMSU E-Voting</h1>
            <p class="text-gray-500 text-sm mt-1">Please sign in to access the dashboard</p>
        </div>

        <?php if ($login_error): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php"> 
            <input type="hidden" name="login_attempt" value="1">
            
            <div class="mb-5">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all" 
                    placeholder="Denver_admin">
            </div>

            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 outline-none transition-all" 
                    placeholder="••••••••">
            </div>

            <button type="submit" class="w-full text-white bg-primary hover:bg-secondary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-300">
                Sign In
            </button>
        </form>
        
        <div class="mt-6 text-center text-xs text-gray-400">
            <p>&copy; <?php echo date("Y"); ?> JRMSU Siocon SSG. All rights reserved.</p>
        </div>
    </div>
</body>
</html>