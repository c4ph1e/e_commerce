<?php include 'header.php'; ?>
<?php include 'db_connect.php'; ?>

<h2 class="mb-4">Our Products</h2>

<?php
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$sql .= " ORDER BY name ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$categories_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
$categories_result = mysqli_query($conn, $categories_query);
?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="get" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo empty($category) ? 'All Categories' : htmlspecialchars($category); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                    <li><a class="dropdown-item" href="products.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>">All Categories</a></li>
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <li><a class="dropdown-item" href="products.php?category=<?php echo urlencode($cat['category']); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo htmlspecialchars($cat['category']); ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if($row['image']): ?>
                        <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                    <?php else: ?>
                        <img src="img/placeholder.jpg" class="card-img-top" alt="Placeholder">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text"><?php echo substr($row['description'], 0, 100); ?>...</p>
                        <p class="card-text"><strong>Price: €<?php echo number_format($row['price'], 2); ?></strong></p>
                        <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">No products found.</div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>