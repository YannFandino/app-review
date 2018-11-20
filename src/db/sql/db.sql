/*
*
* Instrucciones:
* El código es posible ejecutarlo en la consola MySQL
* También puede ser ejecutado a través de PHPMyAdmin
*
*/
-- Crear esquema de la base de datos
CREATE SCHEMA IF NOT EXISTS app_review;

-- Seleccionar base de datos para su uso
USE app_review;

-- Crear tablas de la base de datos
CREATE TABLE IF NOT EXISTS table_categories (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	parent INT,
	CONSTRAINT pk_categories PRIMARY KEY (id),
	CONSTRAINT fk_parent_child FOREIGN KEY (parent) REFERENCES table_categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS table_roles (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	CONSTRAINT pk_roles PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS table_users (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	username VARCHAR(12) NOT NULL,
	email VARCHAR(100) NOT NULL,
	password VARCHAR(20) NOT NULL,
	date_registered DATE NOT NULL,
	rol_id INT NOT NULL,
	CONSTRAINT pk_users PRIMARY KEY (id),
	CONSTRAINT fk_users_roles FOREIGN KEY (rol_id) REFERENCES table_roles(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS table_products (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(25) NOT NULL,
	description VARCHAR(100) NOT NULL,
	details VARCHAR(100),
	category_id INT NOT NULL,
	CONSTRAINT pk_products PRIMARY KEY (id),
	CONSTRAINT fk_products_categories FOREIGN KEY (category_id) REFERENCES table_categories(id)
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS table_images (
	id INT NOT NULL AUTO_INCREMENT,
	url VARCHAR(100) NOT NULL,
	product_id INT NOT NULL,
	CONSTRAINT pk_images PRIMARY KEY (id),
	CONSTRAINT fk_images_products FOREIGN KEY (product_id) REFERENCES table_products(id)
) ENGINE=InnoDB;

-- Inserción de datos
INSERT INTO table_categories (name, parent)
VALUES ('Carretera', NULL), ('Competición', 1);

INSERT INTO table_roles (name)
VALUES ('admin'), ('novato'), ('intermedio'), ('experto');

INSERT INTO table_users (name, username, email, password, date_registered, rol_id)
VALUES ('Test','test01','test@test.com','123456789','2018-10-28', 2);

INSERT INTO table_products (name, description, details, category_id)
VALUES ('Producto 1','Esto es un producto de prueba','Este producto tiene detalle', 1);

INSERT INTO table_reviews (product_id, user_id, points, comment, date_created, last_modified, is_approved)
VALUES (1,1,3,'Esto es un comentario de prueba','2018-10-28', NULL, FALSE);
