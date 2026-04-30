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
    hotel_price INT DEFAULT 0,
    hotel_description TEXT DEFAULT NULL
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

CREATE TABLE TouristSpots (
    spot_id VARCHAR(100) PRIMARY KEY,
    spot_name VARCHAR(150) NOT NULL,
    city VARCHAR(100),
    division VARCHAR(100),
    description TEXT,
    image_url VARCHAR(255),
    best_season VARCHAR(100) DEFAULT 'Year-round',
    entry_fee INT DEFAULT 0,
    estimated_hours DECIMAL(4,1) DEFAULT NULL
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

INSERT INTO TouristSpots (spot_id, spot_name, city, division, description, best_season, entry_fee, estimated_hours) VALUES

('SP001','Lalbagh Fort','Dhaka','Dhaka','A 17th-century Mughal fort in the heart of old Dhaka.','Oct–Mar',20,2.0),
('SP002','Ahsan Manzil','Dhaka','Dhaka','The Pink Palace — former residence of the Nawabs of Dhaka.','Oct–Mar',30,1.5),
('SP003','Sadarghat Launch Terminal','Dhaka','Dhaka','Bustling river port on the Buriganga — a living slice of Dhaka life.','Year-round',0,1.0),
('SP004','Dhakeshwari Temple','Dhaka','Dhaka','The national temple of Bangladesh, dating back to the 12th century.','Oct–Mar',0,1.0),
('SP005','National Museum Dhaka','Dhaka','Dhaka','Four floors of Bangladesh history, art, and natural heritage.','Year-round',20,2.5),
('SP006','Ratargul Swamp Forest','Sylhet','Sylhet','The only freshwater swamp forest in Bangladesh — ethereal when flooded.','Jun–Oct',50,3.0),
('SP007','Jaflong','Sylhet','Sylhet','Stone-lined riverbed on the Piyain river at the Indian border.','Sep–Mar',0,2.5),
('SP008','Srimangal Tea Gardens','Sylhet','Sylhet','Rolling emerald tea estates — the tea capital of Bangladesh.','Sep–Mar',0,3.0),
('SP009','Hazrat Shah Jalal Shrine','Sylhet','Sylhet','The most revered shrine in Sylhet, drawing pilgrims year-round.','Year-round',0,1.0),
('SP010','Bichanakandi','Sylhet','Sylhet','Crystal-clear river surrounded by hills at the India border.','Sep–Feb',0,3.0),
('SP011','Patenga Sea Beach','Chittagong','Chittagong','A popular sea beach at the mouth of the Karnaphuli river.','Nov–Feb',0,2.0),
('SP012','Foy\'s Lake','Chittagong','Chittagong','A serene artificial lake surrounded by hills and woodland.','Nov–Mar',150,2.5),
('SP013','Ethnological Museum','Chittagong','Chittagong','Showcases the tribal and ethnic heritage of Bangladesh.','Year-round',10,1.5),
('SP014','Chandranath Hill','Chittagong','Chittagong','Sacred hilltop temple with panoramic views over Sitakunda.','Nov–Feb',0,3.0),
('SP015','Kaptai Lake','Chittagong','Chittagong','The largest man-made lake in Bangladesh in the hill tracts.','Nov–Mar',0,4.0);