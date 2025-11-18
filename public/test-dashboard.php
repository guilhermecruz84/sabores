<?php
// Simple test to check if CodeIgniter is working
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "PHP Version: " . phpversion() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";

// Test database connection
try {
    $mysqli = new mysqli('br404.hostgator.com.br', 'guil5541_sabores', 'Sm2025.#', 'guil5541_sabores', 3306);
    if ($mysqli->connect_error) {
        echo "Database Error: " . $mysqli->connect_error . "<br>";
    } else {
        echo "Database Connection: OK<br>";

        // Test query
        $result = $mysqli->query('SELECT COUNT(*) as count FROM chamados');
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Chamados Count: " . $row['count'] . "<br>";
        }

        $mysqli->close();
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
}

echo "<br>If you see this, PHP is working!<br>";
echo "<a href='/dashboard'>Try Dashboard</a>";
?>
