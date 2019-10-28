-- tabelka do tymczasowego logowania wyjątków

CREATE TABLE log (
    log_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    log_message TEXT,
    log_file VARCHAR(255),
    log_line INT,
    log_path VARCHAR(255),
    log_stacktrace TEXT,
    created DATETIME NOT NULL DEFAULT NOW()
);

-- tabelka przechowująca ścieżki do plików (żeby w przyszłości móc skorzystać z NFS)

CREATE TABLE file (
    file_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    file_path VARCHAR(255) NOT NULL DEFAULT '',
    file_type VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW()
);

CREATE INDEX file_file_type ON file(file_type);

-- dodajemy obrazki do pracowników

ALTER TABLE employee ADD COLUMN avatar_id BIGINT DEFAULT NULL;
ALTER TABLE employee ADD FOREIGN KEY (avatar_id) REFERENCES file(file_id);

INSERT INTO file (file_path, file_type) VALUES ('/public/storage/avatars/1.jpg', 'jpg');
UPDATE `employee` SET avatar_id = 1 WHERE employee_id = '1234567891234';


