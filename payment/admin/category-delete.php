<?php require_once('header.php'); 

$host = 'localhost';
$dbname = 'deadstock_db';
$username = 'root';
$password = '';

// Create a PDO instance
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Set PDO error mode to exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once('header.php');

// Check if the delete button was clicked
if (isset($_POST['delete'])) {
    // Get the category name from the form (assuming you have a hidden field with the category name)
    $CATEGORY_NAME = $_POST['category_name'];

    try {
        // Delete the category from the database
        $stmt = $pdo->prepare("DELETE FROM category WHERE CATEGORY_NAME = :category_name");
        $stmt->bindParam(':category_name', $CATEGORY_NAME);
        $stmt->execute();

        // Delete the category image from the database (assuming the image is stored in a separate table)
        $stmt = $pdo->prepare("DELETE FROM category_images WHERE CATEGORY_NAME = :category_name");
        $stmt->bindParam(':category_name', $CATEGORY_NAME);
        $stmt->execute();

        // Redirect to the categories page
        header('Location: categories.php');
        exit();
    } catch (PDOException $e) {
        // Handle any database errors
        echo 'Error deleting category: ' . $e->getMessage();
    }
}

// If the delete button was not clicked, redirect to the categories page
header('Location: categories.php');
exit();
?>
