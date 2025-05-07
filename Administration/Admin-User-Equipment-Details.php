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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
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
    .user-card:hover {
            transform: translateY(-2px);
        }
        .tab-active {
            border-bottom: 2px solid #4f46e5;
            color: #4f46e5;
        }
        .tab-inactive {
            border-bottom: 2px solid transparent;
            color: #6b7280;
        }
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }
        .modal-content {
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .modal-visible .modal-content {
            transform: translateY(0);
        }
        .equipment-card {
            transition: all 0.2s ease;
        }
        .equipment-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .loading-spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 3px solid #4f46e5;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .search-results {
            max-height: 300px;
            overflow-y: auto;
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
    <div class="flex-1 ml-64 max-w-5xl mx-auto px-4 mt-6">
    <header class="mb-4">
  <h1 class="text-xl font-semibold text-gray-800">User Equipment Management</h1>
  <p class="text-sm text-gray-600 mt-1">Manage user equipment assignments</p>
</header>

<!-- Search and Filter Section -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative flex-1">
                    <div class="flex">
                        <div class="relative flex-1">
                            <input type="text" id="user-search" placeholder="Search users by name, ID, or college..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <button id="search-user-btn" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Search
                        </button>
                    </div>
                    <!-- Search Results Dropdown -->
                    <div id="user-search-results" class="absolute z-10 mt-1 w-full bg-white rounded-md shadow-lg hidden">
                        <div class="search-results p-2">
                            <div id="user-results-list" class="divide-y divide-gray-200"></div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <select id="college-filter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Colleges</option>
                        <!-- Will be populated dynamically -->
                    </select>
                    <button id="clear-filter-btn" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>


        <!-- Users List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800">Users</h2>
                <p class="text-sm text-gray-600">Click on a user to view their equipment details</p>
            </div>
            
            <div id="users-loading" class="py-8 flex justify-center">
                <div class="loading-spinner"></div>
                <span class="ml-3 text-gray-600">Loading users...</span>
            </div>
            
            <div id="users-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4 hidden"></div>
            
            <div id="no-users" class="py-8 text-center hidden">
                <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No users found</p>
            </div>
        </div>
    </div>

    <!-- User Equipment Modal -->
    <div id="user-equipment-modal" class="fixed inset-0 z-50 modal-overlay hidden opacity-0 flex items-center justify-center">
        <div class="modal-content bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b modal-header">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modal-user-name" class="text-xl font-medium text-gray-900">User Name</h3>
                        <p id="modal-user-details" class="text-sm text-gray-500">User Details</p>
                    </div>
                </div>
                <button id="close-modal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b">
                <button id="current-tab" class="flex-1 py-3 px-4 text-center font-medium tab-active focus:outline-none">
                    Current Equipment
                </button>
                <button id="history-tab" class="flex-1 py-3 px-4 text-center font-medium tab-inactive focus:outline-none">
                    Equipment History
                </button>
            </div>
            
            <!-- Tab Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Current Equipment Tab -->
                <div id="current-equipment-content" class="p-4">
                    <div id="current-loading" class="py-8 flex justify-center">
                        <div class="loading-spinner"></div>
                        <span class="ml-3 text-gray-600">Loading current equipment...</span>
                    </div>
                    
                    <div id="current-equipment-list" class="space-y-4 hidden"></div>
                    
                    <div id="no-current-equipment" class="py-8 text-center hidden">
                        <i class="fas fa-laptop text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">No equipment currently assigned to this user</p>
                    </div>
                </div>
                
                <!-- Equipment History Tab -->
                <div id="equipment-history-content" class="p-4 hidden">
                    <div id="history-loading" class="py-8 flex justify-center">
                        <div class="loading-spinner"></div>
                        <span class="ml-3 text-gray-600">Loading equipment history...</span>
                    </div>
                    
                    <div id="equipment-history-list" class="space-y-4 hidden"></div>
                    
                    <div id="no-equipment-history" class="py-8 text-center hidden">
                        <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">No equipment history for this user</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-4 border-t">
                <button id="close-modal-btn" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div id="return-confirmation-modal" class="fixed inset-0 z-50 modal-overlay hidden opacity-0 flex items-center justify-center">
        <div class="modal-content bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Confirm Equipment Return</h3>
            </div>
            <div class="p-4">
                <p class="text-gray-700">Are you sure you want to mark this equipment as returned?</p>
                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                    <p id="return-equipment-name" class="font-medium text-gray-900">Equipment Name</p>
                    <p id="return-equipment-details" class="text-sm text-gray-500">Equipment Details</p>
                </div>
                <div class="mt-4">
                    <label for="return-notes" class="block text-sm font-medium text-gray-700 mb-1">Return Notes (Optional)</label>
                    <textarea id="return-notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter any notes about the condition or return process..."></textarea>
                </div>
            </div>
            <div class="p-4 border-t flex space-x-3">
                <button id="cancel-return" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Cancel
                </button>
                <button id="confirm-return" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <span id="confirm-return-text">Confirm Return</span>
                    <span id="confirm-return-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 flex items-center z-50">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toast-message">Operation successful!</span>
    </div>


<script>
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

  </script>

<script>
  document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements - with null checks
  const usersList = document.getElementById("users-list")
  const usersLoading = document.getElementById("users-loading")
  const noUsers = document.getElementById("no-users")
  const totalUsersElement = document.getElementById("total-users")
  const totalAssignedElement = document.getElementById("total-assigned")
  const recentReturnsElement = document.getElementById("recent-returns")

  const userEquipmentModal = document.getElementById("user-equipment-modal")
  const modalUserName = document.getElementById("modal-user-name")
  const modalUserDetails = document.getElementById("modal-user-details")
  const closeModal = document.getElementById("close-modal")
  const closeModalBtn = document.getElementById("close-modal-btn")

  const currentTab = document.getElementById("current-tab")
  const historyTab = document.getElementById("history-tab")
  const currentEquipmentContent = document.getElementById("current-equipment-content")
  const equipmentHistoryContent = document.getElementById("equipment-history-content")

  const currentLoading = document.getElementById("current-loading")
  const currentEquipmentList = document.getElementById("current-equipment-list")
  const noCurrentEquipment = document.getElementById("no-current-equipment")

  const historyLoading = document.getElementById("history-loading")
  const equipmentHistoryList = document.getElementById("equipment-history-list")
  const noEquipmentHistory = document.getElementById("no-equipment-history")

  const returnConfirmationModal = document.getElementById("return-confirmation-modal")
  const returnEquipmentName = document.getElementById("return-equipment-name")
  const returnEquipmentDetails = document.getElementById("return-equipment-details")
  const returnNotes = document.getElementById("return-notes")
  const cancelReturn = document.getElementById("cancel-return")
  const confirmReturn = document.getElementById("confirm-return")
  const confirmReturnText = document.getElementById("confirm-return-text")
  const confirmReturnLoading = document.getElementById("confirm-return-loading")

  const successToast = document.getElementById("success-toast")
  const toastMessage = document.getElementById("toast-message")

  const userSearchInput = document.getElementById("user-search")
  const searchUserBtn = document.getElementById("search-user-btn")
  const userSearchResults = document.getElementById("user-search-results")
  const userResultsList = document.getElementById("user-results-list")

  const collegeFilter = document.getElementById("college-filter")
  const clearFilterBtn = document.getElementById("clear-filter-btn")

  // Check if we're on the right page with the necessary elements
  if (!usersList && !userEquipmentModal) {
    console.log("User equipment management elements not found. This might not be the equipment management page.")
    return // Exit early if we're not on the right page
  }

  // State
  let allUsers = []
  let filteredUsers = []
  let currentEquipment = []
  let equipmentHistory = []
  let selectedUser = null
  let selectedEquipmentForReturn = null
  const colleges = new Set()
  const stats = {
    totalUsers: 0,
    totalAssigned: 0,
    recentReturns: 0,
    adminUsers: 0,
  }

  // Fetch all users
  function fetchUsers() {
    return fetch("fetch_users.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .then((responseData) => {
        if (responseData.success && Array.isArray(responseData.data)) {
          return responseData.data
        } else {
          console.error("Unexpected API response format:", responseData)
          throw new Error("Unexpected API response format")
        }
      })
      .catch((error) => {
        console.error("Error fetching users:", error)
        showToast("Failed to load users. Please try again.", "error")
        return []
      })
  }

  // Fetch current equipment for a user
  function fetchUserEquipment(userId) {
    return fetch(`fetch_user_equipment.php?user_id=${userId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .then((responseData) => {
        console.log("Current equipment response:", responseData)
        if (responseData.success && Array.isArray(responseData.data)) {
          return responseData.data
        } else {
          console.error("Unexpected API response format:", responseData)
          throw new Error("Unexpected API response format")
        }
      })
      .catch((error) => {
        console.error("Error fetching user equipment:", error)
        showToast("Failed to load equipment data. Please try again.", "error")
        return []
      })
  }

  // Fetch equipment history for a user
  function fetchUserEquipmentHistory(userId) {
    return fetch(`fetch_user_equipment_history.php?user_id=${userId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .then((responseData) => {
        console.log("Equipment history response:", responseData)
        if (responseData.success && Array.isArray(responseData.data)) {
          return responseData.data
        } else {
          console.error("Unexpected API response format:", responseData)
          throw new Error("Unexpected API response format")
        }
      })
      .catch((error) => {
        console.error("Error fetching user equipment history:", error)
        showToast("Failed to load equipment history. Please try again.", "error")
        return []
      })
  }

  // Return equipment
  function returnEquipment(assignmentId, notes) {
    console.log("Returning equipment with assignment ID:", assignmentId)

    // Ensure assignment_id is a number if it's stored as a string
    const parsedId = Number.parseInt(assignmentId, 10)
    const idToUse = isNaN(parsedId) ? assignmentId : parsedId

    const returnData = {
      assignment_id: idToUse,
      notes: notes || "",
    }

    console.log("Return data being sent:", returnData)
    console.log("JSON payload:", JSON.stringify(returnData))

    return fetch("return_equipment.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(returnData),
    })
      .then((response) => {
        console.log("Response status:", response.status)
        if (!response.ok) {
          throw new Error(`Network response was not ok: ${response.status}`)
        }
        return response.json()
      })
      .then((data) => {
        console.log("Return equipment response:", data)
        return data
      })
      .catch((error) => {
        console.error("Error returning equipment:", error)
        return { success: false, message: `Failed to return equipment: ${error.message}` }
      })
  }

  // Fetch stats
  function fetchStats() {
    // This would ideally be a separate API endpoint
    // For now, we'll calculate from our existing data
    stats.totalUsers = allUsers.length

    // Count admin users
    stats.adminUsers = allUsers.filter((user) => user.is_admin === "1").length

    // Count total assigned equipment
    let assignedCount = 0
    let recentReturnsCount = 0

    // This is a placeholder. In a real app, you'd fetch this from the server
    // For demo purposes, we'll set some values
    assignedCount = Math.floor(Math.random() * 50) + 20 // Random number between 20-70
    recentReturnsCount = Math.floor(Math.random() * 10) + 5 // Random number between 5-15

    stats.totalAssigned = assignedCount
    stats.recentReturns = recentReturnsCount

    updateStatsDisplay()
  }

  // Update stats display
  function updateStatsDisplay() {
    if (totalUsersElement) totalUsersElement.textContent = stats.totalUsers
    if (totalAssignedElement) totalAssignedElement.textContent = stats.totalAssigned
    if (recentReturnsElement) recentReturnsElement.textContent = stats.recentReturns

    // Update admin users count if the element exists
    const adminUsersElement = document.getElementById("admin-users")
    if (adminUsersElement) {
      adminUsersElement.textContent = stats.adminUsers || 0
    }
  }

  // Render users list
  function renderUsersList(users) {
    if (!usersList || !noUsers) return

    usersList.innerHTML = ""

    if (users.length === 0) {
      usersList.classList.add("hidden")
      noUsers.classList.remove("hidden")
      return
    }

    usersList.classList.remove("hidden")
    noUsers.classList.add("hidden")

    users.forEach((user) => {
      const card = document.createElement("div")
      card.className = "user-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-all cursor-pointer"
      card.dataset.id = user.id

      // Use default icon for profile picture
      const profilePicHtml = `<div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
        <i class="fas fa-user"></i>
      </div>`

      // Check if user is admin
      const isAdmin = user.is_admin === "1"
      const adminBadge = isAdmin
      ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
        <i class="fas fa-shield-alt mr-1"></i> Admin
      </span>`
      : ""

      card.innerHTML = `
      <div class="flex items-start">
        <div class="flex-shrink-0 mr-3">
        ${profilePicHtml}
        </div>
        <div class="flex-1 min-w-0">
        <div class="flex items-center">
          <h4 class="text-sm font-medium text-gray-900 truncate">${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}</h4>
          ${adminBadge}
        </div>
        <p class="text-xs text-gray-500">${user.id} | ${user.college || "No College"}</p>
        <p class="text-xs text-gray-500 truncate">${user.email || "No Email"}</p>
        <p class="text-xs text-gray-500 mt-1">${user.role || "No Role"}${user.admin_role ? ` - ${user.admin_role}` : ""}</p>
        </div>
        <div class="flex-shrink-0 ml-2">
        <i class="fas fa-chevron-right text-gray-400"></i>
        </div>
      </div>
      `

      card.addEventListener("click", () => {
        openUserEquipmentModal(user)
      })

      usersList.appendChild(card)
    })
  }

  // Render current equipment
  function renderCurrentEquipment(equipment) {
    if (!currentEquipmentList || !noCurrentEquipment) return

    currentEquipmentList.innerHTML = ""

    if (equipment.length === 0) {
      currentEquipmentList.classList.add("hidden")
      noCurrentEquipment.classList.remove("hidden")
      return
    }

    currentEquipmentList.classList.remove("hidden")
    noCurrentEquipment.classList.add("hidden")

    equipment.forEach((item) => {
      const card = document.createElement("div")
      card.className = "equipment-card p-4 border border-gray-200 rounded-lg"

      // Format assignment date
      let formattedAssignmentDate = item.assigned_at || item.assignment_date
      try {
        const assignmentDate = new Date(item.assigned_at || item.assignment_date)
        if (!isNaN(assignmentDate.getTime())) {
          formattedAssignmentDate = assignmentDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          })
        }
      } catch (e) {
        console.error("Error formatting date:", e)
      }

      card.innerHTML = `
        <div class="flex items-start">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas ${getCategoryIcon(item.category)}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start">
              <h4 class="text-sm font-medium text-gray-900">${item.equipment_name || "Unnamed Equipment"}</h4>
              <button class="return-equipment-btn px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200" data-id="${item.assignment_id}" data-equipment-id="${item.equipment_id}">
              </button>
            </div>
            <p class="text-xs text-gray-500">${item.property_number || "No Property Number"} | ${item.category || "Uncategorized"}</p>
            <p class="text-xs text-gray-500 mt-1">Assigned: ${formattedAssignmentDate || "Unknown date"}</p>
            ${item.notes ? `<p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded">${item.notes}</p>` : ""}
          </div>
        </div>
      `

      // Add event listener to return button
      const returnBtn = card.querySelector(".return-equipment-btn")
      returnBtn.addEventListener("click", function (e) {
        e.stopPropagation()
        e.preventDefault()

        console.log("Return button clicked")
        console.log("Button data attributes:", this.dataset)

        // Get the assignment ID directly from the dataset
        const assignmentId = this.dataset.id
        console.log("Assignment ID:", assignmentId)

        // Find the equipment item
        const equipmentToReturn = equipment.find((eq) => eq.assignment_id === assignmentId)
        console.log("Equipment to return:", equipmentToReturn)

        if (equipmentToReturn) {
          openReturnConfirmationModal(equipmentToReturn)
        } else {
          console.error("Could not find equipment with assignment ID:", assignmentId)
          showToast("Error: Could not find equipment details", "error")
        }
      })

      currentEquipmentList.appendChild(card)
    })
  }

  // Render equipment history
  function renderEquipmentHistory(history) {
    console.log("Rendering equipment history:", history)

    if (!equipmentHistoryList || !noEquipmentHistory) {
      console.error("Equipment history DOM elements not found")
      return
    }

    equipmentHistoryList.innerHTML = ""

    if (!history || history.length === 0) {
      console.log("No equipment history to display")
      equipmentHistoryList.classList.add("hidden")
      noEquipmentHistory.classList.remove("hidden")
      return
    }

    equipmentHistoryList.classList.remove("hidden")
    noEquipmentHistory.classList.add("hidden")

    history.forEach((item) => {
      const card = document.createElement("div")
      card.className = "equipment-card p-4 border border-gray-200 rounded-lg"

      // Format dates
      let formattedAssignmentDate = item.assigned_at || item.assignment_date
      let formattedReturnDate = item.returned_at

      try {
        const assignmentDate = new Date(item.assigned_at || item.assignment_date)
        if (!isNaN(assignmentDate.getTime())) {
          formattedAssignmentDate = assignmentDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          })
        }

        const returnDate = new Date(item.returned_at)
        if (!isNaN(returnDate.getTime())) {
          formattedReturnDate = returnDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          })
        }
      } catch (e) {
        console.error("Error formatting date:", e)
      }

      card.innerHTML = `
        <div class="flex items-start">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas ${getCategoryIcon(item.category)}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start">
              <h4 class="text-sm font-medium text-gray-900">${item.equipment_name || "Unnamed Equipment"}</h4>
              <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                <i class="fas fa-check-circle mr-1"></i> Returned
              </span>
            </div>
            <p class="text-xs text-gray-500">${item.property_number || "No Property Number"} | ${item.category || "Uncategorized"}</p>
            <div class="flex flex-wrap gap-x-4 mt-1">
              <p class="text-xs text-gray-500">
                <i class="fas fa-calendar-plus text-green-500 mr-1"></i> Assigned: ${formattedAssignmentDate || "Unknown"}
              </p>
              <p class="text-xs text-gray-500">
                <i class="fas fa-calendar-minus text-red-500 mr-1"></i> Returned: ${formattedReturnDate || "Unknown"}
              </p>
            </div>
            ${item.return_notes ? `<p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded">${item.return_notes}</p>` : ""}
            ${item.notes ? `<p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded">Assignment notes: ${item.notes}</p>` : ""}
          </div>
        </div>
      `

      equipmentHistoryList.appendChild(card)
    })
  }

  // Open user equipment modal
  function openUserEquipmentModal(user) {
    selectedUser = user

    // Check if user has a profile picture
    const hasProfilePic = user.profile_picture && user.profile_picture !== "null"
    const profilePicHtml = `
  <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
    <i class="fas fa-user text-xl"></i>
  </div>`


    // Update modal header with profile picture if modal exists
    if (userEquipmentModal) {
      const modalHeader =
        userEquipmentModal.querySelector(".modal-header") ||
        userEquipmentModal.querySelector(".flex.items-center.justify-between.p-4.border-b")
      if (modalHeader) {
        const userInfoContainer = modalHeader.querySelector(".flex.items-center") || modalHeader.firstElementChild
        if (userInfoContainer) {
          userInfoContainer.innerHTML = `
            <div class="flex-shrink-0 mr-4">
              ${profilePicHtml}
            </div>
            <div>
              <div class="flex items-center">
                <h3 id="modal-user-name" class="text-xl font-medium text-gray-900">
                  ${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}
                </h3>
                ${
                  user.is_admin === "1"
                    ? `
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                    <i class="fas fa-shield-alt mr-1"></i> Admin
                  </span>`
                    : ""
                }
              </div>
              <p id="modal-user-details" class="text-sm text-gray-500">
                ${user.college || "No College"} | ${user.email || "No Email"}
              </p>
              <p class="text-sm text-gray-500">
                ${user.role || "No Role"}${user.admin_role ? ` - ${user.admin_role}` : ""}
              </p>
            </div>
          `
        }
      } else if (modalUserName && modalUserDetails) {
        // Fallback to the original method if the structure is different
        modalUserName.textContent =
          user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"
        modalUserDetails.textContent = `${user.college || "No College"} | ${user.email || "No Email"}`
      }

      // Show current equipment tab by default
      showTab("current")

      // Show modal
      userEquipmentModal.classList.remove("hidden")
      setTimeout(() => {
        userEquipmentModal.classList.add("opacity-100")
        const modalContent = userEquipmentModal.querySelector(".modal-content")
        if (modalContent) {
          modalContent.classList.add("transform-none")
        }
      }, 10)

      // Reset and show loading indicators
      if (currentLoading) currentLoading.classList.remove("hidden")
      if (historyLoading) historyLoading.classList.remove("hidden")

      if (currentEquipmentList) currentEquipmentList.classList.add("hidden")
      if (noCurrentEquipment) noCurrentEquipment.classList.add("hidden")

      if (equipmentHistoryList) equipmentHistoryList.classList.add("hidden")
      if (noEquipmentHistory) noEquipmentHistory.classList.add("hidden")

      // Fetch and display current equipment
      fetchUserEquipment(user.id).then((data) => {
        currentEquipment = data
        if (currentLoading) currentLoading.classList.add("hidden")
        renderCurrentEquipment(data)
      })

      // Fetch equipment history (but don't display yet)
      fetchUserEquipmentHistory(user.id).then((data) => {
        equipmentHistory = data
        if (historyLoading) historyLoading.classList.add("hidden")

        // If we're already on the history tab, render it now
        if (equipmentHistoryContent && !equipmentHistoryContent.classList.contains("hidden")) {
          renderEquipmentHistory(data)
        }
      })
    }
  }

  // Close user equipment modal
  function closeUserEquipmentModal() {
    if (!userEquipmentModal) return

    userEquipmentModal.classList.remove("opacity-100")
    setTimeout(() => {
      userEquipmentModal.classList.add("hidden")
      // Reset state
      currentEquipment = []
      equipmentHistory = []
      selectedUser = null
    }, 300)
  }

  // Show tab
  function showTab(tabName) {
    if (!currentTab || !historyTab || !currentEquipmentContent || !equipmentHistoryContent) return

    if (tabName === "current") {
      currentTab.classList.add("tab-active")
      currentTab.classList.remove("tab-inactive")
      historyTab.classList.add("tab-inactive")
      historyTab.classList.remove("tab-active")

      currentEquipmentContent.classList.remove("hidden")
      equipmentHistoryContent.classList.add("hidden")
    } else {
      historyTab.classList.add("tab-active")
      historyTab.classList.remove("tab-inactive")
      currentTab.classList.add("tab-inactive")
      currentTab.classList.remove("tab-active")

      equipmentHistoryContent.classList.remove("hidden")
      currentEquipmentContent.classList.add("hidden")

      // Render equipment history if not already rendered
      if (equipmentHistoryList && equipmentHistoryList.innerHTML === "") {
        renderEquipmentHistory(equipmentHistory)
      }
    }
  }

  // Open return confirmation modal
  function openReturnConfirmationModal(equipment) {
    if (!returnConfirmationModal || !returnEquipmentName || !returnEquipmentDetails) return

    selectedEquipmentForReturn = equipment

    returnEquipmentName.textContent = equipment.equipment_name || "Unnamed Equipment"
    returnEquipmentDetails.textContent = `${equipment.property_number || "No Property Number"} | ${equipment.category || "Uncategorized"}`
    if (returnNotes) returnNotes.value = ""

    returnConfirmationModal.classList.remove("hidden")
    setTimeout(() => {
      returnConfirmationModal.classList.add("opacity-100")
      const modalContent = returnConfirmationModal.querySelector(".modal-content")
      if (modalContent) {
        modalContent.classList.add("transform-none")
      }
    }, 10)
  }

  // Close return confirmation modal
  function closeReturnConfirmationModal() {
    returnConfirmationModal.classList.remove("opacity-100")
    setTimeout(() => {
      returnConfirmationModal.classList.add("hidden")
      selectedEquipmentForReturn = null
    }, 300)
  }

  // Process equipment return
  function processEquipmentReturn() {
    if (!selectedEquipmentForReturn) {
      console.error("No equipment selected for return")
      showToast("Error: No equipment selected", "error")
      return
    }

    // Show loading state
    confirmReturnText.classList.add("hidden")
    confirmReturnLoading.classList.remove("hidden")
    confirmReturn.disabled = true

    // Make sure we have a valid assignment ID
    const assignmentId = selectedEquipmentForReturn.assignment_id
    if (!assignmentId) {
      console.error("Missing assignment ID for equipment:", selectedEquipmentForReturn)
      showToast("Error: Missing assignment ID", "error")

      // Reset loading state
      confirmReturnText.classList.remove("hidden")
      confirmReturnLoading.classList.add("hidden")
      confirmReturn.disabled = false
      return
    }

    console.log("Processing return for equipment:", selectedEquipmentForReturn)
    console.log("Assignment ID being sent:", assignmentId)

    // Call the returnEquipment function with the assignment ID
    returnEquipment(assignmentId, returnNotes.value)
      .then((response) => {
        if (response.success) {
          // Close modal
          closeReturnConfirmationModal()

          // Show success message
          showToast("Equipment returned successfully!")

          // Update current equipment list
          currentEquipment = currentEquipment.filter(
            (item) => item.assignment_id !== selectedEquipmentForReturn.assignment_id,
          )
          renderCurrentEquipment(currentEquipment)

          // Refresh equipment history
          fetchUserEquipmentHistory(selectedUser.id).then((data) => {
            equipmentHistory = data
            if (equipmentHistoryContent && !equipmentHistoryContent.classList.contains("hidden")) {
              renderEquipmentHistory(data)
            }
          })

          // Update stats
          stats.totalAssigned--
          stats.recentReturns++
          updateStatsDisplay()
        } else {
          showToast(response.message || "Failed to return equipment. Please try again.", "error")
        }
      })
      .finally(() => {
        // Reset loading state
        confirmReturnText.classList.remove("hidden")
        confirmReturnLoading.classList.add("hidden")
        confirmReturn.disabled = false
      })
  }

  // Show toast message
  function showToast(message, type = "success") {
    if (!successToast || !toastMessage) return

    toastMessage.textContent = message

    if (type === "error") {
      successToast.classList.remove("bg-green-600")
      successToast.classList.add("bg-red-600")
    } else {
      successToast.classList.remove("bg-red-600")
      successToast.classList.add("bg-green-600")
    }

    successToast.classList.remove("translate-y-20", "opacity-0")

    setTimeout(() => {
      successToast.classList.add("translate-y-20", "opacity-0")
    }, 3000)
  }

  // Search users
  function searchUsers() {
    const searchTerm = userSearchInput.value.toLowerCase().trim()

    if (searchTerm === "") {
      userSearchResults.classList.add("hidden")
      return
    }

    const results = allUsers.filter(
      (user) =>
        (user.first_name && user.first_name.toLowerCase().includes(searchTerm)) ||
        (user.last_name && user.last_name.toLowerCase().includes(searchTerm)) ||
        (user.first_name &&
          user.last_name &&
          `${user.first_name} ${user.last_name}`.toLowerCase().includes(searchTerm)) ||
        (user.id && user.id.toString().toLowerCase().includes(searchTerm)) ||
        (user.college && user.college.toLowerCase().includes(searchTerm)) ||
        (user.email && user.email.toLowerCase().includes(searchTerm)) ||
        (user.role && user.role.toLowerCase().includes(searchTerm)) ||
        (user.admin_role && user.admin_role.toLowerCase().includes(searchTerm)),
    )

    userResultsList.innerHTML = ""

    if (results.length === 0) {
      userResultsList.innerHTML = `
        <div class="text-center py-4 text-gray-500">
          <p>No matching users found</p>
        </div>
      `
    } else {
      results.forEach((user) => {
        const card = document.createElement("div")
        card.className = "p-3 hover:bg-gray-50 cursor-pointer"

        // Check if user has a profile picture
        const hasProfilePic = user.profile_picture && user.profile_picture !== "null"
        const profilePicHtml = hasProfilePic
          ? `<img src="${user.profile_picture}" alt="Profile" class="w-8 h-8 rounded-full object-cover">`
          : `<div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
            <i class="fas fa-user"></i>
          </div>`

        card.innerHTML = `
          <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
              ${profilePicHtml}
            </div>
            <div>
              <div class="flex items-center">
                <h4 class="text-sm font-medium text-gray-900">
                  ${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}
                </h4>
                ${
                  user.is_admin === "1"
                    ? `
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                    <i class="fas fa-shield-alt mr-1"></i> Admin
                  </span>`
                    : ""
                }
              </div>
              <p class="text-xs text-gray-500">${user.college || "No College"} | ${user.role || "No Role"}</p>
            </div>
          </div>
        `

        card.addEventListener("click", () => {
          openUserEquipmentModal(user)
          userSearchResults.classList.add("hidden")
          userSearchInput.value = ""
        })

        userResultsList.appendChild(card)
      })
    }

    userSearchResults.classList.remove("hidden")
  }

  // Filter users by college
  function filterUsersByCollege(college) {
    if (!college) {
      filteredUsers = [...allUsers]
    } else {
      filteredUsers = allUsers.filter((user) => user.college === college)
    }

    renderUsersList(filteredUsers)
  }

  // Populate college filter
  function populateCollegeFilter() {
    collegeFilter.innerHTML = '<option value="">All Colleges</option>'

    // Sort colleges alphabetically
    const sortedColleges = Array.from(colleges).sort()

    sortedColleges.forEach((college) => {
      const option = document.createElement("option")
      option.value = college
      option.textContent = college
      collegeFilter.appendChild(option)
    })
  }

  // Helper function to get icon for equipment category
  function getCategoryIcon(category) {
    if (!category) return "fa-tools"

    switch (category.toLowerCase()) {
      case "computer":
        return "fa-laptop"
      case "tablet":
        return "fa-tablet-alt"
      case "office equipment":
        return "fa-print"
      case "accessory":
        return "fa-mouse"
      case "display":
        return "fa-desktop"
      case "audio":
        return "fa-headphones"
      case "appliances":
        return "fa-plug"
      case "machinery":
      case "machines":
        return "fa-cogs"
      case "laboratory equipment":
        return "fa-flask"
      default:
        return "fa-tools"
    }
  }

  // Initialize
  function init() {
    // Load users
    fetchUsers().then((data) => {
      allUsers = data
      filteredUsers = [...data]

      // Extract colleges for filtering
      data.forEach((user) => {
        if (user.college) {
          colleges.add(user.college)
        }
      })

      // Populate college filter if it exists
      if (collegeFilter) {
        populateCollegeFilter()
      }

      // Hide loading, show users if they exist
      if (usersLoading) usersLoading.classList.add("hidden")
      if (usersList) renderUsersList(data)

      // Fetch stats
      fetchStats()
    })

    // Event listeners - add null checks
    if (closeModal) closeModal.addEventListener("click", closeUserEquipmentModal)
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeUserEquipmentModal)

    if (currentTab) currentTab.addEventListener("click", () => showTab("current"))
    if (historyTab) historyTab.addEventListener("click", () => showTab("history"))

    if (cancelReturn) cancelReturn.addEventListener("click", closeReturnConfirmationModal)
    if (confirmReturn) confirmReturn.addEventListener("click", processEquipmentReturn)

    if (searchUserBtn) searchUserBtn.addEventListener("click", searchUsers)
    if (userSearchInput) {
      userSearchInput.addEventListener("keyup", (e) => {
        if (e.key === "Enter") {
          searchUsers()
        }
      })
    }

    if (collegeFilter) {
      collegeFilter.addEventListener("change", () => {
        filterUsersByCollege(collegeFilter.value)
      })
    }

    if (clearFilterBtn) {
      clearFilterBtn.addEventListener("click", () => {
        if (collegeFilter) collegeFilter.value = ""
        filterUsersByCollege("")
      })
    }

    // Close search results when clicking outside
    document.addEventListener("click", (e) => {
      if (
        userSearchResults &&
        !userSearchResults.contains(e.target) &&
        e.target !== userSearchInput &&
        e.target !== searchUserBtn
      ) {
        userSearchResults.classList.add("hidden")
      }
    })

    // Close modals when clicking outside
    if (userEquipmentModal) {
      userEquipmentModal.addEventListener("click", (e) => {
        if (e.target === userEquipmentModal) {
          closeUserEquipmentModal()
        }
      })
    }

    if (returnConfirmationModal) {
      returnConfirmationModal.addEventListener("click", (e) => {
        if (e.target === returnConfirmationModal) {
          closeReturnConfirmationModal()
        }
      })
    }
  }

  // Start the application
  init()
})

</script>
</body>
</html>