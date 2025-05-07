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
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    
    body {
        font-family: 'Inter', sans-serif;
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
    
    .btn-ghost {
        background-color: transparent;
        color: #6b7280;
    }
    
    .btn-ghost:hover {
        background-color: #f9fafb;
        color: #111827;
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
    
    .progress-bar {
        height: 0.5rem;
        border-radius: 0.25rem;
        background-color: #e5e7eb;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    
    .progress-bar-fill {
        height: 100%;
        border-radius: 0.25rem;
    }
</style>
</head>
<body class="bg-gray-50 text-gray-900">
<div class="flex min-h-screen">

      <!-- Sidebar -->
     <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col shadow-lg">
      <div class="py-5 px-6 border-b border-gray-700">
        <div class="flex items-center space-x-2">
          <i class="fas fa-clipboard-check text-primary text-2xl"></i>
          <span class="text-xl font-bold tracking-wide">Admin Portal</span>
        </div>
      </div>
      <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-4">
        <ul class="space-y-1">
          <li>
            <a href="Admin-Dashboard.php" class="flex items-center gap-3 px-4 py-2 rounded-md bg-gray-800 font-medium">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="pending-equipment.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
              <i class="fas fa-clipboard-check"></i>
              <span>Pending Requests</span>
            </a>
          </li>
          <li>
            <a href="Admin-History.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
              <i class="fas fa-history"></i>
              <span>History</span>
            </a>
          </li>
          <li>
            <a href="Admin-Profile.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
              <i class="fas fa-user-circle"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <a href="Admin-settings.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
          </li>
        </ul>

        <div class="pt-8">
          <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Equipment Management</h3>
          <ul class="space-y-1">
          <li>
              <a href="Equipment-Management.php" class="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-800 hover:text-white text-gray-300">
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
      <div class="p-4 border-t border-gray-700">
        <a href="Administration-logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white w-full">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
    </aside>


   <!-- Main Content -->
<div class="flex-1 ml-64" id="main-content">
    <!-- Header -->
    <header class="border-b bg-white shadow-sm" id="dashboard-header">
        <div class="flex h-16 items-center justify-between px-6">
            <div class="flex items-center">
                <button id="sidebar-toggle" class="md:hidden mr-4 text-gray-700 hover:text-gray-900 transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-2xl font-bold text-gray-800" id="dashboard-title">Dashboard</h1>
            </div>

            <div class="flex items-center gap-4">
                <button id="calendar-button" class="btn btn-outline bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span id="calendar-text">March 2025</span>
                </button>
                <div class="relative" id="profile-section">
                    <a href="Admin-Profile.php" class="flex items-center gap-3 px-3 py-1 rounded-full hover:bg-gray-100 transition-all">
                        <div class="h-9 w-9 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center group hover:bg-gray-300">
                            <img id="user-avatar" src="" alt="Profile" class="h-full w-full object-cover hidden" />
                            <i class="fas fa-user text-gray-600 group-hover:text-gray-800" id="fallback-icon"></i>
                        </div>
                        <span id="user-name" class="hidden md:inline font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
                            Admin User
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Dashboard -->
    <main class="p-6" id="dashboard-main">

       <!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="stats-overview">
  <!-- Total Equipment -->
  <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between" id="stat-total-equipment">
    <div class="flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold text-gray-800">Total Equipment</div>
        <div class="text-2xl font-bold text-gray-900" id="total-equipment-count">248</div>
        <div class="text-sm text-green-600 flex items-center mt-1" id="total-equipment-desc">
          <i class="fas fa-arrow-up mr-1"></i> 12% from last month
        </div>
      </div>
      <div class="bg-blue-50 text-blue-600 p-3 rounded-full">
        <i class="fas fa-laptop text-xl"></i>
      </div>
    </div>
  </div>

  <!-- Assigned Equipment -->
  <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between" id="stat-assigned-equipment">
    <div class="flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold text-gray-800">Assigned Equipment</div>
        <div class="text-2xl font-bold text-gray-900" id="assigned-equipment-count">187</div>
        <div class="text-sm text-green-600 mt-1" id="assigned-equipment-desc">75% of total inventory</div>
      </div>
      <div class="bg-green-50 text-green-600 p-3 rounded-full">
        <i class="fas fa-user-check text-xl"></i>
      </div>
    </div>
    <div class="w-full bg-gray-200 h-2 rounded-full mt-4">
      <div class="bg-green-600 h-2 rounded-full" id="assigned-progress-bar" style="width: 75%"></div>
    </div>
  </div>

  <!-- Available Equipment -->
  <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between" id="stat-available-equipment">
    <div class="flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold text-gray-800">Available Equipment</div>
        <div class="text-2xl font-bold text-gray-900" id="available-equipment-count">61</div>
        <div class="text-sm text-gray-500 mt-1" id="available-equipment-desc">25% of total inventory</div>
      </div>
      <div class="bg-gray-50 text-gray-500 p-3 rounded-full">
        <i class="fas fa-box-open text-xl"></i>
      </div>
    </div>
    <div class="w-full bg-gray-200 h-2 rounded-full mt-4">
      <div class="bg-gray-500 h-2 rounded-full" id="available-progress-bar" style="width: 25%"></div>
    </div>
  </div>

  <!-- Pending Requests -->
  <div class="bg-white rounded-lg shadow-lg p-6 flex justify-between items-center" id="stat-pending-requests">
    <div>
      <div class="text-lg font-semibold text-gray-800">Pending Requests</div>
      <div class="text-2xl font-bold text-gray-900" id="pending-request-count">14</div>
      <div class="text-sm text-yellow-600 flex items-center mt-1" id="pending-request-desc">
        <i class="fas fa-arrow-up mr-1"></i> 5 new today
      </div>
    </div>
    <div class="bg-amber-50 text-yellow-600 p-3 rounded-full">
      <i class="fas fa-clock text-xl"></i>
    </div>
  </div>
</div>


        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" id="charts-section">
            <!-- Equipment Distribution -->
            <div class="bg-white rounded-lg border shadow-sm lg:col-span-2" id="equipment-chart-card">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Equipment Distribution</h2>
                    <div class="flex gap-2">
                        <button class="btn btn-outline btn-sm" id="equipment-view-toggle">Monthly</button>
                        <button class="btn btn-outline btn-sm" id="equipment-chart-download">
                            <i class="fas fa-download"></i>
                            <span class="sr-only">Download</span>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="equipmentChart" height="250"></canvas>
                </div>
            </div>

            <!-- Department Distribution -->
            <div class="bg-white rounded-lg border shadow-sm" id="department-chart-card">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Equipment by Department</h2>
                </div>
                <div class="p-6">
                    <canvas id="departmentChart" height="250"></canvas>
                </div>
            </div>
        </div>

       <!-- Recent Activity -->
<div class="bg-white rounded-lg border shadow-sm" id="recent-activity-card">
  <div class="p-6 border-b">
    <h2 class="text-lg font-semibold text-gray-800">Recent Activity</h2>
  </div>
  <div class="p-6">
    <div class="space-y-6" id="activity-list">
      <!-- JS will populate activity logs dynamically -->
    </div>
  </div>
</div>

    </main>
</div>




<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
      });
    }

    // Highlight Active Nav Link
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('#sidebar nav a').forEach(link => {
      if (link.getAttribute('href') === currentPage) {
        link.classList.add('bg-gray-800', 'text-white', 'border-b-2', 'border-red-500');
      }
    });

    // Update Calendar Header
    const calendarText = document.getElementById('calendar-text');
    if (calendarText) {
      const now = new Date();
      const monthYear = now.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
      calendarText.textContent = monthYear;
    }

    // Fetch Admin Info
    fetch('get-admin.php')
      .then(res => res.json())
      .then(data => {
        const userName = document.getElementById('user-name');
        const userAvatar = document.getElementById('user-avatar');
        const fallbackIcon = document.getElementById('fallback-icon');

        if (userName && data.first_name && data.last_name) {
          userName.textContent = `${data.first_name} ${data.last_name}`;
        }

        if (userAvatar && data.profile_picture) {
          const cleanPath = data.profile_picture.replace(/\\/g, '/');
          userAvatar.src = `http://localhost/PMO/${cleanPath}`;
          userAvatar.alt = `${data.first_name} ${data.last_name}`;
          userAvatar.classList.remove('hidden');
          fallbackIcon?.classList.add('hidden');
        }
      })
      .catch(err => console.warn('Failed to fetch admin data:', err));

    // Fetch Equipment Overview Stats
    fetch('equipment-overview.php')
      .then(res => res.json())
      .then(data => {
        const {
          total, assigned, available,
          growth_percentage, pending_requests,
          approved_requests_today, rejected_requests_today
        } = data;

        // Total Equipment
        document.getElementById('total-equipment-count').textContent = total;
        const growthEl = document.getElementById('total-equipment-desc');
        const growthText = `${Math.abs(growth_percentage)}% from last month`;
        growthEl.innerHTML = growth_percentage >= 0
          ? `<i class="fas fa-arrow-up mr-1"></i> <span class="text-green-600">${growthText}</span>`
          : `<i class="fas fa-arrow-down mr-1"></i> <span class="text-red-600">${growthText}</span>`;

        // Assigned Equipment
        document.getElementById('assigned-equipment-count').textContent = assigned;
        const assignedPercent = total ? Math.round((assigned / total) * 100) : 0;
        document.getElementById('assigned-equipment-desc').textContent = `${assignedPercent}% of total inventory`;
        document.getElementById('assigned-progress-bar').style.width = `${assignedPercent}%`;

        // Available Equipment
        document.getElementById('available-equipment-count').textContent = available;
        const availablePercent = total ? Math.round((available / total) * 100) : 0;
        document.getElementById('available-equipment-desc').textContent = `${availablePercent}% of total inventory`;
        document.getElementById('available-progress-bar').style.width = `${availablePercent}%`;

        // Pending Requests
        document.getElementById('pending-request-count').textContent = pending_requests;
        document.getElementById('pending-request-desc').innerHTML =
          `<i class="fas fa-arrow-up mr-1"></i> ${pending_requests} new today`;
      })
      .catch(err => console.error('Failed to fetch equipment overview stats:', err));

    // Equipment Distribution Chart
    const equipmentCanvas = document.getElementById('equipmentChart');
    if (equipmentCanvas) {
      fetch('equipment-stats.php')
        .then(res => res.json())
        .then(chartData => {
          if (chartData?.categoryData?.labels && Array.isArray(chartData.categoryData.datasets)) {
            const ctx = equipmentCanvas.getContext('2d');
            const dataset = chartData.categoryData.datasets[0]; // Assume 1 dataset

            // Generate random colors
            dataset.backgroundColor = dataset.data.map(() => {
              const r = Math.floor(Math.random() * 256);
              const g = Math.floor(Math.random() * 256);
              const b = Math.floor(Math.random() * 256);
              return `rgba(${r}, ${g}, ${b}, 0.8)`;
            });

            new Chart(ctx, {
              type: 'bar',
              data: {
                labels: chartData.categoryData.labels,
                datasets: [dataset]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                  y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Equipments' }
                  },
                  x: {
                    title: { display: true, text: 'Category' }
                  }
                },
                plugins: {
                  legend: { position: 'top' }
                }
              }
            });
          } else {
            console.warn('Invalid equipment chart data structure');
          }
        })
        .catch(err => console.error('Failed to load equipment chart data:', err));
    }

    // Department Distribution Chart
    const departmentCanvas = document.getElementById('departmentChart');
    if (departmentCanvas) {
      fetch('get-department-data.php')
        .then(res => res.json())
        .then(chartData => {
          if (chartData?.labels && chartData?.datasets) {
            const validLabels = chartData.labels.filter(label => label.trim() !== '');
            const validData = chartData.datasets.data.slice(0, validLabels.length);

            const cleanedChartData = {
              labels: validLabels,
              datasets: [{
                data: validData,
                backgroundColor: chartData.datasets.backgroundColor.slice(0, validLabels.length)
              }]
            };

            new Chart(departmentCanvas.getContext('2d'), {
              type: 'doughnut',
              data: cleanedChartData,
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  legend: { position: 'bottom' }
                }
              }
            });
          } else {
            console.warn('Invalid department chart data');
          }
        })
        .catch(err => console.error('Failed to load department chart:', err));
    }

    // Equipment per Category Pie Chart
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
      fetch('equipment-stats.php')
        .then(res => res.json())
        .then(chartData => {
          if (chartData?.categoryData?.labels && Array.isArray(chartData.categoryData.datasets)) {
            const dataset = chartData.categoryData.datasets[0];

            // Apply random colors
            dataset.backgroundColor = dataset.data.map(() => {
              const r = Math.floor(Math.random() * 256);
              const g = Math.floor(Math.random() * 256);
              const b = Math.floor(Math.random() * 256);
              return `rgba(${r}, ${g}, ${b}, 0.7)`;
            });

            new Chart(categoryCanvas.getContext('2d'), {
              type: 'pie',
              data: {
                labels: chartData.categoryData.labels,
                datasets: [dataset]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  legend: { position: 'bottom' },
                  tooltip: {
                    callbacks: {
                      label: function (tooltipItem) {
                        return `${tooltipItem.label}: ${tooltipItem.raw}%`;
                      }
                    }
                  }
                }
              }
            });
          } else {
            console.warn('Invalid category chart data');
          }
        })
        .catch(err => console.error('Failed to load category chart data:', err));
    }

  // Recent Activity Logs
fetch('get-activity.php')
  .then(res => res.json())
  .then(data => {
    const activityList = document.getElementById('activity-list');

    if (!Array.isArray(data) || data.length === 0) {
      activityList.innerHTML = '<p class="text-gray-500">No recent activity found.</p>';
      return;
    }

    console.log('Activity data:', data); // Debug: check structure

    data.forEach(activity => {
      const item = document.createElement('div');
      item.className = 'flex items-start space-x-3 mb-3';

      // Create appropriate message based on activity type
      let message = '';
      let timestamp = '';
      let statusColor = 'bg-blue-500';

      const userName = `${activity.first_name} ${activity.last_name}`;
      
      if (activity.returned_at) {
        // This is a return activity
        message = `${userName} returned equipment (${activity.property_number})`;
        timestamp = activity.returned_at;
        statusColor = 'bg-yellow-500';
        
        if (activity.return_notes) {
          message += ` - Reason: ${activity.return_notes}`;
        }
      } else {
        // This is an assignment activity
        message = `${userName} was assigned equipment (${activity.property_number})`;
        timestamp = activity.assigned_at;
        statusColor = 'bg-green-500';
      }
      
      if (activity.is_pending_transfer === "1") {
        message += " - Pending Transfer";
        statusColor = 'bg-purple-500';
      }

      // Format the timestamp
      const timeFormatted = timestamp ? new Date(timestamp.replace(' ', 'T')).toLocaleString() : 'Unknown time';

      item.innerHTML = `
        <div class="flex-shrink-0 w-2 h-2 rounded-full ${statusColor} mt-2"></div>
        <div>
          <p class="text-sm text-gray-700">${message}</p>
          <p class="text-xs text-gray-400">${timeFormatted}</p>
          <p class="text-xs text-gray-500">PO/JO: ${activity.po_jo_no}</p>
        </div>
      `;

      activityList.appendChild(item);
    });
  })
    .catch(err => {
      console.error('Failed to load activity logs:', err);
      document.getElementById('activity-list').innerHTML =
        '<p class="text-red-500">Failed to load activity logs.</p>';
    });
  }); // Ensure this closing parenthesis matches the opening fetch function

</script>

</body>
</html>
