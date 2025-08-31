CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80),
    email VARCHAR(120),
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  name VARCHAR(80),
  type ENUM('light','lock','fan','vacuum','camera') DEFAULT 'light',
  image VARCHAR(100) DEFAULT 'resimler/light.png',
  state TINYINT(1) DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(160) NOT NULL,
  note TEXT,
  category ENUM(
    'ev',
    'sağlık',
    'alışveriş',
    'iş',
    'eğitim',
    'finans',
    'diğer'
  ) DEFAULT 'diğer',
  due_at DATETIME NULL,
  status ENUM('pending','done') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
