<?php
// Sessions store data on server side
session_start();

// If user already logged in, redirect to dashboard
if(isset($_SESSION["name"])){
    header("location:dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIATE - Login</title>
    
    <!-- Bootstrap 5 CSS - OFFLINE VERSION -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    
                    <!-- Login Card -->
                    <div class="card login-card">
                        <!-- Header -->
                        <div class="login-header">
                            <h3>SLIATE</h3>
                            <p class="mb-0">Subject Management System</p>
                        </div>
                        
                        <!-- Body -->
                        <div class="card-body p-4">
                            <h5 class="text-center mb-4">Login</h5>
                            
                            <?php
                            // Display saved username from cookie if exists
                            $uname = "";
                            if(isset($_COOKIE["username"])){
                                $uname = $_COOKIE["username"];
                                echo '<div class="alert alert-info">Welcome back, ' . htmlspecialchars($uname) . '!</div>';
                            }
                            ?>
                            
                            <!-- Login Form -->
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                
                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="user_name" class="form-label">Username</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="user_name" 
                                           id="user_name" 
                                           value="<?php echo htmlspecialchars($uname); ?>" 
                                           required>
                                </div>
                                
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="pass" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           name="pass" 
                                           id="pass" 
                                           required>
                                </div>
                                
                                <!-- Login Button -->
                                <div class="d-grid">
                                    <button type="submit" name="btnLogin" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <?php
    // Import database connection
    require_once("dbConnection.php");

    // Process login form submission
    if(isset($_POST["btnLogin"])){
        // Sanitize input to prevent XSS attacks
        $userName = htmlspecialchars($_POST["user_name"]);
        $password = htmlspecialchars($_POST["pass"]);

        // Query to get user data from database
        $query = "SELECT password, role FROM users WHERE name='$userName'";
        $result = mysqli_query($con, $query);

        // Check if user exists
        if(mysqli_num_rows($result) > 0){
            $dbUser = mysqli_fetch_assoc($result);
            
            // Verify password against hashed password in database
            if(password_verify($password, $dbUser["password"])){
                // Login successful
                setcookie("username", $userName, time() + 60, "/");
                $_SESSION["name"] = $userName;
                $_SESSION["role"] = $dbUser["role"];
                header("location:dashboard.php");
                exit();
            } else {
                // Invalid password
                echo '<script>alert("Invalid password!");</script>';
            }
        } else {
            // User not found
            echo '<script>alert("Username not found!");</script>';
        }
    }
    ?>

    <!-- Bootstrap 5 JS - OFFLINE VERSION -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>