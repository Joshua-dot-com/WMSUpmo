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
<title>Equipment Assignment History - Admin Dashboard</title>
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
    
    .badge-purple {
        background-color: #8b5cf6;
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
    
    .btn-ghost {
        background-color: transparent;
        color: #6b7280;
    }
    
    .btn-ghost:hover {
        background-color: #f9fafb;
        color: #111827;
    }
    
    .btn-primary-light {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid transparent;
    }
    
    .stat-card {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
        border-radius: 0.5rem;
        background-color: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }
    
    .stat-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-top: 0.5rem;
    }
    
    .stat-desc {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
    
    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .modal.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 0.5rem;
        width: 100%;
        max-width: 700px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }
    
    .modal.active .modal-content {
        transform: scale(1);
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
        <div class="flex h-16 items-center justify-between px-6">
            <div class="flex items-center">
                <button id="sidebar-toggle" class="md:hidden mr-4 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <h1 class="text-2xl font-bold text-gray-800">Equipment History</h1>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm text-gray-500">Total Equipment</div>
                        <div class="text-2xl font-semibold text-gray-800" id="stat-total-equipment">—</div>
                        <div class="text-xs text-gray-400" style="display: none;">Currently assigned: <span id="stat-currently-assigned">—</span></div>
                        <div class="text-xs text-gray-400">Equipments</div>
                    </div>
                    <div class="bg-green-100 text-green-600 rounded-full p-3">
                        <i class="fas fa-laptop text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm text-gray-500">Transfers</div>
                        <div class="text-2xl font-semibold text-gray-800" id="stat-transfers">—</div>
                        <div class="text-xs text-gray-400">Equipment Transfers</div>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg border shadow-sm mb-6">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Search & Filter</h2>
            </div>
            <div class="p-6">
                <form id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="equipment-type" class="block text-sm font-medium text-gray-700 mb-1">Equipment Type</label>
                        <select id="equipment-type" class="w-full rounded-md border-gray-300 px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                            <option value="">All Types</option>
                        </select>
                    </div>

                    <div>
                        <label for="assignment-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="assignment-status" class="w-full rounded-md border-gray-300 px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="returned">Returned</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>

                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">Assigned From</label>
                        <input type="date" id="date-from" class="w-full rounded-md border-gray-300 px-3 py-2 text-sm focus:ring-primary focus:border-primary" />
                    </div>

                    <div class="lg:col-span-3">
                        <label for="search-term" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input
                                type="search"
                                id="search-term"
                                placeholder="Search by user name, equipment ID, or notes..."
                                class="pl-10 h-10 w-full rounded-md border-gray-300 text-sm placeholder-gray-500 focus:ring-primary focus:border-primary"
                            />
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center bg-primary text-white px-4 py-2 rounded-md shadow hover:bg-primary-dark transition">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

       <!-- Equipment Assignment History Table -->
<div class="bg-white rounded-lg border shadow-sm">
    <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-800">Equipment Assignments</h2>
        <div class="flex flex-col md:flex-row items-start md:items-center justify-end gap-4 md:gap-6">
            <!-- Records Per Page -->
            <div class="flex items-center space-x-2">
                <label for="records-per-page" class="text-sm text-gray-500">Show</label>
                <select id="records-per-page" class="text-sm border-gray-300 rounded p-1">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm text-gray-500">records per page</span>
            </div>

            <!-- Pagination Buttons Moved Here -->
            <div id="pagination-controls" class="flex flex-wrap items-center gap-2"></div>
        </div>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-100">
                        <th class="h-12 px-4 text-left font-medium text-gray-500">User</th>
                        <th class="h-12 px-4 text-left font-medium text-gray-500">Department</th>
                        <th class="h-12 px-4 text-left font-medium text-gray-500">Equipment</th>
                        <th class="h-12 px-4 text-left font-medium text-gray-500">Type</th>
                        <th class="h-12 px-4 text-left font-medium text-gray-500">Assigned Date</th>
                        <th class="h-12 px-4 text-left font-medium text-gray-500">Status</th>
                        <th class="h-12 px-4 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium">Sophia Garcia</td>
                        <td class="p-4">Marketing</td>
                        <td class="p-4">iPhone 14 (PHN-2022-145)</td>
                        <td class="p-4">Phone</td>
                        <td class="p-4">Nov 25, 2024</td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">Lost</span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-primary hover:underline text-sm view-details" data-id="ASN-2024-078">View Details</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Record Count Only -->
        <div id="pagination-section" class="pt-4">
            <div id="record-count" class="text-sm text-gray-500">
                Showing <strong>0–0</strong> of <strong>0</strong> records
            </div>
        </div>
    </div>
</div>

    </main>
</div>



<!-- Assignment Details Modal -->
<div id="assignmentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-xl rounded-lg shadow-lg relative">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Assignment Details</h3>
            <button id="closeModal" class="text-gray-500 hover:text-red-500">&times;</button>
        </div>
        <div class="p-6" id="modalContent">
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    }

    // Highlight active nav link
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });

    // Stats Overview AJAX
    fetch('get-equipment-stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            document.getElementById('stat-total-equipment').textContent = data.total_equipment;
            document.getElementById('stat-currently-assigned').textContent = data.currently_assigned;
            document.getElementById('stat-transfers').textContent = data.transfers_30_days;
        })
        .catch(err => console.error('Failed to load stats:', err));

    // Filter + Table + Pagination
    const filterForm = document.getElementById('filter-form');
    const equipmentTypeSelect = document.getElementById('equipment-type');
    const assignmentStatusSelect = document.getElementById('assignment-status');
    const dateFromInput = document.getElementById('date-from');
    const searchTermInput = document.getElementById('search-term');
    const recordsPerPageSelect = document.getElementById('records-per-page');
    const equipmentTable = document.querySelector('table tbody');
    const paginationContainer = document.getElementById('pagination-controls'); // Updated selector
    const recordCountDisplay = document.getElementById('record-count');
    let recordsPerPage = parseInt(recordsPerPageSelect.value);

    function fetchEquipmentTypes() {
        fetch('get-equipment-types.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.name;
                    equipmentTypeSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading equipment types:', error));
    }

    function loadEquipmentData(page = 1) {
        const filters = {
            equipment_type: equipmentTypeSelect.value,
            status: assignmentStatusSelect.value,
            date_from: dateFromInput.value,
            search_term: searchTermInput.value,
            sort_order: 'desc',
            page: page,
            limit: recordsPerPage
        };

        const queryString = new URLSearchParams(filters).toString();
        fetch(`get-equipment-data.php?${queryString}`)
            .then(response => response.json())
            .then(data => {
                renderEquipmentTable(data.records);
                renderPagination(data.totalRecords, page);
            })
            .catch(error => console.error('Error loading equipment data:', error));
    }

    function renderEquipmentTable(records) {
        equipmentTable.innerHTML = '';
        records.forEach(record => {
            const row = document.createElement('tr');
            row.classList.add('hover:bg-gray-50');

            row.innerHTML = `
                <td class="p-4 font-medium">${record.user_name}</td>
                <td class="p-4">${record.department}</td>
                <td class="p-4">${record.equipment}</td>
                <td class="p-4">${record.type}</td>
                <td class="p-4">${record.assigned_date}</td>
                <td class="p-4">
                    <span class="badge badge-${record.status === 'Lost' ? 'destructive' : 'success'}">${record.status}</span>
                </td>
                <td class="p-4 text-right">
                    <button class="btn btn-ghost btn-sm view-details" data-id="${record.assignment_id}">View Details</button>
                </td>
            `;

            equipmentTable.appendChild(row);
        });
    }

    function renderPagination(totalRecords, currentPage) {
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        paginationContainer.innerHTML = '';

        // Update text display
        const startRecord = (currentPage - 1) * recordsPerPage + 1;
        const endRecord = Math.min(currentPage * recordsPerPage, totalRecords);
        if (recordCountDisplay) {
            recordCountDisplay.innerHTML = `Showing <strong>${startRecord}</strong>–<strong>${endRecord}</strong> of <strong>${totalRecords}</strong> records`;
        }

        const prevButton = document.createElement('button');
        prevButton.classList.add('btn', 'btn-outline', 'btn-sm');
        prevButton.textContent = 'Previous';
        prevButton.disabled = currentPage <= 1;
        prevButton.dataset.page = currentPage - 1;
        paginationContainer.appendChild(prevButton);

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.classList.add('btn', 'btn-outline', 'btn-sm');
            pageButton.textContent = i;
            pageButton.dataset.page = i;
            if (i === currentPage) pageButton.classList.add('btn-primary-light');
            paginationContainer.appendChild(pageButton);
        }

        const nextButton = document.createElement('button');
        nextButton.classList.add('btn', 'btn-outline', 'btn-sm');
        nextButton.textContent = 'Next';
        nextButton.disabled = currentPage >= totalPages;
        nextButton.dataset.page = currentPage + 1;
        paginationContainer.appendChild(nextButton);
    }

    // Modal event delegation
    document.body.addEventListener('click', async function (e) {
        if (e.target.classList.contains('view-details')) {
            const assignmentId = e.target.getAttribute('data-id');
            const modal = document.getElementById('assignmentModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            content.innerHTML = '<p class="text-gray-500">Loading...</p>';

            try {
                const response = await fetch(`view-assignment.php?id=${assignmentId}`);
                const html = await response.text();
                content.innerHTML = html;
            } catch (error) {
                content.innerHTML = '<p class="text-red-500">Failed to load assignment details.</p>';
            }
        }

        // Modal close
        if (e.target.id === 'closeModal' || e.target.id === 'assignmentModal') {
            document.getElementById('assignmentModal').classList.add('hidden');
        }
    });

    // Initial setup
    fetchEquipmentTypes();
    loadEquipmentData();

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        loadEquipmentData();
    });

    recordsPerPageSelect.addEventListener('change', function () {
        recordsPerPage = parseInt(this.value);
        loadEquipmentData(1); // Reset to first page
    });

    paginationContainer.addEventListener('click', function (e) {
        if (e.target.tagName === 'BUTTON' && e.target.dataset.page) {
            const page = parseInt(e.target.dataset.page);
            loadEquipmentData(page);
        }
    });
});
</script>


</body>
</html>

