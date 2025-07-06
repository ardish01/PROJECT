<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$pdo = getDBConnection();

// Handle different actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'quick_view':
        handleQuickView();
        break;
    case 'search':
        handleSearch();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleQuickView() {
    global $pdo;
    
    $product_id = $_GET['id'] ?? null;
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        echo json_encode(['success' => true, 'product' => $product]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error loading product']);
    }
}

function handleSearch() {
    global $pdo;
    
    $query = $_GET['q'] ?? '';
    $category_id = $_GET['category'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $sort = $_GET['sort'] ?? 'name';
    $order = $_GET['order'] ?? 'ASC';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    try {
        $where_conditions = [];
        $params = [];
        
        // Search query
        if ($query) {
            $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
            $search_term = "%$query%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        // Category filter
        if ($category_id) {
            $where_conditions[] = "p.category_id = ?";
            $params[] = $category_id;
        }
        
        // Price range
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
        
        echo json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_count' => $total_count,
                'limit' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error searching products']);
    }
}
?> 