CREATE DATABASE IF NOT EXISTS comfygo_db;
USE comfygo_db;

CREATE TABLE Users (
    user_ID VARCHAR(100) PRIMARY KEY,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_phone VARCHAR(20),
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Hotels (
    hotel_registration_number VARCHAR(100) PRIMARY KEY,
    hotel_name VARCHAR(100),
    hotel_division VARCHAR(100),
    hotel_district VARCHAR(100),
    hotel_location VARCHAR(150),
    hotel_rating VARCHAR(50),
    hotel_price INT DEFAULT 0
);

CREATE TABLE Manager (
    manager_ID VARCHAR(100) PRIMARY KEY,
    manager_name VARCHAR(100) NOT NULL,
    manager_email VARCHAR(100) UNIQUE NOT NULL,
    manager_mobile VARCHAR(20),
    hotel_registration_number VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (hotel_registration_number) REFERENCES Hotels(hotel_registration_number)
);

CREATE TABLE Transportation (
    transport_ID VARCHAR(100) PRIMARY KEY,
    transport_type VARCHAR(50),
    transport_route VARCHAR(100),
    transport_fare INT
);

CREATE TABLE Guide (
    guide_NID VARCHAR(100) PRIMARY KEY,
    guide_name VARCHAR(100) NOT NULL,
    guide_email VARCHAR(100) UNIQUE NOT NULL,
    guide_mobile VARCHAR(20),
    guide_division VARCHAR(100),
    guide_district VARCHAR(100),
    guide_rate INT DEFAULT 0,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Booking (
    booking_ID VARCHAR(100) PRIMARY KEY,
    booking_Type VARCHAR(100),
    booking_confirmation VARCHAR(100) DEFAULT 'Pending',
    user_ID VARCHAR(100),
    booking_date DATE,
    guide_NID VARCHAR(100),
    hotel_registration_number VARCHAR(100),
    transport_ID VARCHAR(100),
    FOREIGN KEY (user_ID) REFERENCES Users(user_ID),
    FOREIGN KEY (guide_NID) REFERENCES Guide(guide_NID),
    FOREIGN KEY (hotel_registration_number) REFERENCES Hotels(hotel_registration_number),
    FOREIGN KEY (transport_ID) REFERENCES Transportation(transport_ID)
);

CREATE TABLE Payment (
    payment_ID VARCHAR(100) PRIMARY KEY,
    booking_ID VARCHAR(100),
    price INT,
    user_ID VARCHAR(100),
    payment_date DATE,
    payment_method VARCHAR(50),
    FOREIGN KEY (booking_ID) REFERENCES Booking(booking_ID),
    FOREIGN KEY (user_ID) REFERENCES Users(user_ID)
);

CREATE TABLE ContactMessages (
    message_ID VARCHAR(100) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Hotels (hotel_registration_number, hotel_name, hotel_division, hotel_district, hotel_location, hotel_rating, hotel_price) VALUES
('H001','Hotel Grand Sultan','Sylhet','Sylhet','Zindabazar, Sylhet','5',12000),
('H002','Rose View Hotel','Sylhet','Sylhet','Shahjalal Uposhohor, Sylhet','4',8500),
('H003','Nazimgarh Resort','Sylhet','Sylhet','Khadimnagar, Sylhet','5',15000),
('H004','Pan Pacific Sonargaon','Dhaka','Dhaka','Karwan Bazar, Dhaka','5',18000),
('H005','Hotel InterContinental','Dhaka','Dhaka','Gulshan, Dhaka','5',20000),
('H006','Radisson Blu Dhaka','Dhaka','Dhaka','Airport Road, Dhaka','5',17000),
('H007','Agrabad Hotel','Chittagong','Chittagong','Agrabad, Chattogram','4',9000),
('H008','The Peninsula Chittagong','Chittagong','Chittagong','GEC Circle, Chattogram','5',11000),
('H009','Radisson Blu Chattogram Bay View','Chittagong','Chittagong','Karnaphuli, Chattogram','5',16000);

INSERT INTO Transportation (transport_ID, transport_type, transport_route, transport_fare) VALUES
('T001','Train','Dhaka-Sylhet',450),
('T002','Train','Dhaka-Sylhet',500),
('T003','Train','Dhaka-Sylhet',420),
('T004','Train','Dhaka-Chittagong',550),
('T005','Train','Dhaka-Chittagong',600),
('T006','Train','Dhaka-Chittagong',520),
('T007','Train','Chittagong-Sylhet',650),
('T008','Train','Chittagong-Sylhet',700),

('B001','Bus','Dhaka-Sylhet',700),
('B002','Bus','Dhaka-Sylhet',650),
('B003','Bus','Dhaka-Chittagong',800),
('B004','Bus','Dhaka-Chittagong',750),
('B005','Bus','Chittagong-Sylhet',900),

('A001','Airplane','Dhaka-Sylhet',3500),
('A002','Airplane','Dhaka-Chittagong',4000),
('A003','Airplane','Chittagong-Sylhet',4500),

('L001','Launch','Dhaka-Barisal',600),
('L002','Launch','Dhaka-Mongla',900),
('L003','Launch','Barisal-Chittagong',1200);