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
                row.classList.add("border-b", "hover:bg-gray-50");
                row.setAttribute("data-id", account.id);

                const buttonVisibility = (account.status === "Granted" || account.status === "Rejected") ? "hidden" : "";

                row.innerHTML = `
                    <td class="p-4 font-medium">${account.first_name} ${account.last_name}</td>
                    <td class="p-4">${account.email}</td>
                    <td class="p-4">${account.college}</td>
                    <td class="p-4">${account.role}</td>
                    <td class="p-4">${account.created_at}</td>
                    <td class="p-4 status">${account.status}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2 action-buttons ${buttonVisibility}">
                            <button class="btn btn-success btn-sm" onclick="showModal('approve', ${account.id})">
                                <i class="fas fa-check-circle mr-1"></i> Approve
                            </button>
                            <button class="btn btn-destructive btn-sm" onclick="showModal('reject', ${account.id})">
                                <i class="fas fa-times-circle mr-1"></i> Reject
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
