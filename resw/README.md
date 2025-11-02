# Real Estate Management System

A comprehensive PHP-based real estate management system with admin panel, property management, agent registration, and booking functionality.

## Features

- **Property Management**: Add, edit, and manage property listings
- **User Management**: User registration, authentication, and profile management
- **Agent System**: Agent registration and management with CV upload
- **Booking System**: Property viewing bookings and scheduling
- **Admin Panel**: Complete admin interface for managing all aspects of the system
- **Responsive Design**: Modern, mobile-friendly interface using Bootstrap

## Requirements

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (for local development)

## Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/real-estate-management.git
cd real-estate-management
```

2. Import the database:
   - Open phpMyAdmin or MySQL command line
   - Create a new database named `realestate`
   - Import the `realestate.sql` file

3. Configure database connection:
   - Copy `includes/connection.php.example` to `includes/connection.php`
   - Update database credentials:
   ```php
   $con = mysqli_connect("localhost", "your_username", "your_password", "realestate");
   ```

4. Configure application paths:
   - Copy `includes/config.php.example` to `includes/config.php`
   - Update the `$app_path` variable to match your project folder name

5. Set up file permissions:
```bash
chmod 755 uploads/
```

6. Access the application:
   - Open your browser
   - Navigate to `http://localhost/your-project-folder/`

## Configuration

### Admin Account
Default admin credentials:
- Username: `admin`
- Password: `admin123` (Change this immediately after first login!)

## Project Structure

```
├── admin/                  # Admin panel pages
├── assets/                 # CSS, JS, images, and other static files
├── includes/               # Core PHP files (config, connection, functions)
├── properties/             # Property-related pages
├── images/                 # Property and site images
├── uploads/                # User uploaded files
├── index.php              # Homepage
├── login.php              # User login
├── register.php           # User registration
├── realestate.sql         # Database structure and sample data
└── README.md              # This file
```

## Main Features

### Public Features
- Browse properties
- Search and filter properties
- View property details
- Book property viewings
- Agent registration
- Contact forms

### User Features
- User dashboard
- View booking history
- Profile management

### Admin Features
- Manage properties (add, edit, delete)
- Manage users
- Manage agent applications
- Manage bookings
- System messages

## Security Notes

⚠️ **Important**: This is a development project. Before deploying to production:

1. Change all default passwords
2. Use strong password hashing
3. Implement CSRF protection
4. Add input validation and sanitization
5. Use prepared statements (already implemented in most places)
6. Secure sensitive files (`.gitignore` is already configured)
7. Update file upload security
8. Enable HTTPS

## Technologies Used

- **Backend**: PHP (Procedural)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 3, jQuery
- **Additional**: Owl Carousel, Slit Slider

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues and questions, please create an issue in the GitHub repository.

## Changelog

### Version 1.0
- Initial release
- Property management system
- User authentication
- Agent registration
- Booking system
- Admin panel

