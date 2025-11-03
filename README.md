# JackoTimespiece - Luxury Watch E-commerce Platform

A modern, feature-rich e-commerce platform for luxury watches built with PHP, MySQL, and TailwindCSS.

## 🚀 Features

### Customer Features
- **User Authentication & Registration**
- **Product Catalog** with advanced filtering and search
- **Shopping Cart** with persistent storage
- **Wishlist Management** with quick add/remove functionality
- **Order Management** with status tracking and cancellation
- **Customer Support** ticket system
- **Responsive Design** optimized for all devices
- **Quick Add to Cart** functionality
- **Order History** with detailed tracking

### Admin Features
- **Admin Registration System** with secure codes
- **Dashboard** with comprehensive analytics
- **Product Management** with inventory tracking
- **Order Management** with status updates and assignment
- **Customer Support** ticket management
- **User Management** with permissions
- **Coupon Management** system
- **Sales Analytics** and reporting

### Technical Features
- **Secure Authentication** with password hashing
- **Database-driven** architecture
- **RESTful API** endpoints
- **Mobile-responsive** design
- **Modern UI/UX** with animations
- **SEO Optimized** structure

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

## 🛠️ Installation

### 1. Clone or Download the Project
```bash
# If using git
git clone <repository-url>
cd Watch

# Or download and extract to your web server directory
```

### 2. Database Setup
1. Start your XAMPP/WAMP server
2. Open your web browser and navigate to:
   ```
   http://localhost/Watch/setup-database.php
   ```
3. This will automatically:
   - Create the database
   - Create all required tables
   - Insert sample data
   - Create default admin and customer accounts

### 3. Default Login Credentials

#### Admin Account
- **Email:** admin@jackotimespiece.com
- **Password:** admin123
- **Admin Panel:** http://localhost/Watch/admin/

#### Customer Account
- **Email:** customer@jackotimespiece.com
- **Password:** customer123

#### Admin Registration Codes
Use these codes to register new admin accounts:
- JACKO2024
- ADMIN2024
- SUPER2024

### 4. Access the Website
- **Main Website:** http://localhost/Watch/public/
- **Admin Panel:** http://localhost/Watch/admin/

## 📁 Project Structure

```
Watch/
├── admin/                 # Admin panel files
│   ├── index.php         # Admin dashboard
│   ├── login.php         # Admin login
│   ├── register.php      # Admin registration
│   ├── orders/           # Order management
│   ├── products/         # Product management
│   └── support/          # Support ticket management
├── api/                  # API endpoints
│   ├── cart/            # Cart operations
│   └── wishlist/        # Wishlist operations
├── assets/              # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── images/         # Images and media
├── core/               # Core application files
│   ├── config/         # Configuration files
│   ├── db/            # Database operations
│   ├── helpers/       # Helper functions
│   └── middleware/    # Authentication middleware
├── database/          # Database files
│   ├── schema.sql     # Database schema
│   └── seed.sql       # Sample data
├── public/            # Public-facing pages
│   ├── account/       # Customer account pages
│   ├── pages/         # Static pages
│   └── *.php          # Main pages
├── templates/         # Reusable templates
└── uploads/          # File uploads
```

## 🔧 Configuration

### Database Configuration
Edit `core/db/connection.php` to match your database settings:
```php
$host = 'localhost';
$user = 'root';         // Your MySQL username
$pass = '';             // Your MySQL password
$db   = 'jackotimespiece';
```

### Site Configuration
Edit `core/config/constants.php` to update site URLs and paths.

## 🎯 Key Features Explained

### 1. Enhanced Order Management
- **Editable Order History:** Customers can view, edit status, and cancel orders
- **Admin Order Control:** Admins can update status, assign orders, and manage tracking
- **Order Tracking:** Real-time status updates with tracking numbers

### 2. Advanced Wishlist System
- **Quick Add/Remove:** One-click wishlist management
- **Bulk Operations:** Add all wishlist items to cart
- **Persistent Storage:** Wishlist items saved to database
- **API Integration:** AJAX-powered operations

### 3. Admin Registration System
- **Secure Registration:** Admin accounts require registration codes
- **Role-based Access:** Different admin roles and permissions
- **Footer Link:** Small admin link in footer for easy access

### 4. Customer Support System
- **Ticket Management:** Create and track support tickets
- **Admin Replies:** Admins can respond to customer issues
- **Priority System:** Urgent, high, medium, low priority levels
- **Status Tracking:** Open, in progress, resolved, closed

### 5. Quick Add to Cart
- **AJAX Integration:** Add products without page reload
- **Real-time Updates:** Cart count updates instantly
- **Error Handling:** Stock validation and user feedback

## 🚀 Usage

### For Customers
1. **Browse Products:** Visit the store page to view all watches
2. **Add to Wishlist:** Click the heart icon to save items
3. **Quick Add to Cart:** Use the cart icon for instant purchase
4. **Manage Orders:** View and track your order history
5. **Get Support:** Create support tickets for assistance

### For Admins
1. **Access Admin Panel:** Use admin credentials to login
2. **Manage Products:** Add, edit, and manage inventory
3. **Process Orders:** Update status and assign to team members
4. **Handle Support:** Respond to customer tickets
5. **View Analytics:** Monitor sales and performance

## 🔒 Security Features

- **Password Hashing:** All passwords are securely hashed
- **SQL Injection Protection:** Prepared statements throughout
- **XSS Protection:** Input sanitization and output escaping
- **CSRF Protection:** Token-based form protection
- **Session Security:** Secure session management

## 🎨 UI/UX Features

- **Modern Design:** Clean, professional luxury aesthetic
- **Responsive Layout:** Works perfectly on all devices
- **Smooth Animations:** Enhanced user experience
- **Gold Theme:** Premium color scheme
- **Interactive Elements:** Hover effects and transitions

## 📊 Database Schema

The system includes comprehensive tables for:
- Users and authentication
- Products and categories
- Orders and order items
- Wishlist management
- Support tickets and messages
- Coupons and promotions
- Activity logging
- Settings and configuration

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License.

## 🆘 Support

For support or questions:
- Create an issue in the repository
- Contact: jackotimespiece@gmail.com
- Phone: +91 8160375699

## 🔄 Updates

### Recent Updates
- ✅ Enhanced order management with edit/cancel functionality
- ✅ Advanced wishlist system with quick operations
- ✅ Admin registration system with secure codes
- ✅ Customer support ticket management
- ✅ Quick add to cart with AJAX
- ✅ Comprehensive admin dashboard
- ✅ Mobile-responsive design improvements

### Planned Features
- [ ] Advanced search and filtering
- [ ] Email notifications
- [ ] Payment gateway integration
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] Mobile app integration

---

**JackoTimespiece** - Where Legacy Meets Time 