<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

$isSubDirectory = true;
$page_title = "All Listing Properties - Real Estate Management System";

$query = "select * from properties";
$result = mysqli_query($con, $query);

if (!$result) {
  echo "Error Found!!!";
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

  <script src='../assets/google_analytics_auto.js'></script>
</head>

<body>

<?php include '../includes/nav.php'; ?>

  <!-- banner -->
  <div class="inside-banner">
    <div class="container">
      <h2>Listing All Properties</h2>
    </div>
  </div>
  <!-- banner -->

  <div class="container">
    <div class="properties-listing spacer">

      <div class="row">
        <div class="col-lg-3 col-sm-4 ">

          <div class="search-form">
            <h4><span class="glyphicon glyphicon-search"></span> Search for</h4>
            <form action="search.php" method="post" id="searchForm" onsubmit="return validateSearch()">
              <input type="text" class="form-control" name="search" placeholder="Search of Properties" required>
              <div class="row">
                
                <div class="col-lg-7">
                  <select name="search_price" class="form-control" required>
                    <option value="">Select Price Range</option>
                    <option value="1">Rs5000 - Rs50,000</option>
                    <option value="2">Rs50,000 - Rs100,000</option>
                    <option value="3">Rs100,000 - Rs200,000</option>
                    <option value="4">Rs200,000 - above</option>
                  </select>
                </div>
              </div>

              
              <button name="submit" class="btn btn-primary">Find Now</button>
            </form>

            <script>
              function validateSearch() {
                var searchInput = document.querySelector('input[name="search"]').value;
                var deliveryType = document.querySelector('select[name="delivery_type"]').value;
                var searchPrice = document.querySelector('select[name="search_price"]').value;
                var propertyType = document.querySelector('select[name="property_type"]').value;

                if (!searchInput.trim()) {
                  alert('Please enter a search term');
                  return false;
                }
                if (!deliveryType) {
                  alert('Please select a delivery type');
                  return false;
                }
                if (!searchPrice) {
                  alert('Please select a price range');
                  return false;
                }
                if (!propertyType) {
                  alert('Please select a property type');
                  return false;
                }
                return true;
              }
            </script>
          </div>






        </div>

        <div class="col-lg-9 col-sm-8">
          <div class="sortby clearfix">
            <div class="pull-left result">Showing: All Listing Properties </div>
            <div class="pull-right">
            </div>

          </div>
          <div class="row">

            <!-- properties -->
            <?php
            while ($property_result = mysqli_fetch_assoc($result)) {
              $id = $property_result['property_id'];
              $property_title = $property_result['property_title'];
              $price = $property_result['price'];
              $property_img = $property_result['property_img'];
              $property_address = $property_result['property_address'];
              $floor_space = $property_result['floor_space'];
              $agent_id = $property_result['agent_id'];

              ?>
              <div class="col-lg-4 col-sm-6">
                <div class="properties">
                  <div class="image-holder">
                    <?php 
                    $images = explode(',', $property_img);
                    $main_image = !empty($images[0]) ? '../' . $images[0] : '../images/properties/default1.png';
                    ?>
                    <img src="<?php echo $main_image; ?>" class="img-responsive" alt="<?php echo htmlspecialchars($property_title); ?>">
                  </div>
                  <h4><?php echo $property_title; ?></h4>
                  <p class="price">Price: Rs<?php echo number_format($price); ?></p>
                  <p class="price">Floor Space: <?php echo $floor_space; ?></p>
                  <p class="price">Address: <?php echo $property_address; ?></p>
                  <?php if (isLoggedIn()) { ?>
                    <a class="btn btn-primary" href="property-detail.php?id=<?php echo $id; ?>">View Details</a>
                  <?php } else { ?>
                    <a class="btn btn-primary" href="#" onclick="showLoginPopup(); return false;">View Details</a>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
            <!-- properties -->


          </div>
        </div>
      </div>
    </div>
  </div>




  <?php include '../includes/footer.php';   ?>    



  <!-- Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="loginModalLabel">Login Required</h4>
        </div>
        <div class="modal-body text-center">
          <h4>Please login to view property details</h4>
          <p>Login to your account or register if you're a new user</p>
          <div class="button-group" style="margin-top: 20px;">
            <a href="../login.php" class="btn btn-info">Login</a>
            <a href="../register.php" class="btn btn-info" style="margin-left: 10px;">Register</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showLoginPopup() {
      $('#loginModal').modal('show');
    }

    // Initialize tooltips
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>

</body>

</html>