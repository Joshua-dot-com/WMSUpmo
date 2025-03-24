<?php
session_start();
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Equipment Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="./Assets/wmsu.png" alt="Logo" class="h-20 w-20 mx-auto rounded-full object-cover">
            <h1 class="text-2xl font-bold text-gray-900 mt-4">Equipment Management System</h1>
            <p class="text-gray-600 mt-1">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="login_process.php" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute inset-y-0 left-3 text-gray-400 flex items-center"></i>
                        <input type="email" name="email" required class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500" placeholder="you@example.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute inset-y-0 left-3 text-gray-400 flex items-center"></i>
                        <input type="password" name="password" required class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700">Sign in</button>
            </form>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Don't have an account?
                <a href="Sign-up.php" class="font-medium text-blue-600 hover:underline">Sign up</a>
            </p>
        </div>
    </div>
</body>

</html>
