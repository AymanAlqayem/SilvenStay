CREATE DATABASE flatrent;

-- Users table (for customers, owners, managers)
CREATE TABLE users
(
    user_id          INT AUTO_INCREMENT PRIMARY KEY,
    national_id      VARCHAR(50)         NOT NULL,
    name             VARCHAR(100)        NOT NULL,
    address          VARCHAR(255),
    postal_code      VARCHAR(10),
    date_of_birth    DATE,
    email            VARCHAR(100) UNIQUE NOT NULL,
    mobile_number    VARCHAR(15),
    telephone_number VARCHAR(15),
    bank_name        VARCHAR(100),
    bank_branch      VARCHAR(100),
    account_number   VARCHAR(50),
    username         VARCHAR(100) UNIQUE NOT NULL,
    password         VARCHAR(255)        NOT NULL,
    user_type        ENUM('customer', 'owner', 'manager') NOT NULL,
    customer_id      VARCHAR(9) UNIQUE,
    owner_id         VARCHAR(9) UNIQUE,
    profile_photo    VARCHAR(255)
);

-- Flats table
CREATE TABLE flats
(
    flat_id            INT AUTO_INCREMENT PRIMARY KEY,
    reference_number   VARCHAR(6) UNIQUE NOT NULL,
    owner_id           INT               NOT NULL,
    location           VARCHAR(100)      NOT NULL,
    address            VARCHAR(255)      NOT NULL,
    monthly_rent       DECIMAL(10, 2)    NOT NULL,
    available_from     DATE              NOT NULL,
    available_to       DATE,
    bedrooms           INT               NOT NULL,
    bathrooms          INT               NOT NULL,
    size_sqm           INT               NOT NULL,
    is_furnished       BOOLEAN           NOT NULL,
    has_heating        BOOLEAN           NOT NULL,
    has_ac             BOOLEAN           NOT NULL,
    has_access_control BOOLEAN           NOT NULL,
    has_parking        BOOLEAN           NOT NULL,
    has_backyard       BOOLEAN           NOT NULL,
    has_playground     BOOLEAN           NOT NULL,
    has_storage        BOOLEAN           NOT NULL,
    rental_conditions  TEXT,
    status             ENUM('pending', 'approved', 'rented') DEFAULT 'pending',
    approved_by        INT,
    approval_date      DATETIME,
    FOREIGN KEY (owner_id) REFERENCES users (user_id),
    FOREIGN KEY (approved_by) REFERENCES users (user_id)
);

-- Flat photos
CREATE TABLE flat_photos
(
    photo_id   INT AUTO_INCREMENT PRIMARY KEY,
    flat_id    INT          NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

-- Flat marketing info
CREATE TABLE flat_marketing
(
    marketing_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id      INT          NOT NULL,
    title        VARCHAR(100) NOT NULL,
    description  TEXT,
    url          VARCHAR(255),
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

-- Rentals
CREATE TABLE rentals
(
    rental_id   INT AUTO_INCREMENT PRIMARY KEY,
    flat_id     INT            NOT NULL,
    customer_id INT            NOT NULL,
    start_date  DATE           NOT NULL,
    end_date    DATE           NOT NULL,
    total_cost  DECIMAL(10, 2) NOT NULL,
    status      ENUM('pending', 'current', 'past') DEFAULT 'pending',
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id)
);

-- Pending rentals (for shopping basket)
CREATE TABLE pending_rentals
(
    pending_rental_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id           INT            NOT NULL,
    customer_id       INT            NOT NULL,
    start_date        DATE           NOT NULL,
    end_date          DATE           NOT NULL,
    total_cost        DECIMAL(10, 2) NOT NULL,
    created_at        DATETIME       NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id)
);

-- Payments
CREATE TABLE payments
(
    payment_id        INT AUTO_INCREMENT PRIMARY KEY,
    rental_id         INT            NOT NULL,
    credit_card_number VARCHAR(9)    NOT NULL,
    expiry_date       DATE           NOT NULL,
    cardholder_name   VARCHAR(100)   NOT NULL,
    payment_date      DATETIME       NOT NULL,
    FOREIGN KEY (rental_id) REFERENCES rentals (rental_id)
);

-- Flat availability slots (for preview appointment timetables)
CREATE TABLE flat_availability_slots
(
    slot_id          INT AUTO_INCREMENT PRIMARY KEY,
    flat_id          INT          NOT NULL,
    appointment_date DATE         NOT NULL,
    appointment_time TIME         NOT NULL,
    telephone_number VARCHAR(15)  NOT NULL,
    is_booked        BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

-- Preview appointments
CREATE TABLE appointments
(
    appointment_id   INT AUTO_INCREMENT PRIMARY KEY,
    flat_id          INT  NOT NULL,
    customer_id      INT  NOT NULL,
    slot_id          INT  NOT NULL,
    status           ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id),
    FOREIGN KEY (slot_id) REFERENCES flat_availability_slots (slot_id)
);

-- Messages
CREATE TABLE messages
(
    message_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL,
    title        VARCHAR(100) NOT NULL,
    message_body TEXT         NOT NULL,
    sender       VARCHAR(100) NOT NULL,
    sent_date    DATETIME     NOT NULL,
    is_read      BOOLEAN DEFAULT FALSE,
    message_type ENUM('approval', 'appointment', 'rental', 'other') NOT NULL DEFAULT 'other',
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);

-- Sample data
-- Users
INSERT INTO users (national_id, name, address, postal_code, date_of_birth, email, mobile_number, telephone_number,
                   bank_name, bank_branch, account_number, username, password, user_type, customer_id, owner_id, profile_photo)
VALUES
    ('CUS001', 'John Doe', 'Ramallah, Main St 10', '90001', '1990-05-10', 'john.doe@birzeit.com',
     '+970599123123', '+9702221234', NULL, NULL, NULL, 'john.doe', '1johnz', 'customer', '123456789', NULL, 'images/john_doe.jpg'),
    ('OWN001', 'Owner One', 'Ramallah, Al-Bireh 15', '90002', '1980-02-20', 'owner.one@birzeit.com',
     '+970599456789', '+9702224567', 'Bank of Palestine', 'Ramallah', '1234567891', 'owner.one', '1ownerz', 'owner', NULL, '987654321', 'images/owner_one.jpg'),
    ('MGR001', 'Manager One', 'Ramallah, Central 20', '90003', '1975-08-15', 'manager.one@birzeit.com',
     '+970599789123', '+9702227891', NULL, NULL, NULL, 'manager.one', '1managerz', 'manager', NULL, NULL, 'images/manager_one.jpg'),
    ('CUS002', 'Ahmad Saleh', 'Ramallah, Main St 12', '90001', '1990-03-15', 'ahmad.saleh@birzeit.com',
     '+970599123456', '+9702223456', NULL, NULL, NULL, 'ahmad.saleh', '1ahmadz', 'customer', '123456790', NULL, 'images/ahmad_saleh.jpg'),
    ('OWN002', 'Fatima Omar', 'Ramallah, Al-Bireh 45', '90002', '1985-07-22', 'fatima.omar@birzeit.com',
     '+970599654321', '+9702229876', 'Bank of Palestine', 'Ramallah', '1234567890', 'fatima.omar', '1fatimaz', 'owner', NULL, '987654322', 'images/fatima_omar.jpg'),
    ('CUS003', 'Yousef Ali', 'Ramallah, Downtown 78', '90003', '1995-11-30', 'yousef.ali@birzeit.com',
     '+970599111222', '+9702221111', NULL, NULL, NULL, 'yousef.ali', '1yousefz', 'customer', '123456791', NULL, 'images/yousef_ali.jpg'),
    ('OWN003', 'Laila Hassan', 'Ramallah, Al-Tireh 23', '90004', '1978-04-10', 'laila.hassan@birzeit.com',
     '+970599333444', '+9702223333', 'Jordan Bank', 'Ramallah', '0987654321', 'laila.hassan', '1lailaz', 'owner', NULL, '987654323', 'images/laila_hassan.jpg'),
    ('CUS004', 'Mohammed Zaid', 'Ramallah, Al-Masyoun 56', '90005', '1988-09-05', 'mohammed.zaid@birzeit.com',
     '+970599555666', '+9702225555', NULL, NULL, NULL, 'mohammed.zaid', '1mohammedz', 'customer', '123456792', NULL, 'images/mohammed_zaid.jpg'),
    ('MGR002', 'Sara Khalil', 'Ramallah, Central 89', '90006', '1982-12-01', 'sara.khalil@birzeit.com',
     '+970599777888', '+9702227777', NULL, NULL, NULL, 'sara.khalil', '1saraz', 'manager', NULL, NULL, 'images/sara_khalil.jpg');

-- Flats
INSERT INTO flats (reference_number, owner_id, location, address, monthly_rent, available_from, available_to, bedrooms,
                   bathrooms, size_sqm, is_furnished, has_heating, has_ac, has_access_control, has_parking,
                   has_backyard, has_playground, has_storage, rental_conditions, status, approved_by, approval_date)
VALUES
    ('100001', 2, 'Ramallah', 'Main St 10, Ramallah', 800.00, '2025-06-01', '2026-06-01', 2, 1, 100, 1, 1, 0, 1, 1,
     0, 0, 1, 'No pets allowed', 'approved', 3, '2025-05-10 10:00:00'),
    ('100002', 2, 'Ramallah', 'Al-Bireh 20, Ramallah', 1200.00, '2025-07-01', NULL, 3, 2, 150, 0, 1, 1, 1, 0, 1, 0,
     0, 'Minimum 6-month lease', 'approved', 3, '2025-05-11 12:00:00'),
    ('100003', 5, 'Ramallah', 'Downtown 30, Ramallah', 900.00, '2025-05-20', '2026-05-20', 2, 1, 120, 1, 0, 1, 0, 1,
     0, 1, 0, 'Utilities included', 'pending', NULL, NULL),
    ('100004', 5, 'Ramallah', 'Al-Tireh 40, Ramallah', 1500.00, '2025-08-01', NULL, 4, 3, 200, 1, 1, 1, 1, 1, 1, 0,
     1, 'No smoking', 'approved', 9, '2025-05-12 14:00:00'),
    ('100005', 7, 'Ramallah', 'Al-Masyoun 50, Ramallah', 700.00, '2025-06-15', '2026-06-15', 1, 1, 80, 0, 0, 0, 0, 0,
     0, 0, 0, 'Flexible lease terms', 'approved', 3, '2025-05-13 09:00:00'),
    ('100006', 7, 'Ramallah', 'Central 60, Ramallah', 1100.00, '2025-09-01', NULL, 3, 2, 140, 1, 1, 1, 1, 1, 0, 1, 1,
     'Deposit required', 'pending', NULL, NULL),
    ('100007', 7, 'Ramallah', 'Central 70, Ramallah', 1000.00, '2025-10-01', NULL, 2, 1, 110, 0, 1, 0, 0, 0, 0, 0, 0,
     'No conditions', 'approved', 9, '2025-05-14 11:00:00');

-- Flat photos
INSERT INTO flat_photos (flat_id, photo_path)
VALUES
    (1, 'images/flat1_1.jpg'),
    (1, 'images/flat1_2.jpg'),
    (1, 'images/flat1_3.jpg'),
    (2, 'images/flat2_1.jpg'),
    (2, 'images/flat2_2.jpg'),
    (2, 'images/flat2_3.jpg');

-- Flat marketing
INSERT INTO flat_marketing (flat_id, title, description, url)
VALUES
    (1, 'Nearby School', 'Ramallah High School, 5 min walk', 'http://ramallahschool.edu'),
    (1, 'Supermarket', 'Bravo Supermarket, 2 min drive', 'http://bravosupermarket.com'),
    (1, 'Park', 'Al-Manara Park, 10 min walk', NULL),
    (2, 'Hospital', 'Ramallah Medical Center, 3 min drive', 'http://ramallahhospital.org'),
    (2, 'Cafe', 'Zamn Cafe, 5 min walk', 'http://zamncafe.com'),
    (2, 'Gym', 'Fitness Hub, 7 min walk', NULL);

-- Rentals
INSERT INTO rentals (flat_id, customer_id, start_date, end_date, total_cost, status)
VALUES
    (1, 1, '2025-06-01', '2025-12-01', 4800.00, 'current'),
    (2, 1, '2024-01-01', '2024-12-31', 14400.00, 'past'),
    (1, 4, '2024-06-01', '2024-11-30', 4800.00, 'past'),
    (4, 4, '2025-08-01', '2026-08-01', 18000.00, 'current'),
    (5, 6, '2025-06-15', '2025-12-15', 4200.00, 'current'),
    (2, 8, '2025-07-01', '2026-01-01', 7200.00, 'current'),
    (1, 8, '2025-06-01', '2025-12-01', 4800.00, 'current');

-- Pending rentals
INSERT INTO pending_rentals (flat_id, customer_id, start_date, end_date, total_cost, created_at)
VALUES
    (5, 1, '2025-07-01', '2025-12-31', 4200.00, '2025-05-15 10:00:00');

-- Payments
INSERT INTO payments (rental_id, credit_card_number, expiry_date, cardholder_name, payment_date)
VALUES
    (1, '123456789', '2026-12-31', 'John Doe', '2025-05-15 12:00:00'),
    (4, '987654321', '2026-08-31', 'Ahmad Saleh', '2025-05-16 14:00:00'),
    (5, '456789123', '2026-06-30', 'Yousef Ali', '2025-05-17 11:00:00'),
    (6, '789123456', '2026-01-31', 'Mohammed Zaid', '2025-05-18 13:00:00');

-- Flat availability slots
INSERT INTO flat_availability_slots (flat_id, appointment_date, appointment_time, telephone_number, is_booked)
VALUES
    (1, '2025-05-20', '10:00:00', '+970599456789', TRUE),
    (1, '2025-05-21', '14:00:00', '+970599456789', FALSE),
    (2, '2025-06-15', '14:00:00', '+970599456789', TRUE),
    (4, '2025-07-20', '15:00:00', '+970599654321', TRUE),
    (5, '2025-06-10', '09:00:00', '+970599333444', TRUE),
    (6, '2025-08-15', '16:00:00', '+970599333444', TRUE);

-- Appointments
INSERT INTO appointments (flat_id, customer_id, slot_id, status)
VALUES
    (1, 1, 1, 'approved'),
    (2, 1, 3, 'pending'),
    (3, 4, NULL, 'rejected'),
    (4, 4, 4, 'approved'),
    (5, 6, 5, 'pending'),
    (6, 8, 6, 'approved');

-- Messages
INSERT INTO messages (user_id, title, message_body, sender, sent_date, is_read, message_type)
VALUES
    (3, 'Flat Approval Needed', 'Please review flat 100003 for approval.', 'System', '2025-05-15 08:00:00', 0, 'approval'),
    (1, 'Appointment Approved', 'Your appointment for flat 100001 on 2025-05-20 at 10:00 is approved.', 'Owner One', '2025-05-16 09:00:00', 1, 'appointment'),
    (5, 'New Flat Submitted', 'Your flat 100003 has been submitted for approval.', 'System', '2025-05-14 10:00:00', 0, 'approval'),
    (4, 'Rental Confirmation', 'You have successfully rented flat 100004. Collect keys from Fatima Omar (+970599654321).', 'System', '2025-05-17 12:00:00', 1, 'rental'),
    (6, 'Appointment Request', 'New appointment request for flat 100005 on 2025-06-10 at 09:00.', 'System', '2025-05-16 14:00:00', 0, 'appointment'),
    (9, 'Flat Approval Needed', 'Please review flat 100006 for approval.', 'System', '2025-05-17 15:00:00', 0, 'approval'),
    (5, 'Rental Accepted', 'Your flat 100004 has been rented by Ahmad Saleh.', 'System', '2025-05-18 10:00:00', 0, 'rental');