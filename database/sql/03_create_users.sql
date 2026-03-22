USE advanced_server_side_cw1;

CREATE TABLE IF NOT EXISTS users (
    id int unsigned NOT NULL AUTO_INCREMENT,
    full_name varchar(100) NOT NULL,
    email varchar(255) NOT NULL,
    password_hash varchar(255) NOT NULL,
    role varchar(50) NOT NULL DEFAULT 'alumnus',
    is_verified tinyint(1) NOT NULL DEFAULT 0,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
