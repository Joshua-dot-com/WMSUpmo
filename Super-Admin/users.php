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
    <title>Users - Admin Dashboard</title>
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
        // Add these CSS animations to your stylesheet

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

.opacity-0 {
    opacity: 0;
    transition: opacity 0.15s ease-out;
}

.animate-delay-50 { animation-delay: 50ms; }
.animate-delay-100 { animation-delay: 100ms; }
.animate-delay-150 { animation-delay: 150ms; }
.animate-delay-200 { animation-delay: 200ms; }
.animate-delay-250 { animation-delay: 250ms; }

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
            <!-- Header -->
            <header class="border-b bg-white">
                <div class="container flex h-16 items-center justify-between px-6">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="md:hidden mr-4">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-2xl font-bold">Users</h1>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold">All Users</h2>
                        <div class="flex items-center gap-2">
                        <div class="relative mb-4">
    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
    <input
        type="search"
        id="searchInput"
        placeholder="Search users..."
        class="pl-8 h-10 w-full md:w-[250px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
    />
</div>
                        </div>
                    </div>
                    <div class="p-6">
                    <div class="flex items-center gap-4 mb-4 flex-wrap">
    <button class="btn btn-primary-light btn-sm filter-btn active" data-status="">All Users</button>
    <button class="btn btn-outline btn-sm filter-btn" data-status="Active">Active</button>
    <button class="btn btn-outline btn-sm filter-btn" data-status="Inactive">Inactive</button>
</div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                <tr class="border-b">
    <th class="h-12 px-4 text-left font-medium text-gray-500">Name</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Email</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Department</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Role</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Admin Role / Other Services</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Status</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Account State</th>
    <th class="h-12 px-4 text-left font-medium text-gray-500">Last Login</th>
    <th class="h-12 px-4 text-right font-medium text-gray-500">Actions</th>
</tr>

                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
            
                       <div class="flex flex-col md:flex-row items-center justify-between py-4">
                       <div class="flex flex-col md:flex-row items-center justify-between py-4">
    <div id="showing-text" class="text-sm text-gray-500 mb-4 md:mb-0">
        Showing <strong>0</strong> of <strong>0</strong> users
    </div>
    <div id="pagination" class="flex items-center space-x-2"></div>
</div>

</div>

                    </div>
                </div>
            </main>
        </div>
    </div>

  <!-- Enhanced Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>
    
    <!-- Modal panel -->
    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <!-- Modal header -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center">
          <div class="bg-white/20 p-2 rounded-full mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-white" id="modal-title">Edit User Profile</h2>
        </div>
        <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="closeEditModal()" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Modal body -->
      <div class="px-6 py-4">
        <form id="editUserForm" onsubmit="event.preventDefault(); updateUser();">
          <input type="hidden" id="edit-user-id" name="id">
          
          <!-- User Information Section -->
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4 pb-2 border-b">User Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-first-name">First Name</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" id="edit-first-name" name="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" readonly>
                </div>
              </div>
              
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-last-name">Last Name</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" id="edit-last-name" name="last_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" readonly>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-email">Email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
                <input type="email" id="edit-email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" readonly>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-college">College</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                    </svg>
                  </div>
                  <input type="text" id="edit-college" name="college" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" readonly>
                </div>
              </div>
              
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-role">Role</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" id="edit-role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" readonly>
                </div>
              </div>
            </div>
          </div>
          
          <!-- User Access Control Section -->
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4 pb-2 border-b">Access Control</h3>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-status">Account Status</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                </div>
                <select id="edit-status" name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <p class="text-xs text-gray-500 mt-1">Active accounts can access the system. Inactive accounts cannot log in but are not blocked.</p>
            </div>
            
            <div class="mb-4">
              <label class="flex items-center">
                <input type="checkbox" id="edit-blocked" name="blocked" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
                <span class="ml-2 text-sm font-medium text-gray-700">Block User</span>
              </label>
              <div class="mt-1 ml-7">
                <p class="text-xs text-gray-500">Blocked users cannot log in and will receive a notification that their account has been blocked.</p>
              </div>
            </div>
            
            <div id="block-reason-container" class="mb-4 hidden">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-block-reason">Reason for Blocking</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <textarea id="edit-block-reason" name="block_reason" rows="3" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Enter reason for blocking this user"></textarea>
              </div>
              <p class="text-xs text-gray-500 mt-1">This reason will be displayed to the user when they attempt to log in.</p>
            </div>
            
            <div id="block-duration-container" class="mb-4 hidden">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="edit-block-until">Block Until (Optional)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="date" id="edit-block-until" name="block_until" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
              </div>
              <p class="text-xs text-gray-500 mt-1">Leave empty for indefinite block. The system will automatically unblock the user after this date.</p>
            </div>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-yellow-700">
                    Blocking a user is a serious action. Make sure you have a valid reason and appropriate authorization.
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150" onclick="closeEditModal()">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 shadow-md hover:shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('sidebar')?.classList.toggle('active');
});

document.addEventListener("DOMContentLoaded", () => {
    const currentPageName = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
        if (link.getAttribute("href") === currentPageName) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
    
    // Set up blocking functionality
    setupBlockingFields();
    
    // Set up filtering and fetch initial data
    setupFiltering();
    fetchUsers(); // Initial call
});

function setupBlockingFields() {
    // Toggle block reason and duration fields when block checkbox is clicked
    const blockCheckbox = document.getElementById('edit-blocked');
    if (blockCheckbox) {
        blockCheckbox.addEventListener('change', function() {
            const blockReasonContainer = document.getElementById('block-reason-container');
            const blockDurationContainer = document.getElementById('block-duration-container');
            
            if (this.checked) {
                blockReasonContainer.classList.remove('hidden');
                blockDurationContainer.classList.remove('hidden');
            } else {
                blockReasonContainer.classList.add('hidden');
                blockDurationContainer.classList.add('hidden');
                // Clear values when unchecked
                document.getElementById('edit-block-reason').value = '';
                document.getElementById('edit-block-until').value = '';
            }
        });
    }
    
    // Close modal when clicking outside or pressing Escape
    const modal = document.getElementById('editModal');
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeEditModal();
        }
    });
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEditModal();
            }
        });
    }
}

let currentPage = 1;
const itemsPerPage = 7;
let usersData = [];

function setupFiltering() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('searchInput');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            filterButtons.forEach(btn => btn.classList.replace('btn-primary-light', 'btn-outline'));
            this.classList.replace('btn-outline', 'btn-primary-light');
            this.classList.add('active');

            const status = this.getAttribute('data-status');
            currentPage = 1;
            fetchUsers(status);
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
            renderPagination();
        });
    }
}

function fetchUsers(statusFilter = "") {
    fetch(`fetch_all_users.php?filter=${encodeURIComponent(statusFilter)}`)
        .then(res => res.json())
        .then(data => {
            usersData = Array.isArray(data.data) ? data.data : [];
            renderTable();
            renderPagination();
        })
        .catch(err => {
            console.error("Fetch error:", err);
            showToast('error', 'Error', 'Failed to fetch users data');
        });
}

function renderTable() {
    const tbody = document.querySelector("tbody");
    const showingText = document.getElementById('showing-text');
    const searchInput = document.getElementById('searchInput');
    
    if (!tbody || !searchInput) return;
    
    const searchValue = searchInput.value.trim().toLowerCase();
    
    // Add a fade-out effect before clearing the table
    tbody.classList.add('opacity-0');
    setTimeout(() => {
        tbody.innerHTML = "";
        
        let filtered = usersData.filter(user =>
            `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchValue) ||
            user.email.toLowerCase().includes(searchValue) ||
            (user.college || '').toLowerCase().includes(searchValue) ||
            user.role.toLowerCase().includes(searchValue) ||
            (user.admin_role || '').toLowerCase().includes(searchValue) ||
            (user.status || '').toLowerCase().includes(searchValue) ||
            (user.account_state || '').toLowerCase().includes(searchValue)
        );

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center p-8">
                        <div class="flex flex-col items-center justify-center py-10 animate-fade-in">
                            <div class="bg-gray-100 rounded-full p-4 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-xl font-semibold text-gray-700">No users found</p>
                            <p class="text-gray-500 mt-2 max-w-sm text-center">We couldn't find any users matching your search criteria. Try adjusting your filters or search terms.</p>
                            <button onclick="clearSearch()" class="mt-4 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Search
                            </button>
                        </div>
                    </td>
                </tr>`;
            if (showingText) {
                showingText.innerHTML = `<span class="text-gray-500">No results found</span>`;
            }
            tbody.classList.remove('opacity-0');
            return;
        }

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, filtered.length);
        const paginated = filtered.slice(start, end);

        if (showingText) {
            showingText.innerHTML = `
                <div class="flex items-center">
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-medium mr-2">
                        ${filtered.length}
                    </span>
                    <span>users found</span>
                    <span class="mx-2 text-gray-400">•</span>
                    <span>Showing</span>
                    <span class="font-medium mx-1">${start + 1}-${end}</span>
                </div>
            `;
        }

        // Add rows with staggered animation
        paginated.forEach((user, index) => {
            const statusClass = user.status === 'Granted' 
                ? 'bg-green-100 text-green-800 border border-green-200' 
                : 'bg-red-100 text-red-800 border border-red-200';

            const accountStateClass = user.account_state === 'Active'
                ? 'bg-green-50 text-green-700 border border-green-200'
                : 'bg-red-50 text-red-700 border border-red-200';

            const blockedBadge = user.is_blocked === '1' 
                ? `<span class="bg-yellow-100 text-yellow-800 border border-yellow-200 ml-2 px-2 py-1 rounded-full text-xs font-medium flex items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                     </svg>
                     Blocked
                   </span>` 
                : '';

            // Format last login date nicely
            const lastLogin = user.last_login ? formatLastLogin(user.last_login) : null;
            const lastLoginDisplay = lastLogin 
                ? `<div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                     <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                     <span>${lastLogin}</span>
                   </div>`
                : `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                     <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                     </svg>
                     <span>Never</span>
                   </span>`;

            const tr = document.createElement('tr');
            tr.className = `border-b hover:bg-gray-50 transition-all duration-200 animate-fade-in animate-delay-${index * 50}`;
            tr.style.animationDelay = `${index * 50}ms`;
            
            tr.innerHTML = `
                <td class="p-4 text-center">
                    <div class="flex items-center space-x-3 justify-center">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-medium">
                            ${getInitials(user.first_name, user.last_name)}
                        </div>
                        <div class="font-medium text-gray-900 text-left">${user.first_name} ${user.last_name}</div>
                    </div>
                </td>
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-600">${user.email}</span>
                    </div>
                </td>
                <td class="p-4 text-center">
                    ${user.college ? 
                        `<span class="bg-purple-50 text-purple-700 px-2 py-1 rounded-md text-xs font-medium">${user.college}</span>` : 
                        '<span class="text-gray-400">—</span>'}
                </td>
                <td class="p-4 text-center">
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-xs font-medium">${user.role}</span>
                </td>
                <td class="p-4 text-center">
                    ${user.admin_role ? 
                        `<span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md text-xs font-medium">${user.admin_role}</span>` : 
                        '<span class="text-gray-400">—</span>'}
                </td>
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center">
                        <span class="${statusClass} px-2 py-1 rounded-full text-xs font-medium">${user.status}</span>
                        ${blockedBadge}
                    </div>
                </td>
                <td class="p-4 text-center">
                    <span class="${accountStateClass} px-2 py-1 rounded-full text-xs font-medium">${user.account_state || '—'}</span>
                </td>
                <td class="p-4 text-center">${lastLoginDisplay}</td>
                <td class="p-4 text-center">
                    <button onclick="openEditModal(${user.id})" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transform hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                </td>
            `;
            
            tbody.appendChild(tr);
        });
        
        // Fade in the table
        setTimeout(() => {
            tbody.classList.remove('opacity-0');
        }, 50);
    }, 150); // Short delay for the fade-out effect
}

// Helper function to get initials from name
function getInitials(firstName, lastName) {
    return `${firstName.charAt(0)}${lastName.charAt(0)}`;
}

// Helper function to format last login date
function formatLastLogin(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    // Format the time part
    const time = date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit'
    });
    
    // Today
    if (date.toDateString() === now.toDateString()) {
        return `Today at ${time}`;
    }
    
    // Yesterday
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return `Yesterday at ${time}`;
    }
    
    // Within the last week
    if (diffDays < 7) {
        return `${date.toLocaleDateString('en-US', { weekday: 'short' })} at ${time}`;
    }
    
    // Older than a week
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: now.getFullYear() !== date.getFullYear() ? 'numeric' : undefined
    }) + ` at ${time}`;
}

// Function to clear search
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        renderTable();
    }
}


function renderPagination() {
    const pagination = document.getElementById('pagination');
    const searchInput = document.getElementById('searchInput');
    
    if (!pagination || !searchInput) return;
    
    const searchValue = searchInput.value.trim().toLowerCase();
    let filtered = usersData.filter(user =>
        `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchValue) ||
        user.email.toLowerCase().includes(searchValue) ||
        (user.college || '').toLowerCase().includes(searchValue) ||
        user.role.toLowerCase().includes(searchValue) ||
        (user.admin_role || '').toLowerCase().includes(searchValue) ||
        user.status.toLowerCase().includes(searchValue)
    );

    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    pagination.innerHTML = "";
    if (totalPages <= 1) return;

    // Previous button
    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="hidden sm:inline">Previous</span>
    `;
    prevBtn.className = `flex items-center justify-center px-3 py-1.5 rounded-md border ${currentPage === 1 ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'}`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.setAttribute('aria-label', 'Previous page');
    if (currentPage > 1) {
        prevBtn.onclick = () => {
            currentPage--;
            renderTable();
            renderPagination();
        };
    }
    pagination.appendChild(prevBtn);

    // Page buttons container
    const pageButtonsContainer = document.createElement('div');
    pageButtonsContainer.className = 'flex items-center space-x-1';
    
    // Determine which page buttons to show
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }

    // First page button if not starting at page 1
    if (startPage > 1) {
        const firstPageBtn = document.createElement('button');
        firstPageBtn.textContent = '1';
        firstPageBtn.className = `flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-md border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500`;
        firstPageBtn.onclick = () => {
            currentPage = 1;
            renderTable();
            renderPagination();
        };
        pageButtonsContainer.appendChild(firstPageBtn);
        
        // Add ellipsis if there's a gap
        if (startPage > 2) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'flex items-center justify-center min-w-[2.25rem] h-9 px-2 text-gray-400';
            pageButtonsContainer.appendChild(ellipsis);
        }
    }

    // Page buttons
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i.toString();
        pageBtn.className = `flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-md ${i === currentPage ? 'bg-primary-light text-white font-medium' : 'border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50'} transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500`;
        pageBtn.setAttribute('aria-label', `Page ${i}`);
        if (i === currentPage) {
            pageBtn.setAttribute('aria-current', 'page');
        }
        pageBtn.onclick = () => {
            currentPage = i;
            renderTable();
            renderPagination();
        };
        pageButtonsContainer.appendChild(pageBtn);
    }

    // Last page button if not ending at last page
    if (endPage < totalPages) {
        // Add ellipsis if there's a gap
        if (endPage < totalPages - 1) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'flex items-center justify-center min-w-[2.25rem] h-9 px-2 text-gray-400';
            pageButtonsContainer.appendChild(ellipsis);
        }
        
        const lastPageBtn = document.createElement('button');
        lastPageBtn.textContent = totalPages.toString();
        lastPageBtn.className = `flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-md border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500`;
        lastPageBtn.onclick = () => {
            currentPage = totalPages;
            renderTable();
            renderPagination();
        };
        pageButtonsContainer.appendChild(lastPageBtn);
    }
    
    pagination.appendChild(pageButtonsContainer);

    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = `
        <span class="hidden sm:inline">Next</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    `;
    nextBtn.className = `flex items-center justify-center px-3 py-1.5 rounded-md border ${currentPage === totalPages ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'}`;
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.setAttribute('aria-label', 'Next page');
    if (currentPage < totalPages) {
        nextBtn.onclick = () => {
            currentPage++;
            renderTable();
            renderPagination();
        };
    }
    pagination.appendChild(nextBtn);
}

function openEditModal(userId) {
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');

    const currentUserId = document.getElementById('edit-user-id').value;
    const isSameUser = parseInt(currentUserId) === parseInt(userId);

    // Try to load from saved draft
    const savedFormData = localStorage.getItem('editUserFormData');
    if (savedFormData) {
        const formObject = JSON.parse(savedFormData);
        if (parseInt(formObject.id) === parseInt(userId)) {
            const form = document.getElementById('editUserForm');
            for (const key in formObject) {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = formObject[key] === '1';
                    } else {
                        field.value = formObject[key];
                    }
                }
            }

            // Show/hide block reason & duration sections
            const blockCheckbox = document.getElementById('edit-blocked');
            const reasonContainer = document.getElementById('block-reason-container');
            const durationContainer = document.getElementById('block-duration-container');
            if (blockCheckbox && blockCheckbox.checked) {
                reasonContainer.classList.remove('hidden');
                durationContainer.classList.remove('hidden');
            } else {
                reasonContainer.classList.add('hidden');
                durationContainer.classList.add('hidden');
            }

            return; // Skip fetch if draft exists
        }
    }

    // If no draft or different user, fetch user data
    fetch(`get_user_by_id.php?id=${userId}`)
        .then(res => res.json())
        .then(user => {
            if (!user.success) {
                showToast('error', 'Error', 'Failed to fetch user data');
                closeEditModal();
                return;
            }

            // Only update fields if not editing the same user
            if (!isSameUser) {
                document.getElementById('edit-user-id').value = user.data.id;
                document.getElementById('edit-first-name').value = user.data.first_name;
                document.getElementById('edit-last-name').value = user.data.last_name;
                document.getElementById('edit-email').value = user.data.email;
                document.getElementById('edit-role').value = user.data.role;
                document.getElementById('edit-status').value = user.data.status;

                const collegeInput = document.getElementById('edit-college');
                const collegeLabel = document.querySelector('label[for="edit-college"]');
                if (user.data.role === 'Administrative Officials') {
                    collegeLabel.textContent = 'Administration Role';
                    collegeInput.value = user.data.admin_role || 'N/A';
                } else {
                    collegeLabel.textContent = 'College';
                    collegeInput.value = user.data.college || 'N/A';
                }

                const blockCheckbox = document.getElementById('edit-blocked');
                const reasonContainer = document.getElementById('block-reason-container');
                const durationContainer = document.getElementById('block-duration-container');
                const reasonInput = document.getElementById('edit-block-reason');
                const untilInput = document.getElementById('edit-block-until');

                const isBlocked = user.data.is_blocked === '1';
                blockCheckbox.checked = isBlocked;

                if (isBlocked) {
                    reasonContainer.classList.remove('hidden');
                    durationContainer.classList.remove('hidden');
                    reasonInput.value = user.data.block_reason || '';
                    untilInput.value = user.data.block_until || '';
                } else {
                    reasonContainer.classList.add('hidden');
                    durationContainer.classList.add('hidden');
                    reasonInput.value = '';
                    untilInput.value = '';
                }
            }
        })
        .catch(err => {
            console.error("Error fetching user:", err);
            showToast('error', 'Error', 'Failed to connect to the server');
            closeEditModal();
        });
}



function closeEditModal() {
    const modal = document.getElementById('editModal');
    const modalPanel = modal.querySelector('.transform');

    modalPanel.classList.add('opacity-0', 'scale-95');
    modal.querySelector('.bg-gray-900').classList.add('opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modalPanel.classList.remove('opacity-0', 'scale-95');
        modal.querySelector('.bg-gray-900').classList.remove('opacity-0');
        document.body.style.overflow = '';
    }, 200);

    // ❌ Do NOT reset checkbox or inputs here
}

function resetEditModalForm() {
    document.getElementById('edit-user-id').value = '';
    document.getElementById('edit-first-name').value = '';
    document.getElementById('edit-last-name').value = '';
    document.getElementById('edit-email').value = '';
    document.getElementById('edit-role').value = '';
    document.getElementById('edit-status').value = '';
    document.getElementById('edit-college').value = '';

    const blockCheckbox = document.getElementById('edit-blocked');
    if (blockCheckbox) blockCheckbox.checked = false;

    document.getElementById('block-reason-container').classList.add('hidden');
    document.getElementById('block-duration-container').classList.add('hidden');
    document.getElementById('edit-block-reason').value = '';
    document.getElementById('edit-block-until').value = '';
}


function updateUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const userId = formData.get('id');
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;

    // Save form data to local storage before sending
    saveFormDataToLocalStorage(form);

    // Add blocked status to form data
    const blockCheckbox = document.getElementById('edit-blocked');
    if (blockCheckbox) {
        formData.append('is_blocked', blockCheckbox.checked ? '1' : '0');
        
        // If blocked, include reason and duration
        if (blockCheckbox.checked) {
            const blockReason = document.getElementById('edit-block-reason').value;
            const blockUntil = document.getElementById('edit-block-until').value;
            
            if (!blockReason.trim()) {
                showToast('error', 'Error', 'Please provide a reason for blocking this user');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                return;
            }
            
            formData.append('block_reason', blockReason);
            formData.append('block_until', blockUntil);
        } else {
            // If unblocking, clear these fields
            formData.append('block_reason', '');
            formData.append('block_until', '');
        }
    }
    
    // Send update request
    fetch('update_user.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Success', 'User updated successfully');
                
                // If user was blocked, show confirmation
                if (blockCheckbox && blockCheckbox.checked) {
                    showToast('info', 'User Blocked', 'The user has been blocked and will not be able to log in.');
                }
                
                closeEditModal();
                fetchUsers(); // Refresh user list
            } else {
                showToast('error', 'Error', data.message || 'Failed to update user');
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
            showToast('error', 'Error', 'Failed to connect to the server');
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        });
}

function saveFormDataToLocalStorage(form) {
    const formData = new FormData(form);
    const formObject = {};

    formData.forEach((value, key) => {
        formObject[key] = value;
    });

    // Store form data as a JSON string in local storage
    localStorage.setItem('editUserFormData', JSON.stringify(formObject));
}

function populateFormDataFromLocalStorage() {
    const savedFormData = localStorage.getItem('editUserFormData');
    
    if (savedFormData) {
        const formObject = JSON.parse(savedFormData);
        const form = document.getElementById('editUserForm');

        // Populate the form fields with saved data
        for (const key in formObject) {
            const formField = form.querySelector(`[name="${key}"]`);
            if (formField) {
                if (formField.type === 'checkbox') {
                    formField.checked = formObject[key] === '1';
                } else {
                    formField.value = formObject[key];
                }
            }
        }
    }
}

// Run this when the form is opened
populateFormDataFromLocalStorage();


// Toast notification function
function showToast(type, title, message) {
    // Check if toast container exists, create it if not
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed bottom-4 right-4 z-50';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'flex items-center p-4 mb-3 rounded-lg shadow-lg transform transition-all duration-300 ease-out translate-x-full';
    
    // Set toast style based on type
    if (type === 'success') {
        toast.classList.add('bg-green-50', 'text-green-800', 'border-l-4', 'border-green-500');
    } else if (type === 'error') {
        toast.classList.add('bg-red-50', 'text-red-800', 'border-l-4', 'border-red-500');
    } else {
        toast.classList.add('bg-blue-50', 'text-blue-800', 'border-l-4', 'border-blue-500');
    }
    
    // Set toast content
    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">
            ${type === 'success' ? 
                '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : 
                type === 'error' ? 
                '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            }
        </div>
        <div>
            <p class="font-bold">${title}</p>
            <p class="text-sm">${message}</p>
        </div>
        <button class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 focus:outline-none">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </button>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Animate toast in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    // Set up close button
    const closeButton = toast.querySelector('button');
    closeButton.addEventListener('click', () => {
        removeToast(toast);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

// Function to remove toast with animation
function removeToast(toast) {
    toast.classList.remove('translate-x-0');
    toast.classList.add('translate-x-full');
    setTimeout(() => {
        toast.remove();
    }, 300);
}
</script>

</body>
</html>