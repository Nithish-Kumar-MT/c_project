<?php
$connection = mysqli_connect("localhost", "root", "", "deadstock_db");

// Login functionality
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email and password are correct
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $connection->query($query);
    if ($result->num_rows == 1) {
        // Successful login
        echo '<script>alert("Login Sucessful");
        window.location.href ="header.php"; 
        </script>';
        // header("Location:index.html");
    } else {
        echo '<script>
        alert("Wrong credentials. Please Try Again");
        window.location.href ="header.php";  
        </script>';
        // header("Location:index.html");
    }
}

$connection->close();
?>