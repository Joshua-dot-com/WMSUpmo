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
    <title>Request History - Admin Dashboard</title>
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
        
        .btn-primary-light {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid transparent;
        }
        .active {
    background-color: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

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
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  
  @keyframes slideOut {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(10px); opacity: 0; }
  }
  
  /* Apply animations */
  #date-range-modal:not(.hidden) #modal-backdrop {
    animation: fadeIn 0.3s ease forwards;
  }
  
  #date-range-modal:not(.hidden) #modal-panel {
    animation: slideIn 0.3s ease forwards;
  }
  
  #date-range-modal.hiding #modal-backdrop {
    animation: fadeOut 0.3s ease forwards;
  }
  
  #date-range-modal.hiding #modal-panel {
    animation: slideOut 0.3s ease forwards;
  }
  
  /* Fix for date input styling in some browsers */
  input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
  }
  
  input[type="date"]::-webkit-calendar-picker-indicator {
    background: transparent;
    color: transparent;
    cursor: pointer;
    height: 100%;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
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
        <div class="flex-1 ml-64">
            <!-- Header -->
            <header class="border-b bg-white">
                <div class="flex h-16 items-center px-6">
                    <button id="sidebar-toggle" class="md:hidden mr-4">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-2xl font-bold">Request History</h1>
                </div>
            </header>

            
<!-- Main Content -->
<main class="p-6">
    <div class="bg-white rounded-lg border shadow-sm">
        <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-semibold">Access Request History</h2>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="search"
                        placeholder="Search history..."
                        class="pl-8 h-10 w-full md:w-[250px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        id="search-input"
                    />
                </div>
                <button class="btn btn-outline btn-sm" id="filter-button">
                    <i class="fas fa-filter"></i>
                    <span class="sr-only">Filter</span>
                </button>
                <button class="btn btn-outline btn-sm" id="date-range-button">
                    <i class="fas fa-calendar"></i>
                    <span class="sr-only">Date Range</span>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4 flex-wrap">
                <button class="btn btn-primary-light btn-sm filter-btn active" data-status="all">All</button>
                <button class="btn btn-outline btn-sm filter-btn" data-status="granted">Granted</button>
                <button class="btn btn-outline btn-sm filter-btn" data-status="rejected">Rejected</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Name</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Email</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Department</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Request Date</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Decision Date</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Status</th>
                            <th class="h-12 px-4 text-left font-medium text-gray-500">Reviewed By</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body">
                        <!-- Rows will be injected here by JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between py-4">
                <div class="text-sm text-gray-500 mb-4 md:mb-0" id="entries-info">
                    Showing <strong>0-0</strong> of <strong>0</strong> entries
                </div>
                <div class="flex items-center space-x-2" id="pagination-buttons">
                    <button class="btn btn-outline btn-sm" id="prev-page">Previous</button>
                    <button class="btn btn-primary-light btn-sm" id="page-1">1</button>
                    <button class="btn btn-outline btn-sm" id="page-2">2</button>
                    <button class="btn btn-outline btn-sm" id="page-3">3</button>
                    <button class="btn btn-outline btn-sm" id="next-page">Next</button>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</div>

<!-- Date Range Filter Modal -->
<div id="date-range-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <!-- Background overlay with blur -->
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity duration-300 ease-out" id="modal-backdrop"></div>
  
  <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
    <!-- Modal panel -->
    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md"
         id="modal-panel">
      
      <!-- Modal header with gradient -->
      <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
        <h3 class="text-lg font-medium text-white flex items-center" id="modal-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Select Date Range
        </h3>
        <button type="button" class="text-white hover:text-gray-200 transition-colors" id="modal-close">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Modal body -->
      <div class="bg-white px-6 py-5">
        <div class="space-y-5">
          <!-- Start Date -->
          <div>
            <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <input type="date" id="start-date" 
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 sm:text-sm border-gray-300 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                     placeholder="Select start date">
            </div>
          </div>
          
          <!-- End Date -->
          <div>
            <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <input type="date" id="end-date" 
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 sm:text-sm border-gray-300 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                     placeholder="Select end date">
            </div>
          </div>
          
          <!-- Quick Select Options -->
          <div class="pt-2">
            <p class="text-xs text-gray-500 mb-2">Quick Select:</p>
            <div class="flex flex-wrap gap-2">
              <button type="button" class="quick-select-btn text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-days="7">Last 7 days</button>
              <button type="button" class="quick-select-btn text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-days="30">Last 30 days</button>
              <button type="button" class="quick-select-btn text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-days="90">Last 90 days</button>
              <button type="button" class="quick-select-btn text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-range="month">This Month</button>
              <button type="button" class="quick-select-btn text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-range="year">This Year</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal footer -->
      <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
        <div>
          <button type="button" id="modal-reset" 
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset
          </button>
        </div>
        <div class="flex space-x-3">
          <button type="button" id="modal-cancel" 
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            Cancel
          </button>
          <button type="button" id="modal-apply" 
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Apply Filter
          </button>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
   document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

document.addEventListener("DOMContentLoaded", function () {
    const currentPageVar = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPageVar) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });

    // Fetching, Filtering, Searching, and Pagination
    const searchInput = document.querySelector('#search-input');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const historyTableBody = document.querySelector('#history-table-body');
    const paginationButtons = document.getElementById('pagination-buttons').querySelectorAll('button');
    const dateRangeButton = document.getElementById('date-range-button');
    const modal = document.getElementById('date-range-modal');
    const modalCloseButton = document.getElementById('modal-close');
    const modalCancelButton = document.getElementById('modal-cancel');
    const modalApplyButton = document.getElementById('modal-apply');
    const modalResetButton = document.getElementById('modal-reset');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const quickSelectBtns = document.querySelectorAll('.quick-select-btn');

    let requestData = [];
    let filteredData = [];
    let currentPage = 1;
    let totalPages = 1;
    let activeFilter = { status: 'all', search: '', dateSort: '', startDate: '', endDate: '' };

    const fetchRequestData = (page = 1) => {
        let url = `fetch_history.php?page=${page}&status=${activeFilter.status}&start_date=${activeFilter.startDate}&end_date=${activeFilter.endDate}`;
        if (searchInput.value) {
            url += `&search=${searchInput.value}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    requestData = data.data;
                    filteredData = [...requestData];
                    totalPages = data.totalPages || 1;
                    renderTable();
                } else {
                    console.error('Error fetching data:', data.message);
                    showToast('error', 'Error', data.message || 'Failed to fetch data');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                showToast('error', 'Error', 'Failed to connect to the server');
            });
    };

    const renderTable = () => {
        historyTableBody.innerHTML = '';

        // Update entries info dynamically
        const startEntry = (currentPage - 1) * 7 + 1;
        const endEntry = Math.min(currentPage * 7, filteredData.length);
        const totalEntries = filteredData.length;
        const entriesInfo = document.getElementById('entries-info');
        entriesInfo.innerHTML = `Showing <strong>${startEntry}-${endEntry}</strong> of <strong>${totalEntries}</strong> entries`;
        
        if (filteredData.length === 0) {
            const tr = document.createElement('tr');
            tr.classList.add('border-b');
            tr.innerHTML = `
                <td colspan="7" class="p-4 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center py-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">No records found</p>
                        <p class="text-sm text-gray-400">Try adjusting your search or filter criteria</p>
                    </div>
                </td>
            `;
            historyTableBody.appendChild(tr);
        } else {
            filteredData.forEach(request => {
                const tr = document.createElement('tr');
                tr.classList.add('border-b', 'hover:bg-gray-50', 'transition-colors', 'duration-150');
                tr.innerHTML = `
    <td class="p-4 font-medium">${request.name}</td>
    <td class="p-4">${request.email}</td>
    <td class="p-4">${request.department}</td>
    <td class="p-4">${request.request_date}</td>
    <td class="p-4">${request.decision_date}</td>
    <td class="p-4">
        <span class="px-2 py-1 rounded-full text-sm font-medium 
            ${request.status === 'Granted' 
                ? 'bg-green-100 text-green-800' 
                : request.status === 'Rejected' 
                    ? 'bg-red-100 text-red-800' 
                    : 'bg-gray-100 text-gray-800'}">
            ${request.status}
        </span>
    </td>
    <td class="p-4">${request.reviewed_by || "N/A"}</td>
`;

                historyTableBody.appendChild(tr);
            });
        }

        updatePagination();
        highlightActiveFilters();
        updateDateRangeButton();
    };

    const highlightActiveFilters = () => {
        filterButtons.forEach(button => button.classList.remove('bg-green-500', 'text-white', 'border-none'));
        const activeButton = [...filterButtons].find(button => button.dataset.status === activeFilter.status);
        if (activeButton) activeButton.classList.add('bg-green-500', 'text-white', 'border-none');
    };

    const updatePagination = () => {
        paginationButtons.forEach(button => {
            const pageNumber = parseInt(button.id.split('-')[1]);
            if (pageNumber === currentPage) {
                button.classList.add('bg-green-500', 'text-white');
                button.classList.remove('bg-gray-200', 'text-gray-500');
            } else {
                button.classList.add('bg-gray-200', 'text-gray-500');
                button.classList.remove('bg-green-500', 'text-white');
            }
        });
    };

    // Update date range button to show active filter
    const updateDateRangeButton = () => {
        if (activeFilter.startDate || activeFilter.endDate) {
            dateRangeButton.classList.add('bg-blue-100', 'text-blue-700', 'border-blue-300');
            
            // Format dates for display
            let displayText = 'Date: ';
            if (activeFilter.startDate && activeFilter.endDate) {
                displayText += formatDisplayDate(activeFilter.startDate) + ' - ' + formatDisplayDate(activeFilter.endDate);
            } else if (activeFilter.startDate) {
                displayText += 'From ' + formatDisplayDate(activeFilter.startDate);
            } else if (activeFilter.endDate) {
                displayText += 'Until ' + formatDisplayDate(activeFilter.endDate);
            }
            
            // Update button text if it has a span child
            const buttonSpan = dateRangeButton.querySelector('span');
            if (buttonSpan) {
                buttonSpan.textContent = displayText;
            }
        } else {
            dateRangeButton.classList.remove('bg-blue-100', 'text-blue-700', 'border-blue-300');
            
            // Reset button text if it has a span child
            const buttonSpan = dateRangeButton.querySelector('span');
            if (buttonSpan) {
                buttonSpan.textContent = 'Date Range';
            }
        }
    };

    // Format date for display (MM/DD/YYYY)
    const formatDisplayDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
    };

    // Format date for input fields (YYYY-MM-DD)
    const formatInputDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // Event listener for search input
    searchInput.addEventListener('input', (event) => {
        activeFilter.search = event.target.value.toLowerCase();
        filteredData = requestData.filter(request => {
            return request.name.toLowerCase().includes(activeFilter.search) ||
                request.email.toLowerCase().includes(activeFilter.search) ||
                request.department.toLowerCase().includes(activeFilter.search);
        });
        renderTable();
    });

    // Event listener for filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const status = event.target.dataset.status;
            activeFilter.status = status === 'all' ? 'all' : status;
            currentPage = 1;  // Reset to first page when filter is changed
            fetchRequestData(currentPage);
        });
    });

    // Open modal for date range selection with animation
    dateRangeButton.addEventListener('click', () => {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
        
        // Set current values if they exist
        if (activeFilter.startDate) {
            startDateInput.value = activeFilter.startDate;
        }
        if (activeFilter.endDate) {
            endDateInput.value = activeFilter.endDate;
        }
    });

    // Function to close modal with animation
    const closeModal = () => {
        if (modal.classList.contains('hiding')) return;
        
        modal.classList.add('hiding');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('hiding');
            document.body.style.overflow = ''; // Re-enable scrolling
        }, 300);
    };

    // Apply date range and close modal
    modalApplyButton.addEventListener('click', () => {
        activeFilter.startDate = startDateInput.value;
        activeFilter.endDate = endDateInput.value;
        currentPage = 1; // Reset to first page when date range is applied
        
        // Show loading state on button
        modalApplyButton.disabled = true;
        const originalText = modalApplyButton.innerHTML;
        modalApplyButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Applying...
        `;
        
        fetchRequestData(currentPage);
        
        // Reset button and close modal after fetch
        setTimeout(() => {
            modalApplyButton.innerHTML = originalText;
            modalApplyButton.disabled = false;
            closeModal();
        }, 500);
    });

    // Cancel button to close the modal
    modalCancelButton.addEventListener('click', closeModal);
    
    // Close button to close the modal
    if (modalCloseButton) {
        modalCloseButton.addEventListener('click', closeModal);
    }

    // Reset the date range filter
    modalResetButton.addEventListener('click', () => {
        startDateInput.value = ''; // Clear the start date
        endDateInput.value = ''; // Clear the end date
    });

    // Quick select date range functionality
    if (quickSelectBtns) {
        quickSelectBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const days = this.getAttribute('data-days');
                const range = this.getAttribute('data-range');
                
                const today = new Date();
                let startDate = new Date();
                
                if (days) {
                    // Set start date to X days ago
                    startDate.setDate(today.getDate() - parseInt(days));
                } else if (range === 'month') {
                    // Set to first day of current month
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                } else if (range === 'year') {
                    // Set to first day of current year
                    startDate = new Date(today.getFullYear(), 0, 1);
                }
                
                // Format dates for input fields
                startDateInput.value = formatInputDate(startDate);
                endDateInput.value = formatInputDate(today);
                
                // Highlight the selected button
                quickSelectBtns.forEach(b => b.classList.remove('bg-blue-200', 'text-blue-700'));
                this.classList.add('bg-blue-200', 'text-blue-700');
            });
        });
    }

    // Close modal when user clicks outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Event listener for pagination buttons
    paginationButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const pageNumber = parseInt(e.target.id.split('-')[1]);
            if (pageNumber) {
                currentPage = pageNumber;
                fetchRequestData(currentPage);
            }
        });
    });

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
        } else if (type === 'warning') {
            toast.classList.add('bg-yellow-50', 'text-yellow-800', 'border-l-4', 'border-yellow-500');
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

    // Add keyboard support (Escape to close modal)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Initialize the first page load
    fetchRequestData(currentPage);
});
</script>


</body>
</html>