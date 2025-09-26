-- Created @ 26.09.2025
-- ===== Fresh schema for cinema =====
DROP DATABASE IF EXISTS cinema;
CREATE DATABASE cinema
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;
USE cinema;

-- Movies master
CREATE TABLE movies (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title        VARCHAR(200) NOT NULL,
  description  TEXT NULL,
  runtime_min  INT UNSIGNED NULL,
  rating       ENUM('G','PG','PG-13','NC-16','M18','R21') NULL,
  poster_url   VARCHAR(500) NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_movie_title (title)
) ENGINE=InnoDB;

-- Showtimes (a movie at a specific date/time)
CREATE TABLE showtimes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  movie_id   INT UNSIGNED NOT NULL,
  starts_at  DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_showtime_starts (starts_at),
  UNIQUE KEY uk_movie_slot (movie_id, starts_at),  -- prevents duplicate slot for same movie
  CONSTRAINT fk_showtimes_movies
    FOREIGN KEY (movie_id) REFERENCES movies(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Bookings (free-seating MVP)
CREATE TABLE bookings (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(255) NOT NULL,
  showtime_id  INT UNSIGNED NOT NULL,
  ticket_type  ENUM('Adult','Senior','Child') NOT NULL,  -- no empty ''
  qty          INT UNSIGNED NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bookings_email (email),
  KEY idx_bookings_created (created_at),
  CONSTRAINT chk_qty CHECK (qty BETWEEN 1 AND 10),
  CONSTRAINT fk_bookings_showtimes
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
