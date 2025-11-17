CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pro_title VARCHAR(255),
    pro_add VARCHAR(255),
    bedroom_count INT,
    bath_room_count INT,
    resp_count INT,
    property_photos TEXT,
    short_desc TEXT,
    status TINYINT(1) DEFAULT 0,   -- 1 = active, 0 = inactive
    propname VARCHAR(255),
    ref_no VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
