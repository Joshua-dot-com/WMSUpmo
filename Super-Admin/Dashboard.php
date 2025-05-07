<?php
session_start(); // Start the session

// Check if the user is logged in (session should be set)
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    // Redirect to login page if neither user nor admin is logged in
    header("Location: Login.php");
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
    <title>Admin Dashboard</title>
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
<div class="flex-1 md:ml-64 flex flex-col">
            <!-- Header -->
            <header class="border-b bg-white shadow-sm sticky top-0 z-10">
                <div class="flex h-16 items-center px-6">
                    <button id="sidebar-toggle" class="md:hidden mr-4 text-indigo-600 hover:text-indigo-800 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Dashboard</h1>
                </div>
            </header>

            <main class="p-6 overflow-y-auto bg-gray-50 flex-grow">
                <!-- Stats Cards -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <div class="flex items-center justify-between pb-3">
                            <div class="text-sm font-medium text-gray-600">Total Users</div>
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div id="total-users" class="text-3xl font-bold text-gray-800">Loading...</div>
                        <p id="total-users-change" class="text-xs font-medium mt-2">
                            <span class="text-green-500 bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-arrow-up mr-1"></i>12% from last month
                            </span>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-100 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                        <div class="flex items-center justify-between pb-3">
                            <div class="text-sm font-medium text-gray-600">Granted Access</div>
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div id="granted-access" class="text-3xl font-bold text-gray-800">Loading...</div>
                        <p id="granted-access-change" class="text-xs font-medium mt-2">
                            <span class="text-green-500 bg-green-50 px-2 py-1 rounded-full">
                                90.7% approval rate
                            </span>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-100 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-yellow-500"></div>
                        <div class="flex items-center justify-between pb-3">
                            <div class="text-sm font-medium text-gray-600">Pending Requests</div>
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div id="pending-requests" class="text-3xl font-bold text-gray-800">Loading...</div>
                        <p id="pending-requests-change" class="text-xs font-medium mt-2">
                            <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">
                                <i class="fas fa-exclamation-circle mr-1"></i>Requires attention
                            </span>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-100 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                        <div class="flex items-center justify-between pb-3">
                            <div class="text-sm font-medium text-gray-600">Rejected Requests</div>
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div id="rejected-requests" class="text-3xl font-bold text-gray-800">Loading...</div>
                        <p id="rejected-requests-change" class="text-xs font-medium mt-2">
                            <span class="text-red-500 bg-red-50 px-2 py-1 rounded-full">
                                7.4% rejection rate
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Activity and Stats -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-7 mt-6">
                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-md lg:col-span-4 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3">
                                    <i class="fas fa-history"></i>
                                </span>
                                Recent Activity
                            </h2>
                        </div>
                        <div class="p-6">
                            <div id="recent-activity" class="space-y-4">
                                <!-- Loading state with skeleton -->
                                <div class="animate-pulse flex space-x-4">
                                    <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                                    <div class="flex-1 space-y-2 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                                    </div>
                                </div>
                                <div class="animate-pulse flex space-x-4">
                                    <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                                    <div class="flex-1 space-y-2 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                        <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                                    </div>
                                </div>
                                <div class="animate-pulse flex space-x-4">
                                    <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                                    <div class="flex-1 space-y-2 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
// Mobile sidebar toggle
document.getElementById('sidebar-toggle').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('active');
});

document.addEventListener("DOMContentLoaded", function () {
    // Highlight current nav link in sidebar
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });

    // Load dashboard data
    loadStats();
    loadActivity();
});

// Fetch and display stats dynamically with percentage changes
function loadStats() {
    fetch('load-stats.php')  // Fetching data from the PHP backend
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const current = data.current;
            const lastMonth = data.last_month;

            // Set current values for each card
            document.getElementById('total-users').innerText = current.total_users;
            document.getElementById('granted-access').innerText = current.granted;
            document.getElementById('pending-requests').innerText = current.pending;
            document.getElementById('rejected-requests').innerText = current.rejected;

            // Calculate and set the percentage changes
            document.getElementById('total-users-change').innerText = calculatePercentageChange(current.total_users, lastMonth.total_users) + '% from last month';
            document.getElementById('granted-access-change').innerText = calculatePercentageChange(current.granted, lastMonth.granted) + '% approval rate';
            document.getElementById('pending-requests-change').innerText = calculatePercentageChange(current.pending, lastMonth.pending) + '% increase';
            document.getElementById('rejected-requests-change').innerText = calculatePercentageChange(current.rejected, lastMonth.rejected) + '% rejection rate';
        })
        .catch(error => console.error('Error loading stats:', error));
}

// Helper function to calculate percentage change
function calculatePercentageChange(currentValue, lastMonthValue) {
    if (lastMonthValue === 0 && currentValue === 0) {
        return '0'; // No change when both are zero
    }
    if (lastMonthValue === 0) {
        return currentValue > 0 ? '100.00' : '0.00'; // Return 100% if current value is greater than zero, otherwise 0%
    }
    const change = ((currentValue - lastMonthValue) / Math.max(lastMonthValue, 1)) * 100; // Avoid division by zero
    return change.toFixed(2); // Limiting the decimal places to 2
}

// Fetch and display recent activity
function loadActivity() {
    fetch('load_recent_activity.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.text();
        })
        .then(html => {
            const activityContainer = document.getElementById('recent-activity');
            if (activityContainer) {
                activityContainer.innerHTML = html;
            } else {
                console.error('Element with ID "recent-activity" not found.');
            }
        })
        .catch(error => console.error('Error loading activity:', error));
}

</script>

</body>
</html>