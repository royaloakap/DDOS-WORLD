-- Drop tables if they exist
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS logins;
DROP TABLE IF EXISTS blocked;
DROP TABLE IF EXISTS attacks;

-- Create table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    membership INT NOT NULL,
    expiry DATETIME NOT NULL,
    vip DATETIME NOT NULL,
    private DATETIME NOT NULL,
    cooldown INT NOT NULL,
    concurrents INT NOT NULL,
    maxtime INT NOT NULL,
    ip VARCHAR(255) NOT NULL
);

-- Create table logins
CREATE TABLE logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    ip VARCHAR(255) NOT NULL,
    date DATETIME NOT NULL
);

-- Create table blocked
CREATE TABLE blocked (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(255) NOT NULL
);

-- Create table attacks
CREATE TABLE attacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    target VARCHAR(255) NOT NULL,
    port VARCHAR(10) NOT NULL,
    duration INT NOT NULL,
    method VARCHAR(255) NOT NULL,
    hitted DATETIME NOT NULL DEFAULT NOW(),
    end DATETIME NOT NULL
);

-- Add indexes if needed (e.g., for foreign keys)
ALTER TABLE users ADD INDEX idx_username (username);
