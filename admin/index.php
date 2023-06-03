<?php
include 'partials/header.php';


//fetch current logged in user's post
$current_user_id = $_SESSION['user-id'];
$query = "SELECT id, title, category_id FROM posts WHERE author_id=$current_user_id ORDER BY id DESC";
$posts = mysqli_query($connection, $query);

?>



    <section class="dashboard">
    <?php if(isset($_SESSION['add-post-success'])) : ?>
              <div class="alert__message success container">
                 <p>
                    <?= $_SESSION['add-post-success'];
                    unset($_SESSION['add-post-success']);
                    ?>    
                 </p>
               </div>
     <?php elseif(isset($_SESSION['edit-post'])) : ?>
              <div class="alert__message error container">
                 <p>
                    <?= $_SESSION['edit-post'];
                    unset($_SESSION['edit-post']);
                    ?>    
                 </p>
               </div>
     <?php elseif(isset($_SESSION['edit-post-success'])) : ?>
              <div class="alert__message success container">
                 <p>
                    <?= $_SESSION['edit-post-success'];
                    unset($_SESSION['edit-post-success']);
                    ?>   
                 </p>
               </div>
     <?php elseif(isset($_SESSION['delete-post-success'])) : ?>
              <div class="alert__message success container">
                 <p>
                    <?= $_SESSION['delete-post-success'];
                    unset($_SESSION['delete-post-success']);
                    ?>       
                 </p>
               </div>
     <?php elseif(isset($_SESSION['delete-post'])) : ?>
              <div class="alert__message error container">
                 <p>
                    <?= $_SESSION['delete-post'];
                    unset($_SESSION['delete-post']);
                    ?>  
                 </p>
               </div>                    
    <?php endif;?>
        <div class="container dashboard__container">
            <button class="sidebar__toggle" id="show__sidebar-btn"><i class="uil uil-angle-right-b"></i></button>
            <button class="sidebar__toggle" id="hide__sidebar-btn"><i class="uil uil-angle-left-b"></i></button>
            <aside>
                <ul>
                    <li><a href="add_post.php"><i class="uil uil-pen"></i><h5>Add Post</h5> </a></li>
                    <li><a href="index.php" class="active"><i class="uil uil-postcard"></i><h5>Manage Post</h5> </a></li>
                  <?php if(isset($_SESSION['user_is_admin'])): ?>  
                    <li><a href="add_user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5> </a></li>
                    <li><a href="manage_users.php"><i class="uil uil-users-alt"></i><h5>Manage User</h5> </a></li>
                    <li><a href="add_category.php"><i class="uil uil-edit"></i><h5>Add Category</h5> </a></li>
                    <li><a href="manage_categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5> </a></li>
                  <?php endif ?>  
                </ul>
            </aside>

            <main>
                <h2>Manage Users</h2>
                <?php if(mysqli_num_rows($posts) > 0) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tilte</th>
                            <th>Category</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php while($post = mysqli_fetch_assoc($posts)) :?>
                        <!-- get category title of each post from categories table  -->
                        <?php
                           $category_id = $post['category_id'];
                           $category_query = "SELECT title FROM categories WHERE id=$category_id"; 
                           $category_result = mysqli_query($connection, $category_query);
                           $category = mysqli_fetch_assoc($category_result);
                        ?>
                        <tr>
                            <td><?= $post['title']?></td>
                            <td><?= $category['title']?></td>
                            <td><a href="<?= ROOT_URL ?>admin/edit_post.php?id=<?= $post['id']?>" class="btn sn">Edit</a></td>
                            <td><a href="<?= ROOT_URL ?>admin/delete-post.php?id=<?= $post['id']?>" class="btn sn danger">Delete</a></td>
                            <td>Yes</td>
                        </tr>
                      <?php endwhile;?>
                    </tbody>
                </table>
                <?php else :?>
                    <div class="alert__message error"><?= "No posts found" ?></div> 
                <?php endif?>
            </main>
        </div>
    </section>

<?php
include '../partials/footer.php'

?>
