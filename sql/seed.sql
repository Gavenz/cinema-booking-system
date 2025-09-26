-- Updated @ 26.09.2025
INSERT INTO `movies` (`id`, `title`, `description`, `runtime_min`, `rating`, `poster_url`, `created_at`) VALUES
(1, 'F1: The Movie', 'A Formula One driver comes out of retirement to mentor and team up with a younger driver.', 155, 'PG-13', 'assets/images/f1movie.jpg', '2025-09-26 04:09:33'),
(2, 'The Conjuring: Last Rites', 'Paranormal investigators Ed and Lorraine Warren take on one last terrifying case involving mysterious entities they must confront.', 135, 'NC-16', 'assets/images/theconjuring.jpg', '2025-09-26 04:12:59');

INSERT INTO `showtimes` (`id`, `movie_id`, `starts_at`) VALUES
(1, 2, '2025-10-06 19:30:00'),
(2, 2, '2025-10-06 22:00:00'),
(3, 1, '2025-10-07 11:00:00'),
(4, 1, '2025-10-07 13:00:00');