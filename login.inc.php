<?php

//check login-submit for security
if (isset($_POST['login-submit'])){

    //database connection
    require 'dbh.inc.php';

    //user info
    $mailuid = $_POST['mailuid'];
    $password = $_POST['pwd'];

    //if fields are empty, error
    if (empty($mailuid) || empty($password)){
        header("Location: ../index.php?error=emptyfields");
        exit();
    }

    //chek db if user entered registered password
    else{
        //build query
        $sql = "SELECT * FROM users WHERE uidUsers=?;";

        //get connection string
        $stmt = mysqli_stmt_init($conn);
        //if query fails, error
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../index.php?error=sqlerror");
            exit();
        }
        else{
            //retreive db data
            mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
            //execute prepared statement and grab db data
            mysqli_stmt_execute($stmt);          
            //set db data to variable  
            $result = mysqli_stmt_get_result($stmt);

            //check if queried data has a match
            if($row = mysqli_fetch_assoc($result)){

                //check db for password user entered
                $pwdcheck = password_verify($password, $row['pwdUsers']);
                //wrong password, exit to index
                if($pwdcheck == false){
                    header("Location: ../index.php?error=wrongpwd");
                    exit();
                }
                else if($pwdcheck == true){
                    //check session variables
                    session_start();
                    $_SESSION['userId'] = $row['idUsers'];
                    $_SESSION['userUid'] = $row['uidUsers'];

                    header("Location: ../index.php?login=success");
                    exit();
                }
                else{
                    header("Location: ../index.php?error=wrongpwd");
                    exit();
                }
            }
            //error if no match
            else{
                header("Location: ../index.php?error=nouser");
                exit();
            }
        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}