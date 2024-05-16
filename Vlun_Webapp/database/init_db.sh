#!/bin/bash

mysql -u root -p$MYSQL_ROOT_PASSWORD --execute \
"CREATE DATABASE IF NOT EXISTS store;

USE store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS jaegers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jeager_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) DEFAULT './upload/logo.png',
    model VARCHAR(255) NOT NULL,
    jeager_status VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (username, email, user_password, isAdmin) VALUES ('adminUser', 'admin@master.com', MD5('wakanda_forever'), TRUE);

SET @adminUserId = LAST_INSERT_ID();
INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (@adminUserId, 'Gipsy Danger', './upload/gipsy_danger.jpg', 'Mark-3', 'Active, Nuclear core, Plasma cannon');
INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (@adminUserId, 'Gipsy Danger', './upload/pngwing.com.png', 'Mark-3', 'Active, Nuclear core, Plasma cannon');
INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (@adminUserId, 'Striker Eureka', './upload/striker_eureka.png','Mark-5', 'Active, Missile full loaded, Big boom');

INSERT INTO users (username, email, user_password) VALUES ('user1', 'user1@user1', MD5('user1'));
SET @user1Id = LAST_INSERT_ID();
INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (@user1Id, 'Crimson Typhoon', './upload/crimson_typhoon.jpg', 'Mark-4', 'Repairing, Head missing, useless');
INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (@user1Id, 'Cherno Alpha', './upload/cherno_alpha.jpg', 'Mark-1', 'Destroyed, Head blow, Crashed into trash');
"
