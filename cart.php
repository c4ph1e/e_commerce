<?php include 'header.php'; ?>
<?php include 'db_connect.php'; ?>

<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $cart_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: cart.php");
    exit();
}

if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        if ($quantity > 0) {
            $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
        }
    }
    
    header("Location: cart.php");
    exit();
}
?>

<h2>Shopping Cart</h2>

<?php
$sql = "SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0):
?>

<form method="post" action="">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total = 0;
            while ($row = mysqli_fetch_assoc($result)):
                $total = $row['price'] * $row['quantity'];
                $grand_total += $total;
            ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <?php if($row['image']): ?>
                            <img src="<?php echo $row['image']; ?>" class="img-thumbnail me-2" style="width: 50px;" alt="<?php echo $row['name']; ?>">
                        <?php else: ?>
                            <img src="img/placeholder.jpg" class="img-thumbnail me-2" style="width: 50px;" alt="Placeholder">
                        <?php endif; ?>
                        <span><?php echo $row['name']; ?></span>
                    </div>
                </td>
                <td>€<?php echo number_format($row['price'], 2); ?></td>
                <td>
                    <input type="number" name="quantity[<?php echo $row['id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" class="form-control" style="width: 70px;">
                </td>
                <td>€<?php echo number_format($total, 2); ?></td>
                <td>
                    <a href="cart.php?action=remove&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Remove</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong>€<?php echo number_format($grand_total, 2); ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <div class="d-flex justify-content-between">
        <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
</form>

<?php else: ?>
<div class="alert alert-info">
    Your cart is empty. <a href="products.php">Continue shopping</a>.
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>