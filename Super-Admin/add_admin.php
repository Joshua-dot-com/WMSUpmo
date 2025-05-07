<?php
session_start(); // Start the session

// Check if the user is logged in (session should be set)
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    // Redirect to login page if neither user nor admin is logged in
    header("Location: login.php");
    exit;
}

// Check if it's an admin or a regular user
if (isset($_SESSION['admin'])) {
    // Get admin data from session
    $user = $_SESSION['admin']; // Admin session data
    $isOwner = $user['is_owner'];
    $fullName = $user['first_name'] . ' ' . $user['last_name']; // Admin's full name
    // Handle admin-specific logic here, if any
} elseif (isset($_SESSION['user'])) {
    // Get regular user data from session
    $user = $_SESSION['user']; // Regular user session data
    $isOwner = $user['is_owner']; // Assuming regular users have the same structure for 'is_owner'
    $fullName = $user['first_name'] . ' ' . $user['last_name']; // User's full name
    // Handle user-specific logic here, if any
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'elegant': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        .sidebar {
            transition: all 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
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
            background-color: #10b981;
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
        
        .nav-btn {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-btn:hover, .nav-btn.active {
            border-left-color: #0ea5e9;
            background-color: rgba(14, 165, 233, 0.1);
        }
        
        .form-input {
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }
        
        .form-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .user-card {
            transition: all 0.2s ease;
        }
        
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Animated gradient background */
        .gradient-bg {
            background: linear-gradient(-45deg, #f0f9ff, #e0f2fe, #f5f3ff, #ede9fe);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Button hover effect */
        .btn-hover-effect {
            transition: all 0.3s ease;
        }
        
        .btn-hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.2), 0 4px 6px -2px rgba(14, 165, 233, 0.1);
        }
        /* delete modal styles */
            /* Modal animations */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }
    
    @keyframes slideIn {
      from { 
        transform: translateY(-20px);
        opacity: 0;
      }
      to { 
        transform: translateY(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from { 
        transform: translateY(0);
        opacity: 1;
      }
      to { 
        transform: translateY(20px);
        opacity: 0;
      }
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .modal-backdrop {
      animation: fadeIn 0.3s ease forwards;
      backdrop-filter: blur(5px);
    }
    
    .modal-backdrop.hiding {
      animation: fadeOut 0.3s ease forwards;
    }
    
    .modal-content {
      animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      transform-origin: center;
    }
    
    .modal-content.hiding {
      animation: slideOut 0.3s ease forwards;
    }
    
    .shake-animation {
      animation: shake 0.5s ease;
    }
    
    .pulse-animation {
      animation: pulse 0.5s ease;
    }
    
    /* Button hover effects */
    .btn-cancel {
      transition: all 0.2s ease;
    }
    
    .btn-cancel:hover {
      background-color: #f3f4f6;
      transform: translateY(-1px);
    }
    
    .btn-delete {
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2), 0 2px 4px -1px rgba(239, 68, 68, 0.1);
    }
    
    .btn-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3), 0 4px 6px -2px rgba(239, 68, 68, 0.1);
    }
    
    /* Close button animation */
    .close-btn {
      transition: all 0.2s ease;
    }
    
    .close-btn:hover {
      transform: rotate(90deg);
    }
    /* Ensure the primary and secondary colors are defined */
:root {
  --color-primary-500: #3b82f6; /* Blue-500 */
  --color-primary-600: #2563eb; /* Blue-600 */
  --color-secondary-500: #8b5cf6; /* Violet-500 */
  --color-secondary-600: #7c3aed; /* Violet-600 */
}

.from-primary-500 {
  --tw-gradient-from: var(--color-primary-500);
}

.to-secondary-500 {
  --tw-gradient-to: var(--color-secondary-500);
}

.from-primary-600 {
  --tw-gradient-from: var(--color-primary-600);
}

.to-secondary-600 {
  --tw-gradient-to: var(--color-secondary-600);
}

.focus\:ring-primary-500:focus {
  --tw-ring-color: var(--color-primary-500);
}

.focus\:border-primary-500:focus {
  border-color: var(--color-primary-500);
}

.hover\:from-primary-600:hover {
  --tw-gradient-from: var(--color-primary-600);
}

.hover\:to-secondary-600:hover {
  --tw-gradient-to: var(--color-secondary-600);
}

.text-primary-600 {
  color: var(--color-primary-600);
}

.group-hover\:text-primary-600:hover {
  color: var(--color-primary-600);
}

/* Modal animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

@keyframes modal-appear {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-content {
  animation: modal-appear 0.3s ease-out;
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(40px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-in-up {
  animation: fadeInUp 0.4s ease-out;
}
    </style>
</head>

<body class="bg-gray-50">
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
                <a href="Dashboard.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="pending-approval.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-check"></i>
                    <span>Account Approvals</span>
                </a>
            </li>
            <li>
                <a href="pending-deletion.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-minus"></i>
                    <span>Account Deletion</span>
                </a>
            </li>
            <li>
                <a href="history.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            </li>
            <li>
                <a href="users.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <!-- Only show 'Add Admin' link if 'is_owner' is 1 -->
            <?php if ($isOwner == 1): ?>
                    <li>
                        <a href="add_admin.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="fas fa-users"></i>
                            <span>Add Admin</span>
                        </a>
                    </li>
                <?php endif; ?>
        </ul>
    </nav>
    <div class="mt-auto p-4 border-t border-gray-700">
    <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white w-full">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <div class="min-h-screen gradient-bg p-4 sm:p-8">
                <!-- Main Container -->
                <div class="w-full max-w-6xl mx-auto">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                        <p class="text-gray-600">Add and manage system users</p>
                    </div>

               <!-- Button to open modal -->
<button id="openAddUserModal" type="button" 
        class="inline-flex items-center px-4 py-2 mb-4 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300 transform hover:scale-105 hover:animate-bounce">
  <svg class="h-5 w-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
  </svg>
  Add User
</button>


<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <!-- Background overlay with blur effect -->
  <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

  <!-- Modal container -->
  <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
    <!-- Modal panel -->
    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg fade-in-up">
      <!-- Modal content -->
      <div class="modal-content">
        <!-- Modal header with red gradient -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 relative">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white flex items-center" id="modal-title">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              Add New User
            </h3>
            <button type="button" class="text-white hover:text-gray-200 focus:outline-none" onclick="closeModal()">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p class="text-white text-opacity-90 text-sm mt-1">Create a new user account with appropriate permissions</p>
        </div>

        <!-- Modal body -->
        <div class="px-6 py-5 bg-gray-50">
          <form id="addUserForm" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- First Name -->
              <div class="group">
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <input type="text" id="firstName" name="firstName" class="block w-full pl-10 pr-3 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Enter first name" required>
                </div>
              </div>

              <!-- Last Name -->
              <div class="group">
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <input type="text" id="lastName" name="lastName" class="block w-full pl-10 pr-3 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="Enter last name" required>
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="group">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <input type="email" id="email" name="email" class="block w-full pl-10 pr-3 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="user@example.com" required>
              </div>
            </div>

            <!-- Password -->
            <div class="group">
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input type="password" id="password" name="password" class="block w-full pl-10 pr-10 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="••••••••" required>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>
              <div class="mt-1.5">
                <div class="w-full bg-gray-200 rounded-full h-1">
                  <div id="password-strength" class="bg-red-500 h-1 rounded-full" style="width: 0%"></div>
                </div>
                <p id="password-strength-text" class="text-xs text-gray-500 mt-1">Password strength: Too weak</p>
              </div>
            </div>

            <!-- Role -->
            <div class="group">
              <label for="role" class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <select id="role" name="role" class="block w-full pl-10 pr-10 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 appearance-none transition-colors">
                  <option value="admin">Administrator</option>
                  <option value="moderator">Moderator</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal footer -->
        <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-row-reverse gap-3">
          <button type="button" onclick="submitForm()" class="inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-medium rounded-lg shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300 transform hover:scale-105 hover:animate-bounce">
            <svg class="h-5 w-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add User
          </button>
          <button type="button" onclick="closeModal()" class="inline-flex justify-center items-center px-4 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
                    </div>

                    <!-- Users Lists Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Admins List -->
                        <div class="bg-white rounded-xl shadow-elegant p-6 border border-gray-100">
                            <h2 class="text-lg sm:text-xl font-semibold text-primary-600 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Admins
                            </h2>
                            <div class="bg-gray-50 rounded-xl shadow-sm p-4 max-h-80 overflow-y-auto">
                                <ul id="adminList" class="space-y-3">
                                    <!-- Sample Admin Items -->
                                    <li class="user-card bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">JD</div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-800">John Doe</h3>
                                                <p class="text-xs text-gray-500">john.doe@example.com</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-primary-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <li class="user-card bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">AS</div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-800">Alice Smith</h3>
                                                <p class="text-xs text-gray-500">alice.smith@example.com</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-primary-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Moderators List -->
                        <div class="bg-white rounded-xl shadow-elegant p-6 border border-gray-100">
                            <h2 class="text-lg sm:text-xl font-semibold text-primary-600 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Moderators
                            </h2>
                            <div class="bg-gray-50 rounded-xl shadow-sm p-4 max-h-80 overflow-y-auto">
                                <ul id="moderatorList" class="space-y-3">
                                    <!-- Sample Moderator Items -->
                                    <li class="user-card bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-600 font-semibold">RJ</div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-800">Robert Johnson</h3>
                                                <p class="text-xs text-gray-500">robert.j@example.com</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-primary-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <li class="user-card bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-600 font-semibold">EW</div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-gray-800">Emily Wilson</h3>
                                                <p class="text-xs text-gray-500">emily.w@example.com</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-primary-600 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-600 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update User Modal -->
<div id="updateUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-4">Update User</h2>

    <form id="updateUserForm">
      <input type="hidden" id="updateUserId" name="id" />

      <div class="mb-4">
        <label class="block text-sm font-medium">First Name</label>
        <input type="text" name="first_name" id="updateFirstName" class="w-full p-2 border rounded" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Last Name</label>
        <input type="text" name="last_name" id="updateLastName" class="w-full p-2 border rounded" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" id="updateEmail" class="w-full p-2 border rounded" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Role</label>
        <select name="role" id="updateRole" class="w-full p-2 border rounded">
          <option value="admin">Admin</option>
          <option value="moderator">Moderator</option>
        </select>
      </div>

      <div class="flex justify-end gap-4">
        <button type="button" onclick="closeUpdateModal()" class="text-gray-500 hover:text-gray-700">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Enhanced Modal for Confirmation -->
<div id="deleteUserModal" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop hidden">
  <div class="modal-content bg-white w-full max-w-md p-0 rounded-2xl shadow-2xl overflow-hidden">
    <!-- Modal Header with Gradient -->
    <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6 text-white relative">
      <button id="closeModalBtn" class="close-btn absolute top-4 right-4 text-white hover:text-white/80">
        <i class="fas fa-times text-lg"></i>
      </button>
      <div class="flex items-center">
        <div class="bg-white/20 p-3 rounded-full mr-4">
          <i class="fas fa-exclamation-triangle text-2xl"></i>
        </div>
        <h2 class="text-xl font-bold">Confirm Deletion</h2>
      </div>
    </div>
    
    <!-- Modal Body -->
    <div class="p-6">
      <div class="mb-6">
        <p class="text-gray-700 mb-2">Are you sure you want to delete this user?</p>
        <p class="text-gray-500 text-sm">This action cannot be undone.</p>
      </div>
      
      <!-- User Info Preview (Dynamic) -->
      <div id="userPreview" class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold mr-4">
            <span id="userInitials"></span>
          </div>
          <div>
            <h3 id="userName" class="font-medium text-gray-800"></h3>
            <p id="userEmail" class="text-gray-500 text-sm"></p>
            <p class="text-gray-400 text-xs mt-1">User ID: <span id="userId"></span></p>
          </div>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="flex justify-end gap-4 mt-4">
        <button id="cancelDeleteBtn" class="btn-cancel px-5 py-2.5 rounded-lg font-medium text-gray-700 border border-gray-200">
          Cancel
        </button>
        <button id="confirmDeleteBtn" class="btn-delete bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2.5 rounded-lg font-medium">
          <i id="deleteIcon" class="fas fa-trash-alt mr-2"></i>
          <i id="loadingIcon" class="fas fa-spinner spinner mr-2 hidden"></i>
          <span id="deleteText">Delete User</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 text-center">
    <p id="successMessage" class="text-green-500 font-semibold"></p>
    <button onclick="document.getElementById('successModal').classList.add('hidden')" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-full hover:bg-green-600">OK</button>
  </div>
</div>

<!-- Toast Notification (Optional - for enhanced feedback) -->
<div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
  <div class="flex items-center p-4 rounded-lg shadow-lg max-w-xs">
    <div id="toastIcon" class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full mr-3"></div>
    <div>
      <p id="toastTitle" class="font-medium"></p>
      <p id="toastMessage" class="text-sm"></p>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Function to show a success modal
    function showModal(message) {
        const modal = document.createElement("div");
        modal.innerHTML = `
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 animate-fadeIn">
                <div class="bg-white rounded-lg p-6 shadow-lg max-w-sm w-full text-center transform transition-all scale-95 hover:scale-100">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold mb-2 text-green-600">Success</h2>
                    <p class="mb-4 text-gray-600">${message}</p>
                    <button id="closeSuccessModal" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-all">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Remove the modal when the close button is clicked
        modal.querySelector("#closeSuccessModal").addEventListener("click", function () {
            document.body.removeChild(modal);
        });
    }

    // Function to show an error modal
    function showErrorModal(message) {
        const modal = document.createElement("div");
        modal.innerHTML = `
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 animate-fadeIn">
                <div class="bg-white rounded-lg p-6 shadow-lg max-w-sm w-full text-center transform transition-all scale-95 hover:scale-100">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold mb-2 text-red-600">Error</h2>
                    <p class="mb-4 text-gray-600">${message}</p>
                    <button id="closeErrorModal" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-all">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Remove the modal when the close button is clicked
        modal.querySelector("#closeErrorModal").addEventListener("click", function () {
            document.body.removeChild(modal);
        });
    }

    // Modal functionality
    function openModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Focus the first input when modal opens (for accessibility)
        setTimeout(() => {
            document.getElementById('firstName').focus();
        }, 100);
    }
    
    function closeModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Make closeModal available globally
    window.closeModal = closeModal;
    
    // Make submitForm available globally
    window.submitForm = function() {
        const form = document.getElementById("addUserForm");
        if (form) {
            // Trigger form submission
            const submitEvent = new Event('submit', {
                'bubbles': true,
                'cancelable': true
            });
            form.dispatchEvent(submitEvent);
        }
    };
    
    // Add click event to the "Add User" button to open the modal
    const addUserBtn = document.getElementById('openAddUserModal');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', openModal);
    }
    
    // Close modal when clicking outside
    const addUserModal = document.getElementById('addUserModal');
    if (addUserModal) {
        addUserModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close modal with cancel button
        const cancelBtn = addUserModal.querySelector('button[type="button"][onclick="closeModal()"]');
        if (cancelBtn) {
            cancelBtn.onclick = closeModal;
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && addUserModal && !addUserModal.classList.contains('hidden')) {
            closeModal();
        }
    });
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change the eye icon
            const eyeIcon = this.querySelector('svg');
            if (type === 'text') {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        });
    }
    
    // Password strength meter
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-strength-text');
            
            if (!strengthBar || !strengthText) return;
            
            let strength = 0;
            
            // Calculate password strength
            if (password.length >= 8) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^A-Za-z0-9]/)) strength += 25;
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update color and text based on strength
            if (strength <= 25) {
                strengthBar.className = 'bg-red-500 h-1 rounded-full transition-all duration-300';
                strengthText.textContent = 'Password strength: Too weak';
            } else if (strength <= 50) {
                strengthBar.className = 'bg-orange-500 h-1 rounded-full transition-all duration-300';
                strengthText.textContent = 'Password strength: Weak';
            } else if (strength <= 75) {
                strengthBar.className = 'bg-yellow-500 h-1 rounded-full transition-all duration-300';
                strengthText.textContent = 'Password strength: Good';
            } else {
                strengthBar.className = 'bg-green-500 h-1 rounded-full transition-all duration-300';
                strengthText.textContent = 'Password strength: Strong';
            }
        });
    }

    // Add User Form submission handling
    const form = document.getElementById("addUserForm");
    if (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent the default form submission behavior

            // Get and trim the form data
            const firstName = document.getElementById("firstName").value.trim();
            const lastName = document.getElementById("lastName").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const role = document.getElementById("role").value;

            // Validate the inputs
            if (!firstName || !lastName || !email || !password || !role) {
                showErrorModal("All fields are required.");
                return;
            }

            // Create a FormData object to send the form data via AJAX
            const formData = new FormData();
            formData.append("firstName", firstName);
            formData.append("lastName", lastName);
            formData.append("email", email);
            formData.append("password", password);
            formData.append("role", role);

            // Show loading state
            const submitButton = document.querySelector('button[onclick="submitForm()"]');
            let originalText = '';
            if (submitButton) {
                originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            }

            // Send data to the server using AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_moderator_config.php", true);

            xhr.onload = function () {
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show pop-up success modal
                            showModal(response.message || "User added successfully.");
                            form.reset(); // Reset the form fields after successful submission
                            closeModal(); // Close the add user modal
                        } else {
                            // If the error message contains "email", show the error modal
                            if (response.error && response.error.toLowerCase().includes("email")) {
                                showErrorModal(response.error || "This email is already in use. Please choose another email.");
                            } else {
                                showErrorModal(response.error || "An error occurred. Please try again.");
                            }
                        }
                    } catch (e) {
                        showErrorModal("Invalid response from server. Please try again.");
                    }
                } else {
                    showErrorModal("Request failed. Please try again later.");
                }
            };

            xhr.onerror = function () {
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
                showErrorModal("There was an error with the request. Please check your connection.");
            };

            xhr.send(formData); // Send the FormData object
        });
    }
    
    // Add animation styles if not already present
    if (!document.getElementById('modal-animations')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'modal-animations';
        styleEl.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out;
            }
            
            @keyframes modal-appear {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            .modal-content {
                animation: modal-appear 0.3s ease-out;
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            
            .shake-animation {
                animation: shake 0.5s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(20px); }
            }
            
            .toast-animation {
                animation: fadeIn 0.3s ease forwards, fadeOut 0.5s ease 3s forwards;
            }
        `;
        document.head.appendChild(styleEl);
    }

    // Load users when the page is ready
    if (typeof loadUsers === 'function') {
        loadUsers();
    }

    // Highlight active sidebar link
    if (typeof highlightActiveSidebarLink === 'function') {
        highlightActiveSidebarLink();
    }
});

// Function to load users
function loadUsers() {
  const adminList = document.getElementById("adminList");
  const moderatorList = document.getElementById("moderatorList");

  if (!adminList || !moderatorList) return;

  fetch("get_moderators.php")
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Admins
        adminList.innerHTML = "";
        data.admins.forEach(admin => {
          const li = document.createElement("li");
          li.classList.add("text-sm", "text-gray-700", "dark:text-gray-300", "flex", "justify-between", "items-center", "mb-2");

          li.innerHTML = `
            <div class="flex items-center gap-2">
              <i class="fas fa-user-shield text-red-500"></i>
              ${admin.first_name} ${admin.last_name}
              <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">Admin</span>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-indigo-500 text-sm">${admin.email}</span>
              <button onclick="openUpdateModal(${admin.id})" class="text-yellow-500 hover:text-yellow-600">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="confirmDeleteUser(${admin.id})" class="text-red-500 hover:text-red-600">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          `;
          adminList.appendChild(li);
        });

        // Moderators
        moderatorList.innerHTML = "";
        data.moderators.forEach(moderator => {
          const li = document.createElement("li");
          li.classList.add("text-sm", "text-gray-700", "dark:text-gray-300", "flex", "justify-between", "items-center", "mb-2");

          li.innerHTML = `
            <div class="flex items-center gap-2">
              <i class="fas fa-user-cog text-blue-500"></i>
              ${moderator.first_name} ${moderator.last_name}
              <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">Moderator</span>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-indigo-500 text-sm">${moderator.email}</span>
              <button onclick="openUpdateModal(${moderator.id})" class="text-yellow-500 hover:text-yellow-600">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="confirmDeleteUser(${moderator.id})" class="text-red-500 hover:text-red-600">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          `;
          moderatorList.appendChild(li);
        });
      } else {
        console.error("Failed to load users:", data.error);
        alert("Failed to load users data.");
      }
    })
    .catch(error => {
      console.error("Fetch error:", error);
      alert("There was an error loading user data.");
    });
}

// Function to open the update modal and prefill the form
function openUpdateModal(id) {
  fetch(`get-admin-config.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const user = data.user;
        document.getElementById("updateUserId").value = user.id;
        document.getElementById("updateFirstName").value = user.first_name;
        document.getElementById("updateLastName").value = user.last_name;
        document.getElementById("updateEmail").value = user.email;
        document.getElementById("updateRole").value = user.role;

        // Open the update modal
        const modal = document.getElementById("updateUserModal");
        modal.classList.remove("hidden");
      } else {
        alert("Failed to load user data.");
      }
    })
    .catch(error => {
      console.error("Error fetching user data:", error);
      alert("There was an error loading user data.");
    });
}

// Function to close the update modal
function closeUpdateModal() {
  const modal = document.getElementById("updateUserModal");
  modal.classList.add("hidden");
}

// Function to show the success message modal
function showSuccessModal(message) {
  const successModal = document.getElementById("successModal");
  const successMessage = document.getElementById("successMessage");
  successMessage.textContent = message;

  // Open the success modal
  successModal.classList.remove("hidden");

  // Automatically close the success modal after 3 seconds
  setTimeout(() => {
    successModal.classList.add("hidden");
  }, 3000);
}

// Function to update user data - attach event listener if form exists
document.addEventListener("DOMContentLoaded", function() {
  const updateForm = document.getElementById("updateUserForm");
  if (updateForm) {
    updateForm.addEventListener("submit", function(event) {
      event.preventDefault();

      const formData = new FormData(this);
      
      fetch("update-admin-config.php", {
        method: "POST",
        body: formData
      })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            showSuccessModal("User updated successfully.");
            loadUsers(); // Refresh the list
            closeUpdateModal(); // Close the modal
          } else {
            alert("Failed to update user: " + result.error);
          }
        })
        .catch(error => {
          console.error("Update error:", error);
          alert("Error updating user.");
        });
    });
  }
});

// Function to show confirmation modal for delete
function confirmDeleteUser(id) {
  const modal = document.getElementById("deleteUserModal");
  if (!modal) {
    console.error("Delete modal not found");
    return;
  }
  
  const modalContent = modal.querySelector('.modal-content');
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
  const closeModalBtn = document.getElementById("closeModalBtn");
  
  // Reset button state
  const deleteIcon = document.getElementById("deleteIcon") || document.querySelector("#confirmDeleteBtn i");
  const loadingIcon = document.getElementById("loadingIcon");
  
  if (deleteIcon && loadingIcon) {
    deleteIcon.classList.remove("hidden");
    loadingIcon.classList.add("hidden");
  }
  
  // Update button text if element exists
  const deleteText = document.getElementById("deleteText");
  if (deleteText) {
    deleteText.textContent = "Delete User";
  }
  
  // Enable button
  if (confirmDeleteBtn) {
    confirmDeleteBtn.disabled = false;
  }
  
  // First fetch user data to display in the modal
  fetch(`get-admin-config.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const user = data.user;
        
        // Update user info in the modal
        if (document.getElementById("userInitials")) {
          // Get initials from first and last name
          const initials = (user.first_name.charAt(0) + user.last_name.charAt(0)).toUpperCase();
          document.getElementById("userInitials").textContent = initials;
        }
        
        if (document.getElementById("userName")) {
          document.getElementById("userName").textContent = `${user.first_name} ${user.last_name}`;
        }
        
        if (document.getElementById("userEmail")) {
          document.getElementById("userEmail").textContent = user.email;
        }
        
        if (document.getElementById("userId")) {
          document.getElementById("userId").textContent = id;
        }
        
        // Show modal with animation
        modal.classList.remove("hidden");
        document.body.style.overflow = 'hidden';
      } else {
        // If we can't get user data, still show the modal but without user details
        console.error("Failed to load user data for deletion modal");
        modal.classList.remove("hidden");
        document.body.style.overflow = 'hidden';
      }
    })
    .catch(error => {
      console.error("Error fetching user data for deletion:", error);
      // Still show the modal even if we can't get user data
      modal.classList.remove("hidden");
      document.body.style.overflow = 'hidden';
    });

  // Handle confirmation
  if (confirmDeleteBtn) {
    confirmDeleteBtn.onclick = function() {
      // Show loading state if elements exist
      if (deleteIcon && loadingIcon) {
        deleteIcon.classList.add("hidden");
        loadingIcon.classList.remove("hidden");
      }
      
      if (deleteText) {
        deleteText.textContent = "Deleting...";
      }
      
      confirmDeleteBtn.disabled = true;
      
      // Call delete function
      deleteUser(id);
    };
  }

  // Handle cancellation
  function closeModal() {
    // If we have animation classes, use them
    if (modal.querySelector('.modal-backdrop')) {
      modal.querySelector('.modal-backdrop').classList.add('hiding');
      if (modalContent) {
        modalContent.classList.add('hiding');
      }
      
      setTimeout(() => {
        modal.classList.add("hidden");
        if (modal.querySelector('.modal-backdrop')) {
          modal.querySelector('.modal-backdrop').classList.remove('hiding');
          if (modalContent) {
            modalContent.classList.remove('hiding');
          }
        }
        document.body.style.overflow = '';
      }, 300);
    } else {
      // Fallback to simple hide
      modal.classList.add("hidden");
      document.body.style.overflow = '';
    }
  }
  
  // Set up close handlers
  if (cancelDeleteBtn) {
    cancelDeleteBtn.onclick = closeModal;
  }
  
  // If close button exists, set its handler
  if (closeModalBtn) {
    closeModalBtn.onclick = closeModal;
  }
  
  // Close modal when clicking outside if we have the right structure
  if (modal === modal.querySelector('.modal-backdrop')?.parentNode) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeModal();
      }
    });
    
    // Prevent propagation from modal content
    if (modalContent) {
      modalContent.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    }
  }
  
  // Keyboard support (Escape to close)
  const escHandler = function(e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      closeModal();
      document.removeEventListener('keydown', escHandler);
    }
  };
  
  document.addEventListener('keydown', escHandler);
}

// Function to delete user
function deleteUser(id) {
  const modal = document.getElementById("deleteUserModal");
  if (!modal) return;
  
  const modalContent = modal.querySelector('.modal-content');
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  fetch("delete-Admin.php", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id: id })
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        if (typeof showToast === 'function') {
          showToast("success", "User Deleted", "User has been successfully deleted.");
        } else {
          alert("User deleted successfully.");
        }

        // Close modal with animation if possible
        if (modal.querySelector('.modal-backdrop')) {
          modal.querySelector('.modal-backdrop').classList.add('hiding');
          if (modalContent) {
            modalContent.classList.add('hiding');
          }

          setTimeout(() => {
            modal.classList.add("hidden");
            if (modal.querySelector('.modal-backdrop')) {
              modal.querySelector('.modal-backdrop').classList.remove('hiding');
              if (modalContent) {
                modalContent.classList.remove('hiding');
              }
            }
            document.body.style.overflow = '';

            if (typeof loadUsers === 'function') {
              loadUsers();
            }
          }, 300);
        } else {
          modal.classList.add("hidden");
          document.body.style.overflow = '';

          if (typeof loadUsers === 'function') {
            loadUsers();
          }
        }
      } else {
        handleDeleteError(result.error || "Failed to delete user.");
      }
    })
    .catch(error => {
      console.error("Delete error:", error);
      handleDeleteError("Network error. Please try again.");
    });

  // Helper: handle delete errors
  function handleDeleteError(message) {
    if (modalContent && typeof modalContent.classList.add === 'function') {
      modalContent.classList.add('shake-animation');

      setTimeout(() => {
        modalContent.classList.remove('shake-animation');
        resetDeleteButton();

        if (typeof showToast === 'function') {
          showToast("error", "Error", message);
        } else {
          alert("Failed to delete user: " + message);
        }
      }, 500);
    } else {
      resetDeleteButton();
      alert("Failed to delete user: " + message);
    }
  }
}

// Helper function to reset delete button state
function resetDeleteButton() {
  const deleteIcon = document.getElementById("deleteIcon") || document.querySelector("#confirmDeleteBtn i");
  const loadingIcon = document.getElementById("loadingIcon");
  const deleteText = document.getElementById("deleteText");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  
  if (deleteIcon && loadingIcon) {
    deleteIcon.classList.remove("hidden");
    loadingIcon.classList.add("hidden");
  }
  
  if (deleteText) {
    deleteText.textContent = "Delete User";
  }
  
  if (confirmDeleteBtn) {
    confirmDeleteBtn.disabled = false;
  }
}

// Toast notification function (add this if you want to use toast instead of alerts)
function showToast(type, title, message) {
  // Check if toast element exists, create it if not
  let toast = document.getElementById("toast");
  
  if (!toast) {
    // Create toast element
    toast = document.createElement("div");
    toast.id = "toast";
    toast.className = "fixed bottom-4 right-4 z-50 hidden";
    
    const toastContent = document.createElement("div");
    toastContent.className = "flex items-center p-4 rounded-lg shadow-lg max-w-xs";
    
    const toastIcon = document.createElement("div");
    toastIcon.id = "toastIcon";
    toastIcon.className = "flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full mr-3";
    
    const toastTextDiv = document.createElement("div");
    
    const toastTitle = document.createElement("p");
    toastTitle.id = "toastTitle";
    toastTitle.className = "font-medium";
    
    const toastMessage = document.createElement("p");
    toastMessage.id = "toastMessage";
    toastMessage.className = "text-sm";
    
    toastTextDiv.appendChild(toastTitle);
    toastTextDiv.appendChild(toastMessage);
    
    toastContent.appendChild(toastIcon);
    toastContent.appendChild(toastTextDiv);
    
    toast.appendChild(toastContent);
    document.body.appendChild(toast);
  }
  
  const toastIcon = document.getElementById("toastIcon");
  const toastTitle = document.getElementById("toastTitle");
  const toastMessage = document.getElementById("toastMessage");
  
  // Set toast content based on type
  if (type === "success") {
    toast.querySelector("div").className = "flex items-center p-4 bg-green-50 text-green-800 rounded-lg shadow-lg max-w-xs";
    toastIcon.className = "flex-shrink-0 w-8 h-8 bg-green-100 text-green-500 flex items-center justify-center rounded-full mr-3";
    toastIcon.innerHTML = '<i class="fas fa-check"></i>';
  } else if (type === "error") {
    toast.querySelector("div").className = "flex items-center p-4 bg-red-50 text-red-800 rounded-lg shadow-lg max-w-xs";
    toastIcon.className = "flex-shrink-0 w-8 h-8 bg-red-100 text-red-500 flex items-center justify-center rounded-full mr-3";
    toastIcon.innerHTML = '<i class="fas fa-exclamation"></i>';
  }
  
  toastTitle.textContent = title;
  toastMessage.textContent = message;
  
  // Show toast
  toast.classList.remove("hidden");
  toast.classList.add("toast-animation");
  
  // Hide toast after 3.5 seconds
  setTimeout(() => {
    toast.classList.remove("toast-animation");
    toast.classList.add("hidden");
  }, 3500);
}

// Function to highlight the active sidebar link based on the current page URL
function highlightActiveSidebarLink() {
    let currentPage = window.location.pathname.split("/").pop().toLowerCase();
    if (currentPage.includes("?")) {
        currentPage = currentPage.split("?")[0];
    }

    document.querySelectorAll("#sidebar nav a").forEach(link => {
        let linkPage = link.getAttribute("href").toLowerCase();
        if (linkPage.includes("?")) {
            linkPage = linkPage.split("?")[0];
        }
        if (currentPage === linkPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
}
</script>


</body>
</html>
