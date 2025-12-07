<template>
  <ion-page>
    <ion-header class="ion-no-border" :translucent="true">
      <ion-toolbar class="gradient-toolbar">
        <ion-buttons slot="start">
          <ion-button class="glass-button">
            <ion-back-button default-href="/home" color="light"></ion-back-button>
          </ion-button>
        </ion-buttons>
        <ion-title class="custom-title">
          <div class="title-content">
            <ion-icon :icon="library"></ion-icon>
            My Collection
          </div>
        </ion-title>
        <ion-buttons slot="end">
          <button @click="goToAddBook" class="add-button">
            <ion-icon :icon="add"></ion-icon>
          </button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="custom-content">
      <div class="animated-background"></div>
      
      <ion-refresher slot="fixed" @ionRefresh="handleRefresh">
        <ion-refresher-content 
          pulling-icon="arrow-down"
          refreshing-spinner="circular">
        </ion-refresher-content>
      </ion-refresher>

      <!-- Loading State -->
      <div v-if="loading" class="loading-container">
        <div class="loader-wrapper">
          <div class="custom-loader"></div>
          <p class="loading-text">Loading your collection...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="books.length === 0" class="empty-state">
        <div class="empty-animation">
          <ion-icon :icon="library" class="empty-icon"></ion-icon>
          <div class="empty-glow"></div>
        </div>
        <h2 class="empty-title">No Books Yet</h2>
        <p class="empty-text">Start building your amazing library</p>
        <button @click="goToAddBook" class="premium-button primary-gradient">
          <div class="button-content">
            <ion-icon :icon="add"></ion-icon>
            <span>Add Your First Book</span>
          </div>
        </button>
      </div>

      <!-- Books List -->
      <div v-else class="books-container">
        <div class="search-section">
          <div class="search-wrapper">
            <ion-icon :icon="search" class="search-icon"></ion-icon>
            <input 
              v-model="searchQuery" 
              type="text"
              placeholder="Search by title, author, or genre..."
              class="custom-search"
              @input="handleSearch"
            />
            <div v-if="searchQuery" @click="searchQuery = ''" class="clear-search">
              <ion-icon :icon="closeCircle"></ion-icon>
            </div>
          </div>
          
          <div class="results-badge">
            <ion-icon :icon="bookmarkSharp"></ion-icon>
            <span>{{ filteredBooks.length }} book{{ filteredBooks.length !== 1 ? 's' : '' }}</span>
          </div>
        </div>

        <div class="books-grid">
          <div v-for="(book, index) in filteredBooks" :key="book.id" 
               class="book-card-modern"
               :style="{ animationDelay: `${index * 0.05}s` }">
            <div class="card-glow"></div>
            
            <div class="book-cover-wrapper">
              <div class="cover-container">
                <img 
                  v-if="book.cover_image" 
                  :src="book.cover_image" 
                  :alt="book.title"
                  @error="handleImageError"
                  class="book-cover-img"
                />
                <div v-else class="no-cover-modern">
                  <ion-icon :icon="bookSharp"></ion-icon>
                  <div class="cover-pattern"></div>
                </div>
              </div>
              <div class="cover-overlay">
                <button @click="viewBook(book)" class="quick-view-btn">
                  <ion-icon :icon="eye"></ion-icon>
                  <span>Quick View</span>
                </button>
              </div>
            </div>

            <div class="book-info-modern">
              <div class="book-meta">
                <span v-if="book.genre" class="genre-tag">{{ book.genre }}</span>
                <span v-if="book.published_year" class="year-tag">{{ book.published_year }}</span>
              </div>

              <h3 class="book-title-modern">{{ book.title }}</h3>
              
              <div class="author-section">
                <ion-icon :icon="person" class="author-icon"></ion-icon>
                <p class="author-name">{{ book.author }}</p>
              </div>

              <p v-if="book.description" class="book-desc-modern">
                {{ truncateText(book.description, 80) }}
              </p>

              <div class="action-buttons">
                <button @click="editBook(book.id!)" class="icon-btn edit-btn">
                  <ion-icon :icon="create"></ion-icon>
                </button>
                <button @click="confirmDelete(book)" class="icon-btn delete-btn">
                  <ion-icon :icon="trash"></ion-icon>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButton,
  IonButtons,
  IonBackButton,
  IonIcon,
  IonRefresher,
  IonRefresherContent,
  alertController,
  toastController,
} from '@ionic/vue';
import {
  add,
  library,
  bookSharp,
  person,
  create,
  trash,
  eye,
  search,
  closeCircle,
  bookmarkSharp,
} from 'ionicons/icons';
import { getAllBooks, deleteBook, type Book } from '../services/api';

const router = useRouter();
const books = ref<Book[]>([]);
const loading = ref(true);
const searchQuery = ref('');

onMounted(async () => {
  await loadBooks();
});

const loadBooks = async () => {
  try {
    loading.value = true;
    books.value = await getAllBooks();
  } catch (error) {
    const toast = await toastController.create({
      message: 'Failed to load books. Please check if the backend is running.',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    loading.value = false;
  }
};

const filteredBooks = computed(() => {
  if (!searchQuery.value) {
    return books.value;
  }
  
  const query = searchQuery.value.toLowerCase();
  return books.value.filter(book => 
    book.title.toLowerCase().includes(query) ||
    book.author.toLowerCase().includes(query) ||
    (book.genre && book.genre.toLowerCase().includes(query))
  );
});

const handleSearch = () => {
  // Search is handled by computed property
};

const handleRefresh = async (event: any) => {
  await loadBooks();
  event.target.complete();
};

const truncateText = (text: string, maxLength: number): string => {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

const handleImageError = (event: any) => {
  event.target.style.display = 'none';
};

const goToAddBook = () => {
  router.push('/books/add');
};

const editBook = (id: number) => {
  router.push(`/books/edit/${id}`);
};

const viewBook = async (book: Book) => {
  const alert = await alertController.create({
    header: book.title,
    subHeader: `by ${book.author}`,
    message: `
      <div class="book-details-modal">
        ${book.isbn ? `<p><strong>ISBN:</strong> ${book.isbn}</p>` : ''}
        ${book.published_year ? `<p><strong>Published:</strong> ${book.published_year}</p>` : ''}
        ${book.genre ? `<p><strong>Genre:</strong> ${book.genre}</p>` : ''}
        ${book.description ? `<p><strong>Description:</strong><br>${book.description}</p>` : ''}
      </div>
    `,
    buttons: [
      {
        text: 'Edit',
        handler: () => {
          editBook(book.id!);
        }
      },
      {
        text: 'Close',
        role: 'cancel'
      }
    ],
  });
  
  await alert.present();
};

const confirmDelete = async (book: Book) => {
  const alert = await alertController.create({
    header: 'Confirm Delete',
    message: `Are you sure you want to delete "${book.title}"? This action cannot be undone.`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel',
      },
      {
        text: 'Delete',
        role: 'destructive',
        handler: async () => {
          await handleDelete(book.id!);
        },
      },
    ],
  });
  
  await alert.present();
};

const handleDelete = async (id: number) => {
  try {
    await deleteBook(id);
    
    // Remove from local array
    books.value = books.value.filter(book => book.id !== id);
    
    const toast = await toastController.create({
      message: 'Book deleted successfully',
      duration: 2000,
      color: 'success',
      position: 'top',
    });
    await toast.present();
  } catch (error) {
    const toast = await toastController.create({
      message: 'Failed to delete book',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  }
};
</script>

<style scoped>
ion-page {
  --background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-toolbar {
  --background: transparent;
  --border-width: 0;
}

.custom-title {
  font-size: 20px;
  font-weight: 900;
}

.title-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: white;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.title-content ion-icon {
  font-size: 24px;
}

.glass-button {
  --background: rgba(255, 255, 255, 0.1);
  --border-radius: 12px;
}

.add-button {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f093fb, #f5576c);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
  transition: transform 0.3s ease;
}

.add-button:active {
  transform: scale(0.95);
}

.add-button ion-icon {
  font-size: 24px;
  color: white;
}

.custom-content {
  --background: transparent;
  position: relative;
}

.animated-background {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  z-index: -1;
}

.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 40px;
}

.loader-wrapper {
  text-align: center;
}

.custom-loader {
  width: 60px;
  height: 60px;
  margin: 0 auto 25px;
  border: 5px solid rgba(255, 255, 255, 0.2);
  border-top: 5px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: white;
  font-size: 18px;
  font-weight: 600;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 60px 40px;
  text-align: center;
}

.empty-animation {
  position: relative;
  margin-bottom: 30px;
}

.empty-icon {
  font-size: 120px;
  color: white;
  filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
  animation: float 3s ease-in-out infinite;
}

.empty-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  animation: pulse 2s ease-in-out infinite;
}

.empty-title {
  font-size: 32px;
  font-weight: 900;
  color: white;
  margin-bottom: 15px;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.empty-text {
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 35px;
  font-size: 18px;
  font-weight: 500;
}

.premium-button {
  position: relative;
  border: none;
  padding: 18px 40px;
  border-radius: 25px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  margin-top: 20px;
}

.premium-button:active {
  transform: scale(0.98);
}

.primary-gradient {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.button-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  color: white;
  font-weight: 700;
  font-size: 17px;
}

.button-content ion-icon {
  font-size: 24px;
}

.books-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 100px;
}

.search-section {
  margin-bottom: 25px;
}

.search-wrapper {
  position: relative;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  margin-bottom: 15px;
}

.search-icon {
  font-size: 24px;
  color: white;
  opacity: 0.8;
}

.custom-search {
  flex: 1;
  border: none;
  background: transparent;
  color: white;
  font-size: 16px;
  font-weight: 500;
  outline: none;
}

.custom-search::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.clear-search {
  cursor: pointer;
  display: flex;
  align-items: center;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.clear-search:hover {
  opacity: 1;
}

.clear-search ion-icon {
  font-size: 24px;
  color: white;
}

.results-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  color: white;
  font-weight: 600;
  font-size: 14px;
}

.results-badge ion-icon {
  font-size: 18px;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.book-card-modern {
  position: relative;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  overflow: hidden;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  animation: fadeInUp 0.5s ease-out backwards;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.book-card-modern:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
}

.card-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100%;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
  pointer-events: none;
}

.book-cover-wrapper {
  position: relative;
  width: 100%;
  height: 300px;
  overflow: hidden;
}

.cover-container {
  width: 100%;
  height: 100%;
}

.book-cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-cover-modern {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #a770ef, #cf57a3, #4facfe);
  position: relative;
  overflow: hidden;
}

.no-cover-modern ion-icon {
  font-size: 80px;
  color: white;
  opacity: 0.9;
  z-index: 2;
  position: relative;
}

.cover-pattern {
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(
    45deg,
    transparent,
    transparent 10px,
    rgba(255, 255, 255, 0.05) 10px,
    rgba(255, 255, 255, 0.05) 20px
  );
}

.cover-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.book-card-modern:hover .cover-overlay {
  opacity: 1;
}

.quick-view-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: white;
  border: none;
  border-radius: 20px;
  color: #667eea;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transform: translateY(10px);
  transition: transform 0.3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.book-card-modern:hover .quick-view-btn {
  transform: translateY(0);
}

.quick-view-btn ion-icon {
  font-size: 20px;
}

.book-info-modern {
  padding: 20px;
}

.book-meta {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.genre-tag,
.year-tag {
  font-size: 11px;
  padding: 6px 12px;
  border-radius: 15px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.book-title-modern {
  font-size: 20px;
  font-weight: 800;
  color: white;
  margin: 0 0 12px 0;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.author-section {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.author-icon {
  font-size: 18px;
  color: rgba(255, 255, 255, 0.8);
}

.author-name {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 600;
  margin: 0;
}

.book-desc-modern {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.5;
  margin: 0 0 16px 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.action-buttons {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.icon-btn {
  flex: 1;
  height: 45px;
  border: none;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-weight: 700;
  font-size: 15px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  backdrop-filter: blur(10px);
}

.icon-btn:active {
  transform: scale(0.95);
}

.edit-btn {
  background: rgba(255, 193, 7, 0.3);
  border: 2px solid rgba(255, 193, 7, 0.5);
  color: #ffc107;
  box-shadow: 0 5px 15px rgba(255, 193, 7, 0.2);
}

.delete-btn {
  background: rgba(244, 67, 54, 0.3);
  border: 2px solid rgba(244, 67, 54, 0.5);
  color: #f44336;
  box-shadow: 0 5px 15px rgba(244, 67, 54, 0.2);
}

.icon-btn ion-icon {
  font-size: 24px;
}

@media (max-width: 768px) {
  .books-grid {
    grid-template-columns: 1fr;
  }
  
  .book-cover-wrapper {
    height: 250px;
  }
}

@media (max-width: 576px) {
  .books-container {
    padding: 15px;
  }
  
  .search-wrapper {
    padding: 10px 15px;
  }
  
  .custom-search {
    font-size: 14px;
  }
}
</style>

