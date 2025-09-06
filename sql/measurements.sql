USE db;

CREATE TABLE measurements (
    id INT PRIMARY KEY,
    day_of_month INT NOT NULL,
    temperature_c DECIMAL(4,2),
    status VARCHAR(10) NOT NULL
);

INSERT INTO measurements (id, day_of_month, temperature_c, status) VALUES
(1, 1, 36.6, 'healthy'),
(2, 2, 36.8, 'healthy'),
(3, 3, 36.7, 'healthy'),
(4, 4, 37.8, 'sick'),
(5, 5, 38.2, 'sick'),
(6, 6, 37.6, 'sick'),
(7, 7, NULL, 'n/a'),
(8, 8, NULL, 'n/a'),
(9, 9, NULL, 'n/a'),
(10, 10, 36.9, 'healthy'),
(11, 11, 36.7, 'healthy'),
(12, 12, 36.5, 'healthy'),
(13, 13, 37.4, 'sick'),
(14, 14, 38.1, 'sick'),
(15, 15, 37.7, 'sick'),
(16, 16, 37.9, 'sick'),
(17, 17, 36.8, 'healthy'),
(18, 18, 36.6, 'healthy'),
(19, 19, NULL, 'n/a'),
(20, 20, NULL, 'n/a'),
(21, 21, 36.9, 'healthy'),
(22, 22, 36.7, 'healthy'),
(23, 23, 36.8, 'healthy'),
(24, 24, 37.6, 'sick'),
(25, 25, 38.3, 'sick'),
(26, 26, 37.9, 'sick'),
(27, 27, 36.6, 'healthy'),
(28, 28, 36.7, 'healthy'),
(29, 29, 36.5, 'healthy'),
(30, 30, NULL, 'n/a'),
(31, 31, NULL, 'n/a');