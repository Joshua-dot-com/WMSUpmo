<?php
session_start(); // Start the session

// Include your database connection file
require 'PHP/db_connect.php'; // Include database connection

// Initialize variables
$email = $password = "";
$error = "";

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form data and sanitize it
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check if both fields are filled
    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } else {
        // Prepare the SQL query to fetch user data based on the email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a user with that email exists
        if ($result->num_rows > 0) {
            // Fetch the user data
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, store user data in session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'college' => $user['college'],
                    'admin_role' => $user['admin_role'],
                    'other_services' => $user['other_services'],
                    'status' => $user['status'],
                    'is_admin' => $user['is_admin'],
                    'profile_picture' => $user['profile_picture'],
                    'created_at' => $user['created_at'],
                    'updated_at' => $user['updated_at'],
                    'login_attempts' => $user['login_attempts'],
                    'last_login' => $user['last_login']
                ];

                // Redirect based on user role
                if ($user['is_admin'] == 1) {
                    header("Location: http://localhost/PMO/Administration/Admin-Dashboard.php");
                } else {
                    header("Location: http://localhost/PMO/User/user-profile.php");
                }
                exit;
            } else {
                // Invalid password
                $error = "Incorrect password!";
            }
        } else {
            // User not found
            $error = "No user found with that email!";
        }

        // Close the statement
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Equipment Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
        .dark\:bg-gray-900 {
        --tw-bg-opacity: 1;
        background-color: rgb(17 24 39 / var(--tw-bg-opacity, 1));
    }
    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            --tw-bg-opacity: 1;
            background-color: rgb(31 41 55 / var(--tw-bg-opacity, 1));
        }
    }

    body {
    font-family: 'Poppins', sans-serif;
}

    @tailwind base;
@tailwind components;
@tailwind utilities;

/*Start Animations*/
@-webkit-keyframes animatetop {
	from {
		top: -300px;
		opacity: 0;
	}
	to {
		top: 0;
		opacity: 1;
	}
}
@keyframes animatetop {
	from {
		top: -300px;
		opacity: 0;
	}
	to {
		top: 0;
		opacity: 1;
	}
}
@-webkit-keyframes zoomIn {
	0% {
		opacity: 0;
		-webkit-transform: scale3d(0.3, 0.3, 0.3);
		transform: scale3d(0.3, 0.3, 0.3);
	}
	50% {
		opacity: 1;
	}
}
@keyframes zoomIn {
	0% {
		opacity: 0;
		-webkit-transform: scale3d(0.3, 0.3, 0.3);
		transform: scale3d(0.3, 0.3, 0.3);
	}
	50% {
		opacity: 1;
	}
}
/*End Animations*/
/*
-- Start BackGround Animation 
*/
.area {
	background:rgb(17,24,39);
	background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);
	width: 100%;
	height: 100vh;
	position: absolute;
	z-index: -1;
}

.circles {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 96%;
	overflow: hidden;
}

.circles li {
	position: absolute;
	display: block;
	list-style: none;
	width: 20px;
	height: 20px;
	background: rgba(247, 10, 10, 0.2);
	animation: animate 25s linear infinite;
	bottom: -150px;
}

.circles li:nth-child(1) {
	left: 25%;
	width: 80px;
	height: 80px;
	animation-delay: 0s;
}

.circles li:nth-child(2) {
	left: 10%;
	width: 20px;
	height: 20px;
	animation-delay: 2s;
	animation-duration: 12s;
}

.circles li:nth-child(3) {
	left: 70%;
	width: 20px;
	height: 20px;
	animation-delay: 4s;
}

.circles li:nth-child(4) {
	left: 40%;
	width: 60px;
	height: 60px;
	animation-delay: 0s;
	animation-duration: 18s;
}

.circles li:nth-child(5) {
	left: 65%;
	width: 20px;
	height: 20px;
	animation-delay: 0s;
}

.circles li:nth-child(6) {
	left: 75%;
	width: 110px;
	height: 110px;
	animation-delay: 3s;
}

.circles li:nth-child(7) {
	left: 35%;
	width: 150px;
	height: 150px;
	animation-delay: 7s;
}

.circles li:nth-child(8) {
	left: 50%;
	width: 25px;
	height: 25px;
	animation-delay: 15s;
	animation-duration: 45s;
}

.circles li:nth-child(9) {
	left: 20%;
	width: 15px;
	height: 15px;
	animation-delay: 2s;
	animation-duration: 35s;
}

.circles li:nth-child(10) {
	left: 85%;
	width: 150px;
	height: 150px;
	animation-delay: 0s;
	animation-duration: 11s;
}

@keyframes animate {
	0% {
		transform: translateY(0) rotate(0deg);
		opacity: 1;
		border-radius: 0;
	}

	100% {
		transform: translateY(-1000px) rotate(720deg);
		opacity: 0;
		border-radius: 50%;
	}
}
</style>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="./Assets/wmsu.png" alt="Logo" class="h-20 w-20 mx-auto rounded-full object-cover">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">Equipment Management System</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Sign in to your account</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">

           <!-- PHP Alert Messages -->
<?php if (isset($_GET['error']) && !empty($_GET['error'])) : ?>
    <?php if (isset($_GET['blocked']) && $_GET['blocked'] == 1) : ?>
        <!-- Special styling for blocked account message -->
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-4 py-3 rounded mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-ban text-yellow-600 mr-2 text-lg"></i>
                </div>
                <div>
                    <p class="font-bold">Account Blocked</p>
                    <p class="text-sm"><?= htmlspecialchars($_GET['error']) ?></p>
                    
                    <div class="mt-3 text-sm">
                        <p>If you believe this is a mistake, please contact the administrator:</p>
                        <a href="mailto:admin@wmsu.edu.ph" class="text-blue-600 hover:underline">wmsuequipment@gmail.com</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <!-- Regular error message -->
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Password reset request has been sent! Please check your email or in the spam folder.</span>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['already_requested']) && $_GET['already_requested'] == 1) : ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <span>
                You already requested a password reset. 
                <?php if (isset($_GET['minutes_remaining'])): ?>
                    Please wait <?= htmlspecialchars($_GET['minutes_remaining']) ?> more minute(s) before trying again.
                <?php else: ?>
                    Please wait up to an hour before trying again.
                <?php endif; ?>
            </span>
        </div>
    </div>
<?php endif; ?>

<?php
// Include this if $message and $success are defined before this HTML is loaded
if (isset($message)) :
?>
    <div class="<?= $success ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' ?> px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <?php if ($success): ?>
                <i class="fas fa-check-circle mr-2"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle mr-2"></i>
            <?php endif; ?>
            <span><?= $message ?></span>
        </div>
    </div>
<?php endif; ?>


            <!-- Login Form -->
            <form method="POST" action="http://localhost/PMO/login-process.php" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute inset-y-0 left-3 text-gray-400 flex items-center"></i>
                        <input type="email" name="email" required
                            class="pl-10 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute inset-y-0 left-3 text-gray-400 flex items-center"></i>
                        <input type="password" id="password" name="password" required
                            class="pl-10 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="••••••••">
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 dark:text-gray-300">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="text-right mt-2">
                    <button type="button" onclick="toggleModal(true)"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Forgot Password?
                    </button>
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 bg-red-600 text-white rounded-md transition-all duration-300 hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-600">
                    Sign in
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-300">Don't have an account?
                <a href="Sign-up.php" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Sign up</a>
            </p>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

  <!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-sm relative">
        <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:hover:text-white"
            onclick="toggleModal(false)">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Forgot Password</h2>

        <!-- Alert for already requested -->
        <?php if (isset($_GET['already_requested']) && $_GET['already_requested'] == 1): ?>
            <div id="timerAlert" class="mb-4 p-3 text-yellow-800 bg-yellow-100 border border-yellow-300 rounded text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i>
                <span id="countdownMessage">You have already submitted a request. Please wait for <span id="countdown"></span> before trying again.</span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form id="forgotPasswordForm" method="POST" action="user-forgot-password-process.php" class="space-y-4">
            <div>
                <label for="forgot-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enter your email</label>
                <input type="email" name="email" id="forgot-email" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="you@example.com">
            </div>
            <button id="submitBtn" type="submit"
                class="w-full py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 transition-all">
                Submit
            </button>
        </form>
    </div>
</div>


</body>


    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        function toggleModal(show) {
        const modal = document.getElementById('forgotPasswordModal');
        modal.classList.toggle('hidden', !show);
    }

    // Allow closing modal with ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            toggleModal(false);
        }
    });
    const submitButton = document.getElementById('submitBtn');
    const form = document.getElementById('forgotPasswordForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting immediately

        // Change button text and show spinner
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitButton.disabled = true;

        // Proceed with form submission after a short delay (to simulate sending)
        setTimeout(function() {
            form.submit(); // Actually submit the form
        }, 1000); // Simulate 1-second delay
    });
    window.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('already_requested') === '1') {
            toggleModal(true); // Opens the modal
        }
    });
    window.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        
        // If the user has already requested a password reset
        if (params.get('already_requested') === '1') {
            toggleModal(true); // Open the modal
            
            // Get the time remaining in minutes (if passed in the URL)
            const minutesRemaining = params.get('minutes_remaining') ? parseInt(params.get('minutes_remaining')) : 60; // Default to 60 if not set
            disableSubmitButton(minutesRemaining);

            // Start countdown
            startCountdown(minutesRemaining);
        }
    });

    // Function to disable the submit button
    function disableSubmitButton(minutesRemaining) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true; // Disable submit button
        const timerAlert = document.getElementById('timerAlert');
        timerAlert.style.display = 'block'; // Show the alert message
    }

    // Function to start the countdown timer
    function startCountdown(minutesRemaining) {
        const countdownElem = document.getElementById('countdown');
        let timeLeft = minutesRemaining * 60; // Convert to seconds
        
        // Update countdown every second
        const countdownInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElem.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval); // Stop the countdown
                enableSubmitButton(); // Re-enable the submit button after 1 hour
            }
            timeLeft--;
        }, 1000);
    }

    // Function to enable the submit button after the wait
    function enableSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false; // Enable the submit button
        document.getElementById('timerAlert').style.display = 'none'; // Hide the alert message
    }
    </script>

</body>
</html>
