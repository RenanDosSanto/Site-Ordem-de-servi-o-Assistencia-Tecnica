CREATE DATABASE IF NOT EXISTS assistencia_tecnica
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE assistencia_tecnica;

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(25) NOT NULL,
    email VARCHAR(120) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ordens_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    aparelho VARCHAR(80) NOT NULL,
    marca VARCHAR(80) NULL,
    modelo VARCHAR(100) NULL,
    defeito TEXT NOT NULL,
    servico_realizado TEXT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('Aguardando','Em andamento','Concluído','Entregue') NOT NULL DEFAULT 'Aguardando',
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_os_cliente FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
