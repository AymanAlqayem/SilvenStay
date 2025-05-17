CREATE
DATABASE birzeit_std123456;
USE
birzeit_std123456;

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
    owner_id         VARCHAR(9) UNIQUE
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
    FOREIGN KEY (owner_id) REFERENCES users (user_id)
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
    status      ENUM('current', 'past') DEFAULT 'current',
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id)
);

-- Preview appointments
CREATE TABLE appointments
(
    appointment_id   INT AUTO_INCREMENT PRIMARY KEY,
    flat_id          INT  NOT NULL,
    customer_id      INT  NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status           ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id)
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
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);


-- 1. Users table: Add 3 more users to the existing 3 (Manager, Customer, Owner)
INSERT INTO users (national_id, name, address, postal_code, date_of_birth, email, mobile_number, telephone_number,
                   bank_name, bank_branch, account_number, username, password, user_type, customer_id, owner_id)
VALUES ('CUS002', 'Ahmad Saleh', 'Ramallah, Main St 12', '90001', '1990-03-15', 'ahmad.saleh@birzeit.com',
        '+970599123456', '+9702223456', NULL, NULL, NULL, 'ahmad.saleh', '1ahmadz', 'customer', '123456790',
        NULL),                       -- Customer
       ('OWN002', 'Fatima Omar', 'Ramallah, Al-Bireh 45', '90002', '1985-07-22', 'fatima.omar@birzeit.com',
        '+970599654321', '+9702229876', 'Bank of Palestine', 'Ramallah', '1234567890', 'fatima.omar', '1fatimaz',
        'owner', NULL, '987654322'), -- Owner
       ('CUS003', 'Yousef Ali', 'Ramallah, Downtown 78', '90003', '1995-11-30', 'yousef.ali@birzeit.com',
        '+970599111222', '+9702221111', NULL, NULL, NULL, 'yousef.ali', '1yousefz', 'customer', '123456791',
        NULL),                       -- Customer
       ('OWN003', 'Laila Hassan', 'Ramallah, Al-Tireh 23', '90004', '1978-04-10', 'laila.hassan@birzeit.com',
        '+970599333444', '+9702223333', 'Jordan Bank', 'Ramallah', '0987654321', 'laila.hassan', '1lailaz', 'owner',
        NULL, '987654323'),          -- Owner
       ('CUS004', 'Mohammed Zaid', 'Ramallah, Al-Masyoun 56', '90005', '1988-09-05', 'mohammed.zaid@birzeit.com',
        '+970599555666', '+9702225555', NULL, NULL, NULL, 'mohammed.zaid', '1mohammedz', 'customer', '123456792',
        NULL),                       -- Customer
       ('MGR002', 'Sara Khalil', 'Ramallah, Central 89', '90006', '1982-12-01', 'sara.khalil@birzeit.com',
        '+970599777888', '+9702227777', NULL, NULL, NULL, 'sara.khalil', '1saraz', 'manager', NULL, NULL);
-- Manager

-- 2. Flats table: 6 flats owned by owners (OWN001, OWN002, OWN003)
INSERT INTO flats (reference_number, owner_id, location, address, monthly_rent, available_from, available_to, bedrooms,
                   bathrooms, size_sqm, is_furnished, has_heating, has_ac, has_access_control, has_parking,
                   has_backyard, has_playground, has_storage, rental_conditions, status)
VALUES ('100001', 1, 'Ramallah', 'Main St 10, Ramallah', 800.00, '2025-06-01', '2026-06-01', 2, 1, 100, 1, 1, 0, 1, 1,
        0, 0, 1, 'No pets allowed', 'approved'),      -- Owned by OWN001
       ('100002', 1, 'Ramallah', 'Al-Bireh 20, Ramallah', 1200.00, '2025-07-01', NULL, 3, 2, 150, 0, 1, 1, 1, 0, 1, 0,
        0, 'Minimum 6-month lease', 'approved'),      -- Owned by OWN001
       ('100003', 2, 'Ramallah', 'Downtown 30, Ramallah', 900.00, '2025-05-20', '2026-05-20', 2, 1, 120, 1, 0, 1, 0, 1,
        0, 1, 0, 'Utilities included', 'pending'),    -- Owned by OWN002
       ('100004', 2, 'Ramallah', 'Al-Tireh 40, Ramallah', 1500.00, '2025-08-01', NULL, 4, 3, 200, 1, 1, 1, 1, 1, 1, 0,
        1, 'No smoking', 'approved'),                 -- Owned by OWN002
       ('100005', 3, 'Ramallah', 'Al-Masyoun 50, Ramallah', 700.00, '2025-06-15', '2026-06-15', 1, 1, 80, 0, 0, 0, 0, 0,
        0, 0, 0, 'Flexible lease terms', 'approved'), -- Owned by OWN003
       ('100006', 3, 'Ramallah', 'Central 60, Ramallah', 1100.00, '2025-09-01', NULL, 3, 2, 140, 1, 1, 1, 1, 1, 0, 1, 1,
        'Deposit required', 'pending');
-- Owned by OWN003

-- 3. Flat_photos table: 6 photos (2 flats with 3 photos each)
INSERT INTO flat_photos (flat_id, photo_path)
VALUES (1, 'images/flat1_1.jpg'), -- Flat 100001
       (1, 'images/flat1_2.jpg'),
       (1, 'images/flat1_3.jpg'),
       (2, 'images/flat2_1.jpg'), -- Flat 100002
       (2, 'images/flat2_2.jpg'),
       (2, 'images/flat2_3.jpg');

-- 4. Flat_marketing table: 6 marketing entries (3 for each of 2 flats)
INSERT INTO flat_marketing (flat_id, title, description, url)
VALUES (1, 'Nearby School', 'Ramallah High School, 5 min walk', 'http://ramallahschool.edu'),  -- Flat 100001
       (1, 'Supermarket', 'Bravo Supermarket, 2 min drive', 'http://bravosupermarket.com'),
       (1, 'Park', 'Al-Manara Park, 10 min walk', NULL),
       (2, 'Hospital', 'Ramallah Medical Center, 3 min drive', 'http://ramallahhospital.org'), -- Flat 100002
       (2, 'Cafe', 'Zamn Cafe, 5 min walk', 'http://zamncafe.com'),
       (2, 'Gym', 'Fitness Hub, 7 min walk', NULL);

-- 5. Rentals table: 6 rentals by customers (CUS001, CUS002, CUS003, CUS004)
INSERT INTO rentals (flat_id, customer_id, start_date, end_date, total_cost, status)
VALUES (1, 2, '2025-06-01', '2025-12-01', 4800.00, 'current'),  -- CUS001 rents Flat 100001 (6 months * 800)
       (2, 2, '2024-01-01', '2024-12-31', 14400.00, 'past'),    -- CUS001 rented Flat 100002 (12 months * 1200)
       (1, 4, '2024-06-01', '2024-11-30', 4800.00, 'past'),     -- CUS002 rented Flat 100001 (6 months * 800)
       (4, 4, '2025-08-01', '2026-08-01', 18000.00, 'current'), -- CUS002 rents Flat 100004 (12 months * 1500)
       (5, 6, '2025-06-15', '2025-12-15', 4200.00, 'current'),  -- CUS003 rents Flat 100005 (6 months * 700)
       (2, 8, '2025-07-01', '2026-01-01', 7200.00, 'current');
-- CUS004 rents Flat 100002 (6 months * 1200)

-- 6. Appointments table: 6 appointment requests
INSERT INTO appointments (flat_id, customer_id, appointment_date, appointment_time, status)
VALUES (1, 2, '2025-05-20', '10:00:00', 'approved'), -- CUS001 for Flat 100001
       (2, 2, '2025-06-15', '14:00:00', 'pending'),  -- CUS001 for Flat 100002
       (3, 4, '2025-05-18', '11:00:00', 'rejected'), -- CUS002 for Flat 100003
       (4, 4, '2025-07-20', '15:00:00', 'approved'), -- CUS002 for Flat 100004
       (5, 6, '2025-06-10', '09:00:00', 'pending'),  -- CUS003 for Flat 100005
       (6, 8, '2025-08-15', '16:00:00', 'approved');
-- CUS004 for Flat 100006

-- 7. Messages table: 6 messages for different users
INSERT INTO messages (user_id, title, message_body, sender, sent_date, is_read)
VALUES (1, 'Flat Approval Needed', 'Please review flat 100003 for approval.', 'System', '2025-05-15 08:00:00',
        0),                        -- To Manager (MGR001)
       (2, 'Appointment Approved', 'Your appointment for flat 100001 on 2025-05-20 at 10:00 is approved.', 'Owner One',
        '2025-05-16 09:00:00', 1), -- To CUS001
       (3, 'New Flat Submitted', 'Your flat 100003 has been submitted for approval.', 'System', '2025-05-14 10:00:00',
        0),                        -- To OWN002
       (4, 'Rental Confirmation',
        'You have successfully rented flat 100004. Collect keys from Fatima Omar (+970599654321).', 'System',
        '2025-05-17 12:00:00', 1), -- To CUS002
       (6, 'Appointment Request', 'New appointment request for flat 100005 on 2025-06-10 at 09:00.', 'System',
        '2025-05-16 14:00:00', 0), -- To CUS003
       (7, 'Flat Approval Needed', 'Please review flat 100006 for approval.', 'System', '2025-05-17 15:00:00',
        0); -- To Manager (MGR002)