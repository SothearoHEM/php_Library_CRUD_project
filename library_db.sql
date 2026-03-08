CREATE TABLE members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    gender VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    is_delete TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publish_year INT,
    price DOUBLE(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_delete TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE borrows (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    borrow_date DATETIME NOT NULL,
    return_date DATETIME,
    status ENUM('Borrowed','Returned','Late') DEFAULT 'Borrowed',
    total_books INT NOT NULL,
    is_delete TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (member_id)
        REFERENCES members(member_id)
        ON UPDATE CASCADE
);

CREATE TABLE borrow_details (
    borrow_id INT NOT NULL,
    book_id INT NOT NULL,
    qty INT NOT NULL,
    unit_price DOUBLE(10,2) NOT NULL,
    amount DOUBLE(10,2) NOT NULL,
    is_delete TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (borrow_id, book_id),

    FOREIGN KEY (borrow_id)
        REFERENCES borrows(borrow_id)
        ON UPDATE CASCADE,

    FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON UPDATE CASCADE
);