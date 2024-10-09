<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form'])) {
    $valid = 1;

    if (empty($_POST['CATEGORY_NAME'])) {
        $valid = 0;
        $error_message .= "Category Name can not be empty<br>";

    } else {
        // Duplicate Category checking
        $statement = $pdo->prepare("SELECT * FROM category WHERE CATEGORY_NAME=?");
        $statement->execute(array($_POST['CATEGORY_NAME']));
        $total = $statement->rowCount();
        if ($total) {
            $valid = 0;
            $error_message .= "Top Category Name already exists<br>";
        }
    }

    if ($valid == 1) {
        // Saving data into the main table category
        $category_image = $_FILES['CATEGORY_IMAGE']['name'];
        $target_dir = '/../assets/uploads/'; // adjust the upload directory as needed
        $target_file = $target_dir . basename($category_image);
        move_uploaded_file($_FILES['CATEGORY_IMAGE']['name'], $target_file);

        $statement = $pdo->prepare("INSERT INTO category (CATEGORY_NAME, CATEGORY_IMAGE) VALUES (:CATEGORY_NAME, :CATEGORY_IMAGE)");
        $statement->execute([':CATEGORY_NAME' => $_POST['CATEGORY_NAME'], ':CATEGORY_IMAGE' => $category_image]);

        $success_message = 'Top Category is added successfully.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Category</h1>
    </div>
    <div class="content-header-right">
        <a href="categories.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if ($error_message): ?>
                <div class="callout callout-danger">
                    <p>
                        <?php echo $error_message; ?>
                    </p>
                </div>
            <?php endif; ?>
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
                                <input type="text" class="form-control" name="CATEGORY_NAME">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Category Image <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="file" id="category_image" name="CATEGORY_IMAGE"
                                    accept=".png, .jpg, .jpeg, .gif">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>