CREATE DATABASE IF NOT EXISTS election;
USE election;

CREATE TABLE positions(
    posID INT AUTO_INCREMENT PRIMARY KEY,
    posName VARCHAR(50),
    numOfPositions INT,
    posStat TINYINT(1) DEFAULT 1
);

CREATE TABLE voters(
    voterID VARCHAR(10) PRIMARY KEY,
    voterPass VARCHAR(50),
    voterFName VARCHAR(50),
    voterMName VARCHAR(50),
    voterLName VARCHAR(50),
    voterStat TINYINT(1) DEFAULT 1,
    voted TINYINT(1) DEFAULT 0
);

CREATE TABLE candidates(
    candID INT AUTO_INCREMENT PRIMARY KEY,
    candFName VARCHAR(50),
    candMName VARCHAR(50),
    candLName VARCHAR(50),
    posID INT,
    candStat TINYINT(1) DEFAULT 1,
    FOREIGN KEY (posID) REFERENCES positions(posID)
);

CREATE TABLE votes(
    posID INT,
    voterID VARCHAR(10),
    candID INT,
    FOREIGN KEY (posID) REFERENCES positions(posID),
    FOREIGN KEY (voterID) REFERENCES voters(voterID),
    FOREIGN KEY (candID) REFERENCES candidates(candID)
);
