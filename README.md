# SilvenStay – Flat Rental Web Application

SilvenStay is a comprehensive **flat rental web application** designed to simplify and manage the entire rental workflow.  
The platform connects **Guests, Owners, Customers, and Managers** within a single system, enabling flat discovery, listing, rental requests, viewing scheduling, and approval management through a secure, role-based architecture.

The application focuses on usability, transparency, and efficient communication between all parties involved in the rental process.

---

## 🚀 Key Features

### 👥 User Roles & Capabilities

#### Guest
- Browse available flats without registration
- Filter flats by **price, location, and number of bedrooms**
- View detailed flat information and images

#### Owner
- Submit flats for rent (subject to manager approval)
- Manage personal flat listings
- Receive notifications regarding approval status and rental activity

#### Customer
- Request to rent available flats
- Schedule flat viewings
- Track rental request status (**pending, approved, or declined**)
- Checkout and manage rented flats
- View rental history

#### Manager
- Review and approve or reject flats submitted by owners
- Manage rental requests
- Send notifications to owners and customers
- Oversee platform activity

---

## 🔔 Common Features
- **Messaging & Notification System** for all authenticated users (excluding guests)
- **Dynamic Flat Detail Pages** with images, pricing, and marketing information
- **Role-Based Access Control** to ensure secure feature access
- **Secure User Authentication & Registration** using PHP sessions
- **Responsive Design** for seamless usage across desktop, tablet, and mobile devices
- **Secure Database Operations** using PDO with MySQL to prevent SQL injection

---

## 🛠 Technology Stack
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP (server-side logic, sessions, authentication)  
- **Database:** MySQL (PDO for secure database interactions)  
- **Development Tools:** VS Code, XAMPP (or any local PHP/MySQL server)  

---

## ⚙️ Installation & Setup

### Prerequisites
- PHP 7.4+
- MySQL
- Apache (XAMPP / WAMP / LAMP)
- Git

### Steps
```bash
# Clone the repository
git clone <repository-url>
cd SilvenStay

# Import the MySQL database
# Configure database credentials in the PHP config file

# Start Apache and MySQL services

# Run the application
# Open in browser: http://localhost/SilvenStay
