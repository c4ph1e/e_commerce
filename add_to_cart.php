<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'login_required', 'message' => 'You need to login to add products to cart.']);
    exit();
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    $check_product = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $check_product);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $check_cart = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $check_cart);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $cart_item = mysqli_fetch_assoc($result);
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            $update_cart = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_cart);
            mysqli_stmt_bind_param($stmt, "ii", $new_quantity, $cart_item['id']);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
            }
        } else {
            $add_to_cart = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $add_to_cart);
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $quantity);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add product to cart.']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>