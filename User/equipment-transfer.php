<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transfer Equipment - User Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#fef2f2',
              100: '#fee2e2',
              200: '#fecaca',
              300: '#fca5a5',
              400: '#f87171',
              500: '#ef4444',
              600: '#dc2626',
              700: '#b91c1c',
              800: '#991b1b',
              900: '#7f1d1d',
              950: '#450a0a',
            }
          },
          animation: {
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
    .card-hover {
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
    }
    
    .table-row-hover {
      transition: background-color 0.15s;
    }
    
    .table-row-hover:hover {
      background-color: rgba(243, 244, 246, 0.8);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
    
    .table-header-gradient {
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .modal-animation {
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from { transform: translateY(-20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
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
                    <a href="equipment-transfer.php" class="nav-btn flex items-center gap-3 px-3 py-2 rounded-md bg-gray-800 text-white border-l-4 border-blue-500">
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
   <div class="flex-1 overflow-auto ml-64">
    <div class="p-8 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Transfer Equipment</h2>
          <p class="text-slate-500 mt-1">Manage your equipment transfers and requests</p>
        </div>
        <div class="flex space-x-4">
          <a href="user-history.php" class="group px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 font-medium hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all duration-200 flex items-center">
            <i class="fas fa-history mr-2 text-primary-500 group-hover:text-primary-600 transition-colors"></i>
            View Equipment History
          </a>
        </div>
      </div>

      <!-- Current Equipment -->
      <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6 mb-8 card-hover transition-all">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-slate-800 flex items-center">
            <i class="fas fa-laptop-code text-primary-500 mr-3"></i>
            Currently Assigned Equipment
          </h3>
          <span class="bg-primary-50 text-primary-700 text-xs px-3 py-1 rounded-full font-medium">Active Items</span>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-100">
          <table class="w-full text-left" id="current-equipment-table">
            <thead>
              <tr class="table-header-gradient border-b border-slate-200">
                <th class="p-4 pl-6 text-xs font-semibold text-slate-600 uppercase tracking-wider">Equipment ID</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Category</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Assigned Date</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                <th class="p-4 pr-6 text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody id="current-equipment-body">
              <!-- Sample data for design purposes -->
              <tr class="table-row-hover border-b border-slate-100">
                <td class="p-4 pl-6 text-sm text-slate-700 font-medium">EQ-1001</td>
                <td class="p-4 text-sm text-slate-700">Dell XPS 15 Laptop</td>
                <td class="p-4 text-sm">
                  <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">Computer</span>
                </td>
                <td class="p-4 text-sm text-slate-600">Oct 15, 2023</td>
                <td class="p-4 text-sm">
                  <span class="bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium">Active</span>
                </td>
                <td class="p-4 pr-6">
                  <button onclick="openTransferModal('EQ-1001', 'Dell XPS 15 Laptop')" class="group flex items-center text-sm font-medium text-rose-600 hover:text-rose-700 transition-colors">
                    <i class="fas fa-exchange-alt mr-1.5 group-hover:animate-pulse"></i> Transfer
                  </button>
                </td>
              </tr>
              <tr class="table-row-hover border-b border-slate-100">
                <td class="p-4 pl-6 text-sm text-slate-700 font-medium">EQ-1002</td>
                <td class="p-4 text-sm text-slate-700">iPhone 13 Pro</td>
                <td class="p-4 text-sm">
                  <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full text-xs font-medium">Mobile</span>
                </td>
                <td class="p-4 text-sm text-slate-600">Sep 22, 2023</td>
                <td class="p-4 text-sm">
                  <span class="bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium">Active</span>
                </td>
                <td class="p-4 pr-6">
                  <button onclick="openTransferModal('EQ-1002', 'iPhone 13 Pro')" class="group flex items-center text-sm font-medium text-rose-600 hover:text-rose-700 transition-colors">
                    <i class="fas fa-exchange-alt mr-1.5 group-hover:animate-pulse"></i> Transfer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pending Transfer Requests -->
      <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6 mb-8 card-hover transition-all">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-slate-800 flex items-center">
            <i class="fas fa-clock text-amber-500 mr-3"></i>
            Pending Transfer Requests
          </h3>
          <span class="bg-amber-50 text-amber-700 text-xs px-3 py-1 rounded-full font-medium">Awaiting Approval</span>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-100">
          <table class="w-full text-left" id="transfer-requests-table">
            <thead>
              <tr class="table-header-gradient border-b border-slate-200">
                <th class="p-4 pl-6 text-xs font-semibold text-slate-600 uppercase tracking-wider">Request ID</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Equipment</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Transfer To</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Requested Date</th>
                <th class="p-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                <th class="p-4 pr-6 text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody id="transfer-requests-body">
              <!-- Sample data for design purposes -->
              <tr class="table-row-hover border-b border-slate-100">
                <td class="p-4 pl-6 text-sm text-slate-700 font-medium">TR-501</td>
                <td class="p-4 text-sm text-slate-700">iPad Pro 12.9"</td>
                <td class="p-4 text-sm text-slate-700">Jane Smith</td>
                <td class="p-4 text-sm text-slate-600">Nov 10, 2023</td>
                <td class="p-4 text-sm">
                  <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium flex items-center w-fit">
                    <i class="fas fa-circle text-[8px] mr-1.5 animate-pulse"></i>
                    Pending
                  </span>
                </td>
                <td class="p-4 pr-6">
                  <button onclick="cancelTransfer('TR-501')" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-5 text-center text-sm text-slate-500 flex items-center justify-center bg-slate-50 py-3 rounded-xl">
          <i class="fas fa-info-circle text-primary-500 mr-2"></i>
          Equipment transfer requests are typically processed within 1-2 business days.
        </div>
      </div>

      <!-- Transfer Guidelines -->
      <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-6 card-hover transition-all">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-slate-800 flex items-center">
            <i class="fas fa-book text-indigo-500 mr-3"></i>
            Equipment Transfer Guidelines
          </h3>
        </div>
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 p-5 rounded-xl border border-slate-200">
          <div class="space-y-4 text-slate-700">
            <p class="flex items-start">
              <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full mr-3">
                <i class="fas fa-check"></i>
              </span>
              <span class="text-sm">You can only transfer equipment that is currently assigned to you.</span>
            </p>
            <p class="flex items-start">
              <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full mr-3">
                <i class="fas fa-user"></i>
              </span>
              <span class="text-sm">The recipient must be a registered user in the system.</span>
            </p>
            <p class="flex items-start">
              <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full mr-3">
                <i class="fas fa-envelope"></i>
              </span>
              <span class="text-sm">Both you and the recipient will receive email notifications about the transfer.</span>
            </p>
            <p class="flex items-start">
              <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full mr-3">
                <i class="fas fa-shield-alt"></i>
              </span>
              <span class="text-sm">You remain responsible for the equipment until the transfer is approved.</span>
            </p>
            <p class="flex items-start">
              <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full mr-3">
                <i class="fas fa-phone-alt"></i>
              </span>
              <span class="text-sm">For immediate transfers, please contact the IT department directly.</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Transfer Modal Wizard -->
  <div id="transferModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm hidden">
  <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl p-0 relative modal-animation">

      <!-- Modal Header with gradient -->
      <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-t-2xl p-6 mb-4">
        <div class="flex justify-between items-center">
          <h3 class="text-xl font-bold text-white">Transfer Equipment</h3>
          <button onclick="closeTransferModal()" class="text-white/80 hover:text-white transition-colors duration-200 focus:outline-none">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        <p class="text-primary-100 text-sm mt-2">Complete the steps below to transfer your equipment</p>
      </div>

      <!-- Modal Form -->
      <form id="transferForm" class="p-6 space-y-6">
        <input type="hidden" id="equipment_id" name="equipment_id">
        <input type="hidden" name="recipient" id="recipient">

        <!-- Step Progress Indicator -->
        <div class="flex items-center justify-between mb-4 px-6">
          <div class="flex items-center">
            <div class="relative">
              <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold shadow-md">1</div>
              <div class="absolute top-0 right-0 -mr-1 -mt-1 w-3 h-3 bg-white rounded-full border-2 border-primary-500"></div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-700">Select User</p>
            </div>
          </div>
          <div class="flex-1 mx-4">
            <div class="h-1 bg-gray-200 rounded-full">
              <div class="h-full bg-primary-500 rounded-full w-0" id="progress-bar-1"></div>
            </div>
          </div>
          <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">2</div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-400">Details</p>
            </div>
          </div>
          <div class="flex-1 mx-4">
            <div class="h-1 bg-gray-200 rounded-full">
              <div class="h-full bg-primary-500 rounded-full w-0" id="progress-bar-2"></div>
            </div>
          </div>
          <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">3</div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-400">Confirm</p>
            </div>
          </div>
        </div>

        <!-- Step 1: Select User -->
        <div class="step" id="step1">
          <div class="mb-6 bg-primary-50 p-4 rounded-xl border border-primary-100 text-primary-700 text-sm">
            <p class="flex items-center">
              <i class="fas fa-info-circle mr-2"></i>
              <span>Select the user who will receive the equipment</span>
            </p>
          </div>
          
          <label for="recipient_search" class="block text-sm font-medium text-gray-700 mb-2">Transfer To</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-search text-gray-400"></i>
            </div>
            <input
              type="text"
              id="recipient_search"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-gray-700"
              placeholder="Search for user by name or email..."
            />
          </div>
          
          <div id="recipient_results" class="mt-3 max-h-48 overflow-y-auto border border-gray-200 rounded-xl bg-white shadow-sm divide-y divide-gray-100 text-sm">
            <!-- Sample data for design purposes -->
            <div class="p-3 hover:bg-slate-50 cursor-pointer transition-colors flex items-center">
              <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <p class="font-medium text-slate-800">Jane Smith</p>
                <p class="text-xs text-slate-500">jane.smith@company.com</p>
              </div>
            </div>
            <div class="p-3 hover:bg-slate-50 cursor-pointer transition-colors flex items-center">
              <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <p class="font-medium text-slate-800">John Doe</p>
                <p class="text-xs text-slate-500">john.doe@company.com</p>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end mt-8">
            <button type="button" id="step1NextBtn" disabled class="flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-xl hover:bg-primary-700 transition shadow-md shadow-primary-600/20 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:bg-primary-600">
              <span>Next Step</span>
              <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
        </div>

        <!-- Step 2: Reason + Date -->
        <div class="step hidden" id="step2">
          <div class="grid grid-cols-1 gap-6">
            <div>
              <label for="transfer_date" class="block text-sm font-medium text-gray-700 mb-2">Transfer Date</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-calendar-alt text-gray-400"></i>
                </div>
                <input
                  type="date"
                  id="transfer_date"
                  name="transfer_date"
                  required
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                />
              </div>
            </div>
            
            <div>
              <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Transfer</label>
              <div class="relative">
                <textarea
                  id="reason"
                  name="reason"
                  rows="4"
                  required
                  placeholder="Explain why you are transferring this equipment..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-none"
                ></textarea>
              </div>
              <p class="mt-2 text-xs text-gray-500">Please provide enough detail for approval purposes</p>
            </div>
          </div>
          
          <div class="flex justify-between mt-8">
            <button type="button" onclick="prevStep(1)" class="flex items-center bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition">
              <i class="fas fa-arrow-left mr-2"></i>
              <span>Previous</span>
            </button>
            <button type="button" onclick="nextStep(3)" class="flex items-center bg-primary-600 text-white px-5 py-2.5 rounded-xl hover:bg-primary-700 transition shadow-md shadow-primary-600/20">
              <span>Review</span>
              <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
        </div>

        <!-- Step 3: Preview -->
        <div class="step hidden" id="step3">
          <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 mb-6">
            <h4 class="text-sm font-semibold text-slate-800 mb-4 flex items-center">
              <i class="fas fa-clipboard-check text-primary-500 mr-2"></i>
              Transfer Summary
            </h4>
            
            <div class="space-y-4">
              <div>
                <h5 class="text-xs font-medium text-slate-500 mb-1">Equipment</h5>
                <p id="preview_equipment" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 font-medium"></p>
              </div>
              
              <div>
                <h5 class="text-xs font-medium text-slate-500 mb-1">Recipient</h5>
                <p id="preview_recipient" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 font-medium"></p>
              </div>
              
              <div>
                <h5 class="text-xs font-medium text-slate-500 mb-1">Transfer Date</h5>
                <p id="preview_date" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 font-medium"></p>
              </div>
              
              <div>
                <h5 class="text-xs font-medium text-slate-500 mb-1">Reason</h5>
                <p id="preview_reason" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700"></p>
              </div>
            </div>
          </div>
          
          <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 text-amber-700 text-sm mb-6">
            <p class="flex items-start">
              <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
              <span>By submitting this request, you confirm that all information is correct and you authorize the transfer of this equipment.</span>
            </p>
          </div>
          
          <div class="flex justify-between">
            <button type="button" onclick="prevStep(2)" class="flex items-center bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 transition">
              <i class="fas fa-arrow-left mr-2"></i>
              <span>Previous</span>
            </button>
            <button type="submit" class="flex items-center bg-green-600 text-white px-5 py-2.5 rounded-xl hover:bg-green-700 transition shadow-md shadow-green-600/20">
              <i class="fas fa-check mr-2"></i>
              <span>Submit Request</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const transferModal = document.getElementById('transferModal');
  const recipientSearch = document.getElementById('recipient_search');
  const recipientSelect = document.getElementById('recipient');

  const currentEquipmentTable = document.getElementById('current-equipment-body');
  const transferRequestsTable = document.getElementById('transfer-requests-body');

  let users = [];
  
  // Get base URL for image paths - fix for 404 errors
  const getBaseUrl = () => {
    return window.location.origin + '/PMO/User/'; // Adjust this path based on your project structure
  };
  
  // Fix image path to ensure it's absolute
  const fixImagePath = (path) => {
    if (!path) return null;
    
    // If path already starts with http or https, it's already absolute
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }
    
    // Otherwise, append to base URL
    return getBaseUrl() + path;
  };

  // Utility: Format date
  function formatDate(dateString) {
    const date = new Date(dateString);
    return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
  }

  // Utility: Status badge class
  function getStatusClass(status) {
  switch (status) {
    case 'Assigned':
      return 'bg-green-100 text-green-800';
    case 'Pending Transfer':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}


  // Highlight current sidebar link with red theme
  const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll("#sidebar nav a").forEach(link => {
        // Add hover effect to all links
        link.classList.add("transition-colors", "duration-200", "hover:bg-red-700", "hover:text-white");
        
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("bg-red-600", "text-white", "border-l-4", "border-red-800", "shadow-md");
        }
    });

  // Fetch users - modified to handle the correct API response format
   // Fetch users - modified to encode query parameter
   async function fetchUsers(query = '') {
    try {
      // Encode the query parameter to handle special characters and spaces
      const encodedQuery = encodeURIComponent(query);
      const response = await fetch(`get_users.php?query=${encodedQuery}`);
      if (!response.ok) throw new Error('Failed to fetch users');
      const data = await response.json();
    
      // The API returns users in data.data, not data.users
      if (data.success && Array.isArray(data.data)) {
        users = data.data; // Use the data array directly
      } else {
        users = [];
        console.error('Unexpected API response format:', data);
      }
    
      // Always display results after fetching
      displayRecipientResults(users);
    } catch (error) {
      console.error('Error fetching users:', error);
      alert('An error occurred while fetching users.');
      users = [];
      displayRecipientResults(users);
    }
  }

  function displayRecipientResults(userList) {
    const recipientResults = document.getElementById('recipient_results');
    recipientResults.innerHTML = '';
    
    // Make sure the results container is visible
    recipientResults.classList.remove('hidden');

    if (!userList.length) {
      recipientResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No users found</div>';
      return;
    }

    userList.forEach(user => {
      const div = document.createElement('div');
      div.className = 'p-3 hover:bg-slate-50 cursor-pointer transition-colors flex items-center';
      
      // Create a more visually appealing user item with profile picture if available
      let avatarHtml;
      if (user.profile_picture) {
        // Fix the image path to prevent 404 errors
        const fixedImagePath = fixImagePath(user.profile_picture);
        
        avatarHtml = `
          <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center mr-3 overflow-hidden">
            <img 
              src="${fixedImagePath}" 
              alt="${user.first_name}" 
              class="w-full h-full object-cover"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <i class="fas fa-user text-primary-600" style="display:none;"></i>
          </div>
        `;
      } else {
        avatarHtml = `
          <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3">
            <i class="fas fa-user"></i>
          </div>
        `;
      }
      
      div.innerHTML = `
        ${avatarHtml}
        <div>
          <p class="font-medium text-slate-800">${user.first_name} ${user.last_name}</p>
          <p class="text-xs text-slate-500">${user.email}</p>
          ${user.role ? `<p class="text-xs text-slate-400">${user.role}</p>` : ''}
        </div>
      `;
      
      div.onclick = () => {
        document.getElementById('recipient').value = user.id;
        document.getElementById('recipient_search').value = `${user.first_name} ${user.last_name}`;
        
        // Highlight the selected user
        document.querySelectorAll('#recipient_results > div').forEach(el => {
          el.classList.remove('bg-primary-50', 'border-l-4', 'border-primary-500');
        });
        div.classList.add('bg-primary-50', 'border-l-4', 'border-primary-500');
        
        // Enable next button
        const nextBtn = document.getElementById('step1NextBtn');
        if (nextBtn) nextBtn.disabled = false;
      };
      recipientResults.appendChild(div);
    });
  }

  // Global: user search function - modified to show all users when empty
  window.searchRecipients = async function(query) {
    await fetchUsers(query);
  };

  // Add event listener to search input
  if (recipientSearch) {
    recipientSearch.addEventListener('input', function() {
      searchRecipients(this.value);
    });
  }

  // Open modal - modified to immediately display all users
  window.openTransferModal = async function (equipmentId, equipmentName) {
    document.getElementById('equipment_id').value = equipmentId;
    const equipmentNameInput = document.getElementById('equipment_name');
    if (equipmentNameInput) {
      equipmentNameInput.value = equipmentName;
    }

    // Reset form state
    document.getElementById('recipient').value = '';
    document.getElementById('recipient_search').value = '';
    
    const step1NextBtn = document.getElementById('step1NextBtn');
    if (step1NextBtn) step1NextBtn.disabled = true;
    
    // Reset form validation - remove required attributes until the field is visible
    const transferDateInput = document.getElementById('transfer_date');
    const reasonInput = document.getElementById('reason');
    
    if (transferDateInput) transferDateInput.removeAttribute('required');
    if (reasonInput) reasonInput.removeAttribute('required');
    
    // Reset steps - show step 1 only
    document.querySelectorAll('.step').forEach(el => {
      el.classList.add('hidden');
    });
    const step1 = document.getElementById('step1');
    if (step1) step1.classList.remove('hidden');
    
    // Reset progress bars
    const progressBar1 = document.getElementById('progress-bar-1');
    const progressBar2 = document.getElementById('progress-bar-2');
    if (progressBar1) progressBar1.style.width = '0%';
    if (progressBar2) progressBar2.style.width = '0%';
    
    // Show the modal
    transferModal.classList.remove('hidden');

    // Fetch and display all users immediately
    await fetchUsers();
  };

  // Close modal
  window.closeTransferModal = function () {
    transferModal.classList.add('hidden');
    
    // Clear form
    if (recipientSearch) recipientSearch.value = '';
    const recipientResults = document.getElementById('recipient_results');
    if (recipientResults) recipientResults.innerHTML = '';
  };

  // Step navigation functions - fix for buttons not working
  window.nextStep = function(step) {
    // Validate current step before proceeding
    if (!validateCurrentStep()) {
      return;
    }
    
    // Hide all steps
    document.querySelectorAll('.step').forEach(el => {
      el.classList.add('hidden');
    });
    
    // Show requested step
    const nextStepElement = document.getElementById(`step${step}`);
    if (nextStepElement) {
      nextStepElement.classList.remove('hidden');
    }
    
    // Add required attributes to visible form fields
    if (step === 2) {
      const transferDateInput = document.getElementById('transfer_date');
      const reasonInput = document.getElementById('reason');
      
      if (transferDateInput) transferDateInput.setAttribute('required', 'required');
      if (reasonInput) reasonInput.setAttribute('required', 'required');
    } else {
      // Remove required attributes when not on step 2
      const transferDateInput = document.getElementById('transfer_date');
      const reasonInput = document.getElementById('reason');
      
      if (transferDateInput) transferDateInput.removeAttribute('required');
      if (reasonInput) reasonInput.removeAttribute('required');
    }
    
    // Update progress bars
    const progressBar1 = document.getElementById('progress-bar-1');
    const progressBar2 = document.getElementById('progress-bar-2');
    
    if (progressBar1 && step > 1) {
      progressBar1.style.width = '100%';
    }
    if (progressBar2 && step > 2) {
      progressBar2.style.width = '100%';
    }
    
    // Update preview in step 3
    if (step === 3) {
      updatePreview();
    }
  };
  
  window.prevStep = function(step) {
    // Hide all steps
    document.querySelectorAll('.step').forEach(el => {
      el.classList.add('hidden');
    });
    
    // Show requested step
    const prevStepElement = document.getElementById(`step${step}`);
    if (prevStepElement) {
      prevStepElement.classList.remove('hidden');
    }
    
    // Add/remove required attributes based on step
    if (step === 2) {
      const transferDateInput = document.getElementById('transfer_date');
      const reasonInput = document.getElementById('reason');
      
      if (transferDateInput) transferDateInput.setAttribute('required', 'required');
      if (reasonInput) reasonInput.setAttribute('required', 'required');
    } else {
      // Remove required attributes when not on step 2
      const transferDateInput = document.getElementById('transfer_date');
      const reasonInput = document.getElementById('reason');
      
      if (transferDateInput) transferDateInput.removeAttribute('required');
      if (reasonInput) reasonInput.removeAttribute('required');
    }
    
    // Update progress bars
    const progressBar1 = document.getElementById('progress-bar-1');
    const progressBar2 = document.getElementById('progress-bar-2');
    
    if (progressBar1 && step < 2) {
      progressBar1.style.width = '0%';
    }
    if (progressBar2 && step < 3) {
      progressBar2.style.width = '0%';
    }
  };
  
  // Validate current step
  function validateCurrentStep() {
    // Find current visible step
    let currentStep = 1;
    document.querySelectorAll('.step').forEach((step, index) => {
      if (!step.classList.contains('hidden')) {
        currentStep = index + 1;
      }
    });
    
   // Validate based on current step
if (currentStep === 1) {
  const recipientId = document.getElementById('recipient').value;
  if (!recipientId) {
    showToast('Please select a recipient.', 'error');
    return false;
  }
} else if (currentStep === 2) {
  const transferDate = document.getElementById('transfer_date').value;
  const reason = document.getElementById('reason').value;

  if (!transferDate) {
    showToast('Please select a transfer date.', 'error');
    return false;
  }

  const selectedDate = new Date(transferDate);
  const today = new Date();
  today.setHours(0, 0, 0, 0); // Normalize time to compare just the date

  if (selectedDate < today) {
    showToast('Transfer date cannot be in the past.', 'error');
    return false;
  }

  if (!reason.trim()) {
    showToast('Please provide a reason for the transfer.', 'error');
    return false;
  }
}

    
    return true;
  }
  
  // Add event listener for step1NextBtn
  const step1NextBtn = document.getElementById('step1NextBtn');
  if (step1NextBtn) {
    step1NextBtn.addEventListener('click', function() {
      nextStep(2);
    });
  }
  
  // Update preview function
  function updatePreview() {
    const equipmentId = document.getElementById('equipment_id').value;
    const recipientId = document.getElementById('recipient').value;
    const transferDate = document.getElementById('transfer_date').value;
    const reason = document.getElementById('reason').value;
    
    // Find selected user - convert recipientId to number for comparison if needed
    const selectedUser = users.find(user => user.id == recipientId);
    
    // Get equipment name
    let equipmentName = document.getElementById('equipment_name')?.value || equipmentId;
    
    // Update preview fields
    const previewEquipment = document.getElementById('preview_equipment');
    const previewRecipient = document.getElementById('preview_recipient');
    const previewDate = document.getElementById('preview_date');
    const previewReason = document.getElementById('preview_reason');
    
    if (previewEquipment) {
      previewEquipment.textContent = equipmentName;
    }
    
    if (previewRecipient && selectedUser) {
      previewRecipient.textContent = `${selectedUser.first_name} ${selectedUser.last_name} (${selectedUser.email})`;
    }
    
    if (previewDate) {
      previewDate.textContent = transferDate ? formatDate(transferDate) : 'Not specified';
    }
    
    if (previewReason) {
      previewReason.textContent = reason || 'No reason provided';
    }
  }

  // Handle form submission
  const transferForm = document.getElementById('transferForm');
  if (transferForm) {
    transferForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validate form before submission
      if (!validateCurrentStep()) {
        return;
      }
      
      // Get form data
      const formData = new FormData(this);
      
      // Add recipient ID from the hidden input
      const recipientId = document.getElementById('recipient').value;
      formData.set('recipient_id', recipientId);
      
      // Send form data to server
      fetch('submit_transfer.php', {
        method: 'POST',
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showToast('Transfer request submitted successfully.');
            closeTransferModal();
            fetchAndPopulateCurrentEquipment();
            fetchAndPopulateTransferRequests();
          } else {
            showToast('Error submitting transfer request: ' + (data.message || 'Unknown error'), 'error');
          }
        })
        .catch(error => {
          console.error('Transfer error:', error);
          showToast('An error occurred. Please try again later.', 'error');
        });
    });
  }

  // Cancel transfer
  window.cancelTransfer = function (requestId) {
    const modal = document.getElementById("cancelTransferModal");
    if (!modal) {
      // Fallback if modal doesn't exist
      if (confirm("Are you sure you want to cancel this transfer request?")) {
        cancelTransferRequest(requestId);
      }
      return;
    }
    
    modal.classList.remove("hidden");

    document.getElementById("confirmCancelTransferBtn").onclick = () => {
      modal.classList.add("hidden");
      cancelTransferRequest(requestId);
    };

    document.getElementById("cancelCancelTransferBtn").onclick = () => {
      modal.classList.add("hidden");
    };
  };
  
  // Function to handle the actual cancellation
  function cancelTransferRequest(requestId) {
    fetch('cancel_transfer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'request_id=' + encodeURIComponent(requestId),
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToast('Transfer request cancelled.');
          fetchAndPopulateTransferRequests();
        } else {
          showToast('Cancel failed: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Cancel error:', error);
        showToast('Failed to cancel request.', 'error');
      });
  }

  // Outside modal click closes modal
  window.addEventListener('click', function (event) {
    if (event.target === transferModal) {
      closeTransferModal();
    }
  });

  // Show toast notification
  window.showToast = function(message, type = 'success') {
    // Check if a toast container exists, if not create one
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.id = 'toast-container';
      toastContainer.className = 'fixed bottom-4 right-4 z-50';
      document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `mb-3 p-4 rounded-lg shadow-lg flex items-center ${
      type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white transform transition-all duration-300 translate-y-0 opacity-100`;
    
    toast.innerHTML = `
      <i class="${type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle'} mr-2"></i>
      <span>${message}</span>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate out after delay
    setTimeout(() => {
      toast.classList.replace('translate-y-0', 'translate-y-2');
      toast.classList.replace('opacity-100', 'opacity-0');
      setTimeout(() => {
        toastContainer.removeChild(toast);
      }, 300);
    }, 3000);
  };
  
  // Show error message in a container
  window.showErrorMessage = function(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
      container.innerHTML = `
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fa fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">${message}</p>
            </div>
          </div>
        </div>
      `;
    } else {
      console.error(`Error container #${containerId} not found`);
    }
  };


  function getStatusClass(status) {
  switch ((status || '').toLowerCase()) {
    case 'active':
      return 'bg-green-100 text-green-700';
    case 'inactive':
      return 'bg-yellow-100 text-yellow-700';
    case 'maintenance':
      return 'bg-blue-100 text-blue-700';
    case 'broken':
    case 'damaged':
      return 'bg-red-100 text-red-700';
    default:
      return 'bg-gray-100 text-gray-700';
  }
}

// Fetch current equipment
async function fetchAndPopulateCurrentEquipment() {
  try {
    const response = await fetch('get_current_equipment.php');
    if (!response.ok) throw new Error('Failed to fetch current equipment');
    const { equipment } = await response.json(); // equipment is the correct key

    currentEquipmentTable.innerHTML = '';
    if (!Array.isArray(equipment) || equipment.length === 0) {
      currentEquipmentTable.innerHTML = `<tr><td colspan="6" class="p-3 text-center text-gray-500">No equipment currently assigned</td></tr>`;
      return;
    }

    equipment.forEach(item => {
      const row = document.createElement('tr');
      row.className = 'border-b hover:bg-gray-50';
      row.innerHTML = `
  <td class="p-3" data-equipment-id="${item.equipment_id ?? ''}" title="ID: ${item.equipment_id ?? 'N/A'}">
    ${item.po_jo_no ?? '-'}
  </td>
  <td class="p-3">${item.equipment_name ?? '-'}</td>
  <td class="p-3">${item.category ?? '-'}</td>
  <td class="p-3">${formatDate(item.assigned_at)}</td>
  <td class="p-3">
 <span class="px-2 py-1 rounded-full text-xs ${getStatusClass(item.equipment_status)}">
  ${item.equipment_status ?? '-'}
</span>

</td>

  <td class="p-3 text-center">
  <button 
    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 border border-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition duration-200 transfer-button" 
    data-id="${item.equipment_id}" 
    data-name="${item.equipment_name}"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
    </svg>
    Transfer
  </button>
</td>

`;

      currentEquipmentTable.appendChild(row);
    });
  } catch (error) {
    console.error('Error fetching equipment:', error);
    showErrorMessage('current-equipment-error', 'Unable to load current equipment. Please try again later.');
  }
}



  // Fetch transfer requests
  async function fetchAndPopulateTransferRequests() {
    try {
      const response = await fetch('get_pending_transfer_requests.php');
      if (!response.ok) throw new Error('Failed to fetch transfer requests');
      const { requests } = await response.json();

      transferRequestsTable.innerHTML = '';
      if (!Array.isArray(requests) || requests.length === 0) {
        transferRequestsTable.innerHTML = `<tr><td colspan="6" class="p-3 text-center text-gray-500">No transfer requests found</td></tr>`;
        return;
      }

      requests.forEach(request => {
        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
        row.innerHTML = `
          <td class="p-3">${request.id ?? '-'}</td>
          <td class="p-3">${request.equipment_name ?? '-'}</td>
          <td class="p-3">${request.recipient_name ?? '-'}</td>
          <td class="p-3">${formatDate(request.transfer_date)}</td>
          <td class="p-3">${request.status ?? '-'}</td>
          <td class="p-3">
            ${request.status === 'Pending' ? 
              `<button onclick="cancelTransfer('${request.id}')" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                Cancel
              </button>` : ''}
          </td>
        `;
        transferRequestsTable.appendChild(row);
      });
    } catch (error) {
      console.error('Error fetching transfer requests:', error);
      showErrorMessage('transfer-requests-error', 'Unable to load transfer requests. Please try again later.');
    }
  }

  // Event delegation for transfer buttons
  if (currentEquipmentTable) {
    currentEquipmentTable.addEventListener('click', (e) => {
      const button = e.target.closest('.transfer-button');
      if (button) {
        const equipmentId = button.dataset.id;
        const equipmentName = button.dataset.name;
        window.openTransferModal(equipmentId, equipmentName);
      }
    });
  }

  // Initial load
  fetchAndPopulateCurrentEquipment();
  fetchAndPopulateTransferRequests();
});
</script>


 <!-- User Search Function -->

<script>
document.addEventListener("DOMContentLoaded", function () {
  const transferModal = document.getElementById('transferModal');
  const recipientSearch = document.getElementById('recipient_search');
  const recipientSelect = document.getElementById('recipient');

  const currentEquipmentTable = document.getElementById('current-equipment-body');
  const transferRequestsTable = document.getElementById('transfer-requests-body');

  let users = [];
  
  // Get base URL for image paths - fix for 404 errors
  const getBaseUrl = () => {
    return window.location.origin + '/PMO/User/'; // Adjust this path based on your project structure
  };
  
  // Fix image path to ensure it's absolute
  const fixImagePath = (path) => {
    if (!path) return null;
    
    // If path already starts with http or https, it's already absolute
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }
    
    // Otherwise, append to base URL
    return getBaseUrl() + path;
  };

  // Utility: Format date
  function formatDate(dateString) {
    const date = new Date(dateString);
    return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
  }

  // Utility: Status badge class
  function getStatusClass(status) {
    switch (status) {
      case 'Assigned':
        return 'bg-green-100 text-green-800';
      case 'Pending Transfer':
        return 'bg-yellow-100 text-yellow-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  }

  // Highlight current sidebar link with red theme
  const currentPage = window.location.pathname.split("/").pop();
  document.querySelectorAll("#sidebar nav a").forEach(link => {
    // Add hover effect to all links
    link.classList.add("transition-colors", "duration-200", "hover:bg-red-700", "hover:text-white");
    
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("bg-red-600", "text-white", "border-l-4", "border-red-800", "shadow-md");
    }
  });

  // Fetch users - modified to handle the correct API response format
  async function fetchUsers(query = '') {
    try {
      // Encode the query parameter to handle special characters and spaces
      const encodedQuery = encodeURIComponent(query);
      const response = await fetch(`get_users.php?query=${encodedQuery}`);
      if (!response.ok) throw new Error('Failed to fetch users');
      const data = await response.json();
    
      // The API returns users in data.data, not data.users
      if (data.success && Array.isArray(data.data)) {
        users = data.data; // Use the data array directly
      } else {
        users = [];
        console.error('Unexpected API response format:', data);
      }
    
      // Always display results after fetching
      displayRecipientResults(users, query);
    } catch (error) {
      console.error('Error fetching users:', error);
      alert('An error occurred while fetching users.');
      users = [];
      displayRecipientResults(users, query);
    }
  }

  function displayRecipientResults(userList, query) {
    const recipientResults = document.getElementById('recipient_results');
    recipientResults.innerHTML = '';
    
    // Make sure the results container is visible
    recipientResults.classList.remove('hidden');

    if (!userList.length) {
      recipientResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No users found</div>';
      return;
    }

    // Filter users based on the query
    const filteredUsers = query
      ? userList.filter(user => 
          `${user.first_name} ${user.last_name}`.toLowerCase().includes(query.toLowerCase()) || 
          user.email.toLowerCase().includes(query.toLowerCase())
        )
      : userList;

    if (!filteredUsers.length) {
      recipientResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No users found</div>';
      return;
    }

    filteredUsers.forEach(user => {
      const div = document.createElement('div');
      div.className = 'p-3 hover:bg-slate-50 cursor-pointer transition-colors flex items-center';
      
      // Create a more visually appealing user item with profile picture if available
      let avatarHtml;
      if (user.profile_picture) {
        // Fix the image path to prevent 404 errors
        const fixedImagePath = fixImagePath(user.profile_picture);
        
        avatarHtml = `
          <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center mr-3 overflow-hidden">
            <img 
              src="${fixedImagePath}" 
              alt="${user.first_name}" 
              class="w-full h-full object-cover"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <i class="fas fa-user text-primary-600" style="display:none;"></i>
          </div>
        `;
      } else {
        avatarHtml = `
          <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mr-3">
            <i class="fas fa-user"></i>
          </div>
        `;
      }
      
      div.innerHTML = `
        ${avatarHtml}
        <div>
          <p class="font-medium text-slate-800">${user.first_name} ${user.last_name}</p>
          <p class="text-xs text-slate-500">${user.email}</p>
          ${user.role ? `<p class="text-xs text-slate-400">${user.role}</p>` : ''}
        </div>
      `;
      
      div.onclick = () => {
        document.getElementById('recipient').value = user.id;
        document.getElementById('recipient_search').value = `${user.first_name} ${user.last_name}`;
        
        // Highlight the selected user
        document.querySelectorAll('#recipient_results > div').forEach(el => {
          el.classList.remove('bg-primary-50', 'border-l-4', 'border-primary-500');
        });
        div.classList.add('bg-primary-50', 'border-l-4', 'border-primary-500');
        
        // Enable next button
        const nextBtn = document.getElementById('step1NextBtn');
        if (nextBtn) nextBtn.disabled = false;
      };
      recipientResults.appendChild(div);
    });
  }

  // Add event listener to search input
  if (recipientSearch) {
    recipientSearch.addEventListener('input', function() {
      fetchUsers(this.value);
    });
  }

  // Open modal - modified to immediately display all users
  window.openTransferModal = async function (equipmentId, equipmentName) {
    document.getElementById('equipment_id').value = equipmentId;
    const equipmentNameInput = document.getElementById('equipment_name');
    if (equipmentNameInput) {
      equipmentNameInput.value = equipmentName;
    }

    // Reset form state
    document.getElementById('recipient').value = '';
    document.getElementById('recipient_search').value = '';
    
    const step1NextBtn = document.getElementById('step1NextBtn');
    if (step1NextBtn) step1NextBtn.disabled = true;
    
    // Reset form validation - remove required attributes until the field is visible
    const transferDateInput = document.getElementById('transfer_date');
    const reasonInput = document.getElementById('reason');
    
    if (transferDateInput) transferDateInput.removeAttribute('required');
    if (reasonInput) reasonInput.removeAttribute('required');
    
    // Reset steps - show step 1 only
    document.querySelectorAll('.step').forEach(el => {
      el.classList.add('hidden');
    });
    const step1 = document.getElementById('step1');
    if (step1) step1.classList.remove('hidden');
    
    // Reset progress bars
    const progressBar1 = document.getElementById('progress-bar-1');
    const progressBar2 = document.getElementById('progress-bar-2');
    if (progressBar1) progressBar1.style.width = '0%';
    if (progressBar2) progressBar2.style.width = '0%';
    
    // Show the modal
    transferModal.classList.remove('hidden');

    // Fetch and display all users immediately
    await fetchUsers();
  };

  // Close modal
  window.closeTransferModal = function () {
    transferModal.classList.add('hidden');
    
    // Clear form
    if (recipientSearch) recipientSearch.value = '';
    const recipientResults = document.getElementById('recipient_results');
    if (recipientResults) recipientResults.innerHTML = '';
  };
});
</script>



</body>
</html>