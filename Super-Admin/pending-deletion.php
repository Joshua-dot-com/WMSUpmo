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
    <title>Account Deletion - Admin Dashboard</title>
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

        /* Loading animation */
    .loading-animation {
        position: relative;
    }
    
    .loading-animation:after {
        content: '...';
        position: absolute;
        animation: ellipsis 1.5s infinite;
        width: 1.5em;
        text-align: left;
    }
    
    @keyframes ellipsis {
        0% { content: '.'; }
        33% { content: '..'; }
        66% { content: '...'; }
    }
    
    /* Modal animation */
    #viewModal.hidden {
        opacity: 0;
        pointer-events: none;
    }
    
    #viewModal.hidden > div {
        transform: scale(0.95);
    }
    
    /* Smooth transition for modal */
    #viewModal {
        opacity: 1;
        transition: opacity 0.3s ease;
    }
    
    #viewModal > div {
        transition: transform 0.3s ease;
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
                    <h1 class="text-2xl font-bold">Account Deletion</h1>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6">
                <!-- Pending Deletion Requests -->
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold">Pending Deletion Requests</h2>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input
                                    type="search"
                                    id="search-requests"
                                    placeholder="Search requests..."
                                    class="pl-8 h-10 w-full md:w-[250px] rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <button id="filter-requests" class="btn btn-outline btn-sm">
                                <i class="fas fa-filter"></i>
                                <span class="sr-only">Filter</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" id="deletion-requests-table">
                                <thead>
                                    <tr class="border-b">
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Name</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Email</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Department</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Request Date</th>
                                        <th class="h-12 px-4 text-right font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="deletion-requests-body">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div> 
    </div>
</div>

<!-- View Deletion Reason Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden z-50 transition-all duration-300">
    <div class="bg-white w-[90%] max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100">
        <!-- Modal Header with Gradient -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 p-5 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-2 rounded-full mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold">Deletion Request Details</h2>
            </div>
            <!-- Elegant Close Button -->
            <button onclick="closeModal()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors duration-200 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="ml-2 text-lg font-semibold text-gray-800">Reason for Deletion</h3>
                </div>
                
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div id="deletionReasonText" class="text-gray-700 italic relative pl-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 absolute left-0 top-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span class="loading-animation">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button onclick="closeModal()" class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transform transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Spinner -->
<div id="loadingSpinner" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-30 hidden">
    <div class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function () {

    // === Fetch Deletion Requests ===
    function fetchDeletionRequests(query = "") {
        fetch("fetch-deletion-requests.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `query=${encodeURIComponent(query)}`,
        })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            const tableBody = document.getElementById("deletion-requests-body");
            if (!tableBody) {
                console.error("Table body element not found.");
                return;
            }
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-gray-500">No requests found.</td></tr>`;
                return;
            }

            data.forEach((request) => {
                const row = document.createElement("tr");
                row.classList.add("border-b", "hover:bg-gray-50");
                row.innerHTML = `
                    <td class="p-4 font-medium">${request.name}</td>
                    <td class="p-4">${request.email}</td>
                    <td class="p-4">${request.college}</td>
                    <td class="p-4">${request.request_date}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="btn btn-destructive btn-sm" onclick="deleteRequest(${request.id})">
                                <i class="fas fa-trash-alt mr-1"></i> Delete
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="viewRequest(${request.id})">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="rejectRequest(${request.id})">
                                <i class="fas fa-times-circle mr-1"></i> Reject
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch((error) => {
            console.error("Error fetching requests:", error);
            alert("Failed to fetch deletion requests. Please try again later.");
        });
    }

    // === Reject Request ===
    window.rejectRequest = function(id) {
        if (!confirm("Are you sure you want to reject this deletion request?")) return;

        fetch("reject-deletion-request.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}`,
        })
        .then((res) => res.json())
        .then((data) => {
            alert(data.success ? data.message : data.error);
            if (data.success) {
                fetchDeletionRequests();
            }
        })
        .catch((err) => {
            console.error("Error rejecting request:", err);
            alert("Failed to reject deletion request.");
        });
    };

    // === Delete Request Modal ===
    const deleteModal = document.createElement("div");
    deleteModal.id = "deleteModal";
    deleteModal.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50";
    deleteModal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md border-t-4 border-red-600 animate-fade-in">
            <h2 class="text-xl font-bold text-red-600 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Deletion
            </h2>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this request and the associated user account? This action cannot be undone.</p>
            <div class="flex justify-end gap-2">
                <button id="cancelDelete" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button id="confirmDelete" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Yes, Delete
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(deleteModal);

    let currentDeleteId = null;

    window.deleteRequest = function(id) {
        currentDeleteId = id;
        deleteModal.classList.remove("hidden");
    };

    document.addEventListener("click", function (e) {
        const spinner = document.getElementById("loadingSpinner");

        if (e.target.id === "cancelDelete") {
            deleteModal.classList.add("hidden");
            currentDeleteId = null;
        } else if (e.target.id === "confirmDelete" && currentDeleteId) {
            spinner?.classList.remove("hidden");

            fetch("delete-request.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${currentDeleteId}`,
            })
            .then((res) => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then((data) => {
                alert(data.success ? data.message : data.error);
                if (data.success) {
                    fetchDeletionRequests();
                }
            })
            .catch((error) => {
                console.error("Delete error:", error);
                alert("Failed to delete the request and user account.");
            })
            .finally(() => {
                spinner?.classList.add("hidden");
                deleteModal.classList.add("hidden");
                currentDeleteId = null;
            });
        }
    });

    // === View Request ===
    window.viewRequest = function(id) {
        const modal = document.getElementById("viewModal");
        const reasonText = document.getElementById("deletionReasonText");

        if (!modal || !reasonText) {
            console.error("View modal or reason text element not found.");
            alert("Unable to view request details.");
            return;
        }

        reasonText.textContent = "Loading...";
        modal.classList.remove("hidden");

        fetch("view-deletion-request.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}`
        })
        .then((res) => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then((data) => {
            if (data.success) {
                reasonText.textContent = data.reason || "No reason provided.";
                const nameText = document.getElementById("deletionNameText");
                const emailText = document.getElementById("deletionEmailText");
                const collegeText = document.getElementById("deletionCollegeText");
                const dateText = document.getElementById("deletionDateText");
                if (nameText) nameText.textContent = data.name || "—";
                if (emailText) emailText.textContent = data.email || "—";
                if (collegeText) collegeText.textContent = data.college || "—";
                if (dateText) dateText.textContent = data.requested_at || "—";
            } else {
                reasonText.textContent = data.error || "Request not found.";
            }
        })
        .catch((err) => {
            console.error("View error:", err);
            reasonText.textContent = "Error fetching request details.";
        });
    };

    // === Close View Modal ===
    window.closeModal = function () {
        const modal = document.getElementById("viewModal");
        if (modal) {
            modal.classList.add("hidden");
        }
    };

    // === Search Input ===
    const searchInput = document.getElementById("search-requests");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            fetchDeletionRequests(this.value);
        });
    }

    // === Sidebar Toggle (Mobile) ===
    const sidebarToggle = document.getElementById("sidebar-toggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function () {
            document.getElementById("sidebar")?.classList.toggle("active");
        });
    }

    // === Highlight Active Sidebar Link ===
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");
    navLinks.forEach((link) => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });

    // === Initial Data Load ===
    fetchDeletionRequests();
});
</script>



</body>
</html>