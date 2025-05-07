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
    <title>Pending Approvals - Admin Dashboard</title>
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
        
        .btn-primary-light {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid transparent;
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
            <h1 class="text-2xl font-bold">Account Approvals</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
        <div class="bg-white rounded-lg border shadow-sm">
            <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-lg font-semibold">Pending Account Approvals</h2>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input
                            type="search"
                            id="searchInput"
                            placeholder="Search accounts..."
                            class="pl-8 h-10 w-full md:w-[250px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        />
                    </div>
                    
                    <button id="filterBtn" class="btn btn-outline btn-sm">
                        <i class="fas fa-filter"></i>
                        <span class="sr-only">Filter</span>
                    </button>

                    <!-- Sorting options (latest to oldest, oldest to latest) -->
                    <select id="sortOptions" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="latest">Latest to Oldest</option>
                        <option value="oldest">Oldest to Latest</option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="pendingAccountsTable" class="min-w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="h-12 px-4 font-medium text-gray-600">Name</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Email</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Department</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Requested Role</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Admin Role / Other Services</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Sign-up Date</th>
                                <th class="h-12 px-4 font-medium text-gray-600">Status</th>
                                <th class="h-12 px-4 text-right font-medium text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Pending accounts will be inserted here -->
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
    </main>
</div>
</div>

<!-- Modal for Approve/Reject -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-lg font-bold mb-4">Approve User?</h3>
        <p id="modalMessage" class="mb-4">Select access level for this user:</p>
        
        <div id="accessOptions" class="mb-4">
            <div class="flex items-center mb-2">
                <input type="radio" id="userAccess" name="accessType" value="user" checked class="mr-2">
                <label for="userAccess">Regular User</label>
            </div>
            <div class="flex items-center mb-2">
                <input type="radio" id="adminAccess" name="accessType" value="admin" class="mr-2">
                <label for="adminAccess">Admin User</label>
            </div>
        </div>
        
        <div class="flex justify-end gap-2">
            <button id="cancelButton" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <button id="confirmButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Confirm
            </button>
        </div>
    </div>
</div>

<div id="toastContainer" class="fixed bottom-5 right-5 z-50"></div>

<script>
  // Mobile sidebar toggle
  document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
  });

  document.addEventListener("DOMContentLoaded", function () {
    // Highlight Active Sidebar Link
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
      if (link.getAttribute("href") === currentPage) {
        link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
      }
    });

    // Spinner Modal
    const spinnerModal = document.createElement("div");
    spinnerModal.id = "spinnerModal";
    spinnerModal.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden";
    spinnerModal.innerHTML = `
      <div class="flex flex-col items-center">
        <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <p class="mt-4 text-white font-semibold">Loading...</p>
      </div>`;
    document.body.appendChild(spinnerModal);

    // Toast Container
    const toastContainer = document.getElementById("toastContainer");

    // Modal references
    const modal = document.getElementById("modal");
    const modalTitle = document.getElementById("modalTitle");
    const modalMessage = document.getElementById("modalMessage");
    const accessOptions = document.getElementById("accessOptions");
    const confirmButton = document.getElementById("confirmButton");
    const cancelButton = document.getElementById("cancelButton");
    
    // Variables for tracking current action and user
    let currentAction = null;
    let currentUserId = null;

    // Table body reference
    const tableBody = document.querySelector("#pendingAccountsTable tbody");
    const searchInput = document.querySelector("input[type='search']");
    const filterButton = document.querySelector("button.btn-outline");

    let searchQuery = '';
    let sortOrder = 'desc'; // Default is descending (latest to oldest)

    // Show/Hide spinner
    function showSpinner() { spinnerModal.classList.remove("hidden"); }
    function hideSpinner() { spinnerModal.classList.add("hidden"); }

    // Show toast
    function showToast(message, color = "bg-green-500") {
      const toast = document.createElement("div");
      const id = "toast-" + Date.now();
      toast.id = id;
      toast.className = `${color} text-white px-6 py-3 rounded shadow-lg mb-3 flex items-center justify-between`;
      
      // Add close button and message
      toast.innerHTML = `
        <div class="flex-1 mr-2">${message}</div>
        <button class="text-white hover:text-gray-200" onclick="document.getElementById('${id}').remove()">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      `;
      
      // Add to container
      toastContainer.appendChild(toast);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        if (document.getElementById(id)) {
          document.getElementById(id).remove();
        }
      }, 5000);
    }

    // Open modal for Approve or Reject
    window.showModal = function(action, id) {
      currentAction = action;
      currentUserId = id;
      if (action === "approve") {
        modalTitle.textContent   = "Approve User?";
        modalMessage.textContent = "Select access level for this user:";
        accessOptions.classList.remove("hidden");
      } else {
        modalTitle.textContent   = "Reject User?";
        modalMessage.textContent = "Are you sure you want to reject this user?";
        accessOptions.classList.add("hidden");
      }
      modal.classList.remove("hidden");
    };

    // Close modal
    function hideModal() {
      modal.classList.add("hidden");
      currentAction = null;
      currentUserId = null;
    }

    // Fetch & render pending accounts with search and sort logic
    function fetchAccounts() {
      showSpinner();
      fetch(`../PHP/fetch_pending_accounts.php?search=${searchQuery}&sort=${sortOrder}`)
        .then(res => res.json())
        .then(data => {
          tableBody.innerHTML = "";
          if (!data.success) {
            showToast(data.message || "Failed to fetch accounts", "bg-red-500");
            return;
          }
          if (!Array.isArray(data.data)) {
            console.error("Invalid data");
            return;
          }
          data.data.forEach(acc => {
            const row = document.createElement("tr");
            row.className = "border-b hover:bg-gray-50 text-sm text-gray-700";
            const hiddenIfHandled = (acc.status === "Granted" || acc.status === "Rejected") ? "hidden" : "";
            const statusClass = acc.status === 'Granted' ? 'bg-green-600 text-white'
                              : acc.status === 'Rejected' ? 'bg-red-600 text-white'
                              : 'bg-yellow-600 text-white';
            const adminRoleDisplay = acc.admin_role?.trim() ? acc.admin_role : "-";
            const dt = new Date(acc.created_at);
            const formattedDate = `${dt.toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})}, ${dt.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true})}`;

            row.innerHTML = `
              <td class="p-4 font-medium text-gray-800">${acc.first_name} ${acc.last_name}</td>
              <td class="p-4 text-gray-600">${acc.email}</td>
              <td class="p-4 text-gray-600">${acc.college}</td>
              <td class="p-4 text-gray-600 capitalize">${acc.role}</td>
              <td class="p-4 text-gray-600">${adminRoleDisplay}</td>
              <td class="p-4 text-gray-600">${formattedDate}</td>
              <td class="p-4">
                <span class="${statusClass} px-4 py-1 rounded-full text-xs font-semibold">
                  ${acc.status}
                </span>
              </td>
              <td class="p-4 text-right">
                <div class="flex items-center justify-end gap-6 ${hiddenIfHandled}">
                  <svg onclick="showModal('approve', ${acc.id})" class="w-8 h-8 text-green-500 cursor-pointer hover:scale-125 hover:text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"/>
                  </svg>
                  <svg onclick="showModal('reject', ${acc.id})" class="w-8 h-8 text-red-500 cursor-pointer hover:scale-125 hover:text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75L14.25 14.25M14.25 9.75L9.75 14.25M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"/>
                  </svg>
                </div>
              </td>`;
            tableBody.appendChild(row);
          });
        })
        .catch(err => {
          console.error(err);
          showToast("Error fetching accounts. Please try again.", "bg-red-500");
        })
        .finally(() => hideSpinner());
    }

    // Search event listener
    searchInput.addEventListener("input", function () {
      searchQuery = searchInput.value.trim().toLowerCase();
      fetchAccounts();
    });

    // Toggle filter (sort order)
    filterButton.addEventListener("click", function () {
      sortOrder = sortOrder === 'desc' ? 'asc' : 'desc';
      fetchAccounts();
    });

    // Handle Confirm button
    confirmButton.addEventListener("click", function() {
      if (!currentAction || !currentUserId) return;
      const url = currentAction === "approve"
        ? "../PHP/approve_account.php"
        : "../PHP/reject_account.php";
      const payload = { id: currentUserId };
      if (currentAction === "approve") {
        const sel = document.querySelector('input[name="accessType"]:checked');
        payload.accessType = sel ? sel.value : 'user';
      }
      showSpinner();
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const color = currentAction === "approve" ? "bg-green-500" : "bg-red-500";
          showToast(`User ${currentAction}ed successfully!`, color);
          fetchAccounts();
        } else {
          showToast(data.message || "Action failed", "bg-red-500");
        }
      })
      .catch(err => {
        console.error(err);
        showToast("Error occurred, try again.", "bg-red-500");
      })
      .finally(() => {
        hideSpinner();
        hideModal();
      });
    });

    // Cancel
    cancelButton.addEventListener("click", hideModal);

    // Initial load
    fetchAccounts();
    
    // Example of showing an error toast (for demonstration)
    // Uncomment to test
    // setTimeout(() => {
    //   showToast("The email is already in use.", "bg-red-500");
    // }, 1000);
  });
</script>



</body>
</html>