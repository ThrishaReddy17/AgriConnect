# E-Commerce Platform

A PHP-based e-commerce platform for organic products with features for customers, farmers, and admin management.

## Features

- User authentication and authorization
- Product management
- Shopping cart functionality
- Order processing
- Admin dashboard
- Farmer product management
- Responsive design
- Secure payment processing
- Email notifications

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)
- SSL certificate (recommended for production)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/ecommerce.git
   cd ecommerce
   ```

2. Create a new MySQL database for the project

3. Copy the database configuration file:
   ```bash
   cp includes/db.example.php includes/db.php
   ```

4. Update the database credentials in `includes/db.php`:
   ```php
   define('DB_HOST', 'your_host');
   define('DB_NAME', 'your_database');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

5. Import the database schema:
   - Use phpMyAdmin or MySQL command line to import the database structure
   - The schema will be provided in the `database` directory

6. Set up your web server:
   - For Apache, ensure mod_rewrite is enabled
   - Point your web server to the project's root directory
   - Make sure the `images` and `uploads` directories are writable

7. Configure your web server:
   - For Apache, use the provided `.htaccess` file
   - For Nginx, use the configuration in the `nginx` directory

## Directory Structure

```
ecommerce/
├── admin/                 # Admin panel files
├── images/               # Product images
├── includes/             # Database and common functions
├── pages/                # User-facing pages
├── css/                  # Stylesheets
├── js/                   # JavaScript files
└── vendor/              # Third-party libraries
```

## Security

- All user inputs are sanitized
- Passwords are hashed using PHP's password_hash()
- SQL injection prevention using PDO prepared statements
- XSS protection
- CSRF protection
- Secure session handling

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue in the GitHub repository or contact the development team.

## Acknowledgments

- [Bootstrap](https://getbootstrap.com/) for the frontend framework
- [Font Awesome](https://fontawesome.com/) for icons
- [jQuery](https://jquery.com/) for JavaScript functionality 