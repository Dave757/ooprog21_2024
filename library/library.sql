CREATE DATABASE IF NOT EXISTS library;
USE library;

CREATE TABLE books(
    bookID INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20),
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100),
    publisher VARCHAR(100),
    yearPublished INT,
    category VARCHAR(50), -- added: needed for the Library Reports module (not in original field list)
    copiesAvailable INT NOT NULL DEFAULT 0,
    bookStat TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE members(
    memberID VARCHAR(10) PRIMARY KEY,
    memberPass VARCHAR(50) NOT NULL,
    memberFName VARCHAR(50) NOT NULL,
    memberLName VARCHAR(50) NOT NULL,
    contactNo VARCHAR(20),
    memberStat TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE borrowing(
    borrowID INT AUTO_INCREMENT PRIMARY KEY,
    bookID INT NOT NULL,
    memberID VARCHAR(10) NOT NULL,
    borrowDate DATE NOT NULL,
    dueDate DATE NOT NULL,
    returnDate DATE NULL,
    borrowStat TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (bookID) REFERENCES books(bookID),
    FOREIGN KEY (memberID) REFERENCES members(memberID)
) ENGINE = InnoDB;
