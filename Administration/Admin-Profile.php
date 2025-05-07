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
<title>Admin Profile - Admin Dashboard</title>
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
<div class="flex-1 ml-64 bg-gray-50 min-h-screen">
  <!-- Header -->
  <header class="border-b bg-white shadow-sm">
    <div class="flex h-16 items-center px-6">
      <button id="sidebar-toggle" class="md:hidden mr-4 text-gray-600 hover:text-gray-900">
        <i class="fas fa-bars text-xl"></i>
      </button>
      <h1 class="text-2xl font-semibold text-gray-800">Admin Portal</h1>
    </div>
  </header>

  <!-- Main Content -->
  <main class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
       <!-- Profile Card -->
       <div class="bg-white rounded-lg border shadow-sm lg:col-span-1" id="profile-card">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Profile Information</h2>
                </div>
                <div class="p-6 flex flex-col items-center">
                    <div class="avatar-upload mb-6">
                        <div class="avatar-edit">
                            <label for="profile-image-upload" class="btn btn-primary btn-sm rounded-full h-10 w-10 flex items-center justify-center">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profile-image-upload" class="hidden" accept="image/*" />
                        </div>
                        <div class="avatar-preview">
                            <img id="profile-image-preview" src="https://randomuser.me/api/portraits/men/41.jpg" alt="Profile Image" />
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-1"></h3>
                    <div class="flex gap-2 mb-6">
                        <span class="badge badge-success" id="status">Active</span>
                    </div>
                    <div class="w-full space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm text-gray-500">Email:</span>
                            <span class="text-sm font-medium text-gray-900 email"></span>
                        </div>
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm text-gray-500">Department:</span>
                            <span class="text-sm font-medium text-gray-900 ml-4 college"></span>
                        </div>
                        <div class="flex justify-between items-center border-b pb-2">
                            <span class="text-sm text-gray-500">Role:</span>
                            <span class="text-sm font-medium text-gray-900 role"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Joined:</span>
                            <span class="text-sm font-medium text-gray-900 created-at"></span>
                        </div>
                    </div>
                </div>
            </div>

      <!-- Edit Profile & Stats -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Edit Profile Form -->
        <div class="bg-white rounded-2xl border shadow p-6">
          <div class="border-b pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Edit Profile</h2>
          </div>
          <form id="edit-profile-form" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" id="first-name" name="first_name"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
              </div>
              <div>
                <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" id="last-name" name="last_name"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
              </div>
              <div>
                <label for="college" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <input type="text" id="college" name="college"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
              </div>
            </div>
            <div class="flex justify-end gap-3">
              <button type="button"
                class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
                Cancel
              </button>
              <button type="submit"
                class="px-4 py-2 text-sm font-medium rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>
</div>


<script>
// Mobile sidebar toggle
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Profile image upload preview
document.getElementById('profile-image-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-image-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('profile_image', file);

        fetch('upload-profile-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profile-image-preview').src = data.new_profile_image;
                alert('Profile image updated successfully!');
            } else {
                alert('Failed to upload image: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            alert('An error occurred while uploading the image.');
        });
    }
});

// Highlight active sidebar link based on the current page
document.addEventListener("DOMContentLoaded", function () {
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
});

// Handle form submission with AJAX for profile update
document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('edit-profile-form'));
    const profileImageInput = document.getElementById('profile-image-upload');
    if (profileImageInput.files.length > 0) {
        formData.append('profile_image', profileImageInput.files[0]);
    }

    fetch('update-profile.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            const profileImagePreview = document.getElementById('profile-image-preview');
            if (data.new_profile_image) {
                profileImagePreview.src = data.new_profile_image;
            }
            if (data.updated_user) {
                const updatedUser = data.updated_user;
                const profileCard = document.getElementById('profile-card');
                if (profileCard) {
                    profileCard.querySelector('h3').textContent = `${updatedUser.first_name} ${updatedUser.last_name}`;
                    profileCard.querySelector('.email').textContent = updatedUser.email;
                    profileCard.querySelector('.college').textContent = updatedUser.college;
                    profileCard.querySelector('.role').textContent = updatedUser.role;
                    profileCard.querySelector('.created-at').textContent = new Date(updatedUser.created_at).toLocaleDateString();
                }
            }
        } else {
            alert('Failed to update profile: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the profile.');
    });
});

// Fetch user profile data when the page loads
document.addEventListener("DOMContentLoaded", function() {
    const profileUrl = 'get-profile.php';

    fetch(profileUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                const profileImage = user.profile_picture_url || 'https://randomuser.me/api/portraits/men/41.jpg';
                document.getElementById('profile-image-preview').src = profileImage;

                const profileCard = document.getElementById('profile-card');
                if (profileCard) {
                    profileCard.querySelector('h3').textContent = `${user.first_name} ${user.last_name}`;
                    profileCard.querySelector('.email').textContent = user.email;
                    profileCard.querySelector('.college').textContent = user.college;
                    profileCard.querySelector('.role').textContent = user.role;
                    profileCard.querySelector('.created-at').textContent = new Date(user.created_at).toLocaleDateString();
                }

                // Pre-fill form fields
                const firstNameInput = document.getElementById('first-name');
                const lastNameInput = document.getElementById('last-name');
                const emailInput = document.getElementById('email');
                const collegeInput = document.getElementById('college');

                firstNameInput.value = user.first_name;
                lastNameInput.value = user.last_name;
                emailInput.value = user.email;
                collegeInput.value = user.college;

                // 🔄 Store original values for the cancel reset
                const originalValues = {
                    first_name: user.first_name,
                    last_name: user.last_name,
                    email: user.email,
                    college: user.college
                };

                // ♻️ Cancel button resets form to original values
                const cancelButton = document.querySelector('#edit-profile-form button[type="button"]');
                cancelButton.addEventListener('click', function () {
                    firstNameInput.value = originalValues.first_name;
                    lastNameInput.value = originalValues.last_name;
                    emailInput.value = originalValues.email;
                    collegeInput.value = originalValues.college;
                });
            } else {
                alert('Failed to load profile: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching profile data:', error);
            alert('An error occurred while loading the profile.');
        });
});

</script>

</body>
</html>