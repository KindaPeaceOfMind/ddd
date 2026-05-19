-- Дамп базы данных для ТСЖ «Стрельникова»

CREATE DATABASE IF NOT EXISTS tsj_strelnikova DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tsj_strelnikova;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    apartment VARCHAR(20),
    role ENUM('tenant', 'operator', 'worker', 'admin') NOT NULL DEFAULT 'tenant',
    rating DECIMAL(3,2) DEFAULT 0,
    rating_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица категорий заявок
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

INSERT INTO categories (name) VALUES 
('Сантехника'), ('Электрика'), ('Уборка'), ('Ремонт подъезда'), ('Вывоз мусора'), ('Отопление'), ('Другое');

-- Таблица заявок
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('new', 'assigned', 'in_progress', 'completed', 'closed', 'cancelled') DEFAULT 'new',
    worker_id INT,
    photos TEXT,
    completion_report TEXT,
    work_photos TEXT,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (worker_id) REFERENCES users(id)
);

-- Таблица новостей
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица логов действий
CREATE TABLE IF NOT EXISTS action_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_name VARCHAR(255),
    role VARCHAR(50),
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица баннеров
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    message TEXT,
    photo TEXT,
    active BOOLEAN DEFAULT FALSE
);

-- Таблица квитанций
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    paid BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Демо пользователи
INSERT INTO users (full_name, email, password, apartment, role) VALUES
('Тестовый жилец', 'tenant@example.com', '123', '1', 'tenant'),
('Тестовый оператор', 'operator@example.com', '123', NULL, 'operator'),
('Тестовый рабочий', 'worker@example.com', '123', NULL, 'worker'),
('Администратор', 'admin@example.com', '123', NULL, 'admin');
