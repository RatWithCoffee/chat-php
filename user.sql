  -- Удаление таблиц, если они существуют
  DROP TABLE IF EXISTS city;
  DROP TABLE IF EXISTS users;
  DROP TABLE IF EXISTS message;

  -- Создание таблицы city
  CREATE TABLE city
  (
      id   INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      lat  VARCHAR(255) NOT NULL,
      lng  VARCHAR(255) NOT NULL
  );

  -- Вставка данных в таблицу city
  INSERT INTO city (name, lat, lng)
  VALUES ('Санкт-Петербург', '59.938676', '30.314494'),
        ('Волгоград', '48.707067', '44.516975'),
        ('Екатеринбург', '56.838011', '60.597474'),
        ('Смоленск', '54.782495', '32.048054');

  -- Создание таблицы users
  CREATE TABLE users
  (
      id           INT AUTO_INCREMENT PRIMARY KEY,
      surname      VARCHAR(255) NOT NULL,
      name         VARCHAR(255) NOT NULL,
      patronymic   VARCHAR(255),
      email        VARCHAR(255) UNIQUE NOT NULL,
      birthday     DATE,
      login        VARCHAR(255) NOT NULL,
      registration TIMESTAMP NOT NULL,
      password     BLOB NOT NULL,
      avatar       VARCHAR(255) NOT NULL,
      city_id      INT,
      FOREIGN KEY (city_id) REFERENCES city (id)
  );


  CREATE TABLE message
    (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        text         VARCHAR(255) NOT NULL,
        from_user    INT,
        to_user      INT,
        sending_time TIMESTAMP NOT NULL,
        `read`       TINYINT NOT NULL DEFAULT 0,
        img_name     VARCHAR(255) NOT NULL,
        FOREIGN KEY (from_user) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (to_user) REFERENCES users (id) ON DELETE CASCADE
    );


