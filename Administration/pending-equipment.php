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
    <title>Equipment Transfers - Admin Dashboard</title>
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
        
        .btn-success {
            background-color: #22c55e;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #16a34a;
        }
        
        .btn-destructive {
            background-color: #ef4444;
            color: white;
        }
        
        .btn-destructive:hover {
            background-color: #dc2626;
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
        @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
        <div class="container flex h-14 items-center justify-between px-6">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="md:hidden mr-4">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-2xl font-bold">Equipment Transfers</h1>
                    </div>
                    <div class="relative group inline-block">
 <!-- Button to open the modal -->
<button id="open-transfer-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow px-4 py-2 flex items-center gap-2">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
  </svg>
  New Equipment Transfer
</button>
  <!-- Tooltip (bottom-left, shifted left) -->
<div class="absolute -left-24 top-full mt-2 w-72 text-sm text-white bg-gray-900 rounded-lg px-4 py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20 shadow-md pointer-events-none">
  Only users with assigned equipment can be transferred to another user.
  <div class="absolute top-[-6px] left-28 w-3 h-3 bg-gray-900 rotate-45"></div>
</div>

</div>

                </div>
            </header>

            <!-- Main Content -->
            <main class="p-4 sm:p-6">
            <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold">Pending Equipment Transfers</h2>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input
                                    type="search"
                                    placeholder="Search transfers..."
                                    class="pl-8 h-10 w-full md:w-[250px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <button class="btn btn-outline btn-sm">
                                <i class="fas fa-filter"></i>
                                <span class="sr-only">Filter</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                    <div class="bg-white rounded-lg border shadow-sm p-4 sm:p-5 mt-4 sm:mt-6">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Equipment ID</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Equipment Type</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Current Owner</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">New Owner</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Request Date</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Status</th>
                                        <th class="h-12 px-4 text-right font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                        <div class="flex items-center justify-end space-x-2 py-4">
                            <button class="btn btn-outline btn-sm">
                                Previous
                            </button>
                            <button class="btn btn-outline btn-sm">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
                
               <!-- Equipment History Table -->
               <div class="bg-white rounded-lg border shadow-sm p-4 sm:p-5 mt-4 sm:mt-6">
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Equipment History</h2>
        <div class="flex gap-2">
            <input type="search" id="history-search" placeholder="Search history..." class="border rounded-md px-3 py-2 text-sm">
        </div>
    </div>
    
    <div class="overflow-x-auto">
  <table class="w-full table-auto text-sm">
    <thead>
      <tr class="border-b text-left text-xs font-semibold text-gray-600 bg-gray-50">
        <th class="px-3 py-2 whitespace-nowrap">Equipment ID</th>
        <th class="px-3 py-2 whitespace-nowrap">Equipment Name</th>
        <th class="px-3 py-2 whitespace-nowrap">From User</th>
        <th class="px-3 py-2 whitespace-nowrap">To User</th>
        <th class="px-3 py-2 whitespace-nowrap">Date</th>
        <th class="px-3 py-2 whitespace-nowrap">Notes</th>
      </tr>
    </thead>
    <tbody>
      <!-- Equipment history will be populated here -->
    </tbody>
  </table>
</div>

    
    <div class="flex items-center justify-end mt-4 gap-2">
        <button id="history-prev-button" class="btn btn-outline btn-sm">
            <i class="fas fa-chevron-left mr-1"></i>
            Previous
        </button>
        <button id="history-next-button" class="btn btn-outline btn-sm">
            Next
            <i class="fas fa-chevron-right ml-1"></i>
        </button>
    </div>
</div>
            </main>
        </div>
    </div>

 <!-- New Transfer Modal -->
<div id="new-transfer-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-8 relative max-h-[90vh] flex flex-col">
    <!-- Close Button -->
    <button id="close-modal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

    <h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">Equipment Transfer</h2>
    
    <!-- Progress Steps -->
    <div class="flex justify-center mb-4">
      <div class="flex items-center">
        <button data-step="1" class="step-indicator w-8 h-8 rounded-full flex items-center justify-center font-medium bg-blue-600 text-white">1</button>
        <div class="w-10 h-1 bg-gray-200 step-connector" data-connector="1-2"></div>
        <button data-step="2" class="step-indicator w-8 h-8 rounded-full flex items-center justify-center font-medium bg-gray-200 text-gray-600">2</button>
        <div class="w-10 h-1 bg-gray-200 step-connector" data-connector="2-3"></div>
        <button data-step="3" class="step-indicator w-8 h-8 rounded-full flex items-center justify-center font-medium bg-gray-200 text-gray-600">3</button>
        <div class="w-10 h-1 bg-gray-200 step-connector" data-connector="3-4"></div>
        <button data-step="4" class="step-indicator w-8 h-8 rounded-full flex items-center justify-center font-medium bg-gray-200 text-gray-600">4</button>
      </div>
    </div>
    
    <div class="text-center text-sm text-gray-500 mb-4 step-title">Select Current Owner</div>

    <!-- Content Area (scrollable) -->
    <div class="overflow-y-auto flex-1">
      <!-- Step 1: Search Current Owner -->
      <div id="step-1" class="step-content">
        <div class="relative mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input type="text" id="search-user-1" placeholder="Search by name or email..." class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-9 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Selected User Card (Initially Hidden) -->
        <div id="selected-user-1-card" class="hidden border rounded-lg p-4 mb-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold user-avatar">
                <!-- User initials will be inserted here -->
              </div>
              <div>
                <div class="font-medium user-name"><!-- User name will be inserted here --></div>
                <div class="text-sm text-gray-500 user-email"><!-- User email will be inserted here --></div>
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 mt-1 user-department"><!-- Department --></span>
              </div>
            </div>
            <button class="text-gray-400 hover:text-gray-600" id="clear-user-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Search Results -->
        <div id="user-1-results" class="max-h-[300px] overflow-y-auto border rounded-lg hidden bg-white shadow"></div>
        
        <!-- Empty State -->
        <div id="user-1-empty-state" class="text-center p-8 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p>Search for a user by name or email</p>
          <p class="text-sm mt-1">This user currently owns the equipment you want to transfer</p>
        </div>
      </div>

      <!-- Step 2: Select Equipment -->
      <div id="step-2" class="step-content hidden">
        <!-- Current Owner Info -->
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold user-avatar">
            <!-- User initials will be inserted here -->
          </div>
          <div>
            <div class="font-medium user-name"><!-- User name will be inserted here --></div>
            <div class="text-sm text-gray-500 user-email"><!-- User email will be inserted here --></div>
          </div>
        </div>

        <!-- Equipment Filter and Select All -->
        <div class="flex justify-between items-center mb-4">
          <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="equipment-filter" placeholder="Filter equipment..." class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-9 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div class="ml-2 flex items-center">
            <input type="checkbox" id="select-all-equipment" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4" />
            <label for="select-all-equipment" class="ml-2 text-sm cursor-pointer">Select All</label>
          </div>
        </div>

        <!-- Equipment List -->
        <div id="equipment-list" class="max-h-[300px] overflow-y-auto border rounded-lg p-2 bg-gray-50 mb-4">
          <!-- Equipment items will be dynamically populated here -->
          <div id="equipment-empty-state" class="text-center p-8 text-gray-500 hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p>No equipment found for this user</p>
          </div>
        </div>

        <!-- Selected Equipment Summary -->
        <div id="selected-equipment-summary" class="bg-gray-100 p-3 rounded-md hidden">
          <div class="flex justify-between items-center">
            <span class="font-medium">Selected Equipment</span>
            <span id="selected-count" class="bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded">0 item(s)</span>
          </div>
          <div id="selected-equipment-chips" class="flex flex-wrap gap-2 mt-2 max-h-[80px] overflow-y-auto">
            <!-- Selected equipment chips will be added here -->
          </div>
        </div>
      </div>

      <!-- Step 3: Search New Owner -->
      <div id="step-3" class="step-content hidden">
        <div class="relative mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input type="text" id="search-user-2" placeholder="Search by name or email..." class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-9 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Selected User Card (Initially Hidden) -->
        <div id="selected-user-2-card" class="hidden border rounded-lg p-4 mb-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold user-avatar">
                <!-- User initials will be inserted here -->
              </div>
              <div>
                <div class="font-medium user-name"><!-- User name will be inserted here --></div>
                <div class="text-sm text-gray-500 user-email"><!-- User email will be inserted here --></div>
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 mt-1 user-department"><!-- Department --></span>
              </div>
            </div>
            <button class="text-gray-400 hover:text-gray-600" id="clear-user-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Search Results -->
        <div id="user-2-results" class="max-h-[300px] overflow-y-auto mt-2 border rounded-lg hidden bg-white shadow"></div>
        
        <!-- Empty State -->
        <div id="user-2-empty-state" class="text-center p-8 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p>Search for a user by name or email</p>
          <p class="text-sm mt-1">This user will receive the equipment you selected</p>
        </div>
      </div>

      <!-- Step 4: Confirm Transfer -->
      <div id="step-4" class="step-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <!-- From User -->
          <div class="border rounded-lg p-4">
            <h3 class="font-medium text-sm text-gray-500 mb-2">From</h3>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold user-avatar">
                <!-- User initials will be inserted here -->
              </div>
              <div>
                <div class="font-medium user-name"><!-- User name will be inserted here --></div>
                <div class="text-sm text-gray-500 user-email"><!-- User email will be inserted here --></div>
              </div>
            </div>
          </div>

          <!-- To User -->
          <div class="border rounded-lg p-4">
            <h3 class="font-medium text-sm text-gray-500 mb-2">To</h3>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold user-avatar">
                <!-- User initials will be inserted here -->
              </div>
              <div>
                <div class="font-medium user-name"><!-- User name will be inserted here --></div>
                <div class="text-sm text-gray-500 user-email"><!-- User email will be inserted here --></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Equipment Summary -->
        <div class="border rounded-lg p-4 mb-4">
          <h3 class="font-medium text-sm text-gray-500 mb-2">Equipment (<span id="confirm-equipment-count">0</span>)</h3>
          <div id="confirm-equipment-list" class="max-h-[120px] overflow-y-auto">
            <!-- Equipment summary will be populated here -->
          </div>
        </div>

        <!-- Reason -->
        <div>
          <label class="block text-gray-700 font-semibold mb-1" for="transfer-reason">Reason for Transfer</label>
          <textarea id="transfer-reason" rows="3" placeholder="Provide a reason for the transfer..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>
      </div>
    </div>

    <!-- Footer with Navigation Buttons -->
    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
      <button id="prev-step" class="hidden px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
      </button>
      <button id="cancel-transfer" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        Cancel
      </button>
      
      <button id="next-step" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition duration-150 flex items-center">
        Next
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
      
      <button id="submit-transfer" class="hidden px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition duration-150">
        Submit Transfer
      </button>
    </div>
  </div>
</div>


<div id="notes-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white w-11/12 md:w-1/2 rounded-xl p-6 shadow-lg relative">
    <button onclick="document.getElementById('notes-modal').classList.add('hidden')" 
            class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Notes</h2>
    <p id="notes-modal-content" class="text-gray-700 whitespace-pre-line"></p>
  </div>
</div>


<!-- Loading Spinner Overlay -->
<div id="loading-spinner" style="
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 9999;
    justify-content: center;
    align-items: center;
">
    <div class="spinner" style="
        width: 50px;
        height: 50px;
        border: 6px solid #ccc;
        border-top: 6px solidrgb(190, 31, 19);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    "></div>
</div>



<script>
        // Mobile sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        document.addEventListener("DOMContentLoaded", function () {
  // Get the current page's file name from the URL
  const currentPage = window.location.pathname.split("/").pop();

  // Select all sidebar navigation links
  const navLinks = document.querySelectorAll("#sidebar nav a");

  navLinks.forEach(link => {
    // Get the link's destination file name
    const linkPage = link.getAttribute("href");

    // Check if the current page matches the link's href
    if (linkPage === currentPage) {
      // Add active styling (for example, underline and background color)
      link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
      
      // Optionally, you can remove the hover styles if needed
      // link.classList.remove("hover:bg-gray-800", "hover:text-white");
    }
  });
});
    </script>

<script>
/**
 * Equipment Transfer Management System
 * Main JavaScript file for handling equipment transfers, user management, and dashboard functionality
 */

 document.addEventListener("DOMContentLoaded", function() {
    // Initialize components
    initSidebar();
    initTransferModal();
    
    // Initialize dashboard if elements exist
    const pendingTransfersTable = document.querySelector('main table:first-of-type tbody');
    const recentAssignmentsTable = document.querySelector('main table:last-of-type tbody');
    
    if (pendingTransfersTable && recentAssignmentsTable) {
        initDashboard();
    }
    
    // Initialize equipment history if elements exist
    const equipmentHistoryTable = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.mt-6 table tbody');
    if (equipmentHistoryTable) {
        initEquipmentHistory();
    }
});

/**
 * ===================================
 * SIDEBAR FUNCTIONALITY
 * ===================================
 */
function initSidebar() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
        });
    }

    // Highlight current sidebar link
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
}

/**
 * ===================================
 * TRANSFER MODAL FUNCTIONALITY
 * ===================================
 */
function initTransferModal() {
    // State management
    let selectedUser1 = null;
    let selectedUser2 = null;
    let userEquipment = [];
    let selectedEquipment = [];
    let allUsers = []; // To store all users globally
    let currentStep = 1;

    /* Variables for Modal */
    const modal = document.getElementById('new-transfer-modal');
    const openModalBtn = document.querySelector('.btn-primary') || document.getElementById('open-transfer-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelBtn = document.getElementById('cancel-transfer');
    const nextBtn = document.getElementById('next-step');
    const prevBtn = document.getElementById('prev-step');
    const submitBtn = document.getElementById('submit-transfer');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const stepContents = document.querySelectorAll('.step-content');
    const stepTitle = document.querySelector('.step-title');
    const stepConnectors = document.querySelectorAll('.step-connector');
    
    // Step titles
    const stepTitles = [
        "Select Current Owner",
        "Select Equipment",
        "Select New Owner",
        "Confirm Transfer"
    ];

    /* Open Modal */
    if (openModalBtn && modal) {
        openModalBtn.addEventListener('click', async () => {
            modal.classList.remove('hidden');
            resetForm();
            await loadAllUsers(); // Load all users when modal opens
        });
    }

    /* Close Modal */
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    function closeModal() {
        if (modal) {
            modal.classList.add('hidden');
            resetForm();
            
            // Refresh dashboard data when modal is closed
            if (typeof loadPendingTransfers === 'function') {
                loadPendingTransfers();
            }
            if (typeof loadEquipmentHistory === 'function') {
                loadEquipmentHistory();
            }
        }
    }

    function resetForm() {
        // Reset state
        currentStep = 1;
        selectedUser1 = null;
        selectedUser2 = null;
        userEquipment = [];
        selectedEquipment = [];
        
        // Reset UI - safely check if elements exist
        const searchUser1 = document.getElementById('search-user-1');
        const searchUser2 = document.getElementById('search-user-2');
        const equipmentFilter = document.getElementById('equipment-filter');
        const transferReason = document.getElementById('transfer-reason');
        
        if (searchUser1) searchUser1.value = '';
        if (searchUser2) searchUser2.value = '';
        if (equipmentFilter) equipmentFilter.value = '';
        if (transferReason) transferReason.value = '';
        
        // Reset result containers
        resetElement('user-1-results');
        resetElement('user-2-results');
        resetElement('equipment-list');
        
        // Hide cards and show empty states
        hideElement('selected-user-1-card');
        hideElement('selected-user-2-card');
        hideElement('user-1-results');
        hideElement('user-2-results');
        hideElement('selected-equipment-summary');
        
        // Show empty states
        showElement('user-1-empty-state');
        showElement('user-2-empty-state');
        
        updateStepUI();
    }
    
    // Helper functions for element manipulation
    function resetElement(id) {
        const element = document.getElementById(id);
        if (element) element.innerHTML = '';
    }
    
    function hideElement(id) {
        const element = document.getElementById(id);
        if (element) element.classList.add('hidden');
    }
    
    function showElement(id) {
        const element = document.getElementById(id);
        if (element) element.classList.remove('hidden');
    }

    /* Fetch All Users */
    async function loadAllUsers() {
        try {
            const res = await fetch('fetch_all_users.php');
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            
            const data = await res.json();
            
            // Handle different response formats
            if (Array.isArray(data)) {
                allUsers = data;
            } else if (data && Array.isArray(data.users)) {
                allUsers = data.users;
            } else {
                console.error('Unexpected response format:', data);
                allUsers = [];
            }
            
            // Display all users initially when modal opens
            if (currentStep === 1) {
                displayAllUsers(true);
            }
        } catch (error) {
            console.error('Failed to load users:', error);
            allUsers = []; // Reset to empty array on error
            
            // Show error message in the UI
            const user1Results = document.getElementById('user-1-results');
            if (user1Results) {
                user1Results.innerHTML = `<div class="text-center p-4 text-red-500">Failed to load users. Please try again.</div>`;
                user1Results.classList.remove('hidden');
            }
        }
    }
    
    /* Display all users in the appropriate user list */
    function displayAllUsers(isFromUser = true) {
        const resultBoxId = isFromUser ? 'user-1-results' : 'user-2-results';
        const emptyStateId = isFromUser ? 'user-1-empty-state' : 'user-2-empty-state';
        
        // Hide empty state when showing all users
        hideElement(emptyStateId);
        
        // Filter out the selected "from user" for the "to user" list
        let usersToDisplay = isFromUser ? allUsers : allUsers.filter(user => user.id !== (selectedUser1?.id));
        
        // Limit to a reasonable number to prevent performance issues
        usersToDisplay = usersToDisplay.slice(0, 50);
        
        renderUserResults(usersToDisplay, resultBoxId, isFromUser);
    }

    /* Setup Search Filtering */
    const searchUser1 = document.getElementById('search-user-1');
    if (searchUser1) {
        searchUser1.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            if (searchTerm.length === 0) {
                // Show all users if search is cleared
                displayAllUsers(true);
                return;
            }
            
            const filtered = allUsers.filter(user => {
                if (!user.first_name || !user.last_name || !user.email) return false;
                return (`${user.first_name} ${user.last_name} ${user.email}`).toLowerCase().includes(searchTerm);
            });
            
            renderUserResults(filtered, 'user-1-results', true);
        });
    }

    const searchUser2 = document.getElementById('search-user-2');
    if (searchUser2) {
        searchUser2.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            if (searchTerm.length === 0) {
                // Show all users if search is cleared
                displayAllUsers(false);
                return;
            }
            
            // Filter out the selected "from user"
            const filtered = allUsers.filter(user => {
                if (!user.first_name || !user.last_name || !user.email) return false;
                return user.id !== (selectedUser1?.id) && 
                    (`${user.first_name} ${user.last_name} ${user.email}`).toLowerCase().includes(searchTerm);
            });
            
            renderUserResults(filtered, 'user-2-results', false);
        });
    }

    /* Equipment filter */
    const equipmentFilter = document.getElementById('equipment-filter');
    if (equipmentFilter) {
        equipmentFilter.addEventListener('input', () => {
            renderEquipmentList();
        });
    }

    /* Select all equipment */
    const selectAllCheckbox = document.getElementById('select-all-equipment');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const filteredEquipment = getFilteredEquipment();
            
            if (this.checked) {
                // Add all filtered equipment that's not already selected
                filteredEquipment.forEach(equipment => {
                    if (!selectedEquipment.some(e => e.equipment_id === equipment.equipment_id)) {
                        selectedEquipment.push(equipment);
                    }
                });
            } else {
                // Remove all filtered equipment
                selectedEquipment = selectedEquipment.filter(
                    selected => !filteredEquipment.some(filtered => filtered.equipment_id === selected.equipment_id)
                );
            }
            
            renderEquipmentList();
            updateSelectedEquipmentUI();
            updateConfirmationStep();
            updateStepUI();
        });
    }

    /* Render User Results */
    function renderUserResults(users, resultBoxId, isFromUser = true) {
        const resultBox = document.getElementById(resultBoxId);
        if (!resultBox) return;
        
        resultBox.innerHTML = '';

        if (users.length === 0) {
            resultBox.innerHTML = `<div class="text-center p-4 text-gray-500">No users found</div>`;
            resultBox.classList.remove('hidden');
            return;
        }

        // Limit to a reasonable number to prevent performance issues
        const displayUsers = users.slice(0, 50);
        
        displayUsers.forEach(user => {
            if (!user.first_name || !user.last_name) return; // Skip invalid users
            
            const userEl = document.createElement('button');
            userEl.className = 'w-full text-left p-3 hover:bg-gray-100 flex items-center gap-3 transition-colors';
            
            const initials = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`.toUpperCase();
            
            userEl.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
                    ${initials}
                </div>
                <div>
                    <div class="font-medium">${user.first_name} ${user.last_name}</div>
                    <div class="text-sm text-gray-500">${user.email || ''}</div>
                    ${user.department ? `<span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 mt-1">${user.department}</span>` : ''}
                </div>
            `;
            
            userEl.addEventListener('click', () => {
                if (isFromUser) {
                    selectFromUser(user);
                } else {
                    selectToUser(user);
                }
            });
            
            resultBox.appendChild(userEl);
        });
        
        // Show "more results" message if we're limiting the display
        if (users.length > 50) {
            const moreResults = document.createElement('div');
            moreResults.className = 'text-center p-2 text-sm text-gray-500 border-t';
            moreResults.textContent = `Showing 50 of ${users.length} users. Please refine your search to see more.`;
            resultBox.appendChild(moreResults);
        }
        
        resultBox.classList.remove('hidden');
    }

    function selectFromUser(user) {
        selectedUser1 = user;
        
        // Update UI
        const userCard = document.getElementById('selected-user-1-card');
        if (!userCard) return;
        
        const avatars = userCard.querySelectorAll('.user-avatar');
        const names = userCard.querySelectorAll('.user-name');
        const emails = userCard.querySelectorAll('.user-email');
        const departments = userCard.querySelectorAll('.user-department');
        
        const initials = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`.toUpperCase();
        const fullName = `${user.first_name} ${user.last_name}`;
        
        avatars.forEach(avatar => {
            avatar.textContent = initials;
        });
        
        names.forEach(name => {
            name.textContent = fullName;
        });
        
        emails.forEach(email => {
            email.textContent = user.email || '';
        });
        
        if (departments.length && user.department) {
            departments.forEach(dept => {
                dept.textContent = user.department;
                dept.classList.remove('hidden');
            });
        } else if (departments.length) {
            departments.forEach(dept => {
                dept.classList.add('hidden');
            });
        }
        
        userCard.classList.remove('hidden');
        hideElement('user-1-results');
        hideElement('user-1-empty-state');
        
        if (searchUser1) searchUser1.value = '';
        
        // Load user equipment
        loadUserEquipment(user.id);
        
        // Update step 2 user info
        const step2Avatars = document.querySelectorAll('#step-2 .user-avatar');
        const step2Names = document.querySelectorAll('#step-2 .user-name');
        const step2Emails = document.querySelectorAll('#step-2 .user-email');
        
        step2Avatars.forEach(avatar => {
            avatar.textContent = initials;
        });
        
        step2Names.forEach(name => {
            name.textContent = fullName;
        });
        
        step2Emails.forEach(email => {
            email.textContent = user.email || '';
        });
        
        // Update confirmation step
        updateConfirmationStep();
        
        // Enable next button
        updateStepUI();
    }

    function selectToUser(user) {
        selectedUser2 = user;
        
        // Update UI
        const userCard = document.getElementById('selected-user-2-card');
        if (!userCard) return;
        
        const avatars = userCard.querySelectorAll('.user-avatar');
        const names = userCard.querySelectorAll('.user-name');
        const emails = userCard.querySelectorAll('.user-email');
        const departments = userCard.querySelectorAll('.user-department');
        
        const initials = `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`.toUpperCase();
        const fullName = `${user.first_name} ${user.last_name}`;
        
        avatars.forEach(avatar => {
            avatar.textContent = initials;
        });
        
        names.forEach(name => {
            name.textContent = fullName;
        });
        
        emails.forEach(email => {
            email.textContent = user.email || '';
        });
        
        if (departments.length && user.department) {
            departments.forEach(dept => {
                dept.textContent = user.department;
                dept.classList.remove('hidden');
            });
        } else if (departments.length) {
            departments.forEach(dept => {
                dept.classList.add('hidden');
            });
        }
        
        userCard.classList.remove('hidden');
        hideElement('user-2-results');
        hideElement('user-2-empty-state');
        
        if (searchUser2) searchUser2.value = '';
        
        // Update confirmation step
        updateConfirmationStep();
        
        // Enable next button
        updateStepUI();
    }

    /* Load User Equipment */
    async function loadUserEquipment(userId) {
        try {
            const res = await fetch(`get_user_equipment.php?user_id=${userId}`);
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            
            const data = await res.json();
            userEquipment = data.equipment || [];
            selectedEquipment = []; // Reset selected equipment when changing user
            
            renderEquipmentList();
            updateSelectedEquipmentUI();
            updateConfirmationStep();
            updateStepUI();
        } catch (error) {
            console.error('Failed to load equipment:', error);
            userEquipment = [];
            selectedEquipment = [];
            
            // Show error in equipment list
            const equipmentList = document.getElementById('equipment-list');
            if (equipmentList) {
                equipmentList.innerHTML = `<div class="text-center p-4 text-red-500">Failed to load equipment. Please try again.</div>`;
            }
            
            updateSelectedEquipmentUI();
            updateStepUI();
        }
    }

    function getFilteredEquipment() {
        const filterValue = document.getElementById('equipment-filter')?.value || '';
        
        if (!filterValue) {
            return userEquipment;
        }
        
        const searchTerm = filterValue.toLowerCase();
        return userEquipment.filter(equip => {
            if (!equip) return false;
            
            const nameMatch = equip.equipment_name && equip.equipment_name.toLowerCase().includes(searchTerm);
            const poMatch = equip.po_jo_no && equip.po_jo_no.toLowerCase().includes(searchTerm);
            const propertyMatch = equip.property_number && equip.property_number.toLowerCase().includes(searchTerm);
            
            return nameMatch || poMatch || propertyMatch;
        });
    }

    function renderEquipmentList() {
        const equipmentList = document.getElementById('equipment-list');
        if (!equipmentList) return;
        
        equipmentList.innerHTML = '';
        
        if (!userEquipment || userEquipment.length === 0) {
            showElement('equipment-empty-state');
            return;
        }
        
        hideElement('equipment-empty-state');
        
        const filteredEquipment = getFilteredEquipment();
        
        if (filteredEquipment.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'text-center p-4 text-gray-500';
            noResults.textContent = `No equipment found matching "${document.getElementById('equipment-filter')?.value || ''}"`;
            equipmentList.appendChild(noResults);
            return;
        }
        
        filteredEquipment.forEach(equipment => {
            if (!equipment || !equipment.equipment_id) return; // Skip invalid equipment
            
            const equipEl = document.createElement('div');
            equipEl.className = 'flex items-center p-3 hover:bg-gray-100 rounded-md transition-colors';
            
            const isSelected = selectedEquipment.some(e => e.equipment_id === equipment.equipment_id);
            
            equipEl.innerHTML = `
                <input type="checkbox" id="equip-${equipment.equipment_id}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4" ${isSelected ? 'checked' : ''} />
                <label for="equip-${equipment.equipment_id}" class="ml-3 flex-1 cursor-pointer">
                    <div class="font-medium">${equipment.equipment_name || 'Unnamed Equipment'}</div>
                    <div class="text-sm text-gray-500">PO/JO: ${equipment.po_jo_no || 'N/A'} | Property #: ${equipment.property_number || 'N/A'}</div>
                </label>
            `;
            
            const checkbox = equipEl.querySelector(`input[id="equip-${equipment.equipment_id}"]`);
            checkbox.addEventListener('change', () => {
                toggleEquipment(equipment);
            });
            
            equipmentList.appendChild(equipEl);
        });
        
        // Update select all checkbox
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = filteredEquipment.length > 0 && 
                filteredEquipment.every(e => selectedEquipment.some(se => se.equipment_id === e.equipment_id));
        }
    }

    function toggleEquipment(equipment) {
        const index = selectedEquipment.findIndex(e => e.equipment_id === equipment.equipment_id);
        
        if (index === -1) {
            selectedEquipment.push(equipment);
        } else {
            selectedEquipment.splice(index, 1);
        }
        
        updateSelectedEquipmentUI();
        updateConfirmationStep();
        updateStepUI();
    }

    function updateSelectedEquipmentUI() {
        const summary = document.getElementById('selected-equipment-summary');
        const chips = document.getElementById('selected-equipment-chips');
        const count = document.getElementById('selected-count');
        
        if (!summary || !chips || !count) return;
        
        if (selectedEquipment.length === 0) {
            summary.classList.add('hidden');
            return;
        }
        
        summary.classList.remove('hidden');
        count.textContent = `${selectedEquipment.length} item(s)`;
        
        chips.innerHTML = '';
        
        selectedEquipment.forEach(equipment => {
            const chip = document.createElement('div');
            chip.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800';
            chip.innerHTML = `
                ${equipment.equipment_name || 'Unnamed Equipment'}
                <button class="ml-1 text-gray-500 hover:text-gray-700" data-id="${equipment.equipment_id}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            
            const removeBtn = chip.querySelector(`button[data-id="${equipment.equipment_id}"]`);
            removeBtn.addEventListener('click', () => {
                toggleEquipment(equipment);
                renderEquipmentList();
            });
            
            chips.appendChild(chip);
        });
    }

    function updateConfirmationStep() {
        // Update equipment count
        const confirmCount = document.getElementById('confirm-equipment-count');
        if (confirmCount) {
            confirmCount.textContent = selectedEquipment.length;
        }
        
        // Update equipment list
        const confirmList = document.getElementById('confirm-equipment-list');
        if (confirmList) {
            confirmList.innerHTML = '';
            
            selectedEquipment.forEach(equipment => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center py-2';
                item.innerHTML = `
                    <div>
                        <div class="font-medium">${equipment.equipment_name || 'Unnamed Equipment'}</div>
                        <div class="text-sm text-gray-500">PO/JO: ${equipment.po_jo_no || 'N/A'} | Property #: ${equipment.property_number || 'N/A'}</div>
                    </div>
                `;
                
                confirmList.appendChild(item);
            });
        }
        
        // Update user info in confirmation step
        if (selectedUser1) {
            const initials = `${selectedUser1.first_name.charAt(0)}${selectedUser1.last_name.charAt(0)}`.toUpperCase();
            const fullName = `${selectedUser1.first_name} ${selectedUser1.last_name}`;
            
            const fromAvatars = document.querySelectorAll('#step-4 .border:first-child .user-avatar');
            const fromNames = document.querySelectorAll('#step-4 .border:first-child .user-name');
            const fromEmails = document.querySelectorAll('#step-4 .border:first-child .user-email');
            
            fromAvatars.forEach(avatar => {
                avatar.textContent = initials;
            });
            
            fromNames.forEach(name => {
                name.textContent = fullName;
            });
            
            fromEmails.forEach(email => {
                email.textContent = selectedUser1.email || '';
            });
        }
        
        if (selectedUser2) {
            const initials = `${selectedUser2.first_name.charAt(0)}${selectedUser2.last_name.charAt(0)}`.toUpperCase();
            const fullName = `${selectedUser2.first_name} ${selectedUser2.last_name}`;
            
            const toAvatars = document.querySelectorAll('#step-4 .border:nth-child(2) .user-avatar');
            const toNames = document.querySelectorAll('#step-4 .border:nth-child(2) .user-name');
            const toEmails = document.querySelectorAll('#step-4 .border:nth-child(2) .user-email');
            
            toAvatars.forEach(avatar => {
                avatar.textContent = initials;
            });
            
            toNames.forEach(name => {
                name.textContent = fullName;
            });
            
            toEmails.forEach(email => {
                email.textContent = selectedUser2.email || '';
            });
        }
    }

    /* Clear user selections */
    const clearUser1 = document.getElementById('clear-user-1');
    if (clearUser1) {
        clearUser1.addEventListener('click', () => {
            selectedUser1 = null;
            hideElement('selected-user-1-card');
            showElement('user-1-empty-state');
            hideElement('user-1-results');
            
            displayAllUsers(true);
            userEquipment = [];
            selectedEquipment = [];
            renderEquipmentList();
            updateSelectedEquipmentUI();
            updateStepUI();
        });
    }

    const clearUser2 = document.getElementById('clear-user-2');
    if (clearUser2) {
        clearUser2.addEventListener('click', () => {
            selectedUser2 = null;
            hideElement('selected-user-2-card');
            showElement('user-2-empty-state');
            hideElement('user-2-results');
            
            displayAllUsers(false);
            updateStepUI();
        });
    }

    /* Step Navigation */
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentStep < 4) {
                currentStep++;
                updateStepUI();
                
                // Show all users when entering the "Select New Owner" step
                if (currentStep === 3) {
                    displayAllUsers(false);
                }
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateStepUI();
                
                // Show all users when going back to the "Select Current Owner" step
                if (currentStep === 1) {
                    displayAllUsers(true);
                }
            }
        });
    }
    
    // Step indicators click
    if (stepIndicators) {
        stepIndicators.forEach((indicator) => {
            indicator.addEventListener('click', () => {
                if (!indicator.dataset.step) return;
                
                const step = Number.parseInt(indicator.dataset.step);
                
                // Only allow going back or to completed steps
                if (step < currentStep) {
                    currentStep = step;
                    updateStepUI();
                    
                    // Show appropriate user list when navigating directly to a step
                    if (step === 1) {
                        displayAllUsers(true);
                    } else if (step === 3) {
                        displayAllUsers(false);
                    }
                } else if (step === 2 && selectedUser1) {
                    currentStep = 2;
                    updateStepUI();
                } else if (step === 3 && selectedEquipment.length > 0) {
                    currentStep = 3;
                    updateStepUI();
                    displayAllUsers(false);
                } else if (step === 4 && selectedUser2) {
                    currentStep = 4;
                    updateStepUI();
                }
            });
        });
    }
    
    // Transfer reason input
    const transferReason = document.getElementById('transfer-reason');
    if (transferReason) {
        transferReason.addEventListener('input', () => {
            updateStepUI();
        });
    }

    function updateStepUI() {
        if (!stepIndicators || !stepContents || !stepTitle || !prevBtn || !nextBtn || !cancelBtn || !submitBtn) {
            console.error('Missing required DOM elements for step UI');
            return;
        }
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            const step = index + 1;
            if (step === currentStep) {
                indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-blue-100', 'text-blue-600');
                indicator.classList.add('bg-blue-600', 'text-white');
            } else if (step < currentStep) {
                indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-blue-600', 'text-white');
                indicator.classList.add('bg-blue-100', 'text-blue-600');
            } else {
                indicator.classList.remove('bg-blue-600', 'text-white', 'bg-blue-100', 'text-blue-600');
                indicator.classList.add('bg-gray-200', 'text-gray-600');
            }
        });
        
        // Update connectors
        if (stepConnectors) {
            stepConnectors.forEach((connector) => {
                if (!connector.dataset.connector) return;
                
                const [from, to] = connector.dataset.connector.split('-').map(Number);
                if (currentStep > to) {
                    connector.classList.remove('bg-gray-200');
                    connector.classList.add('bg-blue-600');
                } else if (currentStep > from) {
                    connector.classList.remove('bg-gray-200');
                    connector.classList.add('bg-blue-300');
                } else {
                    connector.classList.remove('bg-blue-600', 'bg-blue-300');
                    connector.classList.add('bg-gray-200');
                }
            });
        }
        
        // Update step content visibility
        stepContents.forEach((content, index) => {
            if (index + 1 === currentStep) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });
        
        // Update step title
        stepTitle.textContent = stepTitles[currentStep - 1] || 'Transfer Equipment';
        
        // Update navigation buttons
        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
            cancelBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            cancelBtn.classList.add('hidden');
        }
        
        if (currentStep === 4) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
        
        // Disable next button based on step completion
        if (currentStep === 1 && !selectedUser1) {
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (currentStep === 2 && selectedEquipment.length === 0) {
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (currentStep === 3 && !selectedUser2) {
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextBtn.disabled = false;
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        // Disable submit button if reason is empty
        if (currentStep === 4) {
            const reason = document.getElementById('transfer-reason')?.value.trim() || '';
            if (!reason) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    /* Submit Transfer */
    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            const reason = document.getElementById('transfer-reason')?.value.trim() || '';

            // Check for minimum length of reason
            if (reason.length < 3) {
                showNotification('Reason must be at least 3 characters long.', 'warning');
                return;
            }

            if (!selectedUser1 || selectedEquipment.length === 0 || !selectedUser2 || !reason) {
                showNotification('Please complete all fields correctly.', 'error');
                return;
            }

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';

                const transferPromises = selectedEquipment.map(equipment => {
                    return fetch('create_transfer.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            current_owner_id: selectedUser1.id,
                            new_owner_id: selectedUser2.id,
                            equipment_id: equipment.equipment_id,
                            reason
                        })
                    });
                });

                const results = await Promise.all(transferPromises);
                const allSuccessful = results.every(res => res.ok);

                if (allSuccessful) {
                    showNotification(`Successfully transferred ${selectedEquipment.length} item(s) to ${selectedUser2.first_name} ${selectedUser2.last_name}`, 'success');
                    closeModal();
                    if (typeof loadPendingTransfers === 'function') loadPendingTransfers();
                    if (typeof loadEquipmentHistory === 'function') loadEquipmentHistory();
                } else {
                    showNotification('Some transfers failed. Please check the system and try again.', 'error');
                }
            } catch (error) {
                console.error('Create Transfer Error:', error);
                showNotification('An error occurred while processing your request.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Transfer';
            }
        });
    }

    // Initialize UI for transfer modal
    updateStepUI();
}

/**
 * ===================================
 * DASHBOARD FUNCTIONALITY
 * ===================================
 */
function initDashboard() {
    // State variables for dashboard
    let pendingTransfers = [];
    let recentAssignments = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let searchTerm = '';
    let filterOptions = {
        status: 'pending', // Default to pending transfers
        dateRange: 'all',
        equipmentType: 'all'
    };

    // DOM Elements for dashboard
    const searchInput = document.querySelector('main input[type="search"]');
    const filterButton = document.querySelector('main .btn-outline.btn-sm');
    const pendingTransfersTable = document.querySelector('main table:first-of-type tbody');
    const recentAssignmentsTable = document.querySelector('main table:last-of-type tbody');
    const prevButton = document.querySelector('main .flex.items-center.justify-end .btn-outline.btn-sm:first-of-type');
    const nextButton = document.querySelector('main .flex.items-center.justify-end .btn-outline.btn-sm:last-of-type');

    // Load initial data
    loadPendingTransfers();
    
    // Set up event listeners
    setupDashboardEventListeners();

    function setupDashboardEventListeners() {
        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                searchTerm = this.value.toLowerCase();
                currentPage = 1; // Reset to first page on search
                loadPendingTransfers();
            }, 300));
        }

        // Filter button
        if (filterButton) {
            filterButton.addEventListener('click', () => {
                showNotification('Filter functionality would open here', 'info');
            });
        }

        // Pagination
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    loadPendingTransfers();
                }
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadPendingTransfers();
                }
            });
        }

        // Delegate event listeners for approve/reject buttons
        document.addEventListener('click', function(e) {
            // Approve button
            if (e.target.closest('.btn-success')) {
                const row = e.target.closest('tr');
                if (row && row.dataset.transferId) {
                    const transferId = row.dataset.transferId;
                    approveTransfer(transferId);
                }
            }
            
            // Reject button
            if (e.target.closest('.btn-destructive')) {
                const row = e.target.closest('tr');
                if (row && row.dataset.transferId) {
                    const transferId = row.dataset.transferId;
                    rejectTransfer(transferId);
                }
            }
        });
    }

    // Load pending transfers from the server
    async function loadPendingTransfers() {
        try {
            updateButtonStates(true); // Disable buttons during load
            
            const params = new URLSearchParams({
                page: currentPage,
                limit: itemsPerPage,
                search: searchTerm,
                status: filterOptions.status,
                dateRange: filterOptions.dateRange,
                equipmentType: filterOptions.equipmentType
            });
            
            const response = await fetch(`get_pending_transfers.php?${params}`);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Process the transfers to ensure all required fields exist
                pendingTransfers = data.transfers.map(transfer => {
                    // Add default values for any missing properties
                    return {
                        id: transfer.id,
                        equipment_id: transfer.equipment_id || 'N/A',
                        equipment_type: transfer.equipment_type || 'Unknown',
                        from_user: transfer.from_user || transfer.current_owner || 'N/A',
                        to_user: transfer.to_user || transfer.new_owner || 'N/A',
                        transfer_date: transfer.transfer_date || new Date().toISOString(),
                        status: transfer.status || 'Pending',
                        reason: transfer.reason || '',
                        approved_at: transfer.approved_at,
                        rejected_at: transfer.rejected_at,
                        rejection_reason: transfer.rejection_reason
                    };
                });
                
                totalPages = data.pagination?.totalPages || 1;
                renderPendingTransfers();
                updatePagination();
            } else {
                showNotification('Failed to load pending transfers', 'error');
                console.error('Server returned error:', data);
            }
        } catch (error) {
            console.error('Error loading pending transfers:', error);
            showNotification('An error occurred while loading transfers', 'error');
        } finally {
            updateButtonStates(false); // Re-enable buttons
        }
    }
    
    // Render pending transfers to the table
    function renderPendingTransfers() {
        if (!pendingTransfersTable) return;
        
        pendingTransfersTable.innerHTML = '';
        
        if (pendingTransfers.length === 0) {
            pendingTransfersTable.innerHTML = `
                <tr class="border-b">
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        No pending transfers found
                    </td>
                </tr>
            `;
            return;
        }
        
        pendingTransfers.forEach(transfer => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.dataset.transferId = transfer.id;
            
            // Format the transfer date
            const formattedDate = formatDate(transfer.transfer_date);
            
            // Equipment ID and Type
            const equipmentId = transfer.equipment_id || 'N/A';
            const equipmentType = transfer.equipment_type || 'N/A';

            const currentOwner = transfer.from_user || 'N/A';
            const newOwner = transfer.to_user || 'N/A';
            
            // Create status badge
            const statusBadge = `<span class="badge badge-${
                transfer.status === 'Pending' ? 'warning' : 
                transfer.status === 'Approved' ? 'success' : 
                transfer.status === 'Rejected' ? 'error' : 'secondary'
            }">${transfer.status}</span>`;
            
            row.innerHTML = `
                <td class="p-4 font-medium">${equipmentId}</td>
                <td class="p-4">${equipmentType}</td>
                <td class="p-4">${currentOwner}</td>
                <td class="p-4">${newOwner}</td>
                <td class="p-4">${formattedDate}</td>
                <td class="p-4">
                    ${statusBadge}
                </td>
                <td class="p-4 text-right">
                    <div class="flex justify-end gap-2">
                        ${transfer.status === 'Pending' ? `
                            <button class="btn btn-success btn-sm approve-transfer" data-id="${transfer.id}">
                                <i class="fas fa-check-circle mr-1"></i> Approve
                            </button>
                            <button class="btn btn-destructive btn-sm reject-transfer" data-id="${transfer.id}">
                                <i class="fas fa-times-circle mr-1"></i> Reject
                            </button>
                        ` : `
                            <button class="btn btn-outline btn-sm view-details" data-id="${transfer.id}">
                                <i class="fas fa-eye mr-1"></i> Details
                            </button>
                        `}
                    </div>
                </td>
            `;
            
            pendingTransfersTable.appendChild(row);
        });
    }

    // Approve a transfer
    async function approveTransfer(transferId) {
        if (!confirm('Are you sure you want to approve this transfer?')) {
            return;
        }

        showLoading(true); // Show spinner

        try {
            const response = await fetch('approve_transfer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ transfer_id: transferId })
            });

            const data = await response.json();
            showLoading(false); // Hide spinner

            if (data.success) {
                showNotification('Transfer approved successfully', 'success');
                loadPendingTransfers(); // Refresh the list
                if (typeof loadEquipmentHistory === 'function') {
                    loadEquipmentHistory(); // Update history if function exists
                }
            } else {
                showNotification(data.error || 'Failed to approve transfer', 'error');
            }
        } catch (error) {
            console.error('Error approving transfer:', error);
            showLoading(false); // Hide spinner
            showNotification('An error occurred while approving the transfer', 'error');
        }
    }

    // Reject a transfer
    async function rejectTransfer(transferId) {
        const reason = prompt('Please provide a reason for rejecting this transfer:');
        if (reason === null) {
            return; // User cancelled
        }

        if (reason.trim().length < 3) {
            showNotification('Please provide a valid reason (at least 3 characters)', 'error');
            return;
        }

        showLoading(true); // Show spinner

        try {
            const response = await fetch('reject_transfer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    transfer_id: transferId,
                    reason: reason
                })
            });

            const data = await response.json();
            showLoading(false); // Hide spinner

            if (data.success) {
                showNotification('Transfer rejected successfully', 'success');
                loadPendingTransfers(); // Refresh list
                if (typeof loadEquipmentHistory === 'function') {
                    loadEquipmentHistory();
                }
            } else {
                showNotification(data.error || 'Failed to reject transfer', 'error');
            }
        } catch (error) {
            console.error('Error rejecting transfer:', error);
            showLoading(false); // Hide spinner
            showNotification('An error occurred while rejecting the transfer', 'error');
        }
    }

    // Update pagination buttons state
    function updatePagination() {
        if (!prevButton || !nextButton) return;
        
        prevButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= totalPages;
        
        prevButton.classList.toggle('opacity-50', currentPage <= 1);
        nextButton.classList.toggle('opacity-50', currentPage >= totalPages);
    }

    // Update button states during loading
    function updateButtonStates(isLoading) {
        const buttons = document.querySelectorAll('main .btn');
        buttons.forEach(button => {
            button.disabled = isLoading;
            if (isLoading) {
                button.classList.add('opacity-50');
            } else {
                button.classList.remove('opacity-50');
            }
        });
    }

    // Make loadPendingTransfers available globally
    window.loadPendingTransfers = loadPendingTransfers;
}

/**
 * ===================================
 * EQUIPMENT HISTORY FUNCTIONALITY
 * ===================================
 */
function initEquipmentHistory() {
    // State variables for equipment history
    let equipmentHistory = [];
    let historyCurrentPage = 1;
    let historyItemsPerPage = 10;
    let historyTotalPages = 1;
    let historySearchTerm = '';
    let historyFilterOptions = {
        actionType: 'all', // 'transfer', 'approve', 'reject'
        dateRange: 'all',
        equipmentType: 'all'
    };

    // DOM Elements
    const historySearchInput = document.getElementById('history-search');
    const equipmentHistoryTable = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.mt-6 table tbody');
    const historyPrevButton = document.getElementById('history-prev-button');
    const historyNextButton = document.getElementById('history-next-button');
    const historyActionTypeFilter = document.getElementById('history-action-type');
    const historyDateRangeFilter = document.getElementById('history-date-range');

    // Load initial data
    loadEquipmentHistory();
    
    // Set up event listeners
    setupHistoryEventListeners();

    function setupHistoryEventListeners() {
        // Search functionality
        if (historySearchInput) {
            historySearchInput.addEventListener('input', debounce(function() {
                historySearchTerm = this.value.toLowerCase();
                historyCurrentPage = 1; // Reset to first page on search
                loadEquipmentHistory();
            }, 300));
        }

        // Filter functionality
        if (historyActionTypeFilter) {
            historyActionTypeFilter.addEventListener('change', function() {
                historyFilterOptions.actionType = this.value;
                historyCurrentPage = 1; // Reset to first page on filter change
                loadEquipmentHistory();
            });
        }

        if (historyDateRangeFilter) {
            historyDateRangeFilter.addEventListener('change', function() {
                historyFilterOptions.dateRange = this.value;
                historyCurrentPage = 1; // Reset to first page on filter change
                loadEquipmentHistory();
            });
        }

        // Pagination
        if (historyPrevButton) {
            historyPrevButton.addEventListener('click', function() {
                if (historyCurrentPage > 1) {
                    historyCurrentPage--;
                    loadEquipmentHistory();
                }
            });
        }

        if (historyNextButton) {
            historyNextButton.addEventListener('click', function() {
                if (historyCurrentPage < historyTotalPages) {
                    historyCurrentPage++;
                    loadEquipmentHistory();
                }
            });
        }
    }

    // Load equipment history from the server
    async function loadEquipmentHistory() {
        try {
            updateHistoryButtonStates(true); // Disable buttons during load
            
            const params = new URLSearchParams({
                page: historyCurrentPage,
                limit: historyItemsPerPage,
                search: historySearchTerm,
                actionType: historyFilterOptions.actionType,
                dateRange: historyFilterOptions.dateRange,
                equipmentType: historyFilterOptions.equipmentType
            });
            
            const response = await fetch(`get_equipment_history.php?${params}`);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                equipmentHistory = data.history || [];
                historyTotalPages = data.pagination?.totalPages || 1;
                renderEquipmentHistory();
                updateHistoryPagination();
                
                // Update filter options if provided by the server
                if (data.filters) {
                    updateFilterOptions(data.filters);
                }
            } else {
                showNotification('Failed to load equipment history', 'error');
                console.error('Server returned error:', data);
            }
        } catch (error) {
            console.error('Error loading equipment history:', error);
            showNotification('An error occurred while loading equipment history', 'error');
        } finally {
            updateHistoryButtonStates(false); // Re-enable buttons
        }
    }

    // Update filter dropdowns with options from server
    function updateFilterOptions(filters) {
        // Update action type filter
        if (filters.actionTypes && historyActionTypeFilter) {
            // Keep the current selection
            const currentValue = historyActionTypeFilter.value;
            
            // Clear existing options except the "All" option
            while (historyActionTypeFilter.options.length > 1) {
                historyActionTypeFilter.remove(1);
            }
            
            // Add new options
            filters.actionTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = capitalizeFirstLetter(type);
                historyActionTypeFilter.appendChild(option);
            });
            
            // Restore selection if it still exists
            if (Array.from(historyActionTypeFilter.options).some(opt => opt.value === currentValue)) {
                historyActionTypeFilter.value = currentValue;
            }
        }
        
        // Update date range filter
        if (filters.dateRanges && historyDateRangeFilter) {
            const currentValue = historyDateRangeFilter.value;
            
            // Clear existing options except the "All" option
            while (historyDateRangeFilter.options.length > 1) {
                historyDateRangeFilter.remove(1);
            }
            
            // Add new options
            filters.dateRanges.forEach(range => {
                if (range.value !== 'all') { // Skip "all" as it's already the first option
                    const option = document.createElement('option');
                    option.value = range.value;
                    option.textContent = range.label;
                    historyDateRangeFilter.appendChild(option);
                }
            });
            
            // Restore selection if it still exists
            if (Array.from(historyDateRangeFilter.options).some(opt => opt.value === currentValue)) {
                historyDateRangeFilter.value = currentValue;
            }
        }
    }

    // Render equipment history to the table
    function renderEquipmentHistory() {
        if (!equipmentHistoryTable) return;
        
        equipmentHistoryTable.innerHTML = '';
        
        if (!equipmentHistory || equipmentHistory.length === 0) {
            equipmentHistoryTable.innerHTML = `
                <tr class="border-b">
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        No equipment history found
                    </td>
                </tr>
            `;
            return;
        }
        
        equipmentHistory.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            
            row.innerHTML = `
  <td class="p-4 font-semibold text-gray-800 whitespace-nowrap">
    ${item.po_jo_no || item.property_number || 'N/A'}
  </td>
  <td class="p-4 text-gray-700 whitespace-nowrap">
    ${item.equipment_name || item.equipment_type || 'N/A'}
  </td>
  <td class="p-4 text-gray-600 whitespace-nowrap">
    <span class="inline-block px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium">
      ${item.from_user || 'N/A'}
    </span>
  </td>
  <td class="p-4 text-gray-600 whitespace-nowrap">
    <span class="inline-block px-2 py-1 rounded bg-green-50 text-green-700 text-xs font-medium">
      ${item.to_user || 'N/A'}
    </span>
  </td>
  <td class="p-4 text-gray-500 text-sm whitespace-nowrap">
    ${formatDate(item.action_date)}
  </td>
  <td class="p-4 text-gray-700 whitespace-nowrap">
  ${item.notes && item.notes.length > 30 ? `
    <button class="text-blue-600 underline text-sm view-more-btn" 
            data-notes="${encodeURIComponent(item.notes)}">
      View More
    </button>` 
    : `<span class="text-sm">${item.notes || item.reason || 'N/A'}</span>`}
</td>

`;

// Delegate modal open events to the parent table
document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.mt-6 table tbody').addEventListener('click', function(event) {
    const button = event.target.closest('.view-more-btn');
    if (button) {
        const notes = decodeURIComponent(button.getAttribute('data-notes'));
        const modalContent = document.getElementById('notes-modal-content');
        modalContent.textContent = notes;
        document.getElementById('notes-modal').classList.remove('hidden');
    }
});
       
            equipmentHistoryTable.appendChild(row);
        });
    }

    // Update pagination buttons state for equipment history
    function updateHistoryPagination() {
        if (!historyPrevButton || !historyNextButton) return;
        
        historyPrevButton.disabled = historyCurrentPage <= 1;
        historyNextButton.disabled = historyCurrentPage >= historyTotalPages;
        
        historyPrevButton.classList.toggle('opacity-50', historyCurrentPage <= 1);
        historyNextButton.classList.toggle('opacity-50', historyCurrentPage >= historyTotalPages);
        
        // Update pagination text if it exists
        const paginationText = document.querySelector('.history-pagination-text');
        if (paginationText) {
            paginationText.textContent = `Page ${historyCurrentPage} of ${historyTotalPages}`;
        }
    }

    // Update button states during loading
    function updateHistoryButtonStates(isLoading) {
        const buttons = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm.p-6.mt-6 .btn');
        buttons.forEach(button => {
            button.disabled = isLoading;
            if (isLoading) {
                button.classList.add('opacity-50');
            } else {
                button.classList.remove('opacity-50');
            }
        });
        
        // Add loading indicator if needed
        const loadingIndicator = document.querySelector('.history-loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = isLoading ? 'block' : 'none';
        }
    }

    // Make loadEquipmentHistory available globally
    window.loadEquipmentHistory = loadEquipmentHistory;
}

/**
 * ===================================
 * UTILITY FUNCTIONS
 * ===================================
 */
// Show/hide loading spinner
function showLoading(show = true) {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = show ? 'flex' : 'none';
    } else if (show) {
        // Create a loading spinner if it doesn't exist
        const spinner = document.createElement('div');
        spinner.id = 'loading-spinner';
        spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        spinner.innerHTML = `
            <div class="bg-white p-5 rounded-lg shadow-lg flex flex-col items-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-3"></div>
                <p class="text-gray-700">Processing...</p>
            </div>
        `;
        document.body.appendChild(spinner);
    }
}

// Helper function to format dates
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    } catch (error) {
        console.error('Date formatting error:', error);
        return dateString; // Return the original string if parsing fails
    }
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Show notification message
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-500 ease-in-out z-50 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 
        type === 'warning' ? 'bg-yellow-600' : 
        'bg-blue-600'
    }`;
    
    // Set icon based on notification type
    let icon = '';
    switch (type) {
        case 'success':
            icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            break;
        case 'error':
            icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            break;
        case 'warning':
            icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
            break;
        default:
            icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                ${icon}
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-y-2');
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Debounce function for search inputs
function debounce(func, delay) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
}


</script>
</body>
</html>