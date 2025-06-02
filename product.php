<?php include 'header.php'; ?>
<?php include 'db_connect.php'; ?>

<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($result);
?>

<div class="container product-details">
    <div class="row">
        <div class="col-md-6">
            <?php if($product['image']): ?>
                <img src="<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo $product['name']; ?>">
            <?php else: ?>
                <img src="img/placeholder.jpg" class="img-fluid rounded" alt="Placeholder">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h2><?php echo $product['name']; ?></h2>
            <p class="lead">€<?php echo number_format($product['price'], 2); ?></p>
            
            <?php if($product['category']): ?>
                <p><strong>Category:</strong> <?php echo $product['category']; ?></p>
            <?php endif; ?>
            
            <?php if($product['stock'] > 0): ?>
                <p class="text-success"><strong>In Stock:</strong> <?php echo $product['stock']; ?> available</p>
            <?php else: ?>
                <p class="text-danger"><strong>Out of Stock</strong></p>
            <?php endif; ?>
            
            <div class="my-4">
                <h4>Description</h4>
                <p><?php echo nl2br($product['description']); ?></p>
            </div>
            
            <?php if($product['stock'] > 0): ?>
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg disabled">Out of Stock</button>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="products.php" class="btn btn-outline-secondary">Back to Products</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>