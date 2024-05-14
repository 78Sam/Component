CREATE TABLE `UserAccounts` (
    `email` varchar(60),
    `password_hash` varchar(60)
);

INSERT INTO `UserAccounts` (`email`, `password_hash`) VALUES ('Sam@gmail.com', '$2y$10$pyx86yo5m.SS7V8r/ipXHOvGkDbyinZOtuAZ0NPgZCGnVPhL0EMv6');
INSERT INTO `UserAccounts` (`email`, `password_hash`) VALUES ('Rob@gmail.com', '$2y$10$pyx86yo5m.SS7V8r/ipXHOvGkDbyinZOtuAZ0NPgZCGnVPhL0EMv6');
