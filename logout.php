<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

        session_start();

        session_unset();//free session variables (remove value)
        session_destroy();//destroy/remove session

        //Redirect to the Login Page (index.php)
        header("location:index.php");
        exit();
    ?>
</body>
</html>