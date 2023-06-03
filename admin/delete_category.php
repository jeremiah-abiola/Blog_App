<?php
require 'config/database.php';

if(isset($_GET['id'])){ //make sure id is set in url
    $id = filter_var($_GET['id'] , FILTER_SANITIZE_NUMBER_INT);

    //update category id post that belongs to this category to id of uncategorized category
    $update_query = "UPDATE posts SET category_id=10 WHERE category_id=$id";
    $update_result = mysqli_query($connection, $update_query);

    if(!mysqli_errno($connection)){
        //delete category
        $query = "DELETE FROM categories WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);
        $_SESSION['delete-category-success'] = "Category deleted successfully";
    }


    //delete category
    $query = "DELETE FROM categories WHERE id=$id LIMIT 1";
    $result = mysqli_query($connection , $query);
    if(mysqli_errno($connection)){
        $_SESSION['delete-user'] = "Couldn't delete category";
    }
    else{
        $_SESSION['delete-category-success'] = "Successfully deleted category.";
    }
}

header('location: ' . ROOT_URL . 'admin/manage_categories.php');