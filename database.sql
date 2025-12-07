-- eLibrary Database Setup
CREATE DATABASE IF NOT EXISTS elibrary;
USE elibrary;

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(50),
    published_year INT,
    genre VARCHAR(100),
    description TEXT,
    cover_image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO books (title, author, isbn, published_year, genre, description, cover_image) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 1925, 'Fiction', 'A classic novel about the American Dream in the 1920s.', 'https://covers.openlibrary.org/b/isbn/9780743273565-M.jpg'),
('To Kill a Mockingbird', 'Harper Lee', '978-0061120084', 1960, 'Fiction', 'A gripping tale of racial injustice and childhood innocence.', 'https://covers.openlibrary.org/b/isbn/9780061120084-M.jpg'),
('1984', 'George Orwell', '978-0451524935', 1949, 'Science Fiction', 'A dystopian social science fiction novel.', 'https://covers.openlibrary.org/b/isbn/9780451524935-M.jpg');

