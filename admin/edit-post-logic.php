<?php
require 'config/database.php';

//make sure submit button was clicked

if(isset($_POST['submit'])){
    $id = filter_var($_POST['id'] , FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'] , FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'] , FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    //set is featured to 0 if it is unchecked
    $is_featured = $is_featured == 1 ?: 0;

    //check and validate input values
    if(!$title){
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form title on edit post page";
    }
    elseif(!$category_id){
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form category on edit post page";
    }
    elseif(!$body){
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form body on edit post page";
    }
    else{
       //delete existing thumbnail if new thumbnail is avaliable
       if($thumbnail['name']){
        $previous_thumbnail_path = '../images/' .$previous_thumbnail_name;
        if($previous_thumbnail_path){
            unlink($previous_thumbnail_path);
          }

          //work on the new thumbnail
          //work on thumbnail
        //rename image
        $time = time();
        $thumbnail_name = $time . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = '../images/'. $thumbnail_name;
        $allowed_files = ['jpg' , 'png' , 'jpg'];
        $extention = explode('.' , $thumbnail_name); 
        $extention = end($extention);
        if(in_array($extention, $allowed_files)){
            // make sure the image is not too large
            if($thumbnail['size'] < 2_000_000){
                // upload thumbnail
                move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            }
            else{
                $_SESSION['edit-post'] = "Couldn't update post. Thumbnail size is too large. Should be less than 2mb";
            }
         }
        else{
            $_SESSION['edit-post'] = "Couldn't update post. Thumbnail should be .png, .jpg, or .jpeg";
         }
       } 
    }

     //redirect to manage categories page if there was any problem
     if(isset($_SESSION['edit-post'])){
        $_SESSION['add-post-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/add_post.php');
        die();
    }
    else{
        //set is_featured of all post to 0 if is_featured fro this post is 1
        if($is_featured == 1){
           $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
           $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        //set thumbnail name if new one was uploaded, else keep old thumbnail name
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;
        $query = "UPDATE posts SET title='$title', body='$body', thumbnail='$thumbnail_to_insert', category_id=$category_id, is_featured=$is_featured WHERE id=$id LIMIT 1 ";
        $result = mysqli_query($connection, $query);
    }
    if(!mysqli_errno($connection)){
        // redirect to manage post page with success message
        $_SESSION['edit-post-success'] = "Post updated successfully";
    }
}

header('location: ' . ROOT_URL . 'admin/');
die();