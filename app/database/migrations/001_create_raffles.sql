-- ==========================================
-- GRL RIFAS v1.0
-- Base de Datos
-- ==========================================

CREATE TABLE raffles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    ticket_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_numbers INT NOT NULL DEFAULT 100,
    draw_date DATETIME NULL,
    whatsapp VARCHAR(20) NULL,
    clabe VARCHAR(30) NULL,
    status ENUM('active','finished','cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE raffle_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raffle_id INT NOT NULL,
    ticket_number INT NOT NULL,
    customer_name VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    payment_status ENUM('available','reserved','paid','winner')
        DEFAULT 'available',
    payment_reference VARCHAR(255) NULL,
    reserved_at DATETIME NULL,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_raffle
        FOREIGN KEY (raffle_id)
        REFERENCES raffles(id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_ticket (raffle_id, ticket_number)
);

CREATE TABLE raffle_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raffle_ticket_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    reference VARCHAR(255),
    proof_image VARCHAR(255),
    status ENUM('pending','approved','rejected')
        DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_ticket
        FOREIGN KEY (raffle_ticket_id)
        REFERENCES raffle_tickets(id)
        ON DELETE CASCADE
);