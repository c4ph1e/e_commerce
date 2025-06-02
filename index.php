<?php include 'header.php'; ?>
<?php include 'db_connect.php'; ?>

<div class="jumbotron">
    <h1 class="display-4">Welcome to E-Shop</h1>
    <p class="lead">Your one-stop shop for quality products at affordable prices.</p>
</div>

<h2 class="mt-5 mb-4">Featured Products</h2>

<div class="row">
    <?php
    $sql = "SELECT * FROM products LIMIT 6";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '<div class="col-md-4 mb-4">';
            echo '<div class="card">';
            if($row['image']) {
                echo '<img src="' . $row['image'] . '" class="card-img-top" alt="' . $row['name'] . '">';
            } else {
                echo '<img src="img/placeholder.jpg" class="card-img-top" alt="Placeholder">';
            }
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $row['name'] . '</h5>';
            echo '<p class="card-text">' . substr($row['description'], 0, 100) . '...</p>';
            echo '<p class="card-text"><strong>Price: €' . $row['price'] . '</strong></p>';
            echo '<a href="product.php?id=' . $row['id'] . '" class="btn btn-primary">View Details</a>';
            echo '</div></div></div>';
        }
    } else {
        echo '<div class="col-12"><p>No products available.</p></div>';
    }
    ?>
</div>

<?php include 'footer.php'; ?>