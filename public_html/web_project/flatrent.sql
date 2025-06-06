drop database flatrent;
create database flatrent;

use flatrent;

CREATE TABLE users
(
    user_id          INT AUTO_INCREMENT PRIMARY KEY,
    national_id      VARCHAR(50)                           NOT NULL,
    name             VARCHAR(100)                          NOT NULL,

    flat_no          VARCHAR(50),
    street           VARCHAR(100),
    city             VARCHAR(100),

    postal_code      VARCHAR(10),
    date_of_birth    DATE,
    email            VARCHAR(100) UNIQUE                   NOT NULL,
    mobile_number    VARCHAR(15),
    telephone_number VARCHAR(15),
    bank_name        VARCHAR(100),
    bank_branch      VARCHAR(100),
    account_number   VARCHAR(50),
    password         VARCHAR(255)                          NOT NULL,
    user_type        ENUM ('customer', 'owner', 'manager') NOT NULL,
    customer_id      VARCHAR(9) UNIQUE,
    owner_id         VARCHAR(9) UNIQUE,
    manager_id       VARCHAR(9) UNIQUE,
    profile_photo    VARCHAR(255)
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


select *
from users;

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

INSERT INTO flats (reference_number, owner_id, location, address, monthly_rent,
                   available_from, bedrooms, bathrooms, size_sqm,
                   is_furnished, has_heating, has_ac, has_access_control,
                   has_parking, has_backyard, has_playground, has_storage,
                   rental_conditions, status)
VALUES ('REF001', 14, 'Ramallah', '123 Al-Manara St', 1200.00, '2025-07-01', 2, 1, 80, TRUE, TRUE, TRUE, FALSE, TRUE,
        FALSE, FALSE, TRUE, 'No pets allowed', 'approved'),
       ('REF002', 14, 'Al-Bireh', '12 Al-Quds St', 950.00, '2025-07-01', 1, 1, 60, TRUE, FALSE, TRUE, TRUE, FALSE,
        FALSE, FALSE, FALSE, '', 'approved'),
       ('REF003', 5, 'Ein Musbah', '45 Old Market Rd', 1350.00, '2025-07-01', 3, 2, 100, TRUE, TRUE, TRUE, TRUE, TRUE,
        TRUE, TRUE, TRUE, '', 'approved'),
       ('REF004', 14, 'BirZeit', '8 University Rd', 800.00, '2025-07-01', 2, 1, 70, FALSE, FALSE, TRUE, FALSE, TRUE,
        FALSE, TRUE, FALSE, '1-year lease required', 'approved'),
       ('REF005', 5, 'Abu Qash', '33 Valley View', 1250.00, '2025-07-01', 3, 2, 95, TRUE, TRUE, FALSE, TRUE, TRUE, TRUE,
        TRUE, TRUE, '', 'approved'),
       ('REF006', 14, 'Surda', '9 Surda Heights', 1100.00, '2025-07-01', 2, 1, 85, FALSE, TRUE, TRUE, FALSE, FALSE,
        FALSE, FALSE, TRUE, '', 'approved'),
       ('REF007', 6, 'Al-Irsal', '17 Al-Irsal Blvd', 1400.00, '2025-07-01', 3, 2, 105, TRUE, TRUE, TRUE, TRUE, TRUE,
        TRUE, TRUE, TRUE, 'No smoking', 'approved'),
       ('REF008', 8, 'Ramallah', '77 Main Circle', 1000.00, '2025-07-01', 2, 1, 75, TRUE, FALSE, FALSE, FALSE, TRUE,
        TRUE, FALSE, FALSE, '', 'approved'),
       ('REF009', 14, 'Al-Bireh', '26 Garden St', 950.00, '2025-07-01', 1, 1, 60, FALSE, TRUE, TRUE, FALSE, FALSE, TRUE,
        TRUE, TRUE, '', 'approved'),
       ('REF010', 5, 'Ein Musbah', '5 East Lane', 1150.00, '2025-07-01', 2, 2, 90, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE,
        TRUE, TRUE, '', 'approved'),
       ('REF011', 7, 'BirZeit', '19 Hillside Rd', 850.00, '2025-07-01', 2, 1, 70, FALSE, TRUE, FALSE, TRUE, TRUE, FALSE,
        FALSE, TRUE, '', 'approved'),
       ('REF012', 8, 'Abu Qash', '88 Olive Rd', 1300.00, '2025-07-01', 3, 2, 100, TRUE, TRUE, TRUE, FALSE, TRUE, TRUE,
        FALSE, FALSE, '', 'approved'),
       ('REF013', 5, 'Surda', '6 Central Rd', 1050.00, '2025-07-01', 2, 1, 85, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE,
        FALSE, FALSE, '', 'approved'),
       ('REF014', 14, 'Al-Irsal', '99 Commercial Ave', 1500.00, '2025-07-01', 3, 2, 110, TRUE, TRUE, TRUE, TRUE, TRUE,
        TRUE, TRUE, TRUE, '', 'approved'),
       ('REF015', 5, 'Ramallah', '22 Al-Karama St', 1250.00, '2025-07-01', 2, 1, 80, TRUE, FALSE, TRUE, FALSE, FALSE,
        TRUE, FALSE, TRUE, '', 'approved'),
       ('REF016', 6, 'Al-Bireh', '71 City View', 980.00, '2025-07-01', 1, 1, 65, FALSE, TRUE, FALSE, FALSE, TRUE, FALSE,
        FALSE, FALSE, '', 'approved'),
       ('REF017', 14, 'Ein Musbah', '39 Market St', 1100.00, '2025-07-01', 2, 2, 85, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE,
        FALSE, TRUE, '', 'approved'),
       ('REF018', 5, 'BirZeit', '7 River Rd', 1200.00, '2025-07-01', 2, 1, 75, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE,
        TRUE, TRUE, '', 'approved'),
       ('REF019', 5, 'Abu Qash', '50 Sunset Rd', 1150.00, '2025-07-01', 3, 2, 90, TRUE, FALSE, TRUE, FALSE, FALSE, TRUE,
        TRUE, TRUE, '', 'approved'),
       ('REF020', 14, 'Surda', '40 Wadi St', 1075.00, '2025-07-01', 2, 1, 80, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE, FALSE,
        TRUE, '', 'approved');

select *
from flats;

-- Flat photos
CREATE TABLE flat_photos
(
    photo_id   INT AUTO_INCREMENT PRIMARY KEY,
    flat_id    INT          NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

-- Flat_photos table: 20 records
INSERT INTO flat_photos (flat_id, photo_path)
VALUES (11, '1.1.png'),
       (11, '1.2.png'),
       (11, '1.3.png'),
       (12, '2.1.png'),
       (12, '2.2.png'),
       (12, '2.3.png'),
       (13, '3.1.png'),
       (13, '3.2.png'),
       (13, '3.3.png'),
       (14, '4.1.png'),
       (14, '4.2.png'),
       (14, '4.3.png'),
       (15, '5.1.png'),
       (15, '5.2.png'),
       (15, '5.3.png'),
       (16, '6.1.png'),
       (16, '6.2.png'),
       (16, '6.3.png'),
       (17, '7.1.png'),
       (17, '7.2.png'),
       (17, '7.3.png'),
       (18, '8.1.png'),
       (18, '8.2.png'),
       (18, '8.3.png'),
       (19, '9.1.png'),
       (19, '9.2.png'),
       (19, '9.3.png'),
       (20, '10.1.png'),
       (20, '10.2.png'),
       (20, '10.3.png'),
       (21, '11.1.png'),
       (21, '11.2.png'),
       (21, '11.3.png'),
       (22, '12.1.png'),
       (22, '12.2.png'),
       (22, '12.3.png'),
       (23, '13.1.png'),
       (23, '13.2.png'),
       (23, '13.3.png'),
       (24, '14.1.png'),
       (24, '14.2.png'),
       (24, '14.3.png'),
       (25, '15.1.png'),
       (25, '15.2.png'),
       (25, '15.3.png'),
       (26, '16.1.png'),
       (26, '16.2.png'),
       (26, '16.3.png'),
       (27, '17.1.png'),
       (27, '17.2.png'),
       (27, '17.3.png'),
       (28, '18.1.png'),
       (28, '18.2.png'),
       (28, '18.3.png'),
       (29, '19.1.png'),
       (29, '19.2.png'),
       (29, '19.3.png'),
       (30, '20.1.png'),
       (30, '20.2.png'),
       (30, '20.3.png');

select *
from flat_photos;


CREATE TABLE flat_descriptions
(
    description_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id        INT  NOT NULL,
    title          VARCHAR(100),  -- Optional: headline for display
    summary        VARCHAR(255),  -- Optional: short preview text
    description    TEXT NOT NULL, -- Full description
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id)
);

INSERT INTO flat_descriptions (flat_id, title, summary, description)
VALUES (10, 'Modern Flat in Ein Musbah', 'Spacious 2-bedroom flat in a prime location.',
        '• Two spacious bedrooms with ample closet space\n• Two modern bathrooms with sleek fittings\n• Fully furnished with contemporary decor\n• Heating and air conditioning included\n• Secure access with controlled entry\n• Private parking spot available\n• Storage room included\n• Walking distance to public transport\n• Ideal for families or professionals'),
       (11, 'Cozy Flat in BirZeit Hills', 'Affordable 2-bedroom near university.',
        '• Comfortable two-bedroom layout\n• One functional bathroom\n• Located near BirZeit University\n• Open kitchen and living area\n• Secure building access\n• Nearby public transportation\n• Backyard access for outdoor relaxation\n• Excellent option for students or faculty\n• One-year lease required'),
       (12, 'Charming Home in Abu Qash', 'Spacious 3-bedroom home with greenery.',
        '• Three bedrooms with natural lighting\n• Two full bathrooms with modern tiling\n• Open-plan living and dining area\n• Fully furnished and move-in ready\n• Air conditioning for year-round comfort\n• Peaceful location in a quiet neighborhood\n• Private parking and backyard space\n• Near local shops and cafés\n• Ideal for families seeking comfort'),
       (13, 'Bright Flat in Surda Central', 'Modern flat with stylish features.',
        '• Two well-lit bedrooms\n• One bathroom with elegant fixtures\n• Fully furnished for convenience\n• Heating and AC included\n• Quiet location with countryside views\n• Nearby educational institutions\n• Great natural ventilation\n• Budget-friendly yet spacious\n• Ideal for young couples'),
       (14, 'Luxury Apartment in Al-Irsal', 'High-end 3-bedroom with all amenities.',
        '• Three bedrooms with built-in wardrobes\n• Two full bathrooms with luxury finishes\n• Furnished with high-end furniture\n• Central heating and cooling\n• Gated access with 24/7 security\n• Private parking, storage, and backyard\n• Kids’ playground within the building\n• Close to restaurants and malls\n• Premium living experience in Ramallah'),
       (15, 'Urban Retreat in Ramallah', '2-bedroom flat with city access.',
        '• Two bedrooms with modern fixtures\n• One bathroom with clean design\n• AC and heating for comfort\n• Close to shopping and public transport\n• Green backyard for relaxation\n• Secure and quiet building\n• Furnished for hassle-free move-in\n• Spacious layout for city life\n• Perfect for professionals or couples'),
       (16, 'Efficient Living in Al-Bireh', 'Compact 1-bedroom for minimal living.',
        '• One bedroom, ideal for single living\n• One bathroom with tiled shower\n• Simple unfurnished layout\n• Heating system installed\n• Quiet building near city center\n• Secure entry and well-maintained\n• Nearby cafes, markets, and gyms\n• Great value for urban dwellers\n• Affordable and efficient space'),
       (17, 'Stylish Flat in Ein Musbah', 'Modern 2-bedroom with top amenities.',
        '• Two spacious bedrooms with storage\n• Two well-equipped bathrooms\n• Air conditioning and heating installed\n• Furnished with modern appliances\n• Access-controlled building\n• Private parking and large storage\n• Natural light throughout flat\n• Kids’ play area nearby\n• Ideal for families and roommates'),
       (18, 'Quiet Living in BirZeit', 'Well-equipped 2-bedroom in calm area.',
        '• Two bright bedrooms\n• One bathroom with full tub\n• Open kitchen and dining area\n• Furnished and ready to occupy\n• Private parking and heating included\n• Safe and family-friendly neighborhood\n• Playground nearby for kids\n• Ample natural lighting\n• Great for students and small families'),
       (19, 'Family Home in Abu Qash', 'Spacious 3-bedroom with green views.',
        '• Three large bedrooms\n• Two full bathrooms with storage\n• Large backyard perfect for gatherings\n• Playground and storage areas included\n• Kitchen with ample counter space\n• Quiet and green surroundings\n• Parking spot provided\n• Ideal for families with kids\n• Great schools nearby'),
       (20, 'Comfortable Flat in Surda', 'Functional 2-bedroom with extras.',
        '• Two bedrooms with neutral decor\n• One bathroom with modern fittings\n• Fully furnished for immediate move-in\n• AC and heating included\n• Gated building with security\n• Storage room and parking spot\n• Family-friendly neighborhood\n• Nearby shops and services\n• Excellent value for price'),
       (21, 'Premium Flat in Al-Irsal', 'Top-floor flat with panoramic views.',
        '• Three large bedrooms with great views\n• Two elegant bathrooms\n• Fully furnished with luxury furniture\n• Heating and air conditioning\n• Full building security and access control\n• Parking, playground, and storage\n• Modern design and lighting\n• Walking distance to business centers\n• Ideal for executives and professionals'),
       (22, 'Bright Apartment in Ramallah', 'Quiet 2-bedroom with green space.',
        '• Two airy bedrooms with big windows\n• One modern bathroom\n• Partially furnished for customization\n• Peaceful location in city center\n• Beautiful backyard view\n• Storage and access to local transport\n• Nearby schools and shops\n• Ideal for small families\n• Affordable with great location'),
       (23, 'Minimal Flat in Al-Bireh', 'Affordable 1-bedroom for individuals.',
        '• One cozy bedroom\n• One clean bathroom\n• Unfurnished to suit personal style\n• Heating installed\n• Quiet, low-rise building\n• Convenient parking nearby\n• Access to central roads\n• Great option for students or singles\n• Cost-effective with good features'),
       (24, 'Sunny Flat in Ein Musbah', 'Modern layout in a peaceful street.',
        '• Two bedrooms with natural light\n• Two bathrooms with high-end fixtures\n• Furnished and ready for tenants\n• Full AC and heating coverage\n• Secure entrance with intercom\n• Parking and private storage available\n• Pet-free policy for quiet living\n• Ideal for small families\n• Nearby parks and markets'),
       (25, 'Peaceful Apartment in BirZeit', 'Comfortable home with amenities.',
        '• Two bedrooms in a quiet location\n• One bathroom with tub\n• Furnished with essential appliances\n• Secure building with gated access\n• Backyard and playground access\n• On-site parking available\n• Close to public transport\n• Safe and family-friendly area\n• Bright and cheerful interior'),
       (26, 'Modern Home in Abu Qash', 'Fully equipped 3-bedroom for families.',
        '• Spacious three-bedroom flat\n• Two full bathrooms\n• Furnished with modern touches\n• AC and heating included\n• Private parking and large backyard\n• Family-friendly environment\n• Walking distance to markets\n• Quiet, scenic neighborhood\n• Move-in ready'),
       (27, 'Central Flat in Surda', 'Convenient and comfortable 2-bedroom.',
        '• Two bedrooms with fitted wardrobes\n• One full bathroom\n• Fully furnished for immediate use\n• AC and heating for year-round comfort\n• Gated building with security\n• Backyard and storage available\n• Parking space included\n• Close to shops and cafés\n• Ideal for couples or small families'),
       (28, 'Elegant Apartment in Al-Irsal', 'Luxury living with all features.',
        '• Three bedrooms with premium finishes\n• Two bathrooms with luxury fittings\n• Fully furnished with upscale decor\n• Full AC and heating\n• Gated with 24/7 access control\n• Parking, backyard, and playground\n• Extra storage space\n• Excellent location for professionals\n• High-end residential building'),
       (29, 'Green View Flat in Abu Qash', 'Nature-facing flat with great features.',
        '• Three bedrooms with beautiful views\n• Two bathrooms with bathtubs\n• Semi-furnished with large backyard\n• AC system installed\n• Playground and storage space\n• Quiet and serene area\n• Nearby hiking trails and parks\n• Great for families and nature lovers\n• Pet-friendly and spacious layout'),
       (30, 'Modern 2-Bedroom in Surda', 'Fully equipped flat with excellent amenities.',
        '• Two bedrooms with ample space and sunlight\n• One bathroom with sleek modern fixtures\n• Fully furnished with quality furniture\n• Heating and air conditioning included\n• Access-controlled building for added security\n• Designated private parking space\n• Spacious backyard for relaxation or gardening\n• Extra storage room for convenience\n• Located in a peaceful and well-connected neighborhood\n• Ideal for small families or working professionals');


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

select *
from flat_marketing;


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

-- Messages
CREATE TABLE messages
(
    message_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT                                                 NOT NULL,
    title          VARCHAR(100)                                        NOT NULL,
    message_body   TEXT                                                NOT NULL,
    sender         VARCHAR(100)                                        NOT NULL,
    sent_date      DATETIME                                            NOT NULL,
    is_read        BOOLEAN                                                      DEFAULT FALSE,
    message_type   ENUM ('approval', 'appointment', 'rental', 'other') NOT NULL DEFAULT 'other',
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    flat_id        INT                                                 NULL,
    appointment_id INT                                                 NULL,
    rental_id      INT                                                 NULL,
    FOREIGN KEY (flat_id) REFERENCES flats (flat_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments (appointment_id),
    FOREIGN KEY (rental_id) REFERENCES rentals (rental_id)
);


select *
from flats;
