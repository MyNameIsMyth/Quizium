<?php
require_once 'config.php';

try {
    // Get table structure
    $stmt = $pdo->query("DESCRIBE Users");
    echo "<h2>Users Table Structure:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'Users'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>Table 'Users' does not exist!</p>";
    } else {
        echo "<p style='color: green;'>Table 'Users' exists.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?> 