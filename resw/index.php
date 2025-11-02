<?php
session_start();
include_once "includes/connection.php";
include_once "includes/functions.php";

// Main query for featured properties in the carousel
$query = "select * from properties";
$result = mysqli_query($con, $query);

if (!$result) {
  echo "Error Found!!!";
}

// Separate query for slider properties - limit to 5 properties
$slider_query = "SELECT * FROM properties ORDER BY property_id DESC LIMIT 5";
$slider_result = mysqli_query($con, $slider_query);

if (!$slider_result) {
  echo "Error loading slider properties!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Real Estate Management System</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/style.css" />
  <link rel="stylesheet" href="assets/navbar.css" />
  <script src="assets/jquery-1.9.1.min.js"></script>
  <script src="assets/bootstrap/js/bootstrap.js"></script>
  <script src="assets/script.js"></script>



  <!-- Owl stylesheet -->
  <link rel="stylesheet" href="assets/owl-carousel/owl.carousel.css">
  <link rel="stylesheet" href="assets/owl-carousel/owl.theme.css">
  <script src="assets/owl-carousel/owl.carousel.js"></script>
  <!-- Owl stylesheet -->


  <!-- slitslider -->
  <link rel="stylesheet" type="text/css" href="assets/slitslider/css/style.css" />
  <link rel="stylesheet" type="text/css" href="assets/slitslider/css/custom.css" />
  <script type="text/javascript" src="assets/slitslider/js/modernizr.custom.79639.js"></script>
  <script type="text/javascript" src="assets/slitslider/js/jquery.ba-cond.min.js"></script>
  <script type="text/javascript" src="assets/slitslider/js/jquery.slitslider.js"></script>
  <!-- slitslider -->

  <script src='assets/google_analytics_auto.js'></script>

  <script type="text/javascript">
    $(document).ready(function() {
      var Page = (function() {
        var $navArrows = $('#nav-arrows'),
          $nav = $('#nav-dots > span'),
          slitslider = $('#slider').slitslider({
            onBeforeChange: function(slide, pos) {
              $nav.removeClass('nav-dot-current');
              $nav.eq(pos).addClass('nav-dot-current');
            }
          }),
          init = function() {
            initEvents();
          },
          initEvents = function() {
            $navArrows.children(':last').on('click', function() {
              slitslider.next();
              return false;
            });
            $navArrows.children(':first').on('click', function() {
              slitslider.prev();
              return false;
            });
            $nav.each(function(i) {
              $(this).on('click', function(event) {
                var $dot = $(this);
                if (!slitslider.isActive()) {
                  $nav.removeClass('nav-dot-current');
                  $dot.addClass('nav-dot-current');
                }
                slitslider.jump(i + 1);
                return false;
              });
            });
          };
        return { init: init };
      })();
      Page.init();
    });
  </script>

  <style>
    .navbar {
      min-height: 50px;
    }

    .navbar-brand {
      padding: 0 15px;
      height: 50px;
      line-height: 50px;
    }

    .navbar-brand img {
      display: inline-block;
      vertical-align: middle;
    }

    .navbar-nav>li>a {
      line-height: 50px;
      padding-top: 0;
      padding-bottom: 0;
    }

    @media (max-width: 767px) {
      .navbar-nav>li>a {
        line-height: normal;
        padding-top: 10px;
        padding-bottom: 10px;
      }
    }

    /* Property Image Styles - Only for Featured Properties */
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

    /* Featured Properties in Owl Carousel */
    #owl-example .item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .owl-item .properties {
      margin: 0 15px;
      height: auto;
      display: flex;
      flex-direction: column;
      padding: 10px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Property details styling */
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

    .properties .listing-detail {
      margin: 10px 0;
    }

    .properties .btn {
      margin-top: auto;
      padding: 6px 12px;
    }

    /* Status badge positioning */
    .properties .status {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1;
    }

    /* Make featured property images responsive */
    @media (max-width: 768px) {
      .properties .image-holder {
        height: 180px;
      }
    }

    /* New color scheme styles */
    .inside-banner {
      background-color: #2c3e50;
      color: white;
    }

    .banner-search {
      background-color: #34495e;
      color: white;
    }

    .properties-listing h2 {
      color: #2c3e50;
    }

    .properties h4 a {
      color: #2c3e50;
    }

    .properties h4 a:hover {
      color: #3498db;
    }

    .btn-primary {
      background-color: #563207;
      border-color: #563207;
    }

    .btn-primary:hover {
      background-color: #3E2405;
      border-color: #3E2405;
    }
    .btn-success{ 
      background-color: #563207;
      border-color: #563207;
    }
    .btn-success:hover {
      background-color: #3E2405;
      border-color: #3E2405;
    }

    .footer-section {
      background-color: #2c3e50;
      color: white;
      padding: 40px 0;
    }

    .footer-section h4 {
      color: #3498db;
    }

    .footer-section a {
      color: #ecf0f1;
    }

    .footer-section a:hover {
      color: #3498db;
    }

    .copyright {
      color: #bdc3c7;
    }
   
    /* Add background image styles for slider */
    .bg-img {
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 100%;
      width: 100%;
      position: absolute;
      top: 0;
      left: 0;
    }
    
    .sl-slider {
      height: 500px;
    }
    
    .sl-slide-inner {
      height: 100%;
      position: relative;
    }
    
    .sl-slide-inner h2 {
      position: absolute;
      bottom: 100px;
      left: 20px;
      color: white;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }
    
    .sl-slide-inner blockquote {
      position: absolute;
      bottom: 40px;
      left: 20px;
      color: white;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }

    .card {
      border: none;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
    }
    
    .card-title {
      color: #2c3e50;
      margin-bottom: 1.5rem;
    }
    
    .list-unstyled li {
      margin-bottom: 0.5rem;
    }
    
    .text-primary {
      color: #563207 !important;
    }
    
    .btn-primary {
      padding: 10px 25px;
      font-weight: 600;
    }
  </style>
</head>

<body>

<?php include 'includes/nav.php'; ?>

  <div class="">
    <div id="slider" class="sl-slider-wrapper">

      <div class="sl-slider">
      
        <?php
        $count = 0;
        $orientations = ["horizontal", "vertical", "horizontal", "vertical", "horizontal"];
        $slice1_rotations = ["-25", "10", "3", "-5", "-5"];
        $slice2_rotations = ["-25", "-15", "3", "25", "10"];
        $slice1_scales = ["2", "1.5", "2", "2", "2"];
        $slice2_scales = ["2", "1.5", "1", "1", "1"];
        
        while ($property = mysqli_fetch_assoc($slider_result)) {
          $id = $property['property_id'];
          $property_title = $property['property_title'];
          $property_address = isset($property['property_address']) ? $property['property_address'] : "Address not available";
          $price = $property['price'];
          $property_img = $property['property_img'];
          
          // Use default values if we've reached the end of our arrays
          $orientation = isset($orientations[$count]) ? $orientations[$count] : "horizontal";
          $slice1_rotation = isset($slice1_rotations[$count]) ? $slice1_rotations[$count] : "0";
          $slice2_rotation = isset($slice2_rotations[$count]) ? $slice2_rotations[$count] : "0";
          $slice1_scale = isset($slice1_scales[$count]) ? $slice1_scales[$count] : "1";
          $slice2_scale = isset($slice2_scales[$count]) ? $slice2_scales[$count] : "1";
          
          // Set the background image class
          $bg_img_class = "bg-img-" . ($count + 1);
        ?>
        <div class="sl-slide" data-orientation="<?php echo $orientation; ?>" 
             data-slice1-rotation="<?php echo $slice1_rotation; ?>" 
             data-slice2-rotation="<?php echo $slice2_rotation; ?>"
             data-slice1-scale="<?php echo $slice1_scale; ?>" 
             data-slice2-scale="<?php echo $slice2_scale; ?>">
          <div class="sl-slide-inner">
            <?php 
            $images = explode(',', $property_img);
            $main_image = !empty($images[0]) ? $images[0] : 'images/properties/default1.png';
            ?>
            <div class="bg-img <?php echo $bg_img_class; ?>" style="background-image: url('<?php echo $main_image; ?>')"></div>
            <h2><a href="./properties/property-detail.php?id=<?php echo $id; ?>"><?php echo $property_title; ?></a></h2>
            <blockquote>
              <p class="location"><span class="glyphicon glyphicon-map-marker"></span> <?php echo $property_address; ?></p>
              <cite style="background-color: #563207;" >Rs <?php echo number_format($price); ?></cite>
            </blockquote>
          </div>
        </div>
        <?php
          $count++;
        }
        ?>

      </div><!-- /sl-slider -->

      <nav id="nav-dots" class="nav-dots">
        <span class="nav-dot-current"></span>
        <?php
        // Generate navigation dots for each slide (minus the first one which is already added above)
        for ($i = 1; $i < mysqli_num_rows($slider_result); $i++) {
          echo '<span></span>';
        }
        ?>
      </nav>

    </div><!-- /slider-wrapper -->
  </div>



  <div class="banner-search">
    <div class="container">
      <!-- banner -->
      <h3>Search Properties</h3>
      <div class="searchbar">
        <div class="row">
          <div class="col-lg-6 col-sm-6">
            <form action="properties/search.php" method="post" id="searchForm" onsubmit="return validateForm()">
              <input name="search" type="text" class="form-control" placeholder="Property title" required>
              <div class="row">
                <div class="col-lg-3 col-sm-3 ">
                  <select name="search_price" class="form-control">
                    <option value="">Price</option>
                    <option value="1">Rs5000 - Rs50,000</option>
                    <option value="2">Rs50,000 - Rs100,000</option>
                    <option value="3">Rs100,000 - Rs200,000</option>
                    <option value="4">Rs200,000 - above</option>
                  </select>
                </div>
                
                <div class="col-lg-3 col-sm-4">
                  <button name="submit" class="btn btn-success" type="submit">Find Now</button>
                </div>
              </div>
            </form>

            <script>
            function validateForm() {
              var search = document.forms["searchForm"]["search"].value;
              var search_price = document.forms["searchForm"]["search_price"].value;
              
              if (search == "" || search_price == "") {
                alert("Please fill in all fields");
                return false;
              }
              return true;
            }
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="properties-listing spacer"> <a href="./properties/list-properties.php" class="pull-right viewall">View All
        Listing</a>
      <h2>Featured Properties</h2>
      <div id="owl-example" class="owl-carousel">



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
          <div class="properties">
            <div class="image-holder">
              <?php 
              $images = explode(',', $property_img);
              $main_image = !empty($images[0]) ? $images[0] : 'images/properties/default1.png';
              ?>
              <img src="<?php echo $main_image; ?>" class="img-responsive" alt="properties">
            </div>
            <h4><a href="./properties/property-detail.php?id=<?php echo $id; ?>"><?php echo $property_title;  ?></a></h4>
            <p class="price">Price: Rs<?php echo number_format($price); ?></p>
            <p class="price">Floor Space: <?php echo $floor_space; ?></p>
            <p class="price">Address: <?php echo $property_address; ?></p>
            <a class="btn btn-primary" href="./properties/property-detail.php?id=<?php echo $id; ?>">View Details</a>
          </div>

        <?php } ?>

      </div>
    </div>
    
  </div>

<!-- Agent Registration Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Become a Real Estate Agent</h3>
                    <p class="card-text">Join our team of professional real estate agents and help people find their dream homes. As an agent, you'll get:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Access to premium property listings</li>
                        <li><i class="fas fa-check-circle text-success"></i> Professional training and support</li>
                        <li><i class="fas fa-check-circle text-success"></i> Competitive commission rates</li>
                        <li><i class="fas fa-check-circle text-success"></i> Flexible working hours</li>
                    </ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="agent-register.php" class="btn btn-primary">Apply Now</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Login to Apply</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Why Choose Us?</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                                <h5>Large Client Base</h5>
                                <p>Access to thousands of potential buyers and sellers</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                                <h5>Growth Opportunities</h5>
                                <p>Clear career progression and development paths</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <i class="fas fa-tools fa-3x mb-3 text-primary"></i>
                                <h5>Modern Tools</h5>
                                <p>Access to latest real estate technology and tools</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-center">
                                <i class="fas fa-handshake fa-3x mb-3 text-primary"></i>
                                <h5>Supportive Team</h5>
                                <p>Work with experienced professionals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php include 'includes/footer.php'; ?>

</body>

</html>