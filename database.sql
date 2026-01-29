-- Easy Sklad database schema and seed data

CREATE DATABASE IF NOT EXISTS easy_sklad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE easy_sklad;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    first_name VARCHAR(120) NOT NULL,
    last_name VARCHAR(120) NULL,
    username VARCHAR(120) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    tariff VARCHAR(32) NOT NULL DEFAULT 'Free',
    balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    inn VARCHAR(32) NULL,
    address VARCHAR(255) NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE company_users (
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(32) NOT NULL,
    PRIMARY KEY (company_id, user_id),
    INDEX idx_company_users_user (user_id),
    CONSTRAINT fk_company_users_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_company_users_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(190) NOT NULL,
    address VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_warehouses_company (company_id),
    CONSTRAINT fk_warehouses_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    sku VARCHAR(64) NOT NULL,
    name VARCHAR(190) NOT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    cost DECIMAL(12,2) NULL,
    unit VARCHAR(32) NULL,
    min_stock DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_products_sku (warehouse_id, sku),
    INDEX idx_products_warehouse (warehouse_id),
    CONSTRAINT fk_products_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_stocks (
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    qty DECIMAL(12,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (product_id, warehouse_id),
    CONSTRAINT fk_product_stocks_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_product_stocks_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    product_id INT NOT NULL,
    type ENUM('in', 'out') NOT NULL,
    qty DECIMAL(12,2) NOT NULL,
    cost DECIMAL(12,2) NULL,
    reference_type VARCHAR(32) NULL,
    reference_id INT NULL,
    supplier VARCHAR(190) NULL,
    movement_date DATE NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_stock_movements_warehouse (warehouse_id),
    INDEX idx_stock_movements_product (product_id),
    CONSTRAINT fk_stock_movements_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    CONSTRAINT fk_stock_movements_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    customer_name VARCHAR(190) NULL,
    payment_method ENUM('cash', 'card', 'transfer') NOT NULL,
    status ENUM('draft', 'paid', 'canceled') NOT NULL DEFAULT 'draft',
    discount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_orders_warehouse (warehouse_id),
    INDEX idx_orders_company (company_id),
    CONSTRAINT fk_orders_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    qty DECIMAL(12,2) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    INDEX idx_order_items_order (order_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(190) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_services_company (company_id),
    CONSTRAINT fk_services_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    service_id INT NOT NULL,
    qty DECIMAL(12,2) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    INDEX idx_order_services_order (order_id),
    CONSTRAINT fk_order_services_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_services_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (id, name, first_name, last_name, username, email, password_hash, tariff, balance, created_at) VALUES
(1, 'Test Owner', 'Test', 'Owner', 'test_owner', 'test@example.com', '$2y$12$QM0cEvilg5XT98FKqCk70OpeuSMmSO3DqsUr3nLEFxPkIaYWqQvTq', 'Free', 0, NOW());

INSERT INTO companies (id, name, inn, address, created_at) VALUES
(1, 'Easy Sklad Demo', '1234567890', 'Demo street 1', NOW());

INSERT INTO company_users (company_id, user_id, role) VALUES
(1, 1, 'owner');

INSERT INTO warehouses (id, company_id, name, address, created_at) VALUES
(1, 1, 'Main Warehouse', 'Warehouse street 1', NOW());

INSERT INTO products (id, warehouse_id, sku, name, price, cost, unit, min_stock, created_at) VALUES
(1, 1, 'SKU-001', 'Test Product A', 100.00, 70.00, 'pcs', 5, NOW()),
(2, 1, 'SKU-002', 'Test Product B', 50.00, 30.00, 'pcs', 3, NOW());

INSERT INTO product_stocks (product_id, warehouse_id, qty) VALUES
(1, 1, 20),
(2, 1, 15);

INSERT INTO services (id, company_id, name, price, description, created_at) VALUES
(1, 1, 'Delivery', 500.00, 'Express delivery', NOW()),
(2, 1, 'Installation', 800.00, 'Installation service', NOW());
