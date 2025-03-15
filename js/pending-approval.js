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

    // Fetch pending accounts from PHP
    function fetchPendingAccounts() {
        fetch("../PHP/fetch_pending_accounts.php")
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); // Debugging output
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
                row.classList.add("border-b", "hover:bg-gray-50");
                row.innerHTML = `
                    <td class="p-4 font-medium">${account.first_name} ${account.last_name}</td>
                    <td class="p-4">${account.email}</td>
                    <td class="p-4">${account.college}</td>
                    <td class="p-4">${account.role}</td>
                    <td class="p-4">${account.created_at}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="btn btn-success btn-sm" onclick="approveAccount(${account.id})">
                                <i class="fas fa-check-circle mr-1"></i>
                                Approve
                            </button>
                            <button class="btn btn-destructive btn-sm" onclick="rejectAccount(${account.id})">
                                <i class="fas fa-times-circle mr-1"></i>
                                Reject
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching pending accounts:", error));        
    }

    // Approve Account
    window.approveAccount = function (id) {
        fetch("approve_account.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) fetchPendingAccounts();
        })
        .catch(error => console.error("Error approving account:", error));
    };

    // Reject Account
    window.rejectAccount = function (id) {
        fetch("reject_account.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) fetchPendingAccounts();
        })
        .catch(error => console.error("Error rejecting account:", error));
    };

    fetchPendingAccounts();
});
