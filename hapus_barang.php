<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database configuration file
require 'config.php';

// Check if the kode is set in the URL
if (isset($_GET['kode'])) {
    $kode = $_GET['kode'];

    // Prepare the SQL delete statement
    $sql = "DELETE FROM barang WHERE kode = ?";
    $stmt = $config->prepare($sql);

    try {
        // Execute the delete statement
        if ($stmt->execute([$kode])) {
            // Redirect to the main page with a success message
            header("Location: index.php?success-delete");
        } else {
            // Redirect to the main page with an error message
            header("Location: index.php?error-delete");
        }
    } catch (PDOException $e) {
        // Redirect to the main page with an error message if an exception occurs
        echo "Error: " . $e->getMessage();
        //header("Location: index.php?error-delete");
    }
} else {
    // Redirect to the main page if the kode is not set
    header("Location: index.php");
}
?>
