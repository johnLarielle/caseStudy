<?php
// Simple CRUD API for books - no classes, just functions
require_once 'config.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Parse the request
$path_parts = explode('/', trim(parse_url($request_uri, PHP_URL_PATH), '/'));
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Route the request
if ($method === 'GET' && $action === 'list') {
    getAllBooks($conn);
} elseif ($method === 'GET' && $action === 'get' && isset($_GET['id'])) {
    getBook($conn, $_GET['id']);
} elseif ($method === 'POST' && $action === 'create') {
    createBook($conn);
} elseif ($method === 'POST' && $action === 'update' && isset($_GET['id'])) {
    updateBook($conn, $_GET['id']);
} elseif ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
    deleteBook($conn, $_GET['id']);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid endpoint']);
}

// Close connection
mysqli_close($conn);

// Function to get all books
function getAllBooks($conn) {
    $sql = "SELECT * FROM books ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $books = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $books]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch books: ' . mysqli_error($conn)]);
    }
}

// Function to get a single book
function getBook($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM books WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'data' => $book]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
    }
}

// Function to create a new book
function createBook($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    if (empty($data['title']) || empty($data['author'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and Author are required']);
        return;
    }
    
    // Escape data
    $title = mysqli_real_escape_string($conn, $data['title']);
    $author = mysqli_real_escape_string($conn, $data['author']);
    $isbn = isset($data['isbn']) ? mysqli_real_escape_string($conn, $data['isbn']) : '';
    $published_year = isset($data['published_year']) ? intval($data['published_year']) : null;
    $genre = isset($data['genre']) ? mysqli_real_escape_string($conn, $data['genre']) : '';
    $description = isset($data['description']) ? mysqli_real_escape_string($conn, $data['description']) : '';
    $cover_image = isset($data['cover_image']) ? mysqli_real_escape_string($conn, $data['cover_image']) : '';
    
    $sql = "INSERT INTO books (title, author, isbn, published_year, genre, description, cover_image) 
            VALUES ('$title', '$author', '$isbn', " . ($published_year ? $published_year : "NULL") . ", '$genre', '$description', '$cover_image')";
    
    if (mysqli_query($conn, $sql)) {
        $new_id = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'message' => 'Book created successfully', 'id' => $new_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create book: ' . mysqli_error($conn)]);
    }
}

// Function to update a book
function updateBook($conn, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    $id = mysqli_real_escape_string($conn, $id);
    
    // Check if book exists
    $check_sql = "SELECT id FROM books WHERE id = '$id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
        return;
    }
    
    // Build update query
    $updates = [];
    
    if (isset($data['title'])) {
        $title = mysqli_real_escape_string($conn, $data['title']);
        $updates[] = "title = '$title'";
    }
    
    if (isset($data['author'])) {
        $author = mysqli_real_escape_string($conn, $data['author']);
        $updates[] = "author = '$author'";
    }
    
    if (isset($data['isbn'])) {
        $isbn = mysqli_real_escape_string($conn, $data['isbn']);
        $updates[] = "isbn = '$isbn'";
    }
    
    if (isset($data['published_year'])) {
        $published_year = intval($data['published_year']);
        $updates[] = "published_year = $published_year";
    }
    
    if (isset($data['genre'])) {
        $genre = mysqli_real_escape_string($conn, $data['genre']);
        $updates[] = "genre = '$genre'";
    }
    
    if (isset($data['description'])) {
        $description = mysqli_real_escape_string($conn, $data['description']);
        $updates[] = "description = '$description'";
    }
    
    if (isset($data['cover_image'])) {
        $cover_image = mysqli_real_escape_string($conn, $data['cover_image']);
        $updates[] = "cover_image = '$cover_image'";
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    
    $sql = "UPDATE books SET " . implode(', ', $updates) . " WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update book: ' . mysqli_error($conn)]);
    }
}

// Function to delete a book
function deleteBook($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    
    // Check if book exists
    $check_sql = "SELECT id FROM books WHERE id = '$id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
        return;
    }
    
    $sql = "DELETE FROM books WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete book: ' . mysqli_error($conn)]);
    }
}
?>

