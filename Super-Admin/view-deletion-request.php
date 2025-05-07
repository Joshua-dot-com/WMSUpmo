<?php
header('Content-Type: application/json');

// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "equipment_database";

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Check if ID is passed
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $query = "SELECT * FROM deletion_requests WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Combine first and last name
            $name = trim($request['first_name'] . ' ' . $request['last_name']);

            // Word-wrap reason at every 50 characters with newlines
            $wrappedReason = wordwrap($request['reason'], 30, "\n", true);

            echo json_encode([
                'success' => true,
                'name' => $name,
                'email' => $request['email'],
                'college' => $request['college'],
                'reason' => $wrappedReason,
                'requested_at' => $request['requested_at']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Request not found.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Error fetching request details: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No ID provided.'
    ]);
}
