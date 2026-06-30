-- Election Database
-- Import this in phpMyAdmin (XAMPP) before running the PHP files

CREATE DATABASE IF NOT EXISTS election;
USE election;

CREATE TABLE positions (
    posID INT AUTO_INCREMENT PRIMARY KEY,
    posName VARCHAR(100) NOT NULL,
    numOfPositions INT NOT NULL,
    posStat TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE voters (
    voterID VARCHAR(20) PRIMARY KEY,
    voterPass VARCHAR(100) NOT NULL,
    voterFName VARCHAR(100) NOT NULL,
    voterMName VARCHAR(100),
    voterLName VARCHAR(100) NOT NULL,
    voterStat TINYINT(1) NOT NULL DEFAULT 1,
    voted TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE candidates (
    candID INT AUTO_INCREMENT PRIMARY KEY,
    candFName VARCHAR(100) NOT NULL,
    candMName VARCHAR(100),
    candLName VARCHAR(100) NOT NULL,
    posID INT NOT NULL,
    candStat TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (posID) REFERENCES positions(posID)
);

CREATE TABLE votes (
    posID INT NOT NULL,
    voterID VARCHAR(20) NOT NULL,
    candID INT NOT NULL,
    FOREIGN KEY (posID) REFERENCES positions(posID),
    FOREIGN KEY (voterID) REFERENCES voters(voterID),
    FOREIGN KEY (candID) REFERENCES candidates(candID)
);

-- Sample data (optional, you can delete this)
INSERT INTO positions (posName, numOfPositions, posStat) VALUES
('President', 1, 1),
('Vice President', 1, 1),
('Senator', 12, 1);
