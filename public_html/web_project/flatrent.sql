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


