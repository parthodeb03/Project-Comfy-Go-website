CREATE DATABASE database_schema;
USE database_schema;

CREATE TABLE Users(
user_ID VARCHAR(100) PRIMARY KEY,
user_email VARCHAR(100),
user_name VARCHAR(100),
user_phone VARCHAR(20),
password VARCHAR(100)
);

CREATE TABLE Hotels(
hotel_registration_number VARCHAR(100) PRIMARY KEY,
hotel_name VARCHAR(100),
hotel_division VARCHAR(100),
hotel_district VARCHAR(100),
hotel_location VARCHAR(150),
hotel_rating VARCHAR(50)
);

CREATE TABLE Manager(
manager_ID VARCHAR(100) PRIMARY KEY,
manager_name VARCHAR(100),
manager_email VARCHAR(100),
manager_mobile VARCHAR(20),
hotel_registration_number VARCHAR(100),
FOREIGN KEY (hotel_registration_number)
REFERENCES Hotels(hotel_registration_number)
);

CREATE TABLE Transportation(
transport_ID VARCHAR(100) PRIMARY KEY,
transport_type VARCHAR(50),
transport_route VARCHAR(100),
transport_fare INT
);

CREATE TABLE Guide(
guide_NID VARCHAR(100) PRIMARY KEY,
guide_name VARCHAR(100),
guide_email VARCHAR(100),
guide_mobile VARCHAR(20),
guide_division VARCHAR(100),
guide_district VARCHAR(100)
);

CREATE TABLE Booking(
booking_ID VARCHAR(100) PRIMARY KEY,
booking_Type VARCHAR(100),
booking_confirmation VARCHAR(100),
user_ID VARCHAR(100),
booking_date DATE,
FOREIGN KEY (user_ID)
REFERENCES Users(user_ID)
);

CREATE TABLE Payment(
payment_ID VARCHAR(100) PRIMARY KEY,
booking_ID VARCHAR(100),
price INT,
user_ID VARCHAR(100),
FOREIGN KEY (booking_ID)
REFERENCES Booking(booking_ID),
FOREIGN KEY (user_ID)
REFERENCES Users(user_ID)
);