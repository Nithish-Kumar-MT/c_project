<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Categories</h1>
	</div>
	<div class="content-header-right">
		<a href="category-add.php" class="btn btn-primary btn-sm">Add New</a>
	</div>
</section>


<section class="content">

  <div class="row">
    <div class="col-md-12">


      <div class="box box-info">
        
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-hover table-striped">
			<thead>
			    <tr>
			        <th>#</th>
			        <th>Category Name</th>
                    <th>picture</th>
			        <th>Action</th>
			    </tr>
			</thead>
            <tbody>
            	<?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * FROM category ORDER BY CATEGORY_ID DESC");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['CATEGORY_NAME']; ?></td>
						<td><?php echo $row['CATEGORY_IMAGE']; ?></td>
                        <td>
                           
                        </td>
	                    <td>
	                        <a href="category-edit.php?id=<?php echo $row['CATEGORY_ID']; ?>" class="btn btn-primary btn-xs">Edit</a>
	                        <a href="#" class="btn btn-danger btn-xs"  data-toggle="modal" data-target="#confirm-delete-<?php echo $row['CATEGORY_ID']; ?>">Delete</a>
	                    </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
        </div>
      </div>
  

</section>

<?php
foreach ($result as $row) {
?>
<form  method="POST" action="category-delete.php">
    <div class="modal fade" id="confirm-delete-<?php echo $row['CATEGORY_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure want to delete this item?</p>
                    <p style="color:red;">Be careful! All products, mid level categories and end level categories under this top lelvel category will be deleted from all the tables like order table, payment table, size table, color table, rating table etc.</p>
                    <input type="hidden" name="category_name" value="<?php echo $row['CATEGORY_NAME']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
}
?>

<?php require_once('footer.php'); ?>