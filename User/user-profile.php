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
<title>User Profile - Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Include the Flag Icon CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icon-css/css/flag-icon.min.css">
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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
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
    
    .badge-primary {
        background-color: #3b82f6;
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
    
    .btn-primary-light {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid transparent;
    }
    
    .btn-ghost {
        background-color: transparent;
        color: #6b7280;
    }
    
    .btn-ghost:hover {
        background-color: #f9fafb;
        color: #111827;
    }
    
    .avatar-upload {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    
    .avatar-edit {
        position: absolute;
        right: 5px;
        bottom: 5px;
        z-index: 1;
    }
    
    .avatar-preview {
        width: 150px;
        height: 150px;
        position: relative;
        border-radius: 100%;
        overflow: hidden;
        border: 4px solid #f8fafc;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stat-card {
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    .stat-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-top: 0.25rem;
    }
    
    .stat-desc {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
</style>
</head>
<body class="bg-gray-50 text-gray-900">
<div class="flex min-h-screen">
   <!-- Sidebar -->
   <aside class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col" id="sidebar">
    <div class="border-b border-gray-700 py-4 px-4">
        <div class="flex items-center">
            <img src="/PMO/Assets/wmsu.png" alt="WMSU Logo" class="h-10 w-10 object-contain">
            <span class="ml-2 text-xl">User Portal</span>
        </div>
    </div>
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <li>
                <a href="user-profile.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="user-settings.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-check"></i>
                    <span>Account Settings</span>
                </a>
            </li>
            <li>
                <a href="user-history.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-check"></i>
                    <span>Account History</span>
                </a>
            </li>
                <li>
                    <a href="equipment-transfer.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Transfer Equipment</span>
                    </a>
                </li>
        </ul>
    </nav>
    <div class="mt-auto p-4 border-t border-gray-700">
            <a href="user-logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white w-full">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
            </a>
        </div>
  </aside>

<!-- Main Content -->
<div class="flex-1 ml-0 md:ml-64 transition-all duration-300">
    <!-- Header -->
    <header class="border-b bg-white shadow-sm sticky top-0 z-10">
    </header>

    <!-- Main Content -->
    <main class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm lg:col-span-1 overflow-hidden transition-all duration-300 hover:shadow-md" id="profile-card">
                    <div class="p-6 border-b bg-gradient-to-r from-red-500 to-red-600">
                        <h2 class="text-lg font-semibold text-white">Profile Information</h2>
                    </div>
                    <div class="p-6 flex flex-col items-center relative">
                        <div class="avatar-upload mb-6 relative">
                            <div class="avatar-edit absolute -right-2 bottom-0 z-10">
                                <label for="profile-image-upload" class="cursor-pointer bg-red-600 hover:bg-red-700 text-white rounded-full h-10 w-10 flex items-center justify-center shadow-md transition-all duration-200 hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input type="file" id="profile-image-upload" class="hidden" accept="image/*" />
                            </div>
                            <div class="avatar-preview">
                                <img id="profile-image-preview" src="https://randomuser.me/api/portraits/men/41.jpg" alt="Profile Image" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 hover:scale-105" />
                            </div>
                        </div>

                        <h3 class="text-xl font-bold mb-1 text-gray-800"></h3>
                        <div class="flex gap-2 mb-6">
                            <span id="status" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5"></span>
                                Active
                            </span>
                        </div>
                        <div class="w-full space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3 group">
                                <span class="text-sm text-gray-500 group-hover:text-red-500 transition-colors">Email:</span>
                                <span class="text-sm font-medium text-gray-900 email"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3 group">
                                <span class="text-sm text-gray-500 group-hover:text-red-500 transition-colors">Department:</span>
                                <span class="text-sm font-medium text-gray-900 ml-4 college"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3 group">
                                <span class="text-sm text-gray-500 group-hover:text-red-500 transition-colors">Role:</span>
                                <span class="text-sm font-medium text-gray-900 role"></span>
                            </div>
                            <div class="flex justify-between items-center group">
                                <span class="text-sm text-gray-500 group-hover:text-red-500 transition-colors">Joined:</span>
                                <span class="text-sm font-medium text-gray-900 created-at"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile & Equipment Stats -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Edit Profile Form -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                        <div class="p-6 border-b bg-gradient-to-r from-red-500 to-red-600 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-white">Edit Profile</h2>
                            <span class="text-xs text-red-100">All fields are required</span>
                        </div>
                        <div class="p-6">
                            <form id="edit-profile-form" enctype="multipart/form-data">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="group">
                                        <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-red-600 transition-colors">First Name</label>
                                        <input type="text" id="first-name" name="first_name" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" />
                                    </div>
                                    <div class="group">
                                        <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-red-600 transition-colors">Last Name</label>
                                        <input type="text" id="last-name" name="last_name" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" />
                                    </div>
                                    <div class="group">
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-red-600 transition-colors">Email</label>
                                        <input type="email" id="email" name="email" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" />
                                    </div>
                                    <div class="group">
                                        <label for="college" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-red-600 transition-colors">Department</label>
                                        <input type="text" id="college" name="college" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" />
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-4">
                                    <button type="button" id="cancel-btn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Equipment Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-t" id="equipment-stats">
                            <div class="p-6 border-r border-gray-100 transition-all duration-300 hover:bg-red-50">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-red-100 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Current Equipment</div>
                                        <div class="text-3xl font-bold text-gray-900" id="current-equipment-count">0</div>
                                        <div class="text-xs text-gray-500 mt-1">Devices currently assigned</div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 transition-all duration-300 hover:bg-red-50">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-red-100 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Total Equipment</div>
                                        <div class="text-3xl font-bold text-gray-900" id="total-equipment-count">0</div>
                                        <div class="text-xs text-gray-500 mt-1">Devices assigned historically</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Equipment -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                        <div class="p-6 border-b bg-gradient-to-r from-red-500 to-red-600 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-white">Current Equipment</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800">
                                Active
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-sm" id="current-equipment-table">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Equipment ID</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Type</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Category</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Assigned Date</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="current-equipment-body" class="divide-y divide-gray-200">
                                        <!-- Current equipment rows will be dynamically populated -->
                                        <tr class="animate-pulse">
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-28"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Equipment History -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                        <div class="p-6 border-b bg-gradient-to-r from-red-500 to-red-600 flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-white">Equipment History</h2>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-sm" id="equipment-history-table">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Equipment ID</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Type</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Assigned Date</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Return Date</th>
                                            <th class="h-12 px-6 text-left font-medium text-gray-500 uppercase tracking-wider border-b">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipment-history-body" class="divide-y divide-gray-200">
                                        <!-- Equipment history rows will be dynamically populated -->
                                        <tr class="animate-pulse">
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-28"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-28"></div></td>
                                            <td class="h-12 px-6"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Sidebar toggle
    document.getElementById('sidebar-toggle')?.addEventListener('click', function () {
        document.getElementById('sidebar')?.classList.toggle('active');
    });

    // Highlight current sidebar link
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });

    // Profile image upload preview and AJAX upload
    const profileImageInput = document.getElementById('profile-image-upload');
    const profilePreview = document.getElementById('profile-image-preview');
    let originalProfileImage = profilePreview?.src;

    profileImageInput?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => {
            if (profilePreview) {
                profilePreview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('profile_image', file);

        fetch('upload-profile-image.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                profilePreview.src = data.new_profile_image;
                originalProfileImage = data.new_profile_image;
                alert('Profile image updated successfully!');
            } else {
                alert('Upload failed: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Upload error:', err);
            alert('An error occurred while uploading the image.');
        });
    });

    // Edit profile form submission
    document.getElementById('edit-profile-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        if (profileImageInput.files.length > 0) {
            formData.append('profile_image', profileImageInput.files[0]);
        }

        fetch('update-profile.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully!');
                if (data.new_profile_image) {
                    profilePreview.src = data.new_profile_image;
                    originalProfileImage = data.new_profile_image;
                }

                const user = data.updated_user;
                // Update profile card with new user data
                updateProfileCard(user);
            } else {
                alert('Update failed: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Update error:', err);
            alert('An error occurred while updating the profile.');
        });
    });

    // Cancel button resets form and image preview
    document.getElementById('cancel-btn')?.addEventListener('click', function () {
        document.getElementById('edit-profile-form').reset();
        if (originalProfileImage) {
            profilePreview.src = originalProfileImage;
        }
    });

    // Fetch profile data
    fetch('get-profile.php')
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data) {
            const user = data.data;
            const img = user.profile_picture || 'https://randomuser.me/api/portraits/men/41.jpg';
            profilePreview.src = img;
            originalProfileImage = img;

            // Check if user object has the necessary fields
            if (user.first_name && user.last_name && user.email) {
                updateProfileCard(user);
                prefillProfileForm(user);
            } else {
                console.error('User data is missing required fields.');
                alert('Error: User data is incomplete.');
            }
        } else {
            console.error('Failed to load profile:', data.error);
            alert('Failed to load profile: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Profile fetch error:', err);
        alert('An error occurred while loading the profile.');
    });

    // Update the profile card with fetched data
    function updateProfileCard(user) {
        document.querySelector('#profile-card h3').textContent = `${user.first_name} ${user.last_name}`;
        document.querySelector('#profile-card .email').textContent = user.email;
        document.querySelector('#profile-card .college').textContent = user.college;
        document.querySelector('#profile-card .role').textContent = user.role || 'Not Provided';
        document.querySelector('#profile-card .created-at').textContent = new Date(user.created_at).toLocaleDateString();
    }

    // Pre-fill the profile form with fetched data
    function prefillProfileForm(user) {
        document.getElementById('first-name').value = user.first_name;
        document.getElementById('last-name').value = user.last_name;
        document.getElementById('email').value = user.email;
        document.getElementById('college').value = user.college;
    }

    // Fetch equipment stats
    fetch('get_equipment_stats.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.stats) {
                document.getElementById('current-equipment-count').textContent = data.stats.currently_assigned || 0;
                document.getElementById('total-equipment-count').textContent = data.stats.total_assignments || 0;
            } else {
                console.error('Equipment stats error:', data.error);
            }
        })
        .catch(err => {
            console.error('Stats fetch error:', err);
        });

    // Fetch equipment history with improved styling
    fetch('get_equipment_history.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const body = document.getElementById('equipment-history-body');
                body.innerHTML = '';
                data.equipment.forEach((item, index) => {
                    const row = document.createElement('tr');
                    // Alternating row colors
                    row.className = `border-b transition-colors duration-200 hover:bg-red-50 ${index % 2 === 0 ? 'bg-white' : 'bg-red-50/30'}`;
                    row.innerHTML = `
                        <td class="h-12 px-6 text-sm text-gray-900">${item.po_jo_no ?? '-'}</td>
                        <td class="h-12 px-6 text-sm text-gray-900">${item.equipment_name ?? '-'}</td>
                        <td class="h-12 px-6 text-sm text-gray-900">${item.assigned_at ? new Date(item.assigned_at).toLocaleString() : '-'}</td>
                        <td class="h-12 px-6 text-sm text-gray-900">${item.returned_at ? new Date(item.returned_at).toLocaleString() : '-'}</td>
                        <td class="h-12 px-6 text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Returned
                            </span>
                        </td>
                    `;
                    body.appendChild(row);
                });
            } else {
                console.error('History load error:', data.error);
            }
        })
        .catch(err => {
            console.error('History fetch error:', err);
        });

    // Fetch current equipment data with improved styling
    fetch('get_current_equipment.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const body = document.getElementById('current-equipment-body');
                body.innerHTML = '';
                data.equipment.forEach((item, index) => {
                    const row = document.createElement('tr');
                    // Alternating row colors
                    row.className = `border-b transition-colors duration-200 hover:bg-red-50 ${index % 2 === 0 ? 'bg-white' : 'bg-red-50/30'}`;
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.po_jo_no}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.equipment_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.category}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${item.assigned_at}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                item.equipment_status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }">
                                ${item.equipment_status}
                            </span>
                        </td>
                    `;
                    body.appendChild(row);
                });
            } else {
                console.error('Current equipment load error:', data.error);
            }
        })
        .catch(err => {
            console.error('Current equipment fetch error:', err);
        });
});
</script>
</body>
</html>