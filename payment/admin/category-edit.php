<?php require_once('header.php'); ?>

<?php
// Get the category ID from the URL parameter
$category_id = $_GET['id'];

// Retrieve the category details from the database
$statement = $pdo->prepare("SELECT * FROM category WHERE CATEGORY_ID = ?");
$statement->execute(array($category_id));
$category = $statement->fetch();

// Check if the category exists
if (!$category) {
    header('Location: categories.php');
    exit();
}

// Set the existing values for the form fields
$category_name = $category['CATEGORY_NAME'];
$category_image = $category['CATEGORY_IMAGE'];

if (isset($_POST['update'])) {
    // Update the category details
    $new_category_name = $_POST['CATEGORY_NAME'];
    if (!empty($_FILES['CATEGORY_IMAGE']['name'])) {
        $new_category_image = $_FILES['CATEGORY_IMAGE']['name'];
        // Update the category image file
        $target_dir = '/../assets/uploads/'; // adjust the upload directory as needed
        $target_file = $target_dir . basename($new_category_image);
        move_uploaded_file($_FILES['CATEGORY_IMAGE']['name'], $target_file);
    } else {
        $new_category_image = $category_image; // keep the existing image
    }

    // Update the category details in the database
    $statement = $pdo->prepare("UPDATE category SET CATEGORY_NAME = ?, CATEGORY_IMAGE = ? WHERE CATEGORY_ID = ?");
    $statement->execute(array($new_category_name, $new_category_image, $category_id));

    $success_message = 'Category updated successfully.';
}

?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Category</h1>
    </div>
    <div class="content-header-right">
        <a href="categories.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Category Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="CATEGORY_NAME"
                                    value="<?php echo $category_name; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Category Image <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="file" id="category_image" name="CATEGORY_IMAGE"
                                    accept=".png, .jpg, .jpeg, .gif">
                                <br>
                                <img src="<?php echo $target_dir . $category_image; ?>"
                                    alt="<?php echo $category_image; ?>" width="100">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="update">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>