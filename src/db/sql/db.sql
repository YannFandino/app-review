/*
*
* Instrucciones:
* El código es posible ejecutarlo en la consola MySQL
* También puede ser ejecutado a través de PHPMyAdmin
*
*/
-- Crear esquema de la base de datos
CREATE SCHEMA IF NOT EXISTS app_review DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;

-- Seleccionar base de datos para su uso
USE app_review;

-- Crear tablas de la base de datos
CREATE TABLE IF NOT EXISTS table_categories (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	parent INT,
	CONSTRAINT pk_categories PRIMARY KEY (id),
	CONSTRAINT fk_parent_child FOREIGN KEY (parent) REFERENCES table_categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_roles (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	CONSTRAINT pk_roles PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_users (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	username VARCHAR(12) UNIQUE NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL,
	date_registered DATE NOT NULL,
	rol_id INT NOT NULL,
	CONSTRAINT pk_users PRIMARY KEY (id),
	CONSTRAINT fk_users_roles FOREIGN KEY (rol_id) REFERENCES table_roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_products (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	description VARCHAR(100) NOT NULL,
	details VARCHAR(100),
	category_id INT NOT NULL,
	CONSTRAINT pk_products PRIMARY KEY (id),
	CONSTRAINT fk_products_categories FOREIGN KEY (category_id) REFERENCES table_categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_reviews (
	id INT NOT NULL AUTO_INCREMENT,
	product_id INT NOT NULL,
	user_id INT NOT NULL,
	points FLOAT DEFAULT 0,
	comment VARCHAR(250),
	date_created DATE NOT NULL,
	last_modified DATE,
	is_approved BOOLEAN DEFAULT false,
	CONSTRAINT pk_reviews PRIMARY KEY (id),
	CONSTRAINT fk_reviews_users FOREIGN KEY (user_id) REFERENCES table_users(id),
	CONSTRAINT fk_reviews_products FOREIGN KEY (product_id) REFERENCES table_products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_images (
	id INT NOT NULL AUTO_INCREMENT,
	url VARCHAR(100) NOT NULL,
	product_id INT NOT NULL,
	CONSTRAINT pk_images PRIMARY KEY (id),
	CONSTRAINT fk_images_products FOREIGN KEY (product_id) REFERENCES table_products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Inserción de datos iniciales
INSERT INTO table_roles (name)
VALUES ('admin'),('moderador'), ('novato'), ('intermedio'), ('experto');

INSERT INTO table_users (name, username, email, password, date_registered, rol_id)
VALUES ('admin','admin','admin@appreview.com','$2y$10$yEfYe.wesUSwAsAgR59fdOt5BE3HocPwzppU8TgRWNwrVIuHyW3bK',CURRENT_DATE, 1);