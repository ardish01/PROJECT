<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();

// Get filter parameters
$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$order = $_GET['order'] ?? 'ASC';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($category_id) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_id;
}

if ($min_price !== '') {
    $where_conditions[] = "p.price >= ?";
    $params[] = $min_price;
}

if ($max_price !== '') {
    $where_conditions[] = "p.price <= ?";
    $params[] = $max_price;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Validate sort field
$allowed_sort_fields = ['name', 'price', 'created_at'];
if (!in_array($sort, $allowed_sort_fields)) {
    $sort = 'name';
}

// Validate order
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_count = $stmt->fetch()['total'];

// Get products
$sql = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $where_clause 
    ORDER BY p.$sort $order 
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$total_pages = ceil($total_count / $limit);

// Get categories for filter
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .products-page {
            padding: 2rem 0;
        }
        
        .filters {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            align-items: end;
        }
        
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover,
        .pagination .current {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination .disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .clear-filters {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .clear-filters:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="index.php">
                        <img src="assets/images/Screenshot 2025-06-27 170834.png" alt="Logo" style="height:40px;vertical-align:middle;margin-right:10px;">
                        <span>R.D.S Gears</span>
                    </a>
                </div>
                
                <div class="nav-menu">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="products.php" class="nav-link active">Products</a>
                    <a href="about.php" class="nav-link">About</a>
                    <a href="contact.php" class="nav-link">Contact</a>
                </div>
                
                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Search products..." id="searchInput" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="user-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <span class="nav-link">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </span>
                            <a href="logout.php" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="nav-link">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="register.php" class="nav-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="cart.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </a>
                    </div>
                </div>
                
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Products Page -->
    <div class="products-page">
        <div class="container">
            <h1>Products</h1>
            
            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="products.php" id="filterForm">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="min_price">Min Price</label>
                            <input type="number" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" min="0" step="0.01">
                        </div>
                        
                        <div class="filter-group">
                            <label for="max_price">Max Price</label>
                            <input type="number" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" min="0" step="0.01">
                        </div>
                        
                        <div class="filter-group">
                            <label for="sort">Sort By</label>
                            <select id="sort" name="sort">
                                <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name</option>
                                <option value="price" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Price</option>
                                <option value="created_at" <?php echo $sort === 'created_at' ? 'selected' : ''; ?>>Newest</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="order">Order</label>
                            <select id="order" name="order">
                                <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                                <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <button type="button" class="clear-filters" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </form>
            </div>
            
            <!-- Results Info -->
            <div class="results-info">
                <div>
                    <strong><?php echo $total_count; ?></strong> products found
                    <?php if ($search || $category_id || $min_price || $max_price): ?>
                        <span style="color: #666;">(filtered)</span>
                    <?php endif; ?>
                </div>
                <div>
                    Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                </div>
            </div>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center" style="padding: 4rem;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search criteria or browse all products.</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-overlay">
                                <button class="btn-quick-view" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-price">
                                <span class="price">NRS <?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span class="stock in-stock">In Stock</span>
                                <?php else: ?>
                                    <span class="stock out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i> Previous</span>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="quickViewContent"></div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function clearFilters() {
            window.location.href = 'products.php';
        }
        
        // Auto-submit form when filters change
        document.getElementById('filterForm').addEventListener('change', function() {
            this.submit();
        });
    </script>
</body>
</html> 