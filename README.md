# E-Commerce Shop

A fully functional, custom-built e-commerce web application built from scratch without any PHP frameworks. This project demonstrates a solid understanding of core web technologies, Object-Oriented Programming (OOP), and database management.

## 🚀 Tech Stack

* **Backend:** PHP (OOP)
* **Database:** MySQL (PDO)
* **Frontend:** HTML5, CSS3, JavaScript (AJAX)
* **Environment:** XAMPP
* **Version Control:** Git / GitHub

## ✨ Key Features

* **Object-Oriented Architecture:** Core logic built using `Product` and `Cart` classes, maintaining clean and reusable code.
* **AJAX-Powered Cart:** Add, update quantity, and remove items from the cart seamlessly without page reloads.
* **Secure Authentication:** User registration and login with secure password hashing and session management.
* **Admin Dashboard (CRUD):** Full control over products (Create, Read, Update, Delete).
* **Robust Checkout Logic:** Real-time stock validation, out-of-stock prevention, and automatic database stock deduction upon successful checkout.
* **Security First:** Protection against SQL Injection using PDO Prepared Statements and XSS protection using custom escaping functions.

## 🛠️ Installation

Follow these steps to run the project locally:

1. **Clone the repository:**
   `git clone //https://github.com/sargis-builds/e-commerce-shop.git`

2. **Move to XAMPP directory:**
   Move the cloned folder into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\`).

3. **Start the servers:**
   Open XAMPP Control Panel and start **Apache** and **MySQL**.

4. **Set up the Database:**
   * Open phpMyAdmin (`http://localhost/phpmyadmin`).
   * Create a new database named `shop_db` (or whatever name you use).
   * Import the `database.sql` file located in the root of the project.

5. **Configure Database Connection:**
   Open `db.php` and update the database credentials (host, dbname, username, password) to match your local setup.

6. **Run the app:**
   Open your browser and go to `http://localhost/e-commerce-shop`.

## 🔐 Demo Credentials

To test the application immediately, use the following demo accounts:

* **Admin:** Username: `admin` | Password: `admin123`
* **User:** Username: `user` | Password: `user123`