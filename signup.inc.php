<?php

if (isset($_POST['signup-submit'])){
    require 'dbh.inc.php';

    $username = $_POST['uid'];
    $email = $_POST['mail'];
    $password = $_POST['pwd'];
    $passwordRepeat = $_POST['pwd-repeat'];

    //check if any of the fields are empty
    if (empty($username) || empty($email) || empty($password) ||empty($passwordRepeat) ){
        //filled fileds are kept filled if errors are made during signup
        header("Location: ../signup.php?error=emptyfields&uid=".$username."&mail=".$email);
        exit();
    }

    //check for valid email && username
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)){
        header("Location: ../signup.php?error=invalidmail&uid");
        exit();
    }

    //to chec for vlid email
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../signup.php?error=invalidmail&uid=".$username);
        exit();
    }

    //to check for valid username
    else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)){
        header("Location: ../signup.php?error=invaliduid&mail=".$email);
        exit();
    }

    //check if passwordRepeat matches password
    else if($passwordRepeat !== $passwordRepeat){
        header("Location: ../signup.php?error=passwordcheck&uid=".$username."&mail=".$email);
        exit();
    }

    //if ther username if already in use
    else{
        //build query
        $sql = "SELECT uidUsers FROM users WHERE uidUsers=?";

        //get connection string
        $stmt = mysqli_stmt_init($conn);
        //if the query fails,error
        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../signup.php?error=sqlerror");
            exit();
        }
        else{
            //pass info from the user to the DB
            mysqli_stmt_bind_param($stmt, "s", $username);

            //run info from user data inside the DB
            mysqli_stmt_execute($stmt);

            //store results from DB to stmn variable
            mysqli_stmt_store_result($stmt);

            //how many results are in the stmt var (and therefore in the DB)
            $resultCheck = mysqli_stmt_num_rows($stmt);

            if ($resultCheck > 0){
                header("Location: ../signup.php?error=usertaken&mail=".$email);
                exit();
            }
            else{
                $sql = "INSERT INTO users (uidUsers, emailUsers, pwdUsers) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)){
                    header("Location: ../signup.php?error=sqlerror");
                    exit();
                }
                else{//insert user creds into DB

                    //password hashing for security 
                    $hashedpwd = password_hash($password, PASSWORD_DEFAULT);

                    //pass info from the user to the DB
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedpwd);

                    //run info from user data inside the DB
                    mysqli_stmt_execute($stmt);
                    
                    //success
                    header("Location: ../signup.php?signup=success");
                    exit();
                    
                }

            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

}
else{
    header("Location: ../signup.php");
    exit();
}