<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Only output the head section if we're not already in a page that has it
if (!isset($head_included)) {
    $head_included = true;
?>
    <!-- Bootstrap and Core CSS -->
    <link rel="stylesheet" href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/style.css" />
    <link rel="stylesheet" href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/navbar.css" />

    <!-- Owl Carousel -->
    <link rel="stylesheet"
        href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/owl-carousel/owl.theme.css">

    <!-- Slit Slider -->
    <link rel="stylesheet" type="text/css"
        href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/slitslider/css/style.css" />
    <link rel="stylesheet" type="text/css"
        href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/slitslider/css/custom.css" />

    <!-- Custom Navbar Styles -->
    <style>
        .navbar-wrapper {
            position: relative;
            z-index: 1000;
        }

        .navbar-inverse {
            background-color:rgb(167, 78, 0); /* Turquoise color from the image */
        }

        .navbar-inverse .navbar-nav > li > a {
            font-size: 15px;
            padding: 20px 20px;
            font-family: din;
            text-transform: uppercase;
            color: #fff;
            transition: background-color 0.3s;
        }

        .navbar-inverse .navbar-nav > li > a:hover {
            background-color:#e16d00;
            color: #fff;
        }

        .navbar-inverse .navbar-nav > .active > a,
        .navbar-inverse .navbar-nav > .active > a:hover,
        .navbar-inverse .navbar-nav > .active > a:focus {
            background-color:#562700;
            color: #fff;
        }

        .navbar-brand {
            padding: 10px 15px;
            height: 50px;
        }

        .navbar-brand img {
            display: inline-block;
            vertical-align: middle;
            max-height: 30px;
        }

        .navbar-inverse .navbar-toggle {
            border-color: #fff;
        }

        .navbar-inverse .navbar-toggle .icon-bar {
            background-color: #fff;
        }

        .navbar-inverse .navbar-toggle:hover,
        .navbar-inverse .navbar-toggle:focus {
            background-color: #6B705C;
        }

        @media (max-width: 767px) {
            .navbar-nav > li > a {
                padding: 10px 15px;
            }
            
            .navbar-inverse .navbar-collapse {
                border-color: transparent;
            }
        }
    </style>

    <!-- Core JavaScript -->
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/jquery-1.9.1.min.js"></script>
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/bootstrap/js/bootstrap.js"></script>
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/script.js"></script>

    <!-- Owl Carousel JavaScript -->
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/owl-carousel/owl.carousel.js"></script>

    <!-- Slit Slider JavaScript -->
    <script
        src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/slitslider/js/modernizr.custom.79639.js"></script>
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/slitslider/js/jquery.ba-cond.min.js"></script>
    <script src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/slitslider/js/jquery.slitslider.js"></script>
<?php } ?>

<!-- Header Starts -->
<div class="navbar-wrapper">
    <div class="navbar-inverse">
        <div class="container1">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>index.php">
                    <img src="<?php echo isset($isSubDirectory) ? '../' : ''; ?>assets/images/logo1.png" alt="Jaggamandu Logo">
                </a>
            </div>

            <!-- Nav Starts -->
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                        <a href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>index.php">HOME</a>
                    </li>
                    <li class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                        <a href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>about.php">ABOUT</a>
                    </li>
                    <li class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">
                        <a href="<?php echo isset($isSubDirectory) ? '../' : ''; ?>contact.php">CONTACT</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <li><a href="<?php echo isset($isSubDirectory) ? '../admin/admin.php' : 'admin/admin.php'; ?>">Welcome, <?php echo $_SESSION['username']; ?></a></li>
                        <?php else: ?>
                            <li><a href="#" style="pointer-events: none;">Welcome, <?php echo $_SESSION['username']; ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo isset($isSubDirectory) ? '../my-bookings.php' : 'my-bookings.php'; ?>">My Bookings</a></li>
                        <li><a href="<?php echo isset($isSubDirectory) ? '../logout.php' : 'logout.php'; ?>">LOGOUT</a></li>
                    <?php else: ?>
                        <li class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">
                            <a href="<?php echo isset($isSubDirectory) ? '../login.php' : 'login.php'; ?>">LOGIN</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- #Nav Ends -->
        </div>
    </div>
</div>
<!-- #Header Ends --> 