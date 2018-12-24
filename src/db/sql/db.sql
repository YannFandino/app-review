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
	name VARCHAR(50) NOT NULL,
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
	username VARCHAR(15) UNIQUE NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL,
	date_registered DATE NOT NULL,
	rol_id INT NOT NULL,
	CONSTRAINT pk_users PRIMARY KEY (id),
	CONSTRAINT fk_users_roles FOREIGN KEY (rol_id) REFERENCES table_roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_products (
	id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	description TEXT NOT NULL,
	details TEXT,
	category_id INT NOT NULL,
	CONSTRAINT pk_products PRIMARY KEY (id),
	CONSTRAINT fk_products_categories FOREIGN KEY (category_id) REFERENCES table_categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS table_reviews (
	id INT NOT NULL AUTO_INCREMENT,
	product_id INT NOT NULL,
	user_id INT NOT NULL,
	multiplier INT NOT NULL,
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
VALUES ('admin','admin','admin@appreview.com','$2y$10$yEfYe.wesUSwAsAgR59fdOt5BE3HocPwzppU8TgRWNwrVIuHyW3bK',CURRENT_DATE, 1),
			 ('José Pérez','novato1','novato1@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 3),
			 ('Adrea Jiménez','intermedio1','intermedio1@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 4),
			 ('Rodrigo García','experto1','experto1@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 5),
			 ('María Gascón','novato2','novato2@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 1),
			 ('Marta Rodríguez','intermedio2','intermedio2@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 2),
			 ('Israel Camacho','experto2','experto2@appreview.com','$2y$10$g7yzrmAbPNXi1RUXbPdr/OSo6zliwTRJDr8s5Kvc/uNMLmFz5.hQy',CURRENT_DATE, 3);

INSERT INTO table_categories (name, parent) VALUES
('ordenadores', NULL),
('perifÃ©ricos', NULL),
('consolas', NULL),
('portÃ¡tiles', 1),
('sobremesa', 1),
('all in one', 1),
('monitores', 2),
('teclados', 2),
('ratones', 2);

INSERT INTO table_products (name, description, details, category_id) VALUES
('sony playstation 4', 'la nueva ps4 mÃ¡s delgada y ligera, la consola mÃ¡s vendida del mundo, incluye gran potencia para tus juegos en versiones de 500 gb y 1 tb. ya disponible en jet black, glacier white y los fantÃ¡sticos gold y silver.', 'Dimensiones 265 x 288 x 39 mm\r\nPeso 2,1kg\r\nResoluciÃ³n 480p, 720p, 1080i, 1080p, 4K (video only)\r\nCPU AMD Jaguar x86-64, 8-core\r\nGPU AMD Radeon, 1.84 TFLOP\r\nRAM 8GB', 3),
('xbox one', 'juega mejor con xbox one x. experimenta una potencia un 40% superior a cualquier otra consola, experiencia realmente inmersiva en juegos 4k. los tÃ­tulos de blockbuster se ven genial, corren con fluidez y cargan rÃ¡pidamente, y puedes llevar contigo todos tus juegos y accesorios de xbox one.', '', 3),
('macbook air 11\'\'', 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'Lorem ipsum dolor sit amet\r\nConsectetur adipiscing elit\r\nSed do eiusmod tempor', 4),
('portatil hp', 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', '', 4),
('pc sobremesa hp', 'excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 6),
('monitor benq gw2280', 'excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'BenQ GW2480', 7),
('apple imac pro 27\'\'', 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', 6),
('all in one dell', 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '', 6);

INSERT INTO table_images (url, product_id) VALUES
('0-79ca8a9743bc129fea59186b7647c5fa.jpg', 1),
('1-79ca8a9743bc129fea59186b7647c5fa.png', 1),
('0-501e12843f302a88dc31a962c01354cd.jpg', 2),
('1-501e12843f302a88dc31a962c01354cd.jpg', 2),
('0-531d756fcaab3c5db9989f1935829b9c.jpg', 3),
('1-531d756fcaab3c5db9989f1935829b9c.jpg', 3),
('2-531d756fcaab3c5db9989f1935829b9c.jpg', 3),
('0-55afe5dc05048461370680cbc93380cf.jpg', 4),
('1-55afe5dc05048461370680cbc93380cf.jpg', 4),
('2-55afe5dc05048461370680cbc93380cf.jpg', 4),
('3-55afe5dc05048461370680cbc93380cf.jpg', 4),
('0-cec1fe3eeb52ddb69445c689a70560e4.jpg', 5),
('1-cec1fe3eeb52ddb69445c689a70560e4.jpg', 5),
('2-cec1fe3eeb52ddb69445c689a70560e4.jpg', 5),
('0-777843b5b374f752de088195cee13971.jpg', 6),
('1-777843b5b374f752de088195cee13971.jpg', 6),
('0-eaa808f2e320534de835d5cb0e8be296.jpg', 7),
('0-963bde9c5d102b9919f470a094f3bab2.jpg', 8),
('1-963bde9c5d102b9919f470a094f3bab2.jpg', 8),
('2-963bde9c5d102b9919f470a094f3bab2.png', 8);

INSERT INTO table_reviews (product_id, user_id, multiplier, points, comment, date_created, last_modified, is_approved) VALUES
(8, 2, 1, 4, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(6, 2, 1, 5, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam.', '2018-12-24', NULL, 0),
(3, 2, 1, 2, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(5, 3, 2, 3, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(3, 3, 2, 3, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(3, 4, 3, 5, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(4, 4, 3, 4, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(5, 4, 3, 2, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0),
(6, 4, 3, 5, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut enim ad minim veniam', '2018-12-24', NULL, 0);


