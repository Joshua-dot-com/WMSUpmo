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
                <a href="Dashboard.html" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
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
                <a href="pending-deletion.html" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-minus"></i>
                    <span>Account Deletion</span>
                </a>
            </li>
            <li>
                <a href="history.html" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            </li>
            <li>
                <a href="users.html" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="mt-auto p-4 border-t border-gray-700">
        <button class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white w-full">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
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
                                    placeholder="Search accounts..."
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
                        <div class="overflow-x-auto">
                            <table id="pendingAccountsTable" class="min-w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr>
                                        <th class="h-12 px-4 font-medium text-gray-600">Name</th>
                                        <th class="h-12 px-4 font-medium text-gray-600">Email</th>
                                        <th class="h-12 px-4 font-medium text-gray-600">Department</th>
                                        <th class="h-12 px-4 font-medium text-gray-600">Requested Role</th>
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

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#pendingAccountsTable tbody");
    let currentAction = null;
    let currentUserId = null;

    // Create modal HTML and append it to the body
    const modalHTML = `
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg p-6 shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800" id="modalTitle">Confirm Action</h2>
                <p class="text-gray-600 my-4" id="modalMessage">Are you sure you want to proceed?</p>
                <div class="flex justify-center gap-4 mt-4">
                    <button id="confirmAction" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Confirm
                    </button>
                    <button id="cancelAction" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        <div id="toastMessage" class="fixed bottom-5 right-5 hidden bg-green-500 text-white px-4 py-2 rounded shadow-lg">
        </div>
    `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);

    const modal = document.getElementById("confirmationModal");
    const toast = document.getElementById("toastMessage");
    const confirmButton = document.getElementById("confirmAction");
    const cancelButton = document.getElementById("cancelAction");
    const modalTitle = document.getElementById("modalTitle");
    const modalMessage = document.getElementById("modalMessage");

    window.showModal = function(action, id) {  // Make showModal global
        currentAction = action;
        currentUserId = id;

        modalTitle.textContent = action === "approve" ? "Approve User?" : "Reject User?";
        modalMessage.textContent = action === "approve" 
            ? "Are you sure you want to approve this user?" 
            : "Are you sure you want to reject this user?";

        modal.classList.remove("hidden");
    };

    function hideModal() {
        modal.classList.add("hidden");
        currentAction = null;
        currentUserId = null;
    }

    function showToast(message, color = "bg-green-500") {
        toast.textContent = message;
        toast.className = `fixed bottom-5 right-5 text-white px-4 py-2 rounded shadow-lg ${color}`;
        toast.classList.remove("hidden");

        setTimeout(() => {
            toast.classList.add("hidden");
        }, 3000);
    }

    function fetchAccounts() {
        fetch("../PHP/fetch_pending_accounts.php")
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Data:", data);
                if (!data.success) {
                    console.error("Error from server:", data.message);
                    return;
                }
                if (!Array.isArray(data.data)) {
                    console.error("Invalid data format received:", data);
                    return;
                }
    
                tableBody.innerHTML = "";
                data.data.forEach(account => {
                    const row = document.createElement("tr");
                    row.classList.add("border-b", "hover:bg-gray-50", "text-sm", "text-gray-700");
                    row.setAttribute("data-id", account.id);
    
                    const buttonVisibility = (account.status === "Granted" || account.status === "Rejected") ? "hidden" : "";
    
                    row.innerHTML = `
                        <td class="p-4 font-semibold text-gray-800">${account.first_name} ${account.last_name}</td>
                        <td class="p-4 text-gray-600">${account.email}</td>
                        <td class="p-4 text-gray-600">${account.college}</td>
                        <td class="p-4 text-gray-600 capitalize">${account.role}</td>
                        <td class="p-4 text-gray-500">${account.created_at}</td>
                        <td class="p-4 status font-medium ${account.status === 'Granted' ? 'text-green-600' : account.status === 'Rejected' ? 'text-red-600' : 'text-yellow-600'}">${account.status}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2 action-buttons ${buttonVisibility}">
                                <button class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition duration-200" 
                                    onclick="showModal('approve', ${account.id})">
                                    <i class="fas fa-check-circle mr-2"></i> Approve
                                </button>
                                <button class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition duration-200" 
                                    onclick="showModal('reject', ${account.id})">
                                    <i class="fas fa-times-circle mr-2"></i> Reject
                                </button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error("Error fetching accounts:", error));
    }
    

    confirmButton.addEventListener("click", function () {
        if (!currentAction || !currentUserId) return;

        const url = currentAction === "approve" ? "../PHP/approve_account.php" : "../PHP/reject_account.php";
        const statusUpdate = currentAction === "approve" ? "Granted" : "Rejected";
        const successMessage = currentAction === "approve" ? "User Approved Successfully" : "User Rejected Successfully";

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: currentUserId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRowStatus(currentUserId, statusUpdate);
                showToast(successMessage);
            } else {
                showToast("Action failed!", "bg-red-500");
            }
        })
        .catch(error => console.error("Error processing request:", error))
        .finally(() => hideModal());
    });

    cancelButton.addEventListener("click", hideModal);

    function updateRowStatus(id, newStatus) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            row.querySelector(".status").textContent = newStatus;
            row.querySelector(".action-buttons").classList.add("hidden");
        }
    }

    fetchAccounts();
});

    </script>
</body>
</html>