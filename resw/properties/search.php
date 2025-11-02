<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

$isSubDirectory = true;
$page_title = "Search Results - Real Estate Management System";

// Initialize variables with default values
$search_value = '';
$search_price = '';

$num_results = 0;
$result = null;

if (isset($_POST['submit'])) {
    $search_value = isset($_POST['search']) ? mysqli_real_escape_string($con, $_POST['search']) : '';
    $search_price = isset($_POST['search_price']) ? mysqli_real_escape_string($con, $_POST['search_price']) : '';

    // Validate required fields
    if (empty($search_value) || empty($search_price)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: search.php");
        exit();
    }

    // Build the query based on price range
    $query = "SELECT p.*, a.agent_name 
              FROM properties p 
              LEFT JOIN agent a ON p.agent_id = a.agent_id 
              WHERE p.property_title LIKE '%$search_value%' 
              ";

    switch ($search_price) {
        case '1':
            $query .= " AND p.price >= 5000 AND p.price <= 50000";
            break;
        case '2':
            $query .= " AND p.price >= 50000 AND p.price <= 100000";
            break;
        case '3':
            $query .= " AND p.price >= 100000 AND p.price <= 200000";
            break;
        case '4':
            $query .= " AND p.price >= 200000";
            break;
        default:
            $_SESSION['error'] = "Invalid price range selected.";
            header("Location: search.php");
            exit();
    }

    $result = mysqli_query($con, $query);

    if (!$result) {
        $_SESSION['error'] = "A database error occurred.";
        header("Location: search.php");
        exit();
    }

    $num_results = mysqli_num_rows($result);

} else {
    // If no form submission, redirect to index
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo $page_title; ?></title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="../assets/style.css" />
  <link rel="stylesheet" href="../assets/navbar.css" />
  <script src="../assets/jquery-1.9.1.min.js"></script>
  <script src="../assets/bootstrap/js/bootstrap.js"></script>
  <script src="../assets/script.js"></script>

  <!-- Owl stylesheet -->
  <link rel="stylesheet" href="../assets/owl-carousel/owl.carousel.css">
  <link rel="stylesheet" href="../assets/owl-carousel/owl.theme.css">
  <script src="../assets/owl-carousel/owl.carousel.js"></script>
  <!-- Owl stylesheet -->

  <!-- slitslider -->
  <link rel="stylesheet" type="text/css" href="../assets/slitslider/css/style.css" />
  <link rel="stylesheet" type="text/css" href="../assets/slitslider/css/custom.css" />
  <script type="text/javascript" src="../assets/slitslider/js/modernizr.custom.79639.js"></script>
  <script type="text/javascript" src="../assets/slitslider/js/jquery.ba-cond.min.js"></script>
  <script type="text/javascript" src="../assets/slitslider/js/jquery.slitslider.js"></script>
  <!-- slitslider -->

  <style>
    .btn-primary {
      background-color: #563207;
      border-color: #563207;
    }
    
    .btn-primary:hover {
      background-color: #3E2405;
      border-color: #3E2405;
    }

    .btn-success {
      background-color: #563207;
      border-color: #563207;
    }

    .btn-success:hover {
      background-color: #3E2405;
      border-color: #3E2405;
    }

    /* Property Image Styles */
    .properties .image-holder {
      width: 100%;
      position: relative;
      overflow: hidden;
      height: 180px;
    }

    .properties .image-holder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }

    /* Property details styling */
    .properties {
      margin-bottom: 30px;
      padding: 10px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .properties h4 {
      margin: 8px 0;
      font-size: 16px;
      line-height: 1.4;
      height: auto;
      overflow: hidden;
    }

    .properties .price {
      font-size: 13px;
      margin: 3px 0;
      color: #666;
    }

    .properties .btn {
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      .properties .image-holder {
        height: 180px;
      }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../includes/nav.php'; ?>

    <!-- banner -->
    <div class="inside-banner">
        <div class="container">
            <h2>Search Results</h2>
        </div>
    </div>
    <!-- banner -->

    <div class="container">
        <div class="properties-listing spacer">
            <div class="row">
                <div class="col-lg-3 col-sm-4">
                    <div class="search-form">
                        <h4><span class="glyphicon glyphicon-search"></span> Search for</h4>
                        <form action="search.php" method="post" id="searchForm" onsubmit="return validateForm()">
                            <input type="text" class="form-control" name="search" placeholder="Search of Properties" value="<?php echo htmlspecialchars($search_value); ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <select name="search_price" class="form-control">
                                        <option>Price</option>
                                        <option value="1">$5000 - $50,000</option>
                                        <option value="2">$50,000 - $100,000</option>
                                        <option value="3">$100,000 - $200,000</option>
                                        <option value="4">$200,000 - above</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-success" name="submit" type="submit">Find Now</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-9 col-sm-8">
                    <div class="sortby clearfix">
                        <div class="pull-left result">Showing: Search Results</div>
                    </div>
                    <div class="row">
                        <?php if ($num_results > 0): ?>
                            <?php while ($property = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-4 col-sm-6">
                                    <div class="properties">
                                        <div class="image-holder">
                                            <?php 
                                            $images = explode(',', $property['property_img']);
                                            $main_image = !empty($images[0]) ? '../' . $images[0] : '../images/properties/default1.png';
                                            ?>
                                            <img src="<?php echo $main_image; ?>" class="img-responsive" alt="properties">
                                        </div>
                                        <h4><a href="property-detail.php?id=<?php echo $property['property_id']; ?>"><?php echo htmlspecialchars($property['property_title']); ?></a></h4>
                                        <p class="price">Price: Rs<?php echo number_format($property['price'], 2); ?></p>
                                        <p class="price">Floor Space: <?php echo htmlspecialchars($property['floor_space']); ?></p>
                                        <p class="price">Address: <?php echo htmlspecialchars($property['property_address']); ?></p>
                                        <a class="btn btn-primary" href="property-detail.php?id=<?php echo $property['property_id']; ?>">View Details</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-lg-12">
                                <div class="alert alert-info">
                                    No properties found matching your search criteria.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        var search = document.forms["searchForm"]["search"].value;
        var search_price = document.forms["searchForm"]["search_price"].value;
        
        if (search == "" || search_price == "Price") {
            alert("Please fill in all fields");
            return false;
        }
        return true;
    }
    </script>

    <?php include '../includes/footer.php'; ?>

    <?php if(isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo $_SESSION['error']; ?>',
            confirmButtonColor: '#563207'
        });
    </script>
    <?php 
        unset($_SESSION['error']);
    endif; 
    ?>
</body>
</html>