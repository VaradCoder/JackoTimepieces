# JackoTimespiece - Setup Guide

## 🚀 Quick Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Database Setup

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Setup Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `jackotimespiece`
   - Import the SQL file located at `database/schema.sql` and `database/seed.sql`

   This will:
   - Create the `jackotimespiece` database
   - Create all necessary tables
   - Insert sample data (watches, categories, users, etc.)

### Step 2: Access the Website

1. **Open your browser and go to:**
   ```
   http://localhost/Watch/
   ```

2. **Default Login Credentials:**
   - **Admin Panel:** `http://localhost/Watch/admin/`
     - Username: `admin`
     - Password: `admin123`
   
   - **Customer Account:** `http://localhost/Watch/public/login.php`
     - Username: `customer`
     - Password: `customer123`

## 📁 Project Structure

```
Watch/
├── public/                 # Main website pages
│   ├── index.php          # Homepage
│   ├── store.php          # All products
│   ├── checkout.php       # Checkout page
│   ├── payment.php        # Payment page
│   └── pages/             # Static pages (about, contact, etc.)
├── admin/                 # Admin panel
│   ├── index.php          # Admin dashboard
│   ├── products/          # Product management
│   └── orders/            # Order management
├── core/                  # Backend logic
│   ├── config/            # Configuration files
│   ├── db/                # Database files
│   └── helpers/           # Helper functions
├── assets/                # Frontend assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── images/            # Images
└── database/              # Database files
    ├── schema.sql         # Database structure
    └── seed.sql           # Sample data
```

## 🛠️ Features

### Customer Features
- ✅ Browse luxury watches
- ✅ Add items to cart
- ✅ Secure checkout process
- ✅ Multiple payment options (Card, UPI, Cash)
- ✅ User account management
- ✅ Wishlist functionality
- ✅ Order tracking

### Admin Features
- ✅ Product management
- ✅ Order management
- ✅ User management
- ✅ Coupon management
- ✅ Sales analytics

## 🎨 Design Features

- **Luxury Theme**: Dark design with gold accents (#c9b37e)
- **Responsive**: Works on all devices
- **Animations**: Smooth transitions and effects
- **Glassmorphism**: Modern translucent design elements

## 🔧 Troubleshooting

### Database Connection Issues
1. Make sure XAMPP MySQL service is running
2. Check if database `jackotimespiece` exists
3. Verify database credentials in `core/db/connection.php`

### Page Not Found Errors
1. Ensure Apache service is running
2. Check if `.htaccess` file exists in root directory
3. Verify file permissions

### Payment Processing Issues
1. Check if all database tables are created
2. Verify order data is being passed correctly
3. Check PHP error logs for specific errors

## 📞 Support

For issues or questions:
- Email: jackotimespiece@gmail.com
- Phone: +91 8160375699

## 🔐 Security Notes

- Change default admin password after first login
- Update database credentials in production
- Enable HTTPS in production environment
- Regular backups of database and files

---

**JackoTimespiece** - Where legacy meets time. 