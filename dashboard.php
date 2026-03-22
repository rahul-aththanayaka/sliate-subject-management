<?php
session_start();

// Check user authentication
if(!isset($_SESSION["name"])){
    header("location:index.php");
    exit();
}

// Import database connection
require_once("dbConnection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIATE - Dashboard</title>
    
    <!-- Bootstrap 5 CSS - OFFLINE VERSION -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-container">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-dark navbar-custom">
        <div class="container-fluid">
            <span class="navbar-brand">SLIATE Dashboard</span>
            <div class="text-white">
                <?php echo htmlspecialchars($_SESSION["name"]) . " (" . htmlspecialchars($_SESSION["role"]) . ")"; ?>
                <a href="logout.php" class="btn btn-light btn-sm ms-2">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        
        <?php
        // Initialize form variables
        $fcode = "";
        $fname = "";
        $fcredits = "";
        $fcourse = "";
        
        // Message variables
        $message = "";
        $messageType = "";
        
        // FIND SUBJECT
        if(isset($_POST["btnFind"])){
            $searchCode = htmlspecialchars($_POST["find"]);
            
            if(empty($searchCode)){
                $message = "Please enter a subject code!";
                $messageType = "warning";
            } else {
                $query = "SELECT * FROM subjects WHERE code='" . $searchCode . "'";
                $result = mysqli_query($con, $query);

                if(mysqli_num_rows($result) > 0){
                    $row = mysqli_fetch_assoc($result);
                    $fcode = $row["code"];
                    $fname = $row["name"];
                    $fcredits = $row["credits"];
                    $fcourse = $row["course"];
                    $message = "Subject found!";
                    $messageType = "success";
                } else {
                    $message = "Subject not found!";
                    $messageType = "danger";
                }
            }
        }

        // INSERT (SAVE) - IMPROVED ERROR HANDLING
        if(isset($_POST["btnSave"])){
            $code = htmlspecialchars($_POST["code"]);
            $name = htmlspecialchars($_POST["name"]);
            $credits = htmlspecialchars($_POST["credits"]);
            $course = htmlspecialchars($_POST["course"]);
            
            if(empty($code) || empty($name) || empty($credits) || empty($course)){
                $message = "All fields are required!";
                $messageType = "warning";
            } else {
                // Check if subject code already exists
                $checkQuery = "SELECT code FROM subjects WHERE code='" . $code . "'";
                $checkResult = mysqli_query($con, $checkQuery);
                
                if(mysqli_num_rows($checkResult) > 0){
                    // Subject code already exists
                    $message = "Subject code '" . $code . "' already exists! Please use a different code.";
                    $messageType = "danger";
                } else {
                    // Code is unique, proceed with insert
                    $query = "INSERT INTO subjects (code, name, credits, course) VALUES('$code', '$name', $credits, '$course')";
                    $result = mysqli_query($con, $query);

                    if($result){
                        $message = "Subject saved successfully!";
                        $messageType = "success";
                        $fcode = $fname = $fcredits = $fcourse = "";
                    } else {
                        $message = "Error saving subject. Please try again.";
                        $messageType = "danger";
                    }
                }
            }
        }

        // UPDATE - IMPROVED ERROR HANDLING
        if(isset($_POST["btnUpdate"])){
            $code = htmlspecialchars($_POST["code"]);
            $name = htmlspecialchars($_POST["name"]);
            $credits = htmlspecialchars($_POST["credits"]);
            $course = htmlspecialchars($_POST["course"]);
            
            if(empty($code) || empty($name) || empty($credits) || empty($course)){
                $message = "All fields are required!";
                $messageType = "warning";
            } else {
                // Check if subject code exists before updating
                $checkQuery = "SELECT code FROM subjects WHERE code='" . $code . "'";
                $checkResult = mysqli_query($con, $checkQuery);
                
                if(mysqli_num_rows($checkResult) == 0){
                    // Subject code doesn't exist
                    $message = "Subject code '" . $code . "' not found!";
                    $messageType = "danger";
                } else {
                    // Code exists, proceed with update
                    $query = "UPDATE subjects SET name='$name', credits=$credits, course='$course' WHERE code='$code'";
                    $result = mysqli_query($con, $query);

                    if($result){
                        $message = "Subject updated successfully!";
                        $messageType = "success";
                    } else {
                        $message = "Error updating subject. Please try again.";
                        $messageType = "danger";
                    }
                }
            }
        }

        // DELETE
        if(isset($_POST["btnDelete"])){
            $code = htmlspecialchars($_POST["dcode"]);
            $query = "DELETE FROM subjects WHERE code='$code'";
            $result = mysqli_query($con, $query);

            if($result){
                $message = "Subject deleted successfully!";
                $messageType = "success";
            } else {
                $message = "Delete failed!";
                $messageType = "danger";
            }
        }

        // Display message
        if(!empty($message)){
            echo '<div class="alert alert-' . $messageType . ' alert-dismissible fade show">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        }
        ?>

        <!-- Cards Row -->
        <div class="row">
            
            <!-- Find Subject Card -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Find Subject</div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <label class="form-label">Subject Code</label>
                            <input type="text" class="form-control mb-2" name="find" required>
                            <button type="submit" name="btnFind" class="btn btn-primary w-100">Search</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Subject Card -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Add / Edit Subject</div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="mainForm">
                            
                            <label class="form-label">Subject Code</label>
                            <input type="text" class="form-control mb-2" name="code" value="<?php echo htmlspecialchars($fcode); ?>" required>

                            <label class="form-label">Subject Name</label>
                            <input type="text" class="form-control mb-2" name="name" value="<?php echo htmlspecialchars($fname); ?>" required>

                            <label class="form-label">Credits</label>
                            <input type="number" class="form-control mb-2" name="credits" value="<?php echo htmlspecialchars($fcredits); ?>" min="1" required>

                            <label class="form-label">Course</label>
                            <select name="course" class="form-select mb-3" required>
                                <option value="">-- Select --</option>
                                <option value="HNDIT" <?php echo ($fcourse == "HNDIT") ? "selected" : ""; ?>>HNDIT</option>
                                <option value="HNDE" <?php echo ($fcourse == "HNDE") ? "selected" : ""; ?>>HNDE</option>
                                <option value="HNDA" <?php echo ($fcourse == "HNDA") ? "selected" : ""; ?>>HNDA</option>
                                <option value="HNDMGT" <?php echo ($fcourse == "HNDMGT") ? "selected" : ""; ?>>HNDMGT</option>
                            </select>

                            <div class="row">
                                <div class="col-4">
                                    <button type="submit" name="btnSave" class="btn btn-success w-100">Save</button>
                                </div>
                                <div class="col-4">
                                    <button type="submit" name="btnUpdate" class="btn btn-warning w-100">Update</button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-secondary w-100" onclick="clearForm()">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Subject List Table -->
        <div class="card">
            <div class="card-header">
                Subject List
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="d-inline float-end">
                    <button type="submit" name="btnLoad" class="btn btn-light btn-sm">Load Data</button>
                </form>
            </div>
            <div class="card-body">
                <?php
                // Load table data
                if(isset($_POST["btnLoad"]) || isset($_POST["btnSave"]) || isset($_POST["btnDelete"])){
                    LoadData($con);
                } else {
                    echo '<p class="text-center text-muted">Click "Load Data" to view subjects</p>';
                }
                ?>
            </div>
        </div>

    </div>

    <?php
    // Function to display all subjects
    function LoadData($con){
        $query = "SELECT * FROM subjects";
        $result = mysqli_query($con, $query);

        if(mysqli_num_rows($result) > 0){
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped">';
            echo '<thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            
            while($row = mysqli_fetch_assoc($result)){
                echo '<tr>';
                    echo '<td>' . htmlspecialchars($row["id"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["code"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["credits"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["course"]) . '</td>';
                    echo '<td>
                            <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post" class="d-inline" onsubmit="return confirm(\'Delete ' . htmlspecialchars($row["code"]) . '?\')">
                                <input type="hidden" name="dcode" value="' . htmlspecialchars($row["code"]) . '"/>
                                <button type="submit" name="btnDelete" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                          </td>';
                echo '</tr>';
            }
            
            echo '</tbody></table></div>';
        } else {
            echo '<p class="text-center">No subjects found</p>';
        }
    }

    mysqli_close($con);
    ?>

    <!-- Bootstrap JS - OFFLINE VERSION -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Clear form function
        function clearForm() {
            document.getElementById('mainForm').reset();
        }
    </script>
</body>
</html>