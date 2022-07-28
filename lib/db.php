<?php
require_once __DIR__.'/../core/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

db::do()->query("CREATE TABLE `books` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` TEXT(100) NOT NULL,
	`autor` TEXT(100) NOT NULL,
	`description` TEXT(500),
	`cover_addr` TEXT(100) NOT NULL,
	`book_addr` INT(100) NOT NULL,
	PRIMARY KEY (`id`))");
db::do()->query("CREATE TABLE `library_tree` (
	`obj_id` INT(11) NOT NULL AUTO_INCREMENT,
	`obj_name` TEXT(100) NOT NULL,
	`parent_id` INT(11) NOT NULL,
	`book_id` INT(11),
	PRIMARY KEY (`obj_id`))");
db::do()->query("INSERT INTO library_tree (obj_name, parent_id) VALUES ('Библиотека', '0')");
