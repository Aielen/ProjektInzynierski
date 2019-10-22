-- employee

CREATE TABLE employee (
    employee_id VARCHAR(255) NOT NULL PRIMARY KEY,
    employee_firstname VARCHAR(255) NOT NULL DEFAULT '',
    employee_lastname VARCHAR(255) NOT NULL DEFAULT '',
    employee_email VARCHAR(255) NOT NULL DEFAULT '',
    employee_phone VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
	modified DATETIME NOT NULL DEFAULT NOW()
);

CREATE TRIGGER trigger_employee_insert BEFORE INSERT ON employee
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_employee_update BEFORE UPDATE ON employee
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- employee_property

CREATE TABLE employee_property (
    property_label VARCHAR(255) NOT NULL PRIMARY KEY,
    property_description VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
	modified DATETIME NOT NULL DEFAULT NOW()
);

CREATE TRIGGER trigger_employee_property_insert BEFORE INSERT ON employee_property
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_employee_property_update BEFORE UPDATE ON employee_property
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- employee_properties

CREATE TABLE employee_properties (
    employee_id VARCHAR(255) NOT NULL,
    property_label VARCHAR(255) NOT NULL,
    property_value VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
	modified DATETIME NOT NULL DEFAULT NOW(),

	PRIMARY KEY (employee_id, property_label),
	FOREIGN KEY fk_employee_property_employee_id(employee_id) REFERENCES employee(employee_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY fk_employee_property_label(property_label) REFERENCES employee_property(property_label) ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TRIGGER trigger_employee_properties_insert BEFORE INSERT ON employee_properties
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_employee_properties_update BEFORE UPDATE ON employee_properties
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- building

CREATE TABLE building (
    building_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    building_name VARCHAR(255) NOT NULL DEFAULT '',
    building_street VARCHAR(255) NOT NULL DEFAULT '',
    building_city VARCHAR(255) NOT NULL DEFAULT '',
    building_postcode VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
    modified DATETIME NOT NULL DEFAULT NOW()
);

CREATE TRIGGER trigger_building_insert BEFORE INSERT ON building
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_building_update BEFORE UPDATE ON building
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- company

CREATE TABLE company (
    company_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
    modified DATETIME NOT NULL DEFAULT NOW()
);

CREATE TRIGGER trigger_company_insert BEFORE INSERT ON company
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_company_update BEFORE UPDATE ON company
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- workplace

CREATE TABLE workplace (
    workplace_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    building_id BIGINT NOT NULL,
    company_id BIGINT NOT NULL,
    workplace_name VARCHAR(255) NOT NULL DEFAULT '',
    created DATETIME NOT NULL DEFAULT NOW(),
    modified DATETIME NOT NULL DEFAULT NOW(),

    FOREIGN KEY fk_workplace_building_id(building_id) REFERENCES building(building_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY fk_company_company_id(company_id) REFERENCES company(company_id) ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TRIGGER trigger_workplace_insert BEFORE INSERT ON workplace
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_workplace_update BEFORE UPDATE ON workplace
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

-- entrance

CREATE TABLE entrance (
	entrance_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	employee_id VARCHAR(255) NOT NULL,
	building_id BIGINT NOT NULL,
	workplace_id BIGINT NOT NULL,
	entrance_type VARCHAR(255) NOT NULL,
	entrance_date DATETIME DEFAULT NOW(),
    created DATETIME NOT NULL DEFAULT NOW(),
    modified DATETIME NOT NULL DEFAULT NOW(),

    FOREIGN KEY fk_employee_employee_id(employee_id) REFERENCES employee(employee_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY fk_building_building_id(building_id) REFERENCES building(building_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    FOREIGN KEY fk_workspace_workspace_id(workplace_id) REFERENCES workplace(workplace_id) ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TRIGGER trigger_entrance_insert BEFORE INSERT ON entrance
    FOR EACH ROW BEGIN
    SET new.created := now();
    SET new.modified := now();
END;

CREATE TRIGGER trigger_entrance_update BEFORE UPDATE ON entrance
    FOR EACH ROW BEGIN
    SET new.modified := now();
END;

