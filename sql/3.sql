INSERT INTO company (company_name) VALUES ('Uniwersytet Pedagogiczny w Krakowie');

INSERT INTO building (building_name, building_street, building_city, building_postcode)
VALUES ('Uniwersytet Pedagogiczny w Krakowie', 'Podchorążych 2', 'Kraków', '30-084');

INSERT INTO workplace (building_id, company_id, workplace_name)
VALUES (1, 1, 'Uniwersytet Pedagogiczny w Krakowie');

ALTER TABLE entrance ADD COLUMN photo_id BIGINT DEFAULT NULL;
ALTER TABLE entrance ADD FOREIGN KEY (photo_id) REFERENCES file(file_id);



