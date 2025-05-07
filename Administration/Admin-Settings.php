<?php
session_start(); // Start the session

// Include your database connection file
require 'db_connect.php'; // Include database connection

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
            // User not found — redirect to Login page
            header("Location: http://localhost/PMO/Login.php");
            exit;
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
<title>Admin Settings - Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#3b82f6',
                        foreground: '#ffffff',
                    },
                    destructive: {
                        DEFAULT: '#ef4444',
                        foreground: '#ffffff',
                    },
                    success: {
                        DEFAULT: '#22c55e',
                        foreground: '#ffffff',
                    },
                    warning: {
                        DEFAULT: '#f59e0b',
                        foreground: '#ffffff',
                    }
                }
            }
        }
    }
</script>
<style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .sidebar {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.active {
            transform: translateX(0);
        }
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.125rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }
    
    .badge-success {
        background-color: #22c55e;
        color: white;
    }
    
    .badge-warning {
        background-color: #f59e0b;
        color: white;
    }
    
    .badge-destructive {
        background-color: #ef4444;
        color: white;
    }
    
    .badge-secondary {
        background-color: #6b7280;
        color: white;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25rem;
        padding: 0.5rem 1rem;
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-outline {
        background-color: transparent;
        border: 1px solid #e5e7eb;
    }
    
    .btn-outline:hover {
        background-color: #f9fafb;
    }
    
    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #2563eb;
    }
    
    .btn-destructive {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-destructive:hover {
        background-color: #dc2626;
    }
    
    .btn-ghost {
        background-color: transparent;
        color: #6b7280;
    }
    
    .btn-ghost:hover {
        background-color: #f9fafb;
        color: #111827;
    }
    
    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 4px;
        background-color: #e5e7eb;
        overflow: hidden;
    }
    
    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease;
    }
    
    .password-strength-weak {
        width: 25%;
        background-color: #ef4444;
    }
    
    .password-strength-fair {
        width: 50%;
        background-color: #f59e0b;
    }
    
    .password-strength-good {
        width: 75%;
        background-color: #3b82f6;
    }
    
    .password-strength-strong {
        width: 100%;
        background-color: #22c55e;
    }
    
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .modal.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 0.5rem;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }
    
    .modal.active .modal-content {
        transform: scale(1);
    }
</style>
</head>
<body class="bg-gray-50 text-gray-900">
<div class="flex min-h-screen">
       <!-- Sidebar -->
       <aside class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col" id="sidebar">
        <div class="border-b border-gray-700 py-4 px-4">
            <div class="flex items-center">
                <i class="fas fa-clipboard-check text-primary text-xl"></i>
                <span class="ml-2 text-xl font-bold">Admin Portal</span>
            </div>
        </div>
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="Admin-Dashboard.php" class="flex items-center gap-3 px-3 py-2 rounded-md bg-gray-800 text-white">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="pending-equipment.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Pending Requests</span>
                    </a>
                </li>
                <li>
                    <a href="Admin-History.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-history"></i>
                        <span>History</span>
                    </a>
                </li>
                <li>
                    <a href="Admin-Profile.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="Admin-settings.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
            
            <div class="mt-8">
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Equipment Management
                </h3>
                <ul class="mt-2 space-y-2">
                        <li>
                            <a href="Equipment-Management.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Manage Equipments</span>
                            </a>
                        </li>
              <li>
                <a href="Admin-Assign-Equipments.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
                  <i class="fas fa-exchange-alt"></i>
                  <span>Assign Equipments</span>
                </a>
              </li>
              <li>
                <a href="Admin-User-Equipment-Details.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
                  <i class="fas fa-exchange-alt"></i>
                  <span>User Equipments</span>
                </a>
              </li>
                    </ul>
            </div>
        </nav>
        <div class="mt-auto p-4 border-t border-gray-700">
            <a href="Administration-logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white w-full">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
            </a>
        </div>
    </aside>


    <!-- Main Content -->
    <div class="flex-1 ml-64">
        <!-- Header -->
        <header class="border-b bg-white">
            <div class="flex h-16 items-center px-6">
                <button id="sidebar-toggle" class="md:hidden mr-4">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-2xl font-bold">Account Settings</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- Password Change Section -->
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Change Password</h2>
                        <p class="text-sm text-gray-500 mt-1">Update your password to keep your account secure</p>
                    </div>
                    <div class="p-6">
                        <form id="password-form">
                            <div class="space-y-4">
                                <div>
                                    <label for="current-password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                    <div class="relative">
                                        <input type="password" id="current-password" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required />
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password" data-target="current-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <div class="relative">
                                        <input type="password" id="new-password" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required />
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password" data-target="new-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="password-strength-bar" id="password-strength-bar"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" id="password-strength-text">Password strength: Enter a new password</p>
                                </div>
                                
                                <div>
                                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <div class="relative">
                                        <input type="password" id="confirm-password" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required />
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 toggle-password" data-target="confirm-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" id="password-match-text"></p>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</h3>
                                    <ul class="text-xs text-gray-500 space-y-1">
                                        <li id="req-length" class="flex items-center gap-2">
                                            <i class="fas fa-circle text-xs"></i>
                                            At least 8 characters long
                                        </li>
                                        <li id="req-uppercase" class="flex items-center gap-2">
                                            <i class="fas fa-circle text-xs"></i>
                                            At least one uppercase letter
                                        </li>
                                        <li id="req-lowercase" class="flex items-center gap-2">
                                            <i class="fas fa-circle text-xs"></i>
                                            At least one lowercase letter
                                        </li>
                                        <li id="req-number" class="flex items-center gap-2">
                                            <i class="fas fa-circle text-xs"></i>
                                            At least one number
                                        </li>
                                        <li id="req-special" class="flex items-center gap-2">
                                            <i class="fas fa-circle text-xs"></i>
                                            At least one special character
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Management Section -->
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Account Management</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your account settings and preferences</p>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div class="border-t pt-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-red-50 rounded-md border border-red-100">
                                <div>
                                    <h3 class="text-base font-medium text-red-600">Delete Account</h3>
                                    <p class="text-sm text-red-500 mt-1">Request to permanently delete your account and all associated data</p>
                                </div>
                                <button id="delete-account-btn" class="btn btn-destructive">Request Deletion</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal" id="delete-account-modal">
    <div class="modal-content p-6 mx-4">
        <div class="text-center mb-4">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-4">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Delete Your Account?</h3>
            <p class="text-sm text-gray-500 mt-2">This action cannot be undone. All your data will be permanently removed.</p>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Before you proceed:</h4>
            <ul class="text-xs text-gray-500 space-y-2">
                <li class="flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5 text-red-500"></i>
                    <span>Your profile information and all personal data will be deleted</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5 text-red-500"></i>
                    <span>You will lose access to all services and applications</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5 text-red-500"></i>
                    <span>Any equipment assigned to you must be returned before account deletion</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5 text-red-500"></i>
                    <span>This process may take up to 30 days to complete</span>
                </li>
            </ul>
        </div>
        
        <div class="mb-4">
            <label for="delete-reason" class="block text-sm font-medium text-gray-700 mb-1">Please tell us why you're leaving (optional)</label>
            <textarea id="delete-reason" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
        </div>
        
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" id="delete-confirm-checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-700">I understand that this action is permanent and cannot be undone</span>
            </label>
        </div>
        
        <div class="flex justify-end gap-3">
            <button id="cancel-delete-btn" class="btn btn-outline">Cancel</button>
            <button id="confirm-delete-btn" class="btn btn-destructive" disabled>Request Deletion</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // === Sidebar Toggle ===
    document.getElementById('sidebar-toggle')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // === Password Visibility Toggle ===
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });

    // === Password Strength & Matching ===
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    const matchText = document.getElementById('password-match-text');

    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    function updateRequirement(el, met) {
        if (!el) return;
        el.classList.toggle('text-green-500', met);
        el.querySelector('i').className = met ? 'fas fa-check-circle text-xs' : 'fas fa-circle text-xs';
    }

    function checkPasswordsMatch() {
        const password = newPasswordInput.value;
        const confirm = confirmPasswordInput.value;

        if (confirm === '') {
            matchText.textContent = '';
            matchText.className = 'text-xs text-gray-500 mt-1';
        } else if (password === confirm) {
            matchText.textContent = 'Passwords match';
            matchText.className = 'text-xs text-green-500 mt-1';
        } else {
            matchText.textContent = 'Passwords do not match';
            matchText.className = 'text-xs text-red-500 mt-1';
        }
    }

    newPasswordInput?.addEventListener('input', function () {
        const val = this.value;
        let strength = 0;

        const hasLength = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSpecial = /[^A-Za-z0-9]/.test(val);

        updateRequirement(reqLength, hasLength);
        updateRequirement(reqUppercase, hasUpper);
        updateRequirement(reqLowercase, hasLower);
        updateRequirement(reqNumber, hasNumber);
        updateRequirement(reqSpecial, hasSpecial);

        strength += hasLength + hasUpper + hasLower + hasNumber + hasSpecial;

        strengthBar.className = 'password-strength-bar';
        if (val === '') {
            strengthBar.style.width = '0%';
            strengthText.textContent = 'Password strength: Enter a new password';
        } else if (strength <= 2) {
            strengthBar.classList.add('password-strength-weak');
            strengthText.textContent = 'Password strength: Weak';
        } else if (strength === 3) {
            strengthBar.classList.add('password-strength-fair');
            strengthText.textContent = 'Password strength: Fair';
        } else if (strength === 4) {
            strengthBar.classList.add('password-strength-good');
            strengthText.textContent = 'Password strength: Good';
        } else {
            strengthBar.classList.add('password-strength-strong');
            strengthText.textContent = 'Password strength: Strong';
        }

        checkPasswordsMatch();
    });

    confirmPasswordInput?.addEventListener('input', checkPasswordsMatch);

// === Password Submit AJAX ===
document.getElementById('password-form')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const currentPassword = document.getElementById('current-password').value;
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (newPassword !== confirmPassword) {
        showErrorModal('New passwords do not match.');
        return;
    }

    fetch('change_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessModal(data.message || 'Password updated successfully!');
            document.getElementById('password-form').reset();
            strengthBar.style.width = '0%';
            strengthText.textContent = 'Password strength: Enter a new password';
            matchText.textContent = '';
            [reqLength, reqUppercase, reqLowercase, reqNumber, reqSpecial].forEach(req => {
                req.classList.remove('text-green-500');
                req.querySelector('i').className = 'fas fa-circle text-xs';
            });
        } else {
            showErrorModal(data.message || 'An unexpected error occurred.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showErrorModal('An error occurred while updating the password.');
    });
});

// Function to show success modal
function showSuccessModal(message) {
    const successModal = document.createElement('div');
    successModal.classList.add('modal', 'active');
    successModal.innerHTML = `
        <div class="modal-content p-6 mx-4 bg-white rounded-lg shadow-lg">
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600 mb-4">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Success</h3>
                <p class="text-sm text-gray-500 mt-2">${message}</p>
            </div>
            <div class="flex justify-end gap-3">
                <button class="btn btn-primary">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(successModal);

    // Dynamically attach close event listener to success modal button
    successModal.querySelector('button').addEventListener('click', closeSuccessModal);
}

// Function to close success modal
function closeSuccessModal() {
    const modal = document.querySelector('.modal.active');
    if (modal) {
        modal.classList.remove('active');
        modal.remove();
    }
}

// Function to show error modal
function showErrorModal(message) {
    const errorModal = document.createElement('div');
    errorModal.classList.add('modal', 'active');
    errorModal.innerHTML = `
        <div class="modal-content p-6 mx-4 bg-white rounded-lg shadow-lg">
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-4">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Error</h3>
                <p class="text-sm text-gray-500 mt-2">${message}</p>
            </div>
            <div class="flex justify-end gap-3">
                <button class="btn btn-destructive">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(errorModal);

    // Dynamically attach close event listener to error modal button
    errorModal.querySelector('button').addEventListener('click', closeErrorModal);
}

// Function to close error modal
function closeErrorModal() {
    const modal = document.querySelector('.modal.active');
    if (modal) {
        modal.classList.remove('active');
        modal.remove();
    }
}


    // === Account Deletion ===
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const deleteAccountModal = document.getElementById('delete-account-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const deleteConfirmCheckbox = document.getElementById('delete-confirm-checkbox');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

    deleteAccountBtn?.addEventListener('click', () => {
        deleteAccountModal.classList.add('active');
    });

    cancelDeleteBtn?.addEventListener('click', () => {
        deleteAccountModal.classList.remove('active');
        deleteConfirmCheckbox.checked = false;
        confirmDeleteBtn.disabled = true;
    });

    deleteConfirmCheckbox?.addEventListener('change', function () {
        confirmDeleteBtn.disabled = !this.checked;
    });

    confirmDeleteBtn?.addEventListener('click', function () {
    if (!deleteConfirmCheckbox.checked) return;

    const reason = document.getElementById('delete-reason').value;

    fetch('request_account_deletion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            // Show the error message in a modal
            showErrorModal(data.message);
        } else if (data.status === 'success') {
            deleteAccountModal.classList.remove('active');
            alert('Account deletion request submitted.');
            deleteConfirmCheckbox.checked = false;
            confirmDeleteBtn.disabled = true;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
});

// Function to close the error modal
window.closeCustomErrorModal = function () {
    const modal = document.querySelector('.custom-error-modal');
    if (modal) {
        modal.classList.remove('active');
        modal.remove();
    }
};

// Function to show the error modal with the message
function showErrorModal(message) {
    // Create the modal content
    const errorModal = document.createElement('div');
    errorModal.classList.add('custom-error-modal', 'modal', 'active');
    errorModal.innerHTML = `
        <div class="modal-content p-6 mx-4">
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Error</h3>
                <p class="text-sm text-gray-500 mt-2">${message}</p>
            </div>
            <div class="flex justify-end gap-3">
                <button class="btn btn-outline" onclick="closeCustomErrorModal()">Close</button>
            </div>
        </div>
    `;
    
    // Append the modal to the body
    document.body.appendChild(errorModal);
}

    // === Sidebar Active Link Highlight ===
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
});
</script>
</body>
</html>

