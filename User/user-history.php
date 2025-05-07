<?php
session_start(); // Start the session

// Include your database connection file
require 'db_connect.php';

$email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // ✅ Save user session
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

                $_SESSION['user_id'] = $user['id']; // for easier access

                // ✅ Fetch assigned equipment and store in session
                $equipment_stmt = $conn->prepare("
                    SELECT 
                        e.id,
                        e.equipment_name,
                        e.description,
                        e.property_number,
                        e.status,
                        ue.assigned_at,
                        ue.returned_at,
                        ue.notes
                    FROM user_equipment ue
                    JOIN equipment e ON ue.equipment_id = e.id
                    WHERE ue.user_id = ?
                ");
                $equipment_stmt->bind_param("i", $user['id']);
                $equipment_stmt->execute();
                $equipment_result = $equipment_stmt->get_result();

                $assigned_equipment = [];
                while ($row = $equipment_result->fetch_assoc()) {
                    $assigned_equipment[] = $row;
                }

                $_SESSION['assigned_equipment'] = $assigned_equipment;

                // Redirect based on user role
                if ($user['is_admin'] == 1) {
                    header("Location: http://localhost/PMO/Administration/Admin-Dashboard.php");
                } else {
                    header("Location: http://localhost/PMO/User/user-profile.php");
                }
                exit;
            } else {
                $error = "Incorrect password!";
            }
        } else {
            header("Location: http://localhost/PMO/Login.php");
            exit;
        }

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
<div class="flex-1 overflow-auto ml-64 bg-gray-50 min-h-screen">
  <div class="p-8 space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-3xl font-bold text-gray-800">My Equipment History</h2>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Currently Assigned -->
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
          <i class="fas fa-laptop text-2xl"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Currently Assigned</p>
          <p class="text-2xl font-semibold" id="currently-assigned-count">0 Items</p>
        </div>
      </div>

      <!-- Total Assignments -->
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <div class="p-3 bg-green-100 text-green-600 rounded-full">
          <i class="fas fa-exchange-alt text-2xl"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Total Assignments</p>
          <p class="text-2xl font-semibold" id="total-assignments-count">0 Items</p>
        </div>
      </div>

      <!-- Returned Items -->
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
          <i class="fas fa-undo-alt text-2xl"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500">Returned Items</p>
          <p class="text-2xl font-semibold" id="returned-items-count">0 Items</p>
        </div>
      </div>
    </div>

    <!-- Currently Assigned Equipment -->
    <div class="bg-white rounded-2xl shadow p-6">
      <h3 class="text-xl font-semibold text-gray-800 mb-4">Currently Assigned Equipment</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700" id="current-equipment-table">
          <thead class="bg-gray-200 text-gray-600 uppercase text-xs">
            <tr>
              <th class="p-3">Equipment ID</th>
              <th class="p-3">Name</th>
              <th class="p-3">Category</th>
              <th class="p-3">Assigned Date</th>
              <th class="p-3">Status</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody id="current-equipment-body">
            <!-- Populated by JavaScript -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Equipment History -->
    <div class="bg-white rounded-2xl shadow p-6">
      <h3 class="text-xl font-semibold text-gray-800 mb-4">Equipment History</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700" id="equipment-history-table">
          <thead class="bg-gray-200 text-gray-600 uppercase text-xs">
          <tr>
  <th class="p-3">Equipment ID</th>
  <th class="p-3">Name</th>
  <th class="p-3">Assigned Date</th>
  <th class="p-3">Return/Transfer Date</th>
  <th class="p-3">Status</th>
  <th class="p-3">Return Notes</th> <!-- Added Return Notes column -->
</tr>

          </thead>
          <tbody id="equipment-history-body">
            <!-- Populated by JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center items-center space-x-2" id="pagination">
        <button id="prev-page" class="px-4 py-2 text-sm rounded-md border bg-gray-100 text-gray-400 cursor-not-allowed" disabled>Previous</button>
        <button id="page-1" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-200">1</button>
        <button id="page-2" style="display: none;" class="px-4 py-2 text-sm rounded-md border bg-white hover:bg-gray-200">2</button>
        <button id="next-page" class="px-4 py-2 text-sm rounded-md border bg-gray-100 text-gray-400 cursor-not-allowed" disabled>Next</button>
      </div>
    </div>
  </div>
</div>

<!-- Equipment Details Modal -->
<div id="equipment-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
    <h3 class="text-xl font-semibold mb-4">Equipment Details</h3>
    <div id="modal-content" class="space-y-2 text-sm text-gray-700">
      <p><strong>ID:</strong> <span id="modal-id"></span></p>
      <p><strong>Name:</strong> <span id="modal-name"></span></p>
      <p><strong>Category:</strong> <span id="modal-category"></span></p>
      <p><strong>Assigned Date:</strong> <span id="modal-assigned"></span></p>
      <p><strong>Status:</strong> <span id="modal-status"></span></p>
      <p><strong>Return/Transfer Date:</strong> <span id="modal-return"></span></p>
      <p><strong>Notes:</strong> <span id="modal-notes"></span></p>
    </div>
    <div class="mt-6 text-right">
      <button id="close-modal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Close</button>
    </div>
  </div>
</div>

<!-- Return Confirmation Modal -->
<div id="returnModal" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-sm shadow-xl">
    <h3 class="text-lg font-semibold mb-4">Return Equipment</h3>
    <p class="text-gray-700">Are you sure you want to return this equipment?</p>
    <div class="mt-6 flex justify-end space-x-2">
      <button id="cancelReturnButton" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-gray-700">Cancel</button>
      <button id="confirmReturnButton" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Return</button>
    </div>
  </div>
</div>

<!-- Reason Modal -->
<div id="reason-modal" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl">
    <h3 class="text-xl font-semibold mb-4">Select a Reason for Return</h3>

    <!-- Reason Selection -->
    <div class="space-y-4">
      <select id="reason-select" class="w-full p-3 border rounded-md">
        <option value="">Select a reason</option>
        <option value="no-longer-needed">No longer needed</option>
        <option value="damaged">Damaged</option>
        <option value="wrong-item">Wrong item assigned</option>
        <option value="other">Other</option>
      </select>

      <!-- Other Reason Input -->
      <div id="other-reason-container" class="hidden">
        <textarea id="other-reason" class="w-full p-3 border rounded-md" placeholder="Please specify the reason"></textarea>
      </div>
    </div>

    <!-- Modal Actions -->
    <div class="mt-6 flex justify-end space-x-2">
      <button id="cancelReturnReasonButton" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-gray-700">Cancel</button>
      <button id="confirmReturnReasonButton" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Confirm</button>
    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
 // Highlight current sidebar link with red theme
 const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
        // Add hover effect to all links
        link.classList.add("transition-colors", "duration-200", "hover:bg-red-700", "hover:text-white");
        
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("bg-red-600", "text-white", "border-l-4", "border-red-800", "shadow-md");
        }
    });

  // Initialize the page
  fetchAndUpdateStats();
  fetchAndPopulateCurrentEquipment();
  fetchAndPopulateEquipmentHistory(currentPage);
  setupEventListeners();
});

// Global variables
let currentPage = 1;
const itemsPerPage = 5;
let totalPages = 0;
let totalItems = 0;

// Fetch and update stats
async function fetchAndUpdateStats() {
  try {
    const response = await fetch('get_equipment_stats.php');
    if (!response.ok) throw new Error('Network response was not ok');
    const stats = await response.json();

    // Ensure stats object is structured correctly
    const currentlyAssigned = stats.stats?.currently_assigned ?? 0;
    const totalAssignments = stats.stats?.total_assignments ?? 0;
    const returnedItems = stats.stats?.returned_items ?? 0;

    // Update the stats on the page
    document.getElementById('currently-assigned-count').textContent = `${currentlyAssigned} Items`;
    document.getElementById('total-assignments-count').textContent = `${totalAssignments} Items`;
    document.getElementById('returned-items-count').textContent = `${returnedItems} Items`;
  } catch (error) {
    console.error('Error fetching stats:', error);
    showErrorMessage('stats-error', 'Unable to load statistics. Please try again later.');
  }
}


// Fetch and populate current equipment
async function fetchAndPopulateCurrentEquipment() {
  try {
    const response = await fetch('get_current_equipment.php');
    if (!response.ok) throw new Error('Network response was not ok');
    const data = await response.json();

    const currentEquipment = data.equipment;
    const tableBody = document.getElementById('current-equipment-body');
    tableBody.innerHTML = '';

    // If no equipment or empty array, display a message
    if (!Array.isArray(currentEquipment) || currentEquipment.length === 0) {
      const row = document.createElement('tr');
      row.innerHTML = `<td colspan="8" class="p-3 text-center text-gray-500">No equipment currently assigned</td>`;
      tableBody.appendChild(row);
      return;
    }

    // Populate the table with the new data, displaying po_jo_no while retaining equipment_id in the payload
    currentEquipment.forEach(item => {
      const row = document.createElement('tr');
      row.className = 'border-b hover:bg-gray-50';
      row.innerHTML = `
        <td class="p-3">${item.po_jo_no ?? '-'}</td> <!-- Display po_jo_no -->
        <td class="p-3">${item.equipment_name ?? '-'}</td> <!-- Display equipment name -->
        <td class="p-3">${item.category ?? '-'}</td> <!-- Display category -->
        <td class="p-3">${formatDate(item.assigned_at)}</td> <!-- Display assigned date -->
        <td class="p-3">
          <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(item.equipment_status)}">${item.equipment_status ?? '-'}</span>
        </td> <!-- Display equipment status -->
        <td class="p-3">
          <button class="return-button text-red-600 hover:text-red-800 mr-2" data-id="${item.equipment_id}">
            <i class="fas fa-arrow-left"></i> Return
          </button>
        </td> <!-- Return button -->
        <td style="display: none;" class="equipment-id">${item.equipment_id}</td> <!-- Hidden equipment_id -->
      `;
      tableBody.appendChild(row);
    });

    // Add event listeners for return buttons
    document.querySelectorAll('.return-button').forEach(button => {
      button.addEventListener('click', (e) => {
        const equipmentId = e.target.getAttribute('data-id');
        showReturnModal(equipmentId);
      });
    });

  } catch (error) {
    console.error('Error fetching current equipment:', error);
    showErrorMessage('current-equipment-error', 'Unable to load current equipment. Please try again later.');
  }
}

// Helper function to format date (assuming you have one or use any suitable formatting)
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleString(); // Modify as needed for your date format
}

// Helper function to get status class (assuming you have one or use any suitable status check)
function getStatusClass(status) {
  if (status === 'Active') return 'bg-green-200 text-green-800'; // Example status styling
  return 'bg-gray-200 text-gray-800'; // Default status styling
}

// Helper function to show error message (optional)
function showErrorMessage(elementId, message) {
  const element = document.getElementById(elementId);
  if (element) {
    element.innerHTML = message;
  }
}


// Show the return equipment confirmation modal
function showReturnModal(equipmentId) {
  const returnModal = document.getElementById('returnModal');
  const confirmReturnButton = document.getElementById('confirmReturnButton');
  const cancelReturnButton = document.getElementById('cancelReturnButton');

  // Show the modal
  returnModal.classList.remove('hidden');

  // Confirm return action
  confirmReturnButton.onclick = () => {
    returnEquipment(equipmentId);
    returnModal.classList.add('hidden');
  };

  // Cancel return action
  cancelReturnButton.onclick = () => {
    returnModal.classList.add('hidden');
  };
}

// Handle the equipment return logic (AJAX call)
// Handle the equipment return logic (AJAX call with reason modal)
async function returnEquipment(equipmentId) {
  // Open the reason modal
  const reasonModal = document.getElementById('reason-modal');
  reasonModal.classList.remove('hidden');

  // Show "Other" input when "Other" is selected
  const reasonSelect = document.getElementById('reason-select');
  const otherReasonContainer = document.getElementById('other-reason-container');

  reasonSelect.addEventListener('change', () => {
    if (reasonSelect.value === 'other') {
      otherReasonContainer.classList.remove('hidden');
    } else {
      otherReasonContainer.classList.add('hidden');
    }
  });

  // Confirm return after reason is selected
  const confirmButton = document.getElementById('confirmReturnReasonButton');
  confirmButton.addEventListener('click', async () => {
    const selectedReason = reasonSelect.value === 'other' ? document.getElementById('other-reason').value.trim() : reasonSelect.value;

    // Validate reason input
    if (!selectedReason) {
      alert('Please select or specify a reason for returning the equipment.');
      return;
    }

    try {
      const response = await fetch('return_equipment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ equipment_id: equipmentId, reason: selectedReason })
      });

      const result = await response.json();
      if (result.success) {
        alert('Equipment returned successfully');
        fetchAndPopulateCurrentEquipment(); // Refresh the table
      } else {
        alert('Error returning equipment');
      }
    } catch (error) {
      console.error('Error returning equipment:', error);
      alert('Unable to return equipment. Please try again later.');
    } finally {
      // Close the modal after action
      reasonModal.classList.add('hidden');
    }
  });

  // Close the modal without action
  const cancelButton = document.getElementById('cancelReturnReasonButton');
  cancelButton.addEventListener('click', () => {
    reasonModal.classList.add('hidden');
  });
}



// Fetch and populate equipment history
async function fetchAndPopulateEquipmentHistory(page) {
  try {
    const response = await fetch(`get_equipment_history.php?page=${page}&items_per_page=${itemsPerPage}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const data = await response.json();

    const historyItems = Array.isArray(data.equipment) ? data.equipment : []; // <- updated key
    totalItems = data.totalItems ?? historyItems.length;
    totalPages = Math.ceil(totalItems / itemsPerPage);

    const tableBody = document.getElementById('equipment-history-body');
    tableBody.innerHTML = '';

    if (historyItems.length === 0) {
      const row = document.createElement('tr');
      row.innerHTML = `<td colspan="7" class="p-3 text-center text-gray-500">No equipment history found</td>`;  <!-- Adjusted colspan -->
      tableBody.appendChild(row);
      updatePaginationInfo();
      updatePaginationButtons();
      return;
    }

    historyItems.forEach(item => {
      const row = document.createElement('tr');
      row.className = 'border-b hover:bg-gray-50';
      row.innerHTML = `
        <td class="p-3">${item.po_jo_no ?? '-'}</td> <!-- PO/JO No -->
        <td class="p-3">${item.equipment_name ?? '-'}</td> <!-- Equipment Name -->
        <td class="p-3">${formatDate(item.assigned_at)}</td> <!-- Assigned Date -->
        <td class="p-3">${formatDate(item.returned_at)}</td> <!-- Returned Date -->
        <td class="p-3">
          <span class="px-2 py-1 rounded-full text-xs ${getStatusClass('Returned')}">Returned</span>
        </td>
        <td class="p-3">${item.return_notes ?? '-'}</td> <!-- Return Notes -->  <!-- Added this line -->
      `;
      row.addEventListener('click', () => fetchAndShowEquipmentDetails(item.user_equipment_id));
      tableBody.appendChild(row);
    });

    updatePaginationInfo();
    updatePaginationButtons();
  } catch (error) {
    console.error('Error fetching equipment history:', error);
    showErrorMessage('history-error', 'Unable to load equipment history. Please try again later.');
  }
}




// Update pagination info
function updatePaginationInfo() {
  const paginationInfo = document.getElementById('pagination-info');
  if (paginationInfo) {
    paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
  }
}

// Update pagination buttons
function updatePaginationButtons() {
  const prevButton = document.getElementById('prev-page');
  const nextButton = document.getElementById('next-page');
  const page1Button = document.getElementById('page-1');
  const page2Button = document.getElementById('page-2');

  // Check if elements exist before manipulating them
  if (prevButton) {
    prevButton.disabled = currentPage === 1;
    prevButton.className = currentPage === 1
      ? 'px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed'
      : 'px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200';
  }

  if (nextButton) {
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
    nextButton.className = currentPage === totalPages || totalPages === 0
      ? 'px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed'
      : 'px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200';
  }

  if (page1Button) {
    page1Button.textContent = '1';
    page1Button.className = currentPage === 1
      ? 'px-3 py-1 border rounded bg-blue-600 text-white'
      : 'px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200';
  }

  if (page2Button) {
    if (totalPages > 1) {
      page2Button.textContent = '2';
      page2Button.style.display = 'block';
      page2Button.className = currentPage === 2
        ? 'px-3 py-1 border rounded bg-blue-600 text-white'
        : 'px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200';
    } else {
      page2Button.style.display = 'none';
    }
  }
}


function setupEventListeners() {
  const prevButton = document.getElementById('prev-page');
  const nextButton = document.getElementById('next-page');
  const page1Button = document.getElementById('page-1');
  const page2Button = document.getElementById('page-2');
  const closeModalButton = document.getElementById('close-modal');

  // Check if the elements exist before adding event listeners
  if (prevButton) {
    prevButton.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        fetchAndPopulateEquipmentHistory(currentPage);
      }
    });
  }

  if (nextButton) {
    nextButton.addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        fetchAndPopulateEquipmentHistory(currentPage);
      }
    });
  }

  if (page1Button) {
    page1Button.addEventListener('click', () => {
      currentPage = 1;
      fetchAndPopulateEquipmentHistory(currentPage);
    });
  }

  if (page2Button) {
    page2Button.addEventListener('click', () => {
      currentPage = 2;
      fetchAndPopulateEquipmentHistory(currentPage);
    });
  }

  if (closeModalButton) {
    closeModalButton.addEventListener('click', () => {
      document.getElementById('equipment-modal').classList.add('hidden');
    });
  }

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('view-details') || e.target.closest('.view-details')) {
      const button = e.target.closest('.view-details');
      const itemId = button.getAttribute('data-id');
      fetchAndShowEquipmentDetails(itemId);
    }
  });

  document.getElementById('equipment-modal').addEventListener('click', (e) => {
    if (e.target.id === 'equipment-modal') {
      document.getElementById('equipment-modal').classList.add('hidden');
    }
  });
}

// Fetch and show equipment details
async function fetchAndShowEquipmentDetails(equipmentId) {
  try {
    const response = await fetch(`get_equipment_details.php?id=${equipmentId}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const item = await response.json();

    document.getElementById('modal-id').textContent = item.id ?? '-';
    document.getElementById('modal-name').textContent = item.name ?? '-';
    document.getElementById('modal-category').textContent = item.category ?? '-';
    document.getElementById('modal-status').textContent = item.status ?? '-';
    document.getElementById('modal-assigned-date').textContent = formatDate(item.assignedDate);
    document.getElementById('modal-return-date').textContent = item.returnDate ? formatDate(item.returnDate) : 'N/A';
    document.getElementById('modal-notes').textContent = item.notes ?? 'N/A';

    document.getElementById('equipment-modal').classList.remove('hidden');
  } catch (error) {
    console.error('Error fetching equipment details:', error);
    showErrorMessage('equipment-details-error', 'Unable to load equipment details. Please try again later.');
  }
}

function showErrorMessage(elementId, message) {
  const errorMessageElement = document.getElementById(elementId);
  if (errorMessageElement) {
    errorMessageElement.textContent = message;
    errorMessageElement.classList.remove('hidden');
  }
}

function formatDate(date) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(date).toLocaleDateString(undefined, options);
}

function getStatusClass(status) {
  switch (status) {
    case 'Assigned':
      return 'bg-green-100 text-green-600';
    case 'Returned':
      return 'bg-blue-100 text-blue-600';
    default:
      return 'bg-gray-100 text-gray-600';
  }
}

</script>
</body>
</html>

