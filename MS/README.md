# R.D.S Gears - E-commerce Website

A modern, responsive e-commerce website built with HTML, CSS, JavaScript, PHP, and MySQL for selling computer accessories and electronics.

## Features

### 🛍️ E-commerce Features
- **Product Catalog**: Browse products by category with filtering and search
- **Shopping Cart**: Add, remove, and update cart items with real-time updates
- **User Authentication**: Secure login and registration system
- **Product Search**: Advanced search with filters (category, price range, sorting)
- **Quick View**: Modal popup for quick product preview
- **Responsive Design**: Mobile-friendly interface

### 🎨 Design Features
- **Modern UI**: Clean, professional design with smooth animations
- **Responsive Layout**: Works perfectly on desktop, tablet, and mobile
- **Interactive Elements**: Hover effects, smooth transitions, and user feedback
- **Font Awesome Icons**: Beautiful iconography throughout the site

### 🔧 Technical Features
- **PHP Backend**: Server-side logic with PDO database connections
- **MySQL Database**: Structured database with relationships
- **AJAX Integration**: Dynamic cart updates and search functionality
- **Session Management**: Secure user sessions and cart persistence
- **Form Validation**: Client-side and server-side validation

## Product Categories

- **Laptops**: Gaming, Business, and Student laptops
- **Headphones**: Wireless, Gaming, and Studio headphones
- **Microphones**: USB, XLR, and Wireless microphones
- **Keyboards**: Mechanical, Wireless, and Gaming keyboards
- **Mouse**: Gaming, Office, and MMO mouse

## Installation & Setup

### Prerequisites
- XAMPP, WAMP, or similar local server environment
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### Step 1: Database Setup
1. Open phpMyAdmin or your MySQL client
2. Create a new database named `ecommerce_db`
3. Import the `database.sql` file to create tables and sample data

### Step 2: Configuration
1. Open `config/database.php`
2. Update the database connection settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ecommerce_db');
   define('DB_USER', 'root');        // Your MySQL username
   define('DB_PASS', '');            // Your MySQL password
   ```

### Step 3: File Structure
Ensure your project structure looks like this:
```
R.D.S Gears/
├── api/
│   ├── cart.php
│   └── products.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── products/
│       └── hero-bg.jpg
├── config/
│   └── database.php
├── index.php
├── products.php
├── cart.php
├── login.php
├── register.php
├── logout.php
├── database.sql
└── README.md
```

### Step 4: Image Setup
1. Create an `assets/images/products/` directory
2. Add product images with the following names:
   - `laptop1.jpg`, `laptop2.jpg`, `laptop3.jpg`
   - `headphones1.jpg`, `headphones2.jpg`, `headphones3.jpg`
   - `microphone1.jpg`, `microphone2.jpg`, `microphone3.jpg`
   - `keyboard1.jpg`, `keyboard2.jpg`, `keyboard3.jpg`
   - `mouse1.jpg`, `mouse2.jpg`, `mouse3.jpg`
3. Add a hero background image as `assets/images/hero-bg.jpg`

### Step 5: Access the Website
1. Start your local server (Apache + MySQL)
2. Navigate to `http://localhost/MS/`
3. The website should now be fully functional!

## Default Admin Account
- **Username**: admin
- **Password**: admin123
- **Email**: admin@ecommerce.com

## Database Schema

### Tables
- **categories**: Product categories
- **products**: Product information
- **users**: User accounts and authentication
- **orders**: Order records
- **order_items**: Individual items in orders
- **cart**: Shopping cart items (session-based)

### Key Features
- Foreign key relationships for data integrity
- JSON support for product specifications
- Timestamp tracking for all records
- Secure password hashing

## API Endpoints

### Cart API (`api/cart.php`)
- `POST action=add`: Add item to cart
- `POST action=remove`: Remove item from cart
- `POST action=update`: Update cart item quantity
- `GET action=count`: Get cart item count
- `GET action=get`: Get full cart contents

### Products API (`api/products.php`)
- `GET action=quick_view&id=X`: Get product details for quick view
- `GET action=search`: Search products with filters

## Customization

### Adding New Products
1. Insert new records into the `products` table
2. Add corresponding product images to `assets/images/products/`
3. Update the image filename in the database

### Modifying Categories
1. Edit the `categories` table
2. Update the category icons in `index.php` (Font Awesome classes)

### Styling Changes
- Main styles: `assets/css/style.css`
- Page-specific styles: Inline `<style>` tags in respective PHP files

## Browser Support
- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers

## Security Features
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Password hashing with `password_hash()`
- Session-based authentication
- Input validation and sanitization

## Performance Optimizations
- Lazy loading for images
- Minified CSS and JavaScript
- Optimized database queries
- Responsive images
- Caching-friendly structure

## Troubleshooting

### Common Issues
1. **Database Connection Error**: Check database credentials in `config/database.php`
2. **Images Not Loading**: Ensure image files exist in correct directories
3. **Cart Not Working**: Check if sessions are enabled in PHP
4. **Search Not Working**: Verify database table structure matches `database.sql`

### Debug Mode
To enable error reporting, add this to the top of PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License
This project is open source and available under the [MIT License](LICENSE).

## Support
For support or questions, please create an issue in the repository or contact the development team.

---

**R.D.S Gears** - Your trusted source for premium computer accessories and electronics! 🚀 