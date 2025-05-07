<?php
session_start();
require 'db_connect.php'; // Adjust path if needed

// Fetch latest users with status Granted, Rejected, Blocked, and account state
$query = "SELECT id, first_name, last_name, status, is_blocked, block_reason, block_until, account_state, created_at, profile_picture
          FROM users 
          WHERE status IN ('Granted', 'Rejected', 'Blocked') 
             OR is_blocked = 1 
             OR account_state IS NOT NULL
          ORDER BY created_at DESC 
          LIMIT 5";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
        $status = $row['status'];
        $accountState = htmlspecialchars($row['account_state']);
        $isBlocked = $row['is_blocked'] == 1;
        $blockReason = htmlspecialchars($row['block_reason']);
        $blockUntil = $row['block_until'] ? date('Y-m-d H:i:s', strtotime($row['block_until'])) : null;
        $timeAgo = time_elapsed_string($row['created_at']);
        $profilePicture = htmlspecialchars($row['profile_picture'] ?? 'default.png');

        // Combine logic to derive final status
        if ($isBlocked) {
            $finalStatus = 'Blocked';
            if ($blockReason) {
                $finalStatus .= ' - ' . $blockReason;
            }
            if ($blockUntil) {
                $finalStatus .= ' (Until: ' . $blockUntil . ')';
            }
        } elseif (!empty($status) && $status !== 'Pending') {
            $finalStatus = $status;
        } elseif (!empty($accountState)) {
            $finalStatus = $accountState;
        } else {
            $finalStatus = 'Unknown';
        }

        // Decide icon/badge/bg color
        $badgeClass = 'badge-secondary';
        $iconClass = 'fa-question-circle text-gray-400';
        $bgColor = 'bg-gray-100';

        if (str_contains($finalStatus, 'Granted')) {
            $badgeClass = 'badge-success';
            $iconClass = 'fa-check-circle text-green-600';
            $bgColor = 'bg-green-100';
        } elseif (str_contains($finalStatus, 'Rejected')) {
            $badgeClass = 'badge-destructive';
            $iconClass = 'fa-times-circle text-red-600';
            $bgColor = 'bg-red-100';
        } elseif (str_contains($finalStatus, 'Blocked')) {
            $badgeClass = 'badge-muted';
            $iconClass = 'fa-ban text-gray-600';
            $bgColor = 'bg-gray-200';
        } elseif (str_contains($finalStatus, 'Active')) {
            $badgeClass = 'badge-info';
            $iconClass = 'fa-user-check text-blue-600';
            $bgColor = 'bg-blue-100';
        }

        echo <<<HTML
        <div class="flex items-center">
            <div class="mr-4 rounded-full p-2 $bgColor">
                <i class="fas $iconClass"></i>
            </div>
            <div class="flex-1 space-y-1">
                <p class="text-sm font-medium leading-none">
                    $fullName was $finalStatus
                </p>
                <p class="text-sm text-gray-500">
                    $timeAgo
                </p>
            </div>
            <span class="badge $badgeClass">$finalStatus</span>
        </div>
HTML;
    }
} else {
    echo '<p class="text-sm text-gray-500">No recent activity found.</p>';
}

// Helper function to calculate time ago
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $string = [
        'y' => 'year', 'm' => 'month', 'd' => 'day',
        'h' => 'hour', 'i' => 'minute', 's' => 'second'
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
