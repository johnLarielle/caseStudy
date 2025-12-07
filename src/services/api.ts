// API service for eLibrary
const API_BASE_URL = 'http://localhost/library/api.php';

export interface Book {
  id?: number;
  title: string;
  author: string;
  isbn?: string;
  published_year?: number;
  genre?: string;
  description?: string;
  cover_image?: string;
  created_at?: string;
  updated_at?: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
}

// Get all books
export async function getAllBooks(): Promise<Book[]> {
  try {
    const response = await fetch(`${API_BASE_URL}?action=list`);
    const result: ApiResponse<Book[]> = await response.json();
    
    if (result.success && result.data) {
      return result.data;
    }
    throw new Error(result.error || 'Failed to fetch books');
  } catch (error) {
    console.error('Error fetching books:', error);
    throw error;
  }
}

// Get single book
export async function getBook(id: number): Promise<Book> {
  try {
    const response = await fetch(`${API_BASE_URL}?action=get&id=${id}`);
    const result: ApiResponse<Book> = await response.json();
    
    if (result.success && result.data) {
      return result.data;
    }
    throw new Error(result.error || 'Failed to fetch book');
  } catch (error) {
    console.error('Error fetching book:', error);
    throw error;
  }
}

// Create new book
export async function createBook(book: Book): Promise<number> {
  try {
    const response = await fetch(`${API_BASE_URL}?action=create`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(book),
    });
    
    const result: ApiResponse<any> = await response.json();
    
    if (result.success) {
      return result.data?.id || 0;
    }
    throw new Error(result.error || 'Failed to create book');
  } catch (error) {
    console.error('Error creating book:', error);
    throw error;
  }
}

// Update book
export async function updateBook(id: number, book: Partial<Book>): Promise<void> {
  try {
    const response = await fetch(`${API_BASE_URL}?action=update&id=${id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(book),
    });
    
    const result: ApiResponse<any> = await response.json();
    
    if (!result.success) {
      throw new Error(result.error || 'Failed to update book');
    }
  } catch (error) {
    console.error('Error updating book:', error);
    throw error;
  }
}

// Delete book
export async function deleteBook(id: number): Promise<void> {
  try {
    const response = await fetch(`${API_BASE_URL}?action=delete&id=${id}`, {
      method: 'DELETE',
    });
    
    const result: ApiResponse<any> = await response.json();
    
    if (!result.success) {
      throw new Error(result.error || 'Failed to delete book');
    }
  } catch (error) {
    console.error('Error deleting book:', error);
    throw error;
  }
}

