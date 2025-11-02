<?php
$isSubDirectory = isset($isSubDirectory) ? $isSubDirectory : false;
$basePath = $isSubDirectory ? '../' : '';
?>

<footer class="modern-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 footer-info">
                    <h3>Real Estate</h3>
                    <p>Your trusted partner in finding the perfect property. We believe in making your real estate journey smooth and successful.</p>
                    <div class="social-links mt-4">
                        <a href="#" class="facebook"><img src="<?php echo $basePath; ?>images/facebook.png" alt="facebook"></a>
                        <a href="#" class="twitter"><img src="<?php echo $basePath; ?>images/twitter.png" alt="twitter"></a>
                        <a href="#" class="linkedin"><img src="<?php echo $basePath; ?>images/linkedin.png" alt="linkedin"></a>
                        <a href="#" class="instagram"><img src="<?php echo $basePath; ?>images/instagram.png" alt="instagram"></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class=""></i> <a href="<?php echo $basePath; ?>">Home</a></li>
                        <li><i class=""></i> <a href="<?php echo $basePath; ?>about.php">About Us</a></li>
                        <li><i class=""></i> <a href="<?php echo $basePath; ?>contact.php">Contact</a></li>
                        <li><i class=""></i> <a href="<?php echo $basePath; ?>properties.php">Properties</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 footer-contact">
                    <h4>Contact Us</h4>
                    <p>
                        <strong>Jaggamandu</strong><br>
                        <i class=""></i> Bhaktapur<br>
                        <i class=""></i> <a href="mailto:jaggamandubkt@gmail.com">jaggamandubkt@gmail.com</a><br>
                        <i class=""></i> +123456789
                    </p>
                </div>
            </div>
        </div>
    </div>

    
</footer> 