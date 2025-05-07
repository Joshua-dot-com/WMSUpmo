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
    .step-active {
      color: #4f46e5;
      border-color: #4f46e5;
    }
    
    .step-completed {
      background-color: #4f46e5;
      color: white;
      border-color: #4f46e5;
    }
    
    .step-connector {
      height: 2px;
      background-color: #e5e7eb;
      flex-grow: 1;
      margin: 0 0.5rem;
    }
    
    .step-connector-active {
      background-color: #4f46e5;
    }
    
    .animate-fade-in {
      animation: fadeIn 0.4s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .equipment-card.selected {
      border-color: #4f46e5;
      background-color: #eef2ff;
    }
    
    .user-card.selected {
      border-color: #4f46e5;
      background-color: #eef2ff;
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
    <div class="flex-1 md:ml-64 flex flex-col p-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Assign Equipment to User</h1>
        <p class="text-gray-600 mt-1">Follow the steps below to assign equipment</p>
      </div>
      
      <!-- Step Indicator -->
      <div class="flex items-center justify-between mb-6 px-4">
        <div class="flex items-center">
          <div id="step-1-indicator" class="w-8 h-8 rounded-full border-2 step-active font-medium flex items-center justify-center">1</div>
          <div class="step-connector" id="connector-1-2"></div>
          <div id="step-2-indicator" class="w-8 h-8 rounded-full border-2 text-gray-400 font-medium flex items-center justify-center">2</div>
          <div class="step-connector" id="connector-2-3"></div>
          <div id="step-3-indicator" class="w-8 h-8 rounded-full border-2 text-gray-400 font-medium flex items-center justify-center">3</div>
        </div>
      </div>
      
      <!-- Step Labels -->
      <div class="flex items-center justify-between mb-6 px-4 text-sm font-medium text-gray-500">
        <div class="text-center w-20 text-indigo-600" id="step-1-label">Select Equipment</div>
        <div class="flex-grow"></div>
        <div class="text-center w-20" id="step-2-label">Select User</div>
        <div class="flex-grow"></div>
        <div class="text-center w-20" id="step-3-label">Review & Confirm</div>
      </div>
      
      <!-- Step Container -->
      <div class="bg-white rounded-xl shadow border overflow-hidden">
        <!-- Step 1: Select Equipment -->
        <div id="step-1" class="animate-fade-in">
          <div class="p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
              <i class="fas fa-laptop text-indigo-500 mr-2"></i>
              Select Equipment
            </h2>
            <p class="text-gray-600 mt-1">Search and select the equipment</p>
          </div>
          
          <div class="p-4">
            <!-- Search Equipment -->
            <div class="mb-4">
              <label for="equipment-search" class="block text-sm font-medium mb-1">Search Equipment</label>
              <div class="relative">
                <input type="text" id="equipment-search" placeholder="Search..." 
                  class="w-full px-3 py-1.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button id="search-equipment-btn" class="absolute right-2 top-2 text-gray-400 hover:text-indigo-500">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
            
            <!-- Equipment List -->
            <div id="equipment-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <div class="col-span-full text-center py-6 text-gray-500">
                <div class="animate-spin h-8 w-8 border-b-2 border-indigo-500 mx-auto mb-2 rounded-full"></div>
                <p>Loading equipment...</p>
              </div>
            </div>
            
            <!-- Search Results (initially hidden) -->
            <div id="equipment-search-results" class="search-results mt-4 hidden">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Search Results</h3>
              <div id="equipment-results-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Search results will be populated here -->
              </div>
            </div>
            
            <!-- Selected Equipment Summary -->
            <div id="selected-equipment-summary" class="mt-4 p-3 bg-gray-50 rounded border hidden">
              <h3 class="text-sm font-medium mb-2">Selected Equipment</h3>
              <div id="selected-equipment-list" class="space-y-2">
                <!-- Selected equipment will be shown here -->
              </div>
            </div>
          </div>
          
          <div class="p-3 bg-gray-50 border-t flex justify-end">
            <button id="next-to-step-2" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
              Next: Select User <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
        </div>
        
        <!-- Step 2: Select User -->
        <div id="step-2" class="hidden animate-fade-in">
          <div class="p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
              <i class="fas fa-user text-indigo-500 mr-2"></i>
              Select User
            </h2>
            <p class="text-gray-600 mt-1">Search and select a user</p>
          </div>
          
          <div class="p-4">
            <!-- Search User -->
            <div class="mb-4">
              <label for="user-search" class="block text-sm font-medium mb-1">Search User</label>
              <div class="relative">
                <input type="text" id="user-search" placeholder="Search..." 
                  class="w-full px-3 py-1.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button id="search-user-btn" class="absolute right-2 top-2 text-gray-400 hover:text-indigo-500">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
            
            <!-- User List -->
            <div id="user-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <div class="col-span-full text-center py-6 text-gray-500">
                <div class="animate-spin h-8 w-8 border-b-2 border-indigo-500 mx-auto mb-2 rounded-full"></div>
                <p>Loading users...</p>
              </div>
            </div>
            
            <!-- Search Results (initially hidden) -->
            <div id="user-search-results" class="search-results mt-4 hidden">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Search Results</h3>
              <div id="user-results-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Search results will be populated here -->
              </div>
            </div>
            
            <!-- Selected User Summary -->
            <div id="selected-user-summary" class="mt-4 p-3 bg-gray-50 rounded border hidden">
              <h3 class="text-sm font-medium mb-2">Selected User</h3>
              <div id="selected-user-info" class="flex items-center">
                <!-- Selected user will be shown here -->
              </div>
            </div>
          </div>
          
          <div class="p-3 bg-gray-50 border-t flex justify-between">
            <button id="back-to-step-1" class="px-3 py-1.5 border rounded text-gray-700 hover:bg-gray-100">
              <i class="fas fa-arrow-left mr-2"></i>Back
            </button>
            <button id="next-to-step-3" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
              Next: Review <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
        </div>
        
        <!-- Step 3: Review and Confirm -->
        <div id="step-3" class="hidden animate-fade-in">
          <div class="p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
              <i class="fas fa-clipboard-check text-indigo-500 mr-2"></i>
              Review & Confirm
            </h2>
            <p class="text-gray-600 mt-1">Review and confirm the assignment</p>
          </div>
          
          <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Equipment Details -->
              <div class="border rounded overflow-hidden">
                <div class="p-3 bg-gray-50 border-b">
                  <h3 class="font-medium">Equipment Details</h3>
                </div>
                <div id="review-equipment-details" class="p-3">
                  <!-- Equipment details will be populated here -->
                </div>
              </div>
              
              <!-- User Details -->
              <div class="border rounded overflow-hidden">
                <div class="p-3 bg-gray-50 border-b">
                  <h3 class="font-medium">User Details</h3>
                </div>
                <div id="review-user-details" class="p-3">
                  <!-- User details will be populated here -->
                </div>
              </div>
            </div>
            
            <!-- Assignment Notes -->
            <div>
              <label for="assignment-notes" class="text-sm font-medium block mb-1">Notes (Optional)</label>
              <textarea id="assignment-notes" rows="2" placeholder="Add any notes about this assignment..." 
                class="w-full px-3 py-1.5 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            
            <!-- Assignment Date -->
            <div>
              <label for="assignment-date" class="text-sm font-medium block mb-1">Assignment Date</label>
              <input type="date" id="assignment-date" 
                class="w-full px-3 py-1.5 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          
          <div class="p-3 bg-gray-50 border-t flex justify-between">
            <button id="back-to-step-2" class="px-3 py-1.5 border rounded text-gray-700 hover:bg-gray-100">
              <i class="fas fa-arrow-left mr-2"></i>Back
            </button>
            <button id="confirm-assignment" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
              <i class="fas fa-check mr-2"></i>Confirm
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Success Modal -->
  <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 animate-fade-in">
      <div class="p-6 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-xl text-green-600"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Assignment Successful!</h3>
        <p class="text-gray-600 mb-4">The equipment has been successfully assigned to the user.</p>
        <div class="flex justify-center">
          <button id="close-success-modal" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
            Done
          </button>
        </div>
      </div>
    </div>
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
  // DOM Elements
  const step1 = document.getElementById("step-1")
  const step2 = document.getElementById("step-2")
  const step3 = document.getElementById("step-3")

  const step1Indicator = document.getElementById("step-1-indicator")
  const step2Indicator = document.getElementById("step-2-indicator")
  const step3Indicator = document.getElementById("step-3-indicator")

  const step1Label = document.getElementById("step-1-label")
  const step2Label = document.getElementById("step-2-label")
  const step3Label = document.getElementById("step-3-label")

  const connector12 = document.getElementById("connector-1-2")
  const connector23 = document.getElementById("connector-2-3")

  const nextToStep2Btn = document.getElementById("next-to-step-2")
  const backToStep1Btn = document.getElementById("back-to-step-1")
  const nextToStep3Btn = document.getElementById("next-to-step-3")
  const backToStep2Btn = document.getElementById("back-to-step-2")
  const confirmAssignmentBtn = document.getElementById("confirm-assignment")

  const equipmentList = document.getElementById("equipment-list")
  const equipmentSearchInput = document.getElementById("equipment-search")
  const searchEquipmentBtn = document.getElementById("search-equipment-btn")
  const equipmentSearchResults = document.getElementById("equipment-search-results")
  const equipmentResultsList = document.getElementById("equipment-results-list")
  const selectedEquipmentSummary = document.getElementById("selected-equipment-summary")
  const selectedEquipmentList = document.getElementById("selected-equipment-list")

  const userList = document.getElementById("user-list")
  const userSearchInput = document.getElementById("user-search")
  const searchUserBtn = document.getElementById("search-user-btn")
  const userSearchResults = document.getElementById("user-search-results")
  const userResultsList = document.getElementById("user-results-list")
  const selectedUserSummary = document.getElementById("selected-user-summary")
  const selectedUserInfo = document.getElementById("selected-user-info")

  const reviewEquipmentDetails = document.getElementById("review-equipment-details")
  const reviewUserDetails = document.getElementById("review-user-details")
  const assignmentNotes = document.getElementById("assignment-notes")
  const assignmentDate = document.getElementById("assignment-date")

  const successModal = document.getElementById("success-modal")
  const closeSuccessModalBtn = document.getElementById("close-success-modal")

  // Optional: Category filter elements
  const categoryFilter = document.getElementById("category-filter")
  const clearFilterBtn = document.getElementById("clear-filter-btn")

  // State
  let selectedEquipment = null
  let selectedUser = null
  let allEquipment = []
  let allUsers = []
  let filteredEquipment = []
  const categories = new Set()

  // Set today's date as default
  const today = new Date()
  const formattedDate = today.toISOString().split("T")[0]
  assignmentDate.value = formattedDate

  function fetchEquipment() {
    return fetch("fetch_available_equipment.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .then((responseData) => {
        // Check if the response has the expected structure
        if (responseData.success && Array.isArray(responseData.data)) {
          return responseData.data
        } else {
          console.error("Unexpected API response format:", responseData)
          throw new Error("Unexpected API response format")
        }
      })
      .catch((error) => {
        console.error("Error fetching equipment:", error)
        equipmentList.innerHTML = `
          <div class="col-span-full text-center py-8 text-gray-500">
            <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
            <p>Error loading equipment data</p>
            <p class="text-sm text-gray-400">Please try again later</p>
          </div>
        `
        return []
      })
  }

  function fetchUsers() {
    return fetch("fetch_users.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .then((responseData) => {
        // Check if the response has the expected structure
        if (responseData.success && Array.isArray(responseData.data)) {
          return responseData.data
        } else {
          console.error("Unexpected API response format:", responseData)
          throw new Error("Unexpected API response format")
        }
      })
      .catch((error) => {
        console.error("Error fetching users:", error)
        userList.innerHTML = `
          <div class="col-span-full text-center py-8 text-gray-500">
            <i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i>
            <p>Error loading user data</p>
            <p class="text-sm text-gray-400">Please try again later</p>
          </div>
        `
        return []
      })
  }

  function assignEquipment(data) {
    // Get property_number and po_jo_no from the selected equipment
    const propertyNumber = selectedEquipment.property_number || ""
    const poJoNo = selectedEquipment.po_jo_no || ""

    // Create the data object with the correct field names
    const assignmentData = {
      user_id: data.userId,
      equipment_id: data.equipmentId,
      assignment_date: data.date,
      notes: data.notes,
      po_jo_no: poJoNo,
      property_number: propertyNumber,
    }

    return fetch("assign_equipment.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(assignmentData),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok")
        }
        return response.json()
      })
      .catch((error) => {
        console.error("Error assigning equipment:", error)
        return { success: false, message: "Failed to assign equipment. Please try again." }
      })
  }

  // Load initial data
  fetchEquipment().then((data) => {
    allEquipment = data
    filteredEquipment = [...data]

    // Extract unique categories for filtering
    data.forEach((item) => {
      if (item.category) {
        categories.add(item.category)
      }
    })

    // Populate category filter if it exists
    if (categoryFilter) {
      populateCategoryFilter()
    }

    renderEquipmentList(data)
  })

  // Populate category filter dropdown
  function populateCategoryFilter() {
    categoryFilter.innerHTML = '<option value="">All Categories</option>'

    // Sort categories alphabetically
    const sortedCategories = Array.from(categories).sort()

    sortedCategories.forEach((category) => {
      const option = document.createElement("option")
      option.value = category
      option.textContent = category
      categoryFilter.appendChild(option)
    })

    // Add event listener for category filter
    categoryFilter.addEventListener("change", () => {
      const selectedCategory = categoryFilter.value

      if (selectedCategory) {
        filteredEquipment = allEquipment.filter((item) => item.category === selectedCategory)
      } else {
        filteredEquipment = [...allEquipment]
      }

      renderEquipmentList(filteredEquipment)
    })

    // Add event listener for clear filter button if it exists
    if (clearFilterBtn) {
      clearFilterBtn.addEventListener("click", () => {
        categoryFilter.value = ""
        filteredEquipment = [...allEquipment]
        renderEquipmentList(filteredEquipment)
      })
    }
  }

  // Navigation functions
  function goToStep1() {
    step1.classList.remove("hidden")
    step2.classList.add("hidden")
    step3.classList.add("hidden")

    step1Indicator.classList.add("step-active")
    step1Indicator.classList.remove("step-completed")
    step2Indicator.classList.remove("step-active", "step-completed")
    step3Indicator.classList.remove("step-active", "step-completed")

    step1Label.classList.add("text-indigo-600")
    step1Label.classList.remove("text-gray-500")
    step2Label.classList.add("text-gray-500")
    step2Label.classList.remove("text-indigo-600")
    step3Label.classList.add("text-gray-500")
    step3Label.classList.remove("text-indigo-600")

    connector12.classList.remove("step-connector-active")
    connector23.classList.remove("step-connector-active")
  }

  function goToStep2() {
    step1.classList.add("hidden")
    step2.classList.remove("hidden")
    step3.classList.add("hidden")

    step1Indicator.classList.remove("step-active")
    step1Indicator.classList.add("step-completed")
    step2Indicator.classList.add("step-active")
    step2Indicator.classList.remove("step-completed")
    step3Indicator.classList.remove("step-active", "step-completed")

    step1Label.classList.remove("text-indigo-600")
    step1Label.classList.add("text-gray-500")
    step2Label.classList.remove("text-gray-500")
    step2Label.classList.add("text-indigo-600")
    step3Label.classList.add("text-gray-500")
    step3Label.classList.remove("text-indigo-600")

    connector12.classList.add("step-connector-active")
    connector23.classList.remove("step-connector-active")

    // Load users if not already loaded
    if (allUsers.length === 0) {
      fetchUsers().then((data) => {
        allUsers = data
        renderUserList(data)
      })
    }
  }

  function goToStep3() {
    step1.classList.add("hidden")
    step2.classList.add("hidden")
    step3.classList.remove("hidden")

    step1Indicator.classList.remove("step-active")
    step1Indicator.classList.add("step-completed")
    step2Indicator.classList.remove("step-active")
    step2Indicator.classList.add("step-completed")
    step3Indicator.classList.add("step-active")

    step1Label.classList.remove("text-indigo-600")
    step1Label.classList.add("text-gray-500")
    step2Label.classList.remove("text-indigo-600")
    step2Label.classList.add("text-gray-500")
    step3Label.classList.remove("text-gray-500")
    step3Label.classList.add("text-indigo-600")

    connector12.classList.add("step-connector-active")
    connector23.classList.add("step-connector-active")

    // Populate review details
    renderReviewDetails()
  }

  // Render functions
  function renderEquipmentList(equipment) {
    equipmentList.innerHTML = ""

    if (equipment.length === 0) {
      equipmentList.innerHTML = `
        <div class="col-span-full text-center py-8 text-gray-500">
          <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
          <p>No equipment found</p>
        </div>
      `
      return
    }

    equipment.forEach((item) => {
      const isAssigned = item.assignment_status === "Assigned"
      const card = document.createElement("div")

      // Add different styling for assigned equipment
      card.className = `equipment-card p-4 border border-gray-200 rounded-lg transition-all 
        ${isAssigned ? "opacity-75 cursor-not-allowed" : "hover:shadow-md cursor-pointer"} 
        ${selectedEquipment && selectedEquipment.id === item.id ? "border-indigo-500 bg-indigo-50 selected" : ""}`

      card.dataset.id = item.id
      card.dataset.assigned = isAssigned ? "true" : "false"

      card.innerHTML = `
        <div class="flex items-start">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas ${getCategoryIcon(item.category)}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-medium text-gray-900 truncate">${item.equipment_name || "Unnamed Equipment"}</h4>
            <p class="text-xs text-gray-500">${item.property_number || "No Property Number"} | ${item.category || "Uncategorized"}</p>
            <div class="mt-1 flex items-center">
              ${
                isAssigned
                  ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                    <i class="fas fa-user-lock mr-1"></i> Assigned to ${item.assigned_to_user_name || "a user"}
                  </span>`
                  : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Available
                  </span>`
              }
            </div>
          </div>
          <div class="flex-shrink-0 ml-2">
            ${
              !isAssigned
                ? `
              <div class="w-5 h-5 border-2 rounded-full ${selectedEquipment && selectedEquipment.id === item.id ? "bg-indigo-600 border-indigo-600" : "border-gray-300"}"></div>
            `
                : `
              <div class="w-5 h-5 text-red-500">
                <i class="fas fa-lock"></i>
              </div>
            `
            }
          </div>
        </div>
      `

      // Only add click event for unassigned equipment
      if (!isAssigned) {
        card.addEventListener("click", () => {
          selectEquipment(item)
        })
      } else {
        // Add tooltip or info click for assigned equipment
        card.addEventListener("click", () => {
          showAssignmentInfo(item)
        })
      }

      equipmentList.appendChild(card)
    })
  }

  function showAssignmentInfo(equipment) {
    // Create a simple alert or modal to show assignment info
    alert(
      `This equipment is currently assigned to ${equipment.assigned_to_user_name || "a user"} and cannot be reassigned until it is returned.`,
    )
  }

  function renderUserList(users) {
    userList.innerHTML = ""

    if (users.length === 0) {
      userList.innerHTML = `
        <div class="col-span-full text-center py-8 text-gray-500">
          <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
          <p>No users found</p>
        </div>
      `
      return
    }

    users.forEach((user) => {
      const card = document.createElement("div")
      card.className = `user-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-all cursor-pointer ${selectedUser && selectedUser.id === user.id ? "border-indigo-500 bg-indigo-50 selected" : ""}`
      card.dataset.id = user.id

      card.innerHTML = `
        <div class="flex items-start">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas fa-user"></i>
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-medium text-gray-900 truncate">${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}</h4>
            <p class="text-xs text-gray-500">${user.id || "No ID"} | ${user.college || "No College"}</p>
            <p class="text-xs text-gray-500 truncate">${user.email || "No Email"}</p>
          </div>
          <div class="flex-shrink-0 ml-2">
            <div class="w-5 h-5 border-2 rounded-full ${selectedUser && selectedUser.id === user.id ? "bg-indigo-600 border-indigo-600" : "border-gray-300"}"></div>
          </div>
        </div>
      `

      card.addEventListener("click", () => {
        selectUser(user)
      })

      userList.appendChild(card)
    })
  }

  function renderReviewDetails() {
    if (!selectedEquipment || !selectedUser) return

    // Format purchase date for better display
    let formattedPurchaseDate = selectedEquipment.purchase_date
    try {
      const purchaseDate = new Date(selectedEquipment.purchase_date)
      if (!isNaN(purchaseDate.getTime())) {
        formattedPurchaseDate = purchaseDate.toLocaleDateString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
        })
      }
    } catch (e) {
      console.error("Error formatting date:", e)
    }

    // Render equipment details
    reviewEquipmentDetails.innerHTML = `
      <div class="space-y-3 bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas ${getCategoryIcon(selectedEquipment.category)}"></i>
          </div>
          <div>
            <h4 class="font-medium text-gray-900">${selectedEquipment.equipment_name || "Unnamed Equipment"}</h4>
            <p class="text-sm text-gray-500">${selectedEquipment.category || "Uncategorized"}</p>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm mt-3">
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">ID:</p>
            <p class="font-medium">${selectedEquipment.id || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Property Number:</p>
            <p class="font-medium">${selectedEquipment.property_number || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Purchase Date:</p>
            <p class="font-medium">${formattedPurchaseDate || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Units:</p>
            <p class="font-medium">${selectedEquipment.units || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Account Code:</p>
            <p class="font-medium">${selectedEquipment.account_code || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">RIS No:</p>
            <p class="font-medium">${selectedEquipment.ris_no || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">PO/JO No:</p>
            <p class="font-medium">${selectedEquipment.po_jo_no || "N/A"}</p>
          </div>
          <div class="col-span-2 bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Description:</p>
            <div class="text-xs max-h-24 overflow-y-auto mt-1 pr-1">${selectedEquipment.description || "No description available"}</div>
          </div>
        </div>
      </div>
    `

    // Render user details
    reviewUserDetails.innerHTML = `
      <div class="space-y-3 bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
            <i class="fas fa-user"></i>
          </div>
          <div>
            <h4 class="font-medium text-gray-900">${selectedUser.first_name && selectedUser.last_name ? `${selectedUser.first_name} ${selectedUser.last_name}` : "Unnamed User"}</h4>
            <p class="text-sm text-gray-500">${selectedUser.role || "No role specified"}</p>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm mt-3">
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">ID:</p>
            <p class="font-medium">${selectedUser.id || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">College:</p>
            <p class="font-medium">${selectedUser.college || "N/A"}</p>
          </div>
          <div class="col-span-2 bg-gray-50 p-2 rounded">
            <p class="text-gray-500 text-xs">Email:</p>
            <p class="font-medium">${selectedUser.email || "N/A"}</p>
          </div>
        </div>
      </div>
    `
  }

  // Helper functions
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

  function truncateText(text, maxLength = 100) {
    if (!text) return ""
    if (text.length <= maxLength) return text
    return text.substring(0, maxLength) + "..."
  }

  function selectEquipment(equipment) {
    // Don't allow selection if equipment is assigned
    if (equipment.assignment_status === "Assigned") {
      showAssignmentInfo(equipment)
      return
    }

    selectedEquipment = equipment

    // Update UI
    document.querySelectorAll(".equipment-card").forEach((card) => {
      if (card.dataset.id === equipment.id) {
        card.classList.add("selected", "border-indigo-500", "bg-indigo-50")
      } else {
        card.classList.remove("selected", "border-indigo-500", "bg-indigo-50")
      }
    })

    // Show selected equipment summary
    selectedEquipmentSummary.classList.remove("hidden")
    selectedEquipmentList.innerHTML = `
      <div class="flex items-center justify-between bg-white p-2 rounded border border-indigo-200">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-2">
            <i class="fas ${getCategoryIcon(equipment.category)}"></i>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-900">${equipment.equipment_name || "Unnamed Equipment"}</h4>
            <p class="text-xs text-gray-500">${equipment.id} | ${equipment.property_number || "No Property Number"}</p>
          </div>
        </div>
        <button class="text-gray-400 hover:text-red-500" id="clear-equipment-selection">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `

    // Add event listener to clear button
    document.getElementById("clear-equipment-selection").addEventListener("click", (e) => {
      e.stopPropagation()
      clearEquipmentSelection()
    })

    // Enable next button
    nextToStep2Btn.disabled = false
  }

  function selectUser(user) {
    selectedUser = user

    // Update UI
    document.querySelectorAll(".user-card").forEach((card) => {
      if (card.dataset.id === user.id) {
        card.classList.add("selected", "border-indigo-500", "bg-indigo-50")
      } else {
        card.classList.remove("selected", "border-indigo-500", "bg-indigo-50")
      }
    })

    // Show selected user summary
    selectedUserSummary.classList.remove("hidden")
    selectedUserInfo.innerHTML = `
      <div class="flex items-center justify-between bg-white p-2 rounded border border-indigo-200 w-full">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-2">
            <i class="fas fa-user"></i>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-900">${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}</h4>
            <p class="text-xs text-gray-500">${user.college || "No College"} | ${user.id}</p>
          </div>
        </div>
        <button class="text-gray-400 hover:text-red-500" id="clear-user-selection">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `

    // Add event listener to clear button
    document.getElementById("clear-user-selection").addEventListener("click", (e) => {
      e.stopPropagation()
      clearUserSelection()
    })

    // Enable next button
    nextToStep3Btn.disabled = false
  }

  function clearEquipmentSelection() {
    selectedEquipment = null
    selectedEquipmentSummary.classList.add("hidden")

    document.querySelectorAll(".equipment-card").forEach((card) => {
      card.classList.remove("selected", "border-indigo-500", "bg-indigo-50")
    })

    nextToStep2Btn.disabled = true
  }

  function clearUserSelection() {
    selectedUser = null
    selectedUserSummary.classList.add("hidden")

    document.querySelectorAll(".user-card").forEach((card) => {
      card.classList.remove("selected", "border-indigo-500", "bg-indigo-50")
    })

    nextToStep3Btn.disabled = true
  }

  function searchEquipment() {
    const searchTerm = equipmentSearchInput.value.toLowerCase().trim()

    if (searchTerm === "") {
      equipmentSearchResults.classList.add("hidden")
      return
    }

    const results = allEquipment.filter(
      (item) =>
        (item.equipment_name && item.equipment_name.toLowerCase().includes(searchTerm)) ||
        (item.id && item.id.toString().toLowerCase().includes(searchTerm)) ||
        (item.category && item.category.toLowerCase().includes(searchTerm)) ||
        (item.property_number && item.property_number.toLowerCase().includes(searchTerm)),
    )

    equipmentResultsList.innerHTML = ""

    if (results.length === 0) {
      equipmentResultsList.innerHTML = `
        <div class="col-span-full text-center py-4 text-gray-500">
          <p>No matching equipment found</p>
        </div>
      `
    } else {
      results.forEach((item) => {
        const isAssigned = item.assignment_status === "Assigned"
        const card = document.createElement("div")

        // Add different styling for assigned equipment
        card.className = `equipment-card p-4 border border-gray-200 rounded-lg transition-all 
          ${isAssigned ? "opacity-75 cursor-not-allowed" : "hover:shadow-md cursor-pointer"} 
          ${selectedEquipment && selectedEquipment.id === item.id ? "border-indigo-500 bg-indigo-50 selected" : ""}`

        card.dataset.id = item.id
        card.dataset.assigned = isAssigned ? "true" : "false"

        card.innerHTML = `
          <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
              <i class="fas ${getCategoryIcon(item.category)}"></i>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-medium text-gray-900 truncate">${item.equipment_name || "Unnamed Equipment"}</h4>
              <p class="text-xs text-gray-500">${item.property_number || "No Property Number"} | ${item.category || "Uncategorized"}</p>
              <div class="mt-1 flex items-center">
                ${
                  isAssigned
                    ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                      <i class="fas fa-user-lock mr-1"></i> Assigned to ${item.assigned_to_user_name || "a user"}
                    </span>`
                    : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                      <i class="fas fa-check-circle mr-1"></i> Available
                    </span>`
                }
              </div>
            </div>
            <div class="flex-shrink-0 ml-2">
              ${
                !isAssigned
                  ? `
                <div class="w-5 h-5 border-2 rounded-full ${selectedEquipment && selectedEquipment.id === item.id ? "bg-indigo-600 border-indigo-600" : "border-gray-300"}"></div>
              `
                  : `
                <div class="w-5 h-5 text-red-500">
                  <i class="fas fa-lock"></i>
                </div>
              `
              }
            </div>
          </div>
        `

        // Only add click event for unassigned equipment
        if (!isAssigned) {
          card.addEventListener("click", () => {
            selectEquipment(item)
            equipmentSearchResults.classList.add("hidden")
            equipmentSearchInput.value = ""
          })
        } else {
          // Add tooltip or info click for assigned equipment
          card.addEventListener("click", () => {
            showAssignmentInfo(item)
          })
        }

        equipmentResultsList.appendChild(card)
      })
    }

    equipmentSearchResults.classList.remove("hidden")
  }

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
        (user.email && user.email.toLowerCase().includes(searchTerm)),
    )

    userResultsList.innerHTML = ""

    if (results.length === 0) {
      userResultsList.innerHTML = `
        <div class="col-span-full text-center py-4 text-gray-500">
          <p>No matching users found</p>
        </div>
      `
    } else {
      results.forEach((user) => {
        const card = document.createElement("div")
        card.className = `user-card p-4 border border-gray-200 rounded-lg hover:shadow-md transition-all cursor-pointer ${selectedUser && selectedUser.id === user.id ? "border-indigo-500 bg-indigo-50 selected" : ""}`
        card.dataset.id = user.id

        card.innerHTML = `
          <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
              <i class="fas fa-user"></i>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-medium text-gray-900 truncate">${user.first_name && user.last_name ? `${user.first_name} ${user.last_name}` : "Unnamed User"}</h4>
              <p class="text-xs text-gray-500">${user.id || "No ID"} | ${user.college || "No College"}</p>
              <p class="text-xs text-gray-500 truncate">${user.email || "No Email"}</p>
            </div>
            <div class="flex-shrink-0 ml-2">
              <div class="w-5 h-5 border-2 rounded-full ${selectedUser && selectedUser.id === user.id ? "bg-indigo-600 border-indigo-600" : "border-gray-300"}"></div>
            </div>
          </div>
        `

        card.addEventListener("click", () => {
          selectUser(user)
          userSearchResults.classList.add("hidden")
          userSearchInput.value = ""
        })

        userResultsList.appendChild(card)
      })
    }

    userSearchResults.classList.remove("hidden")
  }

  // Event listeners
  nextToStep2Btn.addEventListener("click", goToStep2)
  backToStep1Btn.addEventListener("click", goToStep1)
  nextToStep3Btn.addEventListener("click", goToStep3)
  backToStep2Btn.addEventListener("click", goToStep2)

  searchEquipmentBtn.addEventListener("click", searchEquipment)
  equipmentSearchInput.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      searchEquipment()
    }
  })

  searchUserBtn.addEventListener("click", searchUsers)
  userSearchInput.addEventListener("keyup", (e) => {
    if (e.key === "Enter") {
      searchUsers()
    }
  })

  confirmAssignmentBtn.addEventListener("click", () => {
    if (!selectedEquipment || !selectedUser) return

    const assignmentData = {
      equipmentId: selectedEquipment.id,
      userId: selectedUser.id,
      date: assignmentDate.value,
      notes: assignmentNotes.value,
    }

    confirmAssignmentBtn.disabled = true
    confirmAssignmentBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...'

    assignEquipment(assignmentData).then((response) => {
      confirmAssignmentBtn.disabled = false
      confirmAssignmentBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Confirm Assignment'

      if (response.success) {
        successModal.classList.remove("hidden")
      } else {
        alert("Error: " + (response.message || "Failed to assign equipment"))
      }
    })
  })

  closeSuccessModalBtn.addEventListener("click", () => {
    successModal.classList.add("hidden")

    // Reset the form
    clearEquipmentSelection()
    clearUserSelection()
    assignmentNotes.value = ""
    goToStep1()

    // Refresh equipment list to remove assigned equipment
    fetchEquipment().then((data) => {
      allEquipment = data
      filteredEquipment = [...data]
      renderEquipmentList(data)
    })
  })

  // Show equipment details in modal if clicked with Alt key
  document.addEventListener("click", (e) => {
    const equipmentCard = e.target.closest(".equipment-card")
    if (e.altKey && equipmentCard) {
      const equipmentId = equipmentCard.dataset.id
      const equipment = allEquipment.find((item) => item.id === equipmentId)
      if (equipment) {
        showEquipmentDetailsModal(equipment)
      }
    }
  })

  // Function to show equipment details in a modal
  function showEquipmentDetailsModal(equipment) {
    // Create modal if it doesn't exist
    let modal = document.getElementById("equipment-details-modal")
    if (!modal) {
      modal = document.createElement("div")
      modal.id = "equipment-details-modal"
      modal.className = "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden"
      modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col">
          <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Equipment Details</h3>
            <button id="close-details-modal" class="text-gray-400 hover:text-gray-500">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div id="equipment-details-content" class="p-4 overflow-y-auto flex-1"></div>
          <div class="p-4 border-t">
            <button id="close-details-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 w-full">Close</button>
          </div>
        </div>
      `
      document.body.appendChild(modal)

      // Add event listeners to close modal
      document.getElementById("close-details-modal").addEventListener("click", () => {
        modal.classList.add("hidden")
      })
      document.getElementById("close-details-btn").addEventListener("click", () => {
        modal.classList.add("hidden")
      })
    }

    // Format purchase date
    let formattedPurchaseDate = equipment.purchase_date
    try {
      const purchaseDate = new Date(equipment.purchase_date)
      if (!isNaN(purchaseDate.getTime())) {
        formattedPurchaseDate = purchaseDate.toLocaleDateString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
        })
      }
    } catch (e) {
      console.error("Error formatting date:", e)
    }

    // Determine if equipment is assigned
    const isAssigned = equipment.assignment_status === "Assigned"

    // Populate modal content
    const content = document.getElementById("equipment-details-content")
    content.innerHTML = `
      <div class="space-y-4">
        <div class="flex items-center">
          <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-4">
            <i class="fas ${getCategoryIcon(equipment.category)} text-xl"></i>
          </div>
          <div>
            <h4 class="text-xl font-medium text-gray-900">${equipment.equipment_name || "Unnamed Equipment"}</h4>
            <p class="text-sm text-gray-500">${equipment.category || "Uncategorized"}</p>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">ID</p>
            <p>${equipment.id || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Property Number</p>
            <p>${equipment.property_number || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Purchase Date</p>
            <p>${formattedPurchaseDate || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Units</p>
            <p>${equipment.units || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Account Code</p>
            <p>${equipment.account_code || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">RIS No</p>
            <p>${equipment.ris_no || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">PO/JO No</p>
            <p>${equipment.po_jo_no || "N/A"}</p>
          </div>
          <div class="bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Obligation No</p>
            <p>${equipment.oblig_no || "N/A"}</p>
          </div>
          <div class="col-span-2 bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Description</p>
            <p class="whitespace-pre-line text-sm mt-1">${equipment.description || "No description available"}</p>
          </div>
          <div class="col-span-2 bg-gray-50 p-3 rounded">
            <p class="text-gray-500 text-xs font-medium">Status</p>
            ${
              isAssigned
                ? `<p class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                  <i class="fas fa-user-lock mr-1"></i> Assigned to ${equipment.assigned_to_user_name || "a user"}
                </p>`
                : `<p class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                  <i class="fas fa-check-circle mr-1"></i> Available
                </p>`
            }
          </div>
        </div>
      </div>
    `

    // Show modal
    modal.classList.remove("hidden")
  }

  // Close search results when clicking outside
  document.addEventListener("click", (e) => {
    if (
      !equipmentSearchResults.contains(e.target) &&
      e.target !== equipmentSearchInput &&
      e.target !== searchEquipmentBtn
    ) {
      equipmentSearchResults.classList.add("hidden")
    }

    if (!userSearchResults.contains(e.target) && e.target !== userSearchInput && e.target !== searchUserBtn) {
      userSearchResults.classList.add("hidden")
    }
  })
})


  </script>
</body>
</html>