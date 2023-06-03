<?php
include 'config/database.php';

if(isset($_GET['id'])){
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    //fetch post from database in order to delete thumbnail from images folder
    $query = "SELECT * FROM posts WHERE id=$id";
    $result = mysqli_query($connection,$query);

    //make sure one record is returned
    if(mysqli_num_rows($result) == 1){
        $post = mysqli_fetch_assoc($result);
        $thumbnail_name  = $post['thumbnail'];
        $thumbnail_path = '../images/' . $thumbnail_name;
        if($thumbnail_path){
            unlink($thumbnail_path);

            //delete posts from post table
            $delete_post_query = "DELETE FROM posts WHERE id=$id LIMIT 1";
            $delete_post_result = mysqli_query($connection, $delete_post_query);

            if(mysqli_errno($connection)){
                $_SESSION['delete-post'] = "Couldn't delete post";
            }
            else{
                $_SESSION['delete-post-success'] = "Successfully deleted post.";
            }
        }
    }
}

header('location: ' . ROOT_URL . 'admin/' );
die();
