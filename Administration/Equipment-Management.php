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
    <title>Equipment Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .modal-transition {
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }
        
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
        }
        .badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
        }
        .badge-success {
            @apply bg-green-100 text-green-800;
        }
        .badge-warning {
            @apply bg-yellow-100 text-yellow-800;
        }
        .badge-danger {
            @apply bg-red-100 text-red-800;
        }
        .badge-info {
            @apply bg-blue-100 text-blue-800;
        }
        .badge-secondary {
            @apply bg-gray-100 text-gray-800;
        }
        .btn {
            @apply px-4 py-2 rounded-md font-medium transition-colors duration-200;
        }
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        .btn-secondary {
            @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
        }
        .btn-success {
            @apply bg-green-600 text-white hover:bg-green-700;
        }
        .btn-danger {
            @apply bg-red-600 text-white hover:bg-red-700;
        }
        .btn-sm {
            @apply px-3 py-1 text-sm;
        }
        .card {
            @apply bg-white rounded-lg shadow-sm border border-gray-200;
        }
        .card-header {
            @apply p-4 border-b border-gray-200 flex justify-between items-center;
        }
        .card-body {
            @apply p-4;
        }
        .form-control {
            @apply w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
        .table-container {
            @apply overflow-x-auto;
        }
        .table {
            @apply w-full text-left;
        }
        .table th {
            @apply p-3 bg-gray-50 text-gray-600 font-medium;
        }
        .table td {
            @apply p-3 border-b border-gray-100;
        }
        .table tr:hover {
            @apply bg-gray-50;
        }
        .search-container {
            @apply relative;
        }
        .search-container i {
            @apply absolute left-3 top-1/2 -translate-y-1/2 text-gray-400;
        }
        .search-input {
            @apply pl-9 pr-4 py-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
        .filter-dropdown {
            @apply p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
</style>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col" id="sidebar">
            <div class="border-b border-gray-700 py-4 px-4">
                <div class="flex items-center">
                    <i class="fas fa-clipboard-check text-blue-500 text-xl"></i>
                    <span class="ml-2 text-xl font-bold">Admin Portal</span>
                </div>
            </div>
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="Admin-Dashboard.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
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
<div class="flex-1 overflow-auto ml-64 bg-gray-50 min-h-screen">
  <div class="p-8 max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
          <span class="bg-gradient-to-r from-red-500 to-red-700 text-white p-2 rounded-lg mr-3 shadow-md">
            <i class="fas fa-tools"></i>
          </span>
          Equipment Management
        </h2>
        <button id="add-equipment-btn" class="inline-flex items-center bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-md transition duration-300 transform hover:-translate-y-1">
          <i class="fas fa-plus-circle mr-2"></i>
          Add Equipment
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white p-6 rounded-xl shadow border border-gray-200 flex flex-wrap gap-4 items-end">
        <!-- Search Bar -->
        <div class="relative flex-grow min-w-[250px]">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
          </span>
          <input id="search-equipment" type="text" placeholder="Search equipment by name, ID, or property number..."
            class="pl-10 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm transition-all">
        </div>

        <!-- Category Filter -->
        <div class="relative min-w-[200px]">
          <label for="category-filter" class="block text-sm text-gray-500 mb-1">Category</label>
          <select id="category-filter" class="w-full appearance-none border border-gray-300 rounded-lg px-4 py-2.5 pr-8 text-sm focus:ring-2 focus:ring-red-500 text-gray-700 bg-white">
            <option value="">All Categories</option>
            <!-- Populated by JS -->
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-600 mt-6">
            <i class="fas fa-chevron-down text-xs"></i>
          </div>
        </div>

        <!-- Status Filter -->
        <div class="relative min-w-[200px]">
          <label for="status-filter" class="block text-sm text-gray-500 mb-1">Status</label>
          <select id="status-filter" class="w-full appearance-none border border-gray-300 rounded-lg px-4 py-2.5 pr-8 text-sm focus:ring-2 focus:ring-red-500 text-gray-700 bg-white">
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Under Repair">Under Repair</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Retired">Retired</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-600 mt-6">
            <i class="fas fa-chevron-down text-xs"></i>
          </div>
        </div>

        <!-- Reset Button -->
        <button id="reset-filters" class="text-sm text-gray-600 hover:text-red-600 inline-flex items-center mt-1 transition-colors">
          <i class="fas fa-redo-alt mr-2"></i> Reset Filters
        </button>
      </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
      <!-- Header -->
      <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
          <i class="fas fa-clipboard-list text-red-500 mr-2"></i>
          Available Equipment
        </h3>
      </div>

      <!-- Responsive Table -->
      <div class="overflow-x-auto">
        <table id="equipment-table" class="w-full text-sm text-left table-auto min-w-[720px]">
          <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
            <tr>
              <th class="px-6 py-3 font-medium">ID/Property No.</th>
              <th class="px-6 py-3 font-medium">Name</th>
              <th class="px-6 py-3 font-medium">Category</th>
              <th class="px-6 py-3 font-medium">Status</th>
              <th class="px-6 py-3 font-medium text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="equipment-list-body" class="bg-white divide-y divide-gray-100">
            <tr>
              <td colspan="5" class="text-center px-4 py-10 text-gray-500">
                <div class="animate-pulse flex flex-col items-center">
                  <div class="rounded-full bg-gray-200 h-12 w-12 mb-4"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/4 mb-2.5"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t border-gray-100 flex justify-between items-center bg-gray-50">
        <span class="text-sm text-gray-500">
          Page <span id="current-page" class="font-medium">1</span> of <span id="total-pages" class="font-medium">1</span>
        </span>
        <div class="flex gap-2">
          <button id="prev-page" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
            <i class="fas fa-chevron-left mr-1"></i> Previous
          </button>
          <button id="next-page" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
            Next <i class="fas fa-chevron-right ml-1"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Equipment Modal -->
<div id="equipmentModal" class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-2 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 flex items-center justify-between">
      <h2 class="text-white font-medium text-base flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        Add New Equipment
      </h2>
      <button onclick="closeEquipmentModal()" class="text-white/80 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <form id="equipmentForm" class="p-4 max-h-[60vh] overflow-y-auto">
      <div class="grid grid-cols-2 gap-x-3 gap-y-2">
        <!-- Equipment Name -->
        <div class="col-span-2 sm:col-span-1">
          <label for="equipment_name" class="block text-xs font-medium text-gray-700 mb-1">
            Equipment Name <span class="text-red-500">*</span>
          </label>
          <input type="text" id="equipment_name" name="equipment_name" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- PO/JO Number -->
        <div class="col-span-2 sm:col-span-1">
          <label for="po_jo_no" class="block text-xs font-medium text-gray-700 mb-1">
            PO/JO No. <span class="text-red-500">*</span>
          </label>
          <input type="text" id="po_jo_no" name="po_jo_no" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- Property Number -->
        <div class="col-span-2 sm:col-span-1">
          <label for="property_number" class="block text-xs font-medium text-gray-700 mb-1">
            Property Number <span class="text-red-500">*</span>
          </label>
          <input type="text" id="property_number" name="property_number" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- Account Code -->
        <div class="col-span-2 sm:col-span-1">
          <label for="account_code" class="block text-xs font-medium text-gray-700 mb-1">
            Account Code <span class="text-red-500">*</span>
          </label>
          <input type="text" id="account_code" name="account_code" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- RIS Number -->
        <div class="col-span-2 sm:col-span-1">
          <label for="ris_no" class="block text-xs font-medium text-gray-700 mb-1">
            RIS No. <span class="text-red-500">*</span>
          </label>
          <input type="text" id="ris_no" name="ris_no" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- OBLIG Number -->
        <div class="col-span-2 sm:col-span-1">
          <label for="oblig_no" class="block text-xs font-medium text-gray-700 mb-1">
            OBLIG No. <span class="text-red-500">*</span>
          </label>
          <input type="text" id="oblig_no" name="oblig_no" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- Units -->
        <div class="col-span-2 sm:col-span-1">
          <label for="units" class="block text-xs font-medium text-gray-700 mb-1">
            Units <span class="text-red-500">*</span>
          </label>
          <input type="number" id="units" name="units" min="1" required
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <!-- Purchase Date -->
        <div class="col-span-2 sm:col-span-1">
          <label for="purchase_date" class="block text-xs font-medium text-gray-700 mb-1">
            Purchase Date <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
              </svg>
            </div>
            <input 
              type="date" 
              id="purchase_date" 
              class="w-full pl-7 pr-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400"
            >
          </div>
        </div>

        <!-- Category Dropdown -->
        <div class="col-span-2">
          <label for="categorySelect" class="block text-xs font-medium text-gray-700 mb-1">
            Category <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
              </svg>
            </div>
            <select 
              id="categorySelect" 
              class="w-full pl-7 pr-6 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400 appearance-none"
            >
              <option value="" disabled selected>Select category</option>
              <option value="Vehicles">Vehicles</option>
              <option value="Computers">Computers</option>
              <option value="Office Equipment">Office Equipment</option>
              <option value="Tools">Tools</option>
              <option value="Furniture">Furniture</option>
              <option value="Machinery">Machinery</option>
              <option value="Electronics">Electronics</option>
              <option value="Laboratory Equipment">Laboratory Equipment</option>
              <option value="Appliances">Appliances</option>
              <option value="Others">Others</option>
            </select>
            
            <div class="absolute inset-y-0 right-2 flex items-center text-gray-400">
              <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Add this right after the Category Dropdown div -->
<div id="otherCategoryContainer" class="col-span-2 hidden mt-2">
  <label for="other_category" class="block text-xs font-medium text-gray-700 mb-1">
    Specify Category <span class="text-red-500">*</span>
  </label>
  <input 
    type="text" 
    id="other_category" 
    name="other_category" 
    class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400"
  >
</div>

        <!-- Description -->
        <div class="col-span-2 mt-1">
          <label for="description" class="block text-xs font-medium text-gray-700 mb-1">
            Description <span class="text-red-500">*</span>
          </label>
          <textarea 
            id="description" 
            rows="2" 
            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-400"
          ></textarea>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="mt-4 flex justify-end gap-2 border-t border-gray-100 pt-3">
        <button 
          type="button" 
          onclick="closeEquipmentModal()" 
          class="px-3 py-1 text-xs font-medium rounded border border-gray-300 hover:bg-gray-50 transition-colors"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          class="px-3 py-1 text-xs font-medium rounded bg-red-500 text-white hover:bg-red-600 transition-colors flex items-center gap-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Add Equipment
        </button>
      </div>
    </form>
  </div>
</div>

<script>
 document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-equipment")
  const categoryFilter = document.getElementById("category-filter")
  const statusFilter = document.getElementById("status-filter")
  const resetBtn = document.getElementById("reset-filters")
  const tableBody = document.getElementById("equipment-list-body")

  let equipmentData = []

  // Fix the data structure handling in fetchEquipmentData function
  function fetchEquipmentData() {
    fetch("get-equipment-category.php")
      .then((res) => res.json())
      .then((response) => {
        // Check if the response has the expected structure with data property
        if (response.data && Array.isArray(response.data)) {
          equipmentData = response.data
        } else if (Array.isArray(response)) {
          equipmentData = response
        } else {
          console.error("Unexpected API response format:", response)
          equipmentData = []
        }

        populateCategoryFilter(equipmentData)
        renderTable()
      })
      .catch((err) => {
        console.error("Error fetching equipment data:", err)
        equipmentData = []
        renderTable() // Still render the table to show error state
      })
  }

  function populateCategoryFilter(data) {
    const categories = [...new Set(data.map((item) => item.category))]
    categoryFilter.innerHTML = '<option value="">All Categories</option>' // reset
    categories.forEach((cat) => {
      const option = document.createElement("option")
      option.value = cat
      option.textContent = cat
      categoryFilter.appendChild(option)
    })
  }

  // Fix the renderTable function to handle potential missing properties
  function renderTable() {
    const searchTerm = searchInput.value.toLowerCase()
    const selectedCategory = categoryFilter.value
    const selectedStatus = statusFilter.value

    const filtered = equipmentData.filter((item) => {
      // Safely access properties with fallbacks for null/undefined values
      const equipmentName = (item.name || "").toLowerCase()
      const id = (item.id || "").toString()
      const propertyNumber = (item.property_number || "").toLowerCase()
      const category = item.category || ""
      const status = item.status || ""

      const matchesSearch =
        equipmentName.includes(searchTerm) || id.includes(searchTerm) || propertyNumber.includes(searchTerm)

      const matchesCategory = selectedCategory ? category === selectedCategory : true
      const matchesStatus = selectedStatus ? status === selectedStatus : true

      return matchesSearch && matchesCategory && matchesStatus
    })

    tableBody.innerHTML = filtered.length
      ? filtered
          .map(
            (item) => `
    <tr>
      <td class="p-3">${item.property_number || ""}</td>
      <td class="p-3">${item.name || ""}</td>
      <td class="p-3">${item.category || ""}</td>
      <td class="p-3">
        <span class="status-badge inline-block px-3 py-1 rounded-full text-xs font-semibold mt-4 uppercase tracking-wide shadow-sm transition-colors duration-200">
          ${item.status || "Unknown"}
        </span>
      </td>
      <td class="p-3">
        <button class="text-blue-500 p-2 rounded-full hover:bg-blue-100 transition-colors duration-150" onclick="viewEquipmentDetails(${item.id})">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" />
          </svg>
        </button>
      </td>
    </tr>
  `,
          )
          .join("")
      : `
    <tr>
      <td colspan="5" class="text-center py-6 text-gray-500">No equipment found.</td>
    </tr>
  `

    // Update the colors of status badges dynamically
    document.querySelectorAll(".status-badge").forEach((badge) => {
      const status = badge.textContent.trim()
      badge.classList.remove(
        "bg-green-100",
        "text-green-800",
        "bg-yellow-100",
        "text-yellow-800",
        "bg-red-100",
        "text-red-800",
        "bg-orange-100",
        "text-orange-800",
      )

      if (status === "Active") {
        badge.classList.add("bg-green-100", "text-green-800")
      } else if (status === "Under Repair") {
        badge.classList.add("bg-orange-100", "text-orange-800")
      } else if (status === "Maintenance") {
        badge.classList.add("bg-yellow-100", "text-yellow-800")
      } else if (status === "Retired") {
        badge.classList.add("bg-red-100", "text-red-800")
      } else {
        badge.classList.add("bg-gray-100", "text-gray-800") // Default for unknown status
      }
    })
  }

  searchInput.addEventListener("input", renderTable)
  categoryFilter.addEventListener("change", renderTable)
  statusFilter.addEventListener("change", renderTable)
  resetBtn.addEventListener("click", () => {
    searchInput.value = ""
    categoryFilter.value = ""
    statusFilter.value = ""
    renderTable()
  })

  fetchEquipmentData()

  // Add inline colors to status filter options
  const statusOptions = statusFilter.querySelectorAll("option")
  statusOptions.forEach((option) => {
    switch (option.value) {
      case "Active":
        option.style.color = "green"
        break
      case "Under Repair":
        option.style.color = "orange"
        break
      case "Maintenance":
        option.style.color = "yellow"
        break
      case "Retired":
        option.style.color = "red"
        break
      default:
        option.style.color = "black"
    }
  })
})

// Define the function globally so that it's accessible outside of the DOMContentLoaded block
window.closeEquipmentModal = () => {
  const modal = document.getElementById("equipmentModal")
  if (modal) {
    modal.classList.add("hidden")
  }
}

window.closeEquipmentDetailsModal = () => {
  const modal = document.getElementById("equipmentDetailsModal")
  if (modal) {
    modal.classList.add("hidden")
  }
}

// Toggle between view and edit modes in the equipment details modal
window.toggleEditMode = (equipmentId) => {
  const viewElements = document.querySelectorAll(".view-mode")
  const editElements = document.querySelectorAll(".edit-mode")

  // Toggle visibility of elements
  viewElements.forEach((el) => el.classList.toggle("hidden"))
  editElements.forEach((el) => el.classList.toggle("hidden"))
}

// Save equipment changes
window.saveEquipmentChanges = (equipmentId) => {
  // Get all form values
  const equipmentName = document.getElementById("edit-equipment-name").value
  const category = document.getElementById("edit-category").value
  const poJoNo = document.getElementById("edit-po-jo-no").value
  const propertyNumber = document.getElementById("edit-property-number").value
  const accountCode = document.getElementById("edit-account-code").value
  const purchaseDate = document.getElementById("edit-purchase-date").value
  const risNo = document.getElementById("edit-ris-no").value
  const obligNo = document.getElementById("edit-oblig-no").value
  const units = document.getElementById("edit-units").value
  const status = document.getElementById("edit-status").value
  const description = document.getElementById("edit-description").value

  // Show loading overlay
  const loadingOverlay = document.createElement("div")
  loadingOverlay.id = "saveEquipmentLoading"
  loadingOverlay.className = "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  loadingOverlay.innerHTML = `
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="text-gray-700 font-medium">Saving changes...</span>
    </div>
  `
  document.body.appendChild(loadingOverlay)

  // Prepare payload
  const payload = {
    id: equipmentId,
    equipment_name: equipmentName,
    category: category,
    po_jo_no: poJoNo,
    property_number: propertyNumber,
    account_code: accountCode,
    purchase_date: purchaseDate,
    ris_no: risNo,
    oblig_no: obligNo,
    units: Number.parseInt(units, 10),
    status: status,
    description: description,
  }

  // Send update request
  fetch("update_equipment.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((data) => {
      // Remove loading overlay
      document.getElementById("saveEquipmentLoading").remove()

      if (data.success) {
        // Update view mode elements with new values
        document.getElementById("view-equipment-name").textContent = equipmentName
        document.getElementById("view-category").textContent = category
        document.getElementById("view-po-jo-no").textContent = poJoNo
        document.getElementById("view-property-number").textContent = propertyNumber
        document.getElementById("view-account-code").textContent = accountCode
        document.getElementById("view-purchase-date").textContent = formatDate(purchaseDate)
        document.getElementById("view-ris-no").textContent = risNo
        document.getElementById("view-oblig-no").textContent = obligNo
        document.getElementById("view-units").textContent = units

        // Update status badge
        const statusBadge = document.querySelector(".status-badge")
        if (statusBadge) {
          // Remove all existing status classes
          statusBadge.classList.remove(
            "bg-green-100",
            "text-green-800",
            "bg-yellow-100",
            "text-yellow-800",
            "bg-red-100",
            "text-red-800",
            "bg-orange-100",
            "text-orange-800",
          )

          // Add appropriate status classes
          if (status === "Active") {
            statusBadge.classList.add("bg-green-100", "text-green-800")
          } else if (status === "Under Repair") {
            statusBadge.classList.add("bg-orange-100", "text-orange-800")
          } else if (status === "Maintenance") {
            statusBadge.classList.add("bg-yellow-100", "text-yellow-800")
          } else if (status === "Retired") {
            statusBadge.classList.add("bg-red-100", "text-red-800")
          }

          statusBadge.textContent = status
        }

        // Update description
        document.getElementById("view-description").innerHTML = nl2br(description)

        // Show success notification
        showNotification("Equipment updated successfully!", "success")

        // Switch back to view mode
        toggleEditMode(equipmentId)
      } else {
        showNotification("Failed to update equipment: " + (data.message || "Unknown error"), "error")
      }
    })
    .catch((err) => {
      document.getElementById("saveEquipmentLoading").remove()
      console.error("Error updating equipment:", err)
      showNotification("An error occurred while updating equipment.", "error")
    })
}

// Helper function to format date
window.formatDate = (dateStr) => {
  if (!dateStr) return "—"
  try {
    return new Date(dateStr).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })
  } catch {
    return dateStr
  }
}

// Helper function to convert newlines to <br>
window.nl2br = (str) => {
  if (!str) return "—"
  return String(str).replace(/\n/g, "<br>")
}

document.addEventListener("DOMContentLoaded", () => {
  // Current page highlighting in the sidebar
  const currentPage = window.location.pathname.split("/").pop()
  const navLinks = document.querySelectorAll("#sidebar nav a")

  navLinks.forEach((link) => {
    const linkPage = link.getAttribute("href")
    if (linkPage === currentPage) {
      link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500")
    }
  })

  // Enhanced modal functionality
  const openBtn = document.getElementById("add-equipment-btn")
  const modal = document.getElementById("equipmentModal")
  const modalContent =
    document.getElementById("modalContent") || modal?.querySelector(".bg-white") || modal?.firstElementChild

  if (openBtn && modal) {
    // Show modal with animation
    openBtn.addEventListener("click", () => {
      openEquipmentModal()
    })
  }

  // Close modal when clicking outside or on close button
  if (modal) {
    // Close when clicking outside the modal content
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeEquipmentModal()
      }
    })

    // Close button functionality (if it exists)
    const closeBtn = document.getElementById("close-modal") || modal.querySelector("[onclick*='closeEquipmentModal']")
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        closeEquipmentModal()
      })
    }
  }

  // Handle category selection to show/hide custom category input
  const categorySelect = document.getElementById("categorySelect")
  if (categorySelect) {
    categorySelect.addEventListener("change", toggleOtherCategoryInput)
  }

  // Handle form submission (Add Equipment)
  const equipmentForm = document.getElementById("equipmentForm")
  if (equipmentForm) {
    equipmentForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Get category value (either selected or custom)
      const categorySelect = document.getElementById("categorySelect")
      const otherCategoryInput = document.getElementById("other_category")
      const selectedCategory =
        categorySelect.value === "Others" && otherCategoryInput ? otherCategoryInput.value.trim() : categorySelect.value

      // Prepare payload for submission
      const payload = {
        equipment_name: equipmentForm.equipment_name.value.trim(),
        po_jo_no: equipmentForm.po_jo_no.value.trim(),
        property_number: equipmentForm.property_number.value.trim(),
        account_code: equipmentForm.account_code.value.trim(),
        category: selectedCategory, // Use the custom category if "Others" is selected
        purchase_date: equipmentForm.purchase_date.value,
        ris_no: equipmentForm.ris_no.value.trim(),
        oblig_no: equipmentForm.oblig_no.value.trim(),
        units: Number.parseInt(equipmentForm.units.value, 10),
        description: equipmentForm.description.value.trim(),
      }

      // Show loading state
      const submitBtn = equipmentForm.querySelector('button[type="submit"]')
      const originalBtnText = submitBtn.innerHTML
      submitBtn.disabled = true
      submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Submitting...
      `

      // Submit the form data via AJAX
      fetch("add-equipment.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      })
        .then((res) => res.json())
        .then((data) => {
          // Reset button state
          submitBtn.disabled = false
          submitBtn.innerHTML = originalBtnText

          if (data.success) {
            // Show success notification
            showNotification("Equipment added successfully!", "success")

            // Close modal and reset form
            closeEquipmentModal()
            equipmentForm.reset()
            
            // Hide other category input if visible
            const otherCategoryContainer = document.getElementById("otherCategoryContainer")
            if (otherCategoryContainer) {
              otherCategoryContainer.classList.add("hidden")
            }
            
            categorySelect.selectedIndex = 0 // Reset category dropdown

            // Refresh equipment table if it exists
            if (typeof refreshEquipmentTable === "function") {
              refreshEquipmentTable()
            }
          } else {
            showNotification("Failed to add equipment: " + (data.message || "Unknown error"), "error")
          }
        })
        .catch((err) => {
          // Reset button state
          submitBtn.disabled = false
          submitBtn.innerHTML = originalBtnText

          console.error("AJAX error:", err)
          showNotification("An error occurred while submitting the form.", "error")
        })
    })
  }

  // Fetch and populate the equipment table
  const equipmentTableBody = document.getElementById("equipment-list-body")

  if (equipmentTableBody) {
    fetchEquipmentData()
  }

  // Function to fetch equipment data
  function fetchEquipmentData() {
    // Show loading state in table
    if (equipmentTableBody) {
      equipmentTableBody.innerHTML = `
        <tr>
          <td colspan="4" class="p-4 text-center">
            <div class="flex justify-center items-center space-x-2">
              <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Loading equipment data...</span>
            </div>
          </td>
        </tr>
      `
    }

    fetch("fetch_equipment.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          const equipment = data.data
          equipmentTableBody.innerHTML = ""

          if (equipment.length === 0) {
            equipmentTableBody.innerHTML = `
              <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">
                  No equipment found. Add some equipment to get started.
                </td>
              </tr>
            `
            return
          }

          equipment.forEach((item) => {
            const row = document.createElement("tr")
            row.classList.add("border-b", "hover:bg-gray-50", "transition-colors", "duration-150")

            // Display names or fallback to 'None'
            const assignedUsers = item.assigned_users ? item.assigned_users : "None"

            row.innerHTML = `
              <td class="p-3">${item.po_jo_no}</td>
              <td class="p-3">${item.equipment_name}</td>
              <td class="p-3">${item.category}</td>
            <span class="status-badge inline-block px-3 py-1 rounded-full text-xs font-semibold mt-4 uppercase tracking-wide shadow-sm transition-colors duration-200">
  ${item.status}
</span>

              <td class="p-3">
                <button class="text-blue-500 p-2 rounded-full hover:bg-blue-100 transition-colors duration-150" onclick="viewEquipmentDetails(${item.id})">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" />
                  </svg>
                </button>
              </td>
            `
            // Apply color classes to status badge
            const statusBadge = row.querySelector(".status-badge")
            const status = item.status

            statusBadge.classList.remove(
              "bg-green-100",
              "text-green-800",
              "bg-yellow-100",
              "text-yellow-800",
              "bg-red-100",
              "text-red-800",
              "bg-orange-100",
              "text-orange-800",
            )

            if (status === "Active") {
              statusBadge.classList.add("bg-green-100", "text-green-800")
            } else if (status === "Under Repair") {
              statusBadge.classList.add("bg-orange-100", "text-orange-800")
            } else if (status === "Maintenance") {
              statusBadge.classList.add("bg-yellow-100", "text-yellow-800")
            } else if (status === "Retired") {
              statusBadge.classList.add("bg-red-100", "text-red-800")
            }
            equipmentTableBody.appendChild(row)
          })
        } else {
          showNotification("Failed to load equipment data.", "error")
          equipmentTableBody.innerHTML = `
            <tr>
              <td colspan="4" class="p-4 text-center text-red-500">
                Error loading data. Please try refreshing the page.
              </td>
            </tr>
          `
        }
      })
      .catch((err) => {
        console.error("Error fetching data:", err)
        showNotification("An error occurred while fetching equipment data.", "error")
        equipmentTableBody.innerHTML = `
          <tr>
            <td colspan="4" class="p-4 text-center text-red-500">
              Error loading data. Please try refreshing the page.
            </td>
          </tr>
        `
      })
  }

  // Make the function available globally for refreshing the table
  window.refreshEquipmentTable = fetchEquipmentData
})

// Function to toggle the visibility of the "Other Category" input field
function toggleOtherCategoryInput() {
  const categorySelect = document.getElementById("categorySelect")
  const otherCategoryContainer = document.getElementById("otherCategoryContainer")
  const otherCategoryInput = document.getElementById("other_category")
  
  if (categorySelect && otherCategoryContainer && otherCategoryInput) {
    if (categorySelect.value === "Others") {
      otherCategoryContainer.classList.remove("hidden")
      otherCategoryInput.setAttribute("required", "required")
    } else {
      otherCategoryContainer.classList.add("hidden")
      otherCategoryInput.removeAttribute("required")
      otherCategoryInput.value = "" // Clear the input when hidden
    }
  }
}

// Make the function available globally
window.toggleOtherCategoryInput = toggleOtherCategoryInput

// Function to open the modal with animation
function openEquipmentModal() {
  const modal = document.getElementById("equipmentModal")
  const modalContent = modal.querySelector(".bg-white") || modal.firstElementChild

  // Show the modal container
  modal.classList.remove("hidden")

  // Add transition classes if they don't exist
  if (modalContent && !modalContent.classList.contains("transform")) {
    modalContent.classList.add("transform", "transition-all", "duration-300", "scale-95", "opacity-0")
  }

  // Trigger animation after a small delay
  setTimeout(() => {
    if (modalContent) {
      modalContent.classList.remove("scale-95", "opacity-0")
      modalContent.classList.add("scale-100", "opacity-100")
    }
  }, 10)
}

// Function to close the modal with animation
function closeEquipmentModal() {
  const modal = document.getElementById("equipmentModal")
  const modalContent = modal.querySelector(".bg-white") || modal.firstElementChild

  // Add transition classes if they don't exist
  if (modalContent && !modalContent.classList.contains("transform")) {
    modalContent.classList.add("transform", "transition-all", "duration-300")
  }

  // Start closing animation
  if (modalContent) {
    modalContent.classList.remove("scale-100", "opacity-100")
    modalContent.classList.add("scale-95", "opacity-0")
  }

  // Hide the modal after animation completes
  setTimeout(() => {
    modal.classList.add("hidden")
  }, 300)
}

function showNotification(message, type = "info") {
  // Create notification container if it doesn't exist
  let notificationContainer = document.getElementById("notification-container")

  if (!notificationContainer) {
    notificationContainer = document.createElement("div")
    notificationContainer.id = "notification-container"
    notificationContainer.className = "fixed bottom-6 right-6 z-50 space-y-4"
    document.body.appendChild(notificationContainer)
  }

  // Create notification element
  const notification = document.createElement("div")
  notification.className = `
    max-w-sm w-full shadow-xl rounded-lg border border-gray-200 pointer-events-auto
    transform transition-all duration-300 translate-y-4 opacity-0 animate-slide-in
  `

  // Set color and icon based on type
  let iconSvg = "",
    bgColor = "",
    borderColor = ""

  switch (type) {
    case "success":
      bgColor = "bg-green-50"
      borderColor = "border-green-200"
      iconSvg = `
        <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      `
      break
    case "error":
      bgColor = "bg-red-50"
      borderColor = "border-red-200"
      iconSvg = `
        <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      `
      break
    default:
      bgColor = "bg-blue-50"
      borderColor = "border-blue-200"
      iconSvg = `
        <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      `
  }

  notification.innerHTML = `
    <div class="${bgColor} ${borderColor} p-4 rounded-lg">
      <div class="flex items-start">
        <div class="flex-shrink-0 mt-0.5">
          ${iconSvg}
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium text-gray-900">${message}</p>
        </div>
        <button class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
          <span class="sr-only">Close</span>
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>
  `

  notificationContainer.appendChild(notification)

  // Animate in
  requestAnimationFrame(() => {
    notification.classList.remove("translate-y-4", "opacity-0")
  })

  // Close button handler
  const closeBtn = notification.querySelector("button")
  closeBtn.addEventListener("click", () => removeNotification(notification))

  // Auto remove after 5s
  setTimeout(() => removeNotification(notification), 5000)
}

// Remove notification with fade out
function removeNotification(notification) {
  notification.classList.add("translate-y-4", "opacity-0")
  setTimeout(() => notification.remove(), 300)
}

// Function to view equipment details (placeholder)
function viewEquipmentDetails(id) {
  // This function would be implemented elsewhere or could be added here
  console.log(`Viewing equipment with ID: ${id}`)
  // You could fetch details and show in another modal
}

// View equipment details function
window.viewEquipmentDetails = (equipmentId) => {
  // Show loading overlay
  const loadingOverlay = document.createElement("div")
  loadingOverlay.id = "equipmentDetailsLoading"
  loadingOverlay.className = "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  loadingOverlay.innerHTML = `
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
      <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="text-gray-700 font-medium">Loading equipment details...</span>
    </div>
  `
  document.body.appendChild(loadingOverlay)

  fetch(`get_equipment_details.php?id=${equipmentId}`)
    .then((res) => res.json())
    .then((data) => {
      // Remove loading overlay
      document.getElementById("equipmentDetailsLoading").remove()

      if (!data.success) {
        showNotification("Failed to fetch equipment details: " + (data.message || "Unknown error"), "error")
        return
      }

      const equipment = data.data[0]

      // Helper to escape HTML
      function escapeHtml(str) {
        if (!str) return "—"
        return String(str).replace(
          /[&<>"'`=/]/g,
          (match) =>
            ({
              "&": "&amp;",
              "<": "&lt;",
              ">": "&gt;",
              '"': "&quot;",
              "'": "&#039;",
              "`": "&#96;",
              "=": "&#61;",
              "/": "&#47;",
            })[match],
        )
      }

      // Convert newlines to <br>
      function nl2br(str) {
        if (!str) return "—"
        return String(str).replace(/\n/g, "<br>")
      }

      // Format date
      function formatDate(dateStr) {
        if (!dateStr) return "—"
        try {
          return new Date(dateStr).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })
        } catch {
          return dateStr
        }
      }

      const description = nl2br(equipment.description)
      const purchaseDate = formatDate(equipment.purchase_date)
      const trueId = equipment.id // <–– grab the real ID here
      const status = equipment.status || "Active"

      // Remove any existing modal
      const existingModal = document.getElementById("equipmentDetailsModal")
      if (existingModal) existingModal.remove()

      // Determine status badge color
      let statusBadgeClass = "bg-green-100 text-green-800" // Default for Active
      if (status === "Maintenance") {
        statusBadgeClass = "bg-yellow-100 text-yellow-800"
      } else if (status === "Under Repair") {
        statusBadgeClass = "bg-orange-100 text-orange-800"
      } else if (status === "Retired") {
        statusBadgeClass = "bg-red-100 text-red-800"
      }

      // Build and insert your modal, now using ${trueId} instead of equipmentId
      const modalHTML = `
        <div id="equipmentDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800" id="modal-title">
                    Equipment Details
                  </h3>
                  <button onclick="closeEquipmentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    &times;
                  </button>
                </div>
              </div>
              
              <!-- View Mode Header -->
              <div class="px-6 py-4 view-mode">
                <h4 id="view-equipment-name" class="text-xl font-medium text-gray-900 mb-2">${escapeHtml(equipment.equipment_name)}</h4>
                <span id="view-category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  ${escapeHtml(equipment.category)}
                </span>
              </div>
              
              <!-- Edit Mode Header -->
              <div class="px-6 py-4 edit-mode hidden">
                <div class="mb-3">
                  <label for="edit-equipment-name" class="block text-sm font-medium text-gray-700 mb-1">Equipment Name</label>
                  <input type="text" id="edit-equipment-name" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${escapeHtml(equipment.equipment_name)}">
                </div>
                <div>
                  <label for="edit-category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <input type="text" id="edit-category" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${escapeHtml(equipment.category)}">
                </div>
              </div>

              <!-- View Mode Reference Info -->
              <div class="px-6 view-mode">
                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <p class="text-xs text-gray-500">PO/JO No.</p>
                      <p id="view-po-jo-no" class="font-medium">${escapeHtml(equipment.po_jo_no)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Property Number</p>
                      <p id="view-property-number" class="font-medium">${escapeHtml(equipment.property_number)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Account Code</p>
                      <p id="view-account-code" class="font-medium">${escapeHtml(equipment.account_code)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Purchase Date</p>
                      <p id="view-purchase-date" class="font-medium">${purchaseDate}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Edit Mode Reference Info -->
              <div class="px-6 edit-mode hidden">
                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label for="edit-po-jo-no" class="block text-xs text-gray-500">PO/JO No.</label>
                      <input type="text" id="edit-po-jo-no" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.po_jo_no)}">
                    </div>
                    <div>
                      <label for="edit-property-number" class="block text-xs text-gray-500">Property Number</label>
                      <input type="text" id="edit-property-number" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.property_number)}">
                    </div>
                    <div>
                      <label for="edit-account-code" class="block text-xs text-gray-500">Account Code</label>
                      <input type="text" id="edit-account-code" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.account_code)}">
                    </div>
                    <div>
                      <label for="edit-purchase-date" class="block text-xs text-gray-500">Purchase Date</label>
                      <input type="date" id="edit-purchase-date" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${equipment.purchase_date}">
                    </div>
                  </div>
                </div>
              </div>

              <!-- View Mode Additional Details -->
              <div class="px-6 view-mode">
                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <p class="text-xs text-gray-500">R.I.S No.</p>
                      <p id="view-ris-no" class="font-medium">${escapeHtml(equipment.ris_no)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Oblig. No.</p>
                      <p id="view-oblig-no" class="font-medium">${escapeHtml(equipment.oblig_no)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Units</p>
                      <p id="view-units" class="font-medium">${escapeHtml(equipment.units)}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500">Status</p>
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${statusBadgeClass} status-badge" data-equipment-id="${trueId}">
                        ${status}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Edit Mode Additional Details -->
              <div class="px-6 edit-mode hidden">
                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label for="edit-ris-no" class="block text-xs text-gray-500">R.I.S No.</label>
                      <input type="text" id="edit-ris-no" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.ris_no)}">
                    </div>
                    <div>
                      <label for="edit-oblig-no" class="block text-xs text-gray-500">Oblig. No.</label>
                      <input type="text" id="edit-oblig-no" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.oblig_no)}">
                    </div>
                    <div>
                      <label for="edit-units" class="block text-xs text-gray-500">Units</label>
                      <input type="number" id="edit-units" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" value="${escapeHtml(equipment.units)}">
                    </div>
                    <div>
                      <label for="edit-status" class="block text-xs text-gray-500">Status</label>
                      <select id="edit-status" class="w-full px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Active" ${status === "Active" ? "selected" : ""}>Active</option>
                        <option value="Under Repair" ${status === "Under Repair" ? "selected" : ""}>Under Repair</option>
                        <option value="Maintenance" ${status === "Maintenance" ? "selected" : ""}>Maintenance</option>
                        <option value="Retired" ${status === "Retired" ? "selected" : ""}>Retired</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- View Mode Description -->
              <div class="px-6 view-mode">
                <div class="mt-4">
                  <h5 class="text-xs font-semibold text-gray-500 uppercase mb-1">Description</h5>
                  <div id="view-description" class="bg-gray-50 p-3 rounded-lg whitespace-pre-line text-sm text-gray-700 max-h-48 overflow-y-auto">
                    ${description}
                  </div>
                </div>
              </div>
              
              <!-- Edit Mode Description -->
              <div class="px-6 edit-mode hidden">
                <div class="mt-4">
                  <label for="edit-description" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Description</label>
                  <textarea id="edit-description" rows="4" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">${escapeHtml(equipment.description)}</textarea>
                </div>
              </div>

              <!-- Footer -->
              <div class="px-6 py-3 bg-gray-50 flex justify-between items-center">
                <span class="text-xs text-gray-500">
                  Last updated: ${formatDate(equipment.updated_at)}
                </span>
                <div class="flex space-x-2">
                  <!-- View Mode Buttons -->
                  <button
                    id="edit-button"
                    onclick="toggleEditMode(${trueId})"
                    class="view-mode px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >Edit</button>
                  
                  <!-- Edit Mode Buttons -->
                  <button
                    id="cancel-button"
                    onclick="toggleEditMode(${trueId})"
                    class="edit-mode hidden px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >Cancel</button>
                  
                  <button
                    id="save-button"
                    onclick="saveEquipmentChanges(${trueId})"
                    class="edit-mode hidden px-4 py-2 rounded-md text-white bg-green-600 hover:bg-green-700"
                  >Save Changes</button>
                  
                  <button
                    onclick="closeEquipmentDetailsModal()"
                    class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700"
                  >Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `
      document.body.insertAdjacentHTML("beforeend", modalHTML)

      // Trigger scale-in animation
      setTimeout(() => {
        const modal = document.getElementById("equipmentDetailsModal")
        const content = modal.querySelector(".transform")
        if (content) {
          content.classList.add("sm:scale-100")
          content.classList.remove("sm:scale-95")
        }
      }, 10)
    })
    .catch((err) => {
      const loadingOverlay = document.getElementById("equipmentDetailsLoading")
      if (loadingOverlay) loadingOverlay.remove()
      console.error("Error fetching equipment details:", err)
      showNotification("An error occurred while fetching equipment details.", "error")
    })
}

</script>

</body>
</html>