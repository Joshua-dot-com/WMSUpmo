<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Dashboard</title>
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
                <div class="container flex h-16 items-center justify-between px-6">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="md:hidden mr-4">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-2xl font-bold">Users</h1>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold">All Users</h2>
                        <div class="flex items-center gap-2">
                        <div class="relative mb-4">
    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
    <input
        type="search"
        id="searchInput"
        placeholder="Search users..."
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
                    <div class="flex items-center gap-4 mb-4 flex-wrap">
    <button class="btn btn-primary-light btn-sm filter-btn active" data-status="">All Users</button>
    <button class="btn btn-outline btn-sm filter-btn" data-status="Active">Active</button>
    <button class="btn btn-outline btn-sm filter-btn" data-status="Inactive">Inactive</button>
</div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Name</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Email</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Department</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Role</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Admin Role / Other Services</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Status</th>
                                        <th class="h-12 px-4 text-left font-medium text-gray-500">Last Login</th>
                                        <th class="h-12 px-4 text-right font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
            
                       <div class="flex flex-col md:flex-row items-center justify-between py-4">
                       <div class="flex flex-col md:flex-row items-center justify-between py-4">
    <div id="showing-text" class="text-sm text-gray-500 mb-4 md:mb-0">
        Showing <strong>0</strong> of <strong>0</strong> users
    </div>
    <div id="pagination" class="flex items-center space-x-2"></div>
</div>

</div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
        <h2 class="text-2xl font-semibold mb-4">Edit User</h2>
        <form id="editUserForm" onsubmit="event.preventDefault(); updateUser();">
            <input type="hidden" id="edit-user-id" name="id">

            <div class="mb-4">
                <label>First Name</label>
                <input type="text" id="edit-first-name" name="first_name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label>Last Name</label>
                <input type="text" id="edit-last-name" name="last_name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label>Email</label>
                <input type="email" id="edit-email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
    <label for="edit-college">College</label>
    <input type="text" id="edit-college" name="college" class="w-full p-2 border rounded">
</div>

            <div class="mb-4">
                <label>Role</label>
                <input type="text" id="edit-role" name="role" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label>Status</label>
                <select id="edit-status" name="status" class="w-full p-2 border rounded">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded mr-2" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>



<script>
// Mobile sidebar toggle
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

document.addEventListener("DOMContentLoaded", function () {
    // Highlight active link
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll("#sidebar nav a");

    navLinks.forEach(link => {
        const linkPage = link.getAttribute("href");
        if (linkPage === currentPage) {
            link.classList.add("bg-gray-800", "text-white", "border-b-2", "border-red-500");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const paginationContainer = document.getElementById('pagination');
    const showingText = document.getElementById('showing-text');
    const searchInput = document.getElementById('searchInput'); // Search Input

    let currentPage = 1;
    const itemsPerPage = 7;
    let usersData = [];

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Reset all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-primary-light', 'active');
                btn.classList.add('btn-outline');
            });

            // Highlight active button
            this.classList.remove('btn-outline');
            this.classList.add('btn-primary-light', 'active');

            const status = this.getAttribute('data-status');
            currentPage = 1; // Reset to first page when filter changes
            fetchUsers(status);
        });
    });

    // Listen for search input
    searchInput.addEventListener('input', function () {
        currentPage = 1; // Reset to first page when searching
        renderTable();   // Re-render table as the user types
        renderPagination();
    });

    fetchUsers(); // Initial fetch

    function fetchUsers(statusFilter = "") {
        fetch(`../PHP/fetch_all_users.php?filter=${encodeURIComponent(statusFilter)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success || !Array.isArray(data.data)) {
                    usersData = [];
                } else {
                    usersData = data.data;
                }
                renderTable();
                renderPagination();
            })
            .catch(error => {
                console.error("Error fetching users:", error);
            });
    }

    function renderTable() {
        const tbody = document.querySelector("tbody");
        tbody.innerHTML = "";

        let filteredData = [...usersData];

        // Apply search filter if there is input
        const searchValue = searchInput.value.trim().toLowerCase();
        if (searchValue) {
            filteredData = filteredData.filter(user =>
                (`${user.first_name} ${user.last_name}`.toLowerCase().includes(searchValue) ||
                 user.email.toLowerCase().includes(searchValue) ||
                 (user.college || '').toLowerCase().includes(searchValue) ||
                 user.role.toLowerCase().includes(searchValue) ||
                 (user.admin_role || '').toLowerCase().includes(searchValue) ||
                 user.status.toLowerCase().includes(searchValue))
            );
        }

        if (filteredData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center p-4">No users found.</td></tr>`;
            showingText.innerHTML = "Showing <strong>0</strong> of <strong>0</strong> users";
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
        const paginatedUsers = filteredData.slice(startIndex, endIndex);

        showingText.innerHTML = `Showing <strong>${startIndex + 1}-${endIndex}</strong> of <strong>${filteredData.length}</strong> users`;

        paginatedUsers.forEach(user => {
            const row = document.createElement("tr");
            row.classList.add("border-b", "hover:bg-gray-50");

            row.innerHTML = `
                <td class="p-4 font-medium text-center">${user.first_name} ${user.last_name}</td>
                <td class="p-4 text-center">${user.email}</td>
                <td class="p-4 text-center">${user.college || '╼'}</td>
                <td class="p-4 text-center">${user.role}</td>
                <td class="p-4 text-center">${user.admin_role || '╼'}</td>
                <td class="p-4 text-center">
                    <span class="${user.status === 'Active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'} px-2 py-1 rounded-full">${user.status}</span>
                </td>
                <td class="p-4 text-center">${user.last_login || '╼'}</td>
                <td class="p-4 text-center">
                    <button onclick="openEditModal(${user.id})" 
                            class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             fill="none" viewBox="0 0 24 24" 
                             stroke-width="1.5" stroke="currentColor" 
                             class="w-6 h-6 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 
                                  2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 
                                  1.13L6 18l.8-2.685a4.5 4.5 0 0 1 
                                  1.13-1.897l8.932-8.931Zm0 
                                  0L19.5 7.125M18 14v4.75A2.25 
                                  2.25 0 0 1 15.75 21H5.25A2.25 
                                  2.25 0 0 1 3 18.75V8.25A2.25 
                                  2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderPagination() {
        paginationContainer.innerHTML = "";

        // Use filtered data length for pagination
        const searchValue = searchInput.value.trim().toLowerCase();
        let filteredData = [...usersData];
        if (searchValue) {
            filteredData = filteredData.filter(user =>
                (`${user.first_name} ${user.last_name}`.toLowerCase().includes(searchValue) ||
                 user.email.toLowerCase().includes(searchValue) ||
                 (user.college || '').toLowerCase().includes(searchValue) ||
                 user.role.toLowerCase().includes(searchValue) ||
                 (user.admin_role || '').toLowerCase().includes(searchValue) ||
                 user.status.toLowerCase().includes(searchValue))
            );
        }

        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (totalPages <= 1) return; // No need for pagination

        // Previous Button
        const prevBtn = document.createElement('button');
        prevBtn.className = `btn btn-outline btn-sm ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
                renderPagination();
            }
        };
        paginationContainer.appendChild(prevBtn);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `btn btn-sm ${i === currentPage ? 'btn-primary-light' : 'btn-outline'}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => {
                currentPage = i;
                renderTable();
                renderPagination();
            };
            paginationContainer.appendChild(pageBtn);
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.className = `btn btn-outline btn-sm ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
        nextBtn.textContent = 'Next';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
                renderPagination();
            }
        };
        paginationContainer.appendChild(nextBtn);
    }
});


function openEditModal(userId) {
    fetch(`../PHP/get_user_by_id.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            if (!user.success) {
                alert("Failed to fetch user data.");
                return;
            }

            // Populate general user data
            document.getElementById('edit-user-id').value = user.data.id;
            document.getElementById('edit-first-name').value = user.data.first_name;
            document.getElementById('edit-last-name').value = user.data.last_name;
            document.getElementById('edit-email').value = user.data.email;
            document.getElementById('edit-role').value = user.data.role;
            document.getElementById('edit-status').value = user.data.status;

            const collegeLabel = document.querySelector('label[for="edit-college"]');
            const collegeInput = document.getElementById('edit-college');

            // Conditional logic for role
            if (user.data.role === 'Administrative Officials') {
                // Change label to Administration Role
                collegeLabel.textContent = 'Administration Role';
                // Display the value from admin_role column
                collegeInput.value = user.data.admin_role || 'N/A';
            } else {
                // Keep the label as College
                collegeLabel.textContent = 'College';
                // Display the value from college column
                collegeInput.value = user.data.college || 'N/A';
            }

            // Show the modal
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error("Error fetching user:", error);
        });
}


// Close modal
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// ✅ AJAX Function to Update User
function updateUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);

    fetch('../PHP/update_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            closeEditModal();
            fetchUsers(); // Refresh user list after update
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error updating user:', error);
    });
}
</script>
</body>
</html>