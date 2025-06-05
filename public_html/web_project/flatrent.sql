-- Users table (for customers, owners, managers)
CREATE TABLE users
(
    user_id          INT AUTO_INCREMENT PRIMARY KEY,
    national_id      VARCHAR(50)         NOT NULL,
    name             VARCHAR(100)        NOT NULL,

    -- Split address into components
    flat_no          VARCHAR(50),
    street           VARCHAR(100),
    city             VARCHAR(100),

    postal_code      VARCHAR(10),
    date_of_birth    DATE,
    email            VARCHAR(100) UNIQUE NOT NULL,
    mobile_number    VARCHAR(15),
    telephone_number VARCHAR(15),
    bank_name        VARCHAR(100),
    bank_branch      VARCHAR(100),
    account_number   VARCHAR(50),
    password         VARCHAR(255)        NOT NULL,
    user_type        ENUM ('customer', 'owner', 'manager') NOT NULL,
    customer_id      VARCHAR(9) UNIQUE,
    owner_id         VARCHAR(9) UNIQUE,
    manager_id       VARCHAR(9) UNIQUE,
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
    status             ENUM ('pending', 'approved', 'rented') DEFAULT 'pending',
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
    status      ENUM ('pending', 'current', 'past') DEFAULT 'pending',
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
    payment_id         INT AUTO_INCREMENT PRIMARY KEY,
    rental_id          INT          NOT NULL,
    credit_card_number VARCHAR(9)   NOT NULL,
    expiry_date        DATE         NOT NULL,
    cardholder_name    VARCHAR(100) NOT NULL,
    payment_date       DATETIME     NOT NULL,
    FOREIGN KEY (rental_id) REFERENCES rentals (rental_id)
);

-- Flat availability slots (for preview appointment timetables)
CREATE TABLE flat_availability_slots
(
    slot_id          INT AUTO_INCREMENT PRIMARY KEY,
    flat_id          INT         NOT NULL,
    appointment_date DATE        NOT NULL,
    appointment_time TIME        NOT NULL,
    telephone_number VARCHAR(15) NOT NULL,
    is_booked        BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

-- Preview appointments
CREATE TABLE appointments
(
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id        INT NOT NULL,
    customer_id    INT NOT NULL,
    slot_id        INT NOT NULL,
    status         ENUM ('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (customer_id) REFERENCES users (user_id),
    FOREIGN KEY (slot_id) REFERENCES flat_availability_slots (slot_id)
);

-- Messages
CREATE TABLE messages
(
    message_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT          NOT NULL,
    title          VARCHAR(100) NOT NULL,
    message_body   TEXT         NOT NULL,
    sender         VARCHAR(100) NOT NULL,
    sent_date      DATETIME     NOT NULL,
    is_read        BOOLEAN DEFAULT FALSE,
    message_type   ENUM ('approval', 'appointment', 'rental', 'other') NOT NULL DEFAULT 'other',
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    flat_id        INT NULL,
    appointment_id INT NULL,
    rental_id      INT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments (appointment_id),
    FOREIGN KEY (rental_id) REFERENCES rentals (rental_id)
);


INSERT INTO users (user_id, national_id, name, flat_no, street, city, postal_code, date_of_birth, email, mobile_number,
                   telephone_number, bank_name, bank_branch, account_number, password, user_type, customer_id, owner_id,
                   manager_id, profile_photo)
VALUES (1, '1234567890', 'Alice Johnson', '12A', 'Maple Street', 'Springfield', '12345', '1990-05-10',
        'alice@example.com',
        '1234567890', '021234567', NULL, NULL, NULL, '1passworda', 'customer', '123456789', NULL, NULL,
        'photos/alice.jpg'),
       (2, '9876543210', 'Bob Smith', '45B', 'Oak Avenue', 'Riverside', '54321', '1985-11-20', 'bob@example.com',
        '9876543210', NULL, NULL, NULL, NULL, '1secureb', 'customer', '234567890', NULL, NULL, NULL),
       (3, '4567891230', 'Carol White', '7C', 'Elm Street', 'Hillview', '11111', '1992-03-15', 'carol@example.com',
        '1112223333', '023456789', NULL, NULL, NULL, '2rentalc', 'customer', '345678901', NULL, NULL,
        'photos/carol.jpg'),
       (4, '7891234560', 'David Brown', '3D', 'Pine Road', 'Greenwich', '22222', '1988-07-22', 'david@example.com',
        '2223334444', NULL, NULL, NULL, NULL, '3homed', 'customer', '456789012', NULL, NULL, NULL),
       (5, '3216547890', 'Emma Green', '88', 'Cedar Lane', 'Brooktown', '33333', '1975-01-01', 'emma@example.com',
        '3334445555', '025678901', 'ABC Bank', 'Main', 'ACC123456', '4flate', 'owner', NULL, '567890123', NULL,
        'photos/emma.jpg'),
       (6, '6549873210', 'Frank Hall', '6B', 'Birch Lane', 'Lakeside', '44444', '1980-08-18', 'frank@example.com',
        '4445556666', '026789012', 'XYZ Bank', 'Central', 'ACC987654', '5ownerf', 'owner', NULL, '678901234', NULL,
        NULL),
       (7, '1472583690', 'Grace Lee', '9A', 'Willow Drive', 'Seaside', '55555', '1978-04-12', 'grace@example.com',
        '5556667777', NULL, 'Global Bank', 'Downtown', 'ACC192837', '6customerg', 'owner', NULL, '789012345', NULL,
        'photos/grace.jpg'),
       (8, '2583691470', 'Henry King', '4E', 'Sycamore Road', 'Mountainview', '66666', '1983-06-25',
        'henry@example.com',
        '6667778888', '027890123', 'Capital Bank', 'East', 'ACC564738', '7ownerh', 'owner', NULL, '890123456', NULL,
        NULL),
       (9, '3691472580', 'Isabel Moore', '1F', 'Laurel Lane', 'Fairfield', '77777', '1987-09-30', 'isabel@example.com',
        '7778889999', '028901234', NULL, NULL, NULL, '8manageri', 'manager', NULL, NULL, '901234567',
        'photos/isabel.jpg'),
       (10, '7418529630', 'Jack Taylor', '2G', 'Magnolia Street', 'Westville', '88888', '1990-12-05',
        'jack@example.com',
        '8889990000', NULL, NULL, NULL, NULL, '9managerj', 'manager', NULL, NULL, '012345678', NULL),
       (11, '8529637410', 'Kelly Wilson', '5H', 'Chestnut Avenue', 'Easttown', '99999', '1984-02-28',
        'kelly@example.com',
        '9990001111', '029012345', NULL, NULL, NULL, '1rentalk', 'manager', NULL, NULL, '123456789',
        'photos/kelly.jpg'),
       (12, '9637418520', 'Liam Davis', '8I', 'Poplar Road', 'Northcity', '00000', '1986-10-17', 'liam@example.com',
        '0001112222', NULL, NULL, NULL, NULL, '2managerl', 'manager', NULL, NULL, '234567890', NULL);

-- Flats table: 10 records
INSERT INTO flats (flat_id, reference_number, owner_id, location, address, monthly_rent, available_from, available_to,
                   bedrooms,
                   bathrooms, size_sqm, is_furnished, has_heating, has_ac, has_access_control, has_parking,
                   has_backyard,
                   has_playground, has_storage, rental_conditions, status, approved_by, approval_date)
VALUES (1, 'FL1001', 5, 'Springfield', '12A Maple Street, Springfield', 1200.00, '2025-01-01', '2025-12-31', 2, 1, 80,
        TRUE, TRUE, FALSE, TRUE, TRUE, FALSE, FALSE, TRUE, 'No pets allowed.', 'approved', 9, '2024-12-01 10:00:00'),
       (2, 'FL1002', 6, 'Riverside', '45B Oak Avenue, Riverside', 1500.00, '2025-02-01', NULL, 3, 2, 100, FALSE, TRUE,
        TRUE, FALSE, TRUE, TRUE, FALSE, FALSE, '1-year lease minimum.', 'pending', NULL, NULL),
       (3, 'FL1003', 7, 'Hillview', '7C Elm Street, Hillview', 900.00, '2025-03-01', '2025-09-30', 1, 1, 60, TRUE,
        FALSE, FALSE, TRUE, FALSE, FALSE, TRUE, TRUE, 'No smoking.', 'approved', 10, '2024-12-15 14:30:00'),
       (4, 'FL1004', 8, 'Greenwich', '3D Pine Road, Greenwich', 2000.00, '2025-01-15', NULL, 4, 3, 150, TRUE, TRUE,
        TRUE, TRUE, TRUE, TRUE, TRUE, FALSE, 'Background check required.', 'rented', 11, '2024-12-20 09:00:00'),
       (5, 'FL1005', 5, 'Brooktown', '88 Cedar Lane, Brooktown', 1100.00, '2025-04-01', '2025-10-31', 2, 1, 75, FALSE,
        TRUE, FALSE, FALSE, TRUE, FALSE, FALSE, TRUE, 'No subletting.', 'approved', 12, '2024-12-25 11:00:00'),
       (6, 'FL1006', 6, 'Lakeside', '6B Birch Lane, Lakeside', 1300.00, '2025-05-01', NULL, 2, 2, 90, TRUE, TRUE, TRUE,
        TRUE, FALSE, TRUE, FALSE, TRUE, 'Pets allowed with deposit.', 'pending', NULL, NULL),
       (7, 'FL1007', 7, 'Seaside', '9A Willow Drive, Seaside', 1600.00, '2025-06-01', '2025-12-31', 3, 2, 110, TRUE,
        FALSE, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE, 'No parties.', 'approved', 9, '2025-01-01 13:00:00'),
       (8, 'FL1008', 8, 'Mountainview', '4E Sycamore Road, Mountainview', 950.00, '2025-07-01', NULL, 1, 1, 55, FALSE,
        TRUE, FALSE, FALSE, FALSE, TRUE, FALSE, TRUE, 'Quiet hours after 10 PM.', 'pending', NULL, NULL),
       (9, 'FL1009', 5, 'Fairfield', '1F Laurel Lane, Fairfield', 1800.00, '2025-08-01', '2026-01-31', 3, 2, 120, TRUE,
        TRUE, TRUE, TRUE, TRUE, TRUE, FALSE, FALSE, 'Credit check required.', 'approved', 10, '2025-01-10 15:00:00'),
       (10, 'FL1010', 6, 'Westville', '2G Magnolia Street, Westville', 1400.00, '2025-09-01', NULL, 2, 1, 85, FALSE,
        TRUE, TRUE, FALSE, TRUE, FALSE, TRUE, TRUE, 'No modifications allowed.', 'rented', 11, '2025-01-15 12:00:00');

-- Flat_photos table: 20 records
INSERT INTO flat_photos (photo_id, flat_id, photo_path)
VALUES (1, 1, 'photos/flat1_1.jpg'),
       (2, 1, 'photos/flat1_2.jpg'),
       (3, 2, 'photos/flat2_1.jpg'),
       (4, 2, 'photos/flat2_2.jpg'),
       (5, 2, 'photos/flat2_3.jpg'),
       (6, 3, 'photos/flat3_1.jpg'),
       (7, 3, 'photos/flat3_2.jpg'),
       (8, 4, 'photos/flat4_1.jpg'),
       (9, 4, 'photos/flat4_2.jpg'),
       (10, 4, 'photos/flat4_3.jpg'),
       (11, 5, 'photos/flat5_1.jpg'),
       (12, 5, 'photos/flat5_2.jpg'),
       (13, 6, 'photos/flat6_1.jpg'),
       (14, 6, 'photos/flat6_2.jpg'),
       (15, 7, 'photos/flat7_1.jpg'),
       (16, 7, 'photos/flat7_2.jpg'),
       (17, 7, 'photos/flat7_3.jpg'),
       (18, 8, 'photos/flat8_1.jpg'),
       (19, 9, 'photos/flat9_1.jpg'),
       (20, 10, 'photos/flat10_1.jpg');

-- Flat_marketing table: 10 records
INSERT INTO flat_marketing (marketing_id, flat_id, title, description, url)
VALUES (1, 1, 'Cozy Springfield Apartment', 'Bright and modern 2-bedroom flat in the heart of Springfield.',
        'https://example.com/flat1'),
       (2, 2, 'Spacious Riverside Home', 'Unfurnished 3-bedroom with river views.', 'https://example.com/flat2'),
       (3, 3, 'Compact Hillview Studio', 'Perfect for singles, fully furnished with playground access.',
        'https://example.com/flat3'),
       (4, 4, 'Luxury Greenwich Villa', 'Spacious 4-bedroom with all amenities.', 'https://example.com/flat4'),
       (5, 5, 'Affordable Brooktown Flat', '2-bedroom apartment close to public transport.',
        'https://example.com/flat5'),
       (6, 6, 'Lakeside Modern Apartment', 'Pet-friendly 2-bedroom with AC.', 'https://example.com/flat6'),
       (7, 7, 'Seaside Family Home', '3-bedroom with playground and ocean views.', 'https://example.com/flat7'),
       (8, 8, 'Quiet Mountainview Studio', 'Ideal for remote workers, unfurnished.', 'https://example.com/flat8'),
       (9, 9, 'Fairfield Deluxe Flat', '3-bedroom with premium amenities.', 'https://example.com/flat9'),
       (10, 10, 'Westville Cozy Home', '2-bedroom with parking and storage.', 'https://example.com/flat10');

-- Rentals table: 8 records
INSERT INTO rentals (rental_id, flat_id, customer_id, start_date, end_date, total_cost, status)
VALUES (1, 1, 1, '2025-01-01', '2025-12-31', 14400.00, 'current'),
       (2, 4, 2, '2025-01-15', '2026-01-14', 24000.00, 'current'),
       (3, 3, 3, '2025-03-01', '2025-08-31', 5400.00, 'past'),
       (4, 5, 4, '2025-04-01', '2025-10-31', 7700.00, 'current'),
       (5, 7, 1, '2025-06-01', '2025-12-31', 11200.00, 'pending'),
       (6, 9, 2, '2025-08-01', '2026-01-31', 10800.00, 'pending'),
       (7, 10, 3, '2025-09-01', '2026-08-31', 16800.00, 'current'),
       (8, 1, 4, '2026-01-01', '2026-12-31', 14400.00, 'pending');

-- Pending_rentals table: 8 records
INSERT INTO pending_rentals (pending_rental_id, flat_id, customer_id, start_date, end_date, total_cost, created_at)
VALUES (1, 2, 1, '2025-02-01', '2025-07-31', 9000.00, '2025-06-01 08:00:00'),
       (2, 6, 2, '2025-05-01', '2025-10-31', 9100.00, '2025-06-02 09:00:00'),
       (3, 8, 3, '2025-07-01', '2025-12-31', 5700.00, '2025-06-03 10:00:00'),
       (4, 2, 4, '2025-08-01', '2026-07-31', 18000.00, '2025-06-04 11:00:00'),
       (5, 3, 1, '2025-10-01', '2026-03-31', 5400.00, '2025-06-05 12:00:00'),
       (6, 5, 2, '2025-11-01', '2026-04-30', 6600.00, '2025-06-05 13:00:00'),
       (7, 7, 3, '2026-01-01', '2026-06-30', 9600.00, '2025-06-05 14:00:00'),
       (8, 9, 4, '2026-02-01', '2026-07-31', 10800.00, '2025-06-05 15:00:00');

-- Payments table: 8 records
INSERT INTO payments (payment_id, rental_id, credit_card_number, expiry_date, cardholder_name, payment_date)
VALUES (1, 1, '123456789', '2026-12-31', 'Alice Johnson', '2025-01-01 12:00:00'),
       (2, 2, '987654321', '2027-01-31', 'Bob Smith', '2025-01-15 14:00:00'),
       (3, 3, '456789123', '2026-06-30', 'Carol White', '2025-03-01 10:00:00'),
       (4, 4, '789123456', '2026-11-30', 'David Brown', '2025-04-01 11:00:00'),
       (5, 5, '321654987', '2027-02-28', 'Alice Johnson', '2025-06-01 13:00:00'),
       (6, 6, '654987321', '2026-08-31', 'Bob Smith', '2025-08-01 15:00:00'),
       (7, 7, '147258369', '2027-03-31', 'Carol White', '2025-09-01 09:00:00'),
       (8, 8, '258369147', '2026-09-30', 'David Brown', '2026-01-01 12:00:00');

-- Flat_availability_slots table: 12 records
INSERT INTO flat_availability_slots (slot_id, flat_id, appointment_date, appointment_time, telephone_number, is_booked)
VALUES (1, 1, '2025-01-05', '10:00:00', '1234567890', TRUE),
       (2, 1, '2025-01-05', '14:00:00', '1234567890', FALSE),
       (3, 2, '2025-02-10', '11:00:00', '4445556666', TRUE),
       (4, 3, '2025-03-15', '15:00:00', '5556667777', FALSE),
       (5, 4, '2025-01-20', '09:00:00', '6667778888', TRUE),
       (6, 5, '2025-04-05', '13:00:00', '3334445555', TRUE),
       (7, 6, '2025-05-10', '12:00:00', '4445556666', FALSE),
       (8, 7, '2025-06-15', '16:00:00', '5556667777', TRUE),
       (9, 8, '2025-07-20', '10:00:00', '6667778888', FALSE),
       (10, 9, '2025-08-25', '14:00:00', '3334445555', TRUE),
       (11, 10, '2025-09-01', '11:00:00', '4445556666', FALSE),
       (12, 10, '2025-09-01', '15:00:00', '4445556666', TRUE);

-- Appointments table: 10 records
INSERT INTO appointments (appointment_id, flat_id, customer_id, slot_id, status)
VALUES (1, 1, 1, 1, 'approved'),
       (2, 2, 2, 3, 'pending'),
       (3, 3, 3, 4, 'rejected'),
       (4, 4, 4, 5, 'approved'),
       (5, 5, 1, 6, 'pending'),
       (6, 6, 2, 7, 'approved'),
       (7, 7, 3, 8, 'rejected'),
       (8, 8, 4, 9, 'pending'),
       (9, 9, 1, 10, 'approved'),
       (10, 10, 2, 12, 'pending');

-- Messages table: 12 records
INSERT INTO messages (message_id, user_id, title, message_body, sender, sent_date, is_read, message_type, flat_id,
                      appointment_id, rental_id)
VALUES (1, 1, 'Rental Approved', 'Your rental for Flat FL1001 has been approved.', 'System', '2025-01-01 12:00:00',
        TRUE, 'rental', 1, NULL, 1),
       (2, 1, 'System Maintenance', 'Scheduled maintenance on 2025-06-10.', 'Admin', '2025-06-01 08:00:00', FALSE,
        'other', NULL, NULL, NULL),
       (3, 2, 'Appointment Scheduled', 'Your appointment for Flat FL1002 is confirmed.', 'System',
        '2025-02-01 09:00:00', FALSE, 'appointment', 2, 2, NULL),
       (4, 2, 'Rental Pending', 'Your rental request for Flat FL1004 is pending.', 'System', '2025-01-15 14:00:00',
        TRUE, 'rental', 4, NULL, 2),
       (5, 3, 'Flat Approval', 'Flat FL1003 has been approved for listing.', 'System', '2024-12-15 14:30:00', TRUE,
        'approval', 3, NULL, NULL),
       (6, 3, 'Appointment Rejected', 'Your appointment for Flat FL1007 was rejected.', 'System', '2025-06-15 16:00:00',
        FALSE, 'appointment', 7, 7, NULL),
       (7, 4, 'Welcome to Platform', 'Welcome to our rental platform!', 'Admin', '2025-04-01 10:00:00', TRUE, 'other',
        NULL, NULL, NULL),
       (8, 4, 'Rental Payment Due', 'Payment for Flat FL1001 is due.', 'System', '2026-01-01 12:00:00', FALSE, 'rental',
        1, NULL, 8),
       (9, 5, 'New Flat Listing', 'Your flat FL1005 has been listed.', 'System', '2024-12-25 11:00:00', TRUE,
        'approval', 5, NULL, NULL),
       (10, 6, 'Tenant Inquiry', 'A tenant has inquired about Flat FL1006.', 'System', '2025-05-01 13:00:00', FALSE,
        'other', 6, NULL, NULL),
       (11, 9, 'Approval Task', 'Please review Flat FL1007 for approval.', 'System', '2025-01-01 13:00:00', TRUE,
        'approval', 7, NULL, NULL),
       (12, 10, 'Appointment Confirmation', 'Appointment for Flat FL1009 confirmed.', 'System', '2025-08-25 14:00:00',
        FALSE, 'appointment', 9, 9, NULL);

















