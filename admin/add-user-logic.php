<?php
require 'config/database.php';

//get user from form if submit button is clicked

if(isset($_POST['submit'])){
    $firstname = filter_var($_POST['firstname'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);

    $avatar = $_FILES['avatar'];
    
    if(!$firstname){
        $_SESSION['add-user'] = "Please enter your First Name";
    }
    elseif(!$lastname){
        $_SESSION['add-user'] = "Please enter your Last Name";
    }
    elseif(!$username){
        $_SESSION['add-user'] = "Please enter your User Name";
    }
    elseif(!$email){
        $_SESSION['add-user'] = "Please enter a valid email";
    }
    elseif(strlen($createpassword) < 8 || strlen($confirmpassword) < 8){
        $_SESSION['add-user'] = "Password should be 8+ characters";
    }
    elseif(!$avatar['name']){
        $_SESSION['add-user'] = "Please add avatar";
    }

    else{
        if($createpassword !== $confirmpassword){
            $_SESSION['add-user'] = "Passwords do not match!!!";
        }
        else{
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);
            
            $user_check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
            $user_check_result = mysqli_query($connection , $user_check_query);  // to execute sql query
            if(mysqli_num_rows($user_check_result) > 0){
                $_SESSION['add-user'] = "Username or Email already exists";
            }
            else{
                // work on avatar
                // rename avatar
                $time = time();
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = '../images/'. $avatar_name;
                $allowed_files = ['jpg' , 'png' , 'jpg'];
                $extention = explode('.' , $avatar_name);
                $extention = end($extention);
                if(in_array($extention, $allowed_files)){
                    // make sure the image is not too large
                    if($avatar['size'] < 1000000){
                        // upload avatar
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                    }
                    else{
                        $_SESSION['add-user'] = "File size is too large. Should be less than 1mb";
                    }
                }
                else{
                    $_SESSION['add-user'] = "File should be .png, .jpg, or .jpeg";
                }
            }
        }
    }
    //redirect to signup page if there was any problem
    if(isset($_SESSION['add-user'])){
        //pass form data to sign up page.
        $_SESSION['add-user-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add_user.php');
        die();
    }
    else{
        //insert new user to database
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES('$firstname', '$lastname', '$username', '$email', '$hashed_password', '$avatar_name', $is_admin)";

        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if(!mysqli_errno($connection)){
            // redirect to login page with success message
            $_SESSION['add-user-success'] = "New user $firstname $lastname added successfully";
            header('location:' . ROOT_URL . 'admin/manage_users.php');
            die();
        }
    }
}



else{
    header('location: ' . ROOT_URL . 'admin/add_user.php');
    exit();
}