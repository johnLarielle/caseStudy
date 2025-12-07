# eLibrary Backend Setup

## Quick Setup Instructions

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Go to SQL tab
   - Copy and paste the content from `database.sql` file
   - Click "Go" to execute

3. **Test API**
   - Open browser: http://localhost/library/api.php?action=list
   - You should see JSON response with sample books

## API Endpoints

Base URL: `http://localhost/library/api.php`

- **GET** `?action=list` - Get all books
- **GET** `?action=get&id=1` - Get single book
- **POST** `?action=create` - Create new book
- **POST** `?action=update&id=1` - Update book
- **DELETE** `?action=delete&id=1` - Delete book

## Book Fields

- title (required)
- author (required)
- isbn (optional)
- published_year (optional)
- genre (optional)
- description (optional)
- cover_image (optional - URL)

