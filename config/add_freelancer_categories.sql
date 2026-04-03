-- Категории исполнителей (каталожный режим): связь пользователь ↔ категория
USE inwork;

CREATE TABLE IF NOT EXISTS freelancer_categories (
    user_id INT UNSIGNED NOT NULL,
    category VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, category),
    CONSTRAINT fk_fc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_fc_category (category)
) ENGINE=InnoDB;
