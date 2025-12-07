<template>
  <ion-page>
    <ion-header class="ion-no-border" :translucent="true">
      <ion-toolbar class="gradient-toolbar">
        <ion-title class="custom-title">
          <div class="title-content">
            <ion-icon :icon="sparkles"></ion-icon>
            eLibrary
          </div>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="custom-content">
      <div class="animated-background"></div>
      
      <div class="welcome-container">
        <div class="hero-section">
          <h1 class="gradient-text animate-fade-in">eLibrary</h1>
          <p class="subtitle animate-fade-in-delay">Manage Your Book Collection</p>
          <div class="decorative-line"></div>
        </div>

        <div class="stats-wrapper">
          <div class="stat-card-horizontal gradient-card-1 animate-slide-up">
            <div class="card-shine"></div>
            <div class="stat-left">
              <div class="icon-wrapper purple-glow">
                <ion-icon :icon="book" class="stat-icon"></ion-icon>
              </div>
            </div>
            <div class="stat-right">
              <h2 class="count-number">{{ totalBooks }}</h2>
              <p class="stat-label">Books in Library</p>
            </div>
          </div>

          <div class="stat-card-horizontal gradient-card-2 animate-slide-up-delay">
            <div class="card-shine"></div>
            <div class="stat-left">
              <div class="icon-wrapper blue-glow">
                <ion-icon :icon="people" class="stat-icon"></ion-icon>
              </div>
            </div>
            <div class="stat-right">
              <h2 class="count-number">{{ totalAuthors }}</h2>
              <p class="stat-label">Unique Authors</p>
            </div>
          </div>
        </div>

        <div class="actions-grid">
          <div class="action-card" @click="goToBooks">
            <div class="action-icon-wrapper gradient-1">
              <ion-icon :icon="grid"></ion-icon>
            </div>
            <h3>Browse Books</h3>
            <p>Explore your collection</p>
          </div>

          <div class="action-card" @click="goToAddBook">
            <div class="action-icon-wrapper gradient-2">
              <ion-icon :icon="addCircle"></ion-icon>
            </div>
            <h3>Add Book</h3>
            <p>Expand your library</p>
          </div>
        </div>

        <div class="quick-info">
          <div class="info-text">
            <ion-icon :icon="informationCircle"></ion-icon>
            <span>Tap on any book to view, edit, or delete</span>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonContent,
  IonHeader,
  IonPage,
  IonTitle,
  IonToolbar,
  IonIcon,
} from '@ionic/vue';
import {
  book,
  people,
  grid,
  addCircle,
  sparkles,
  informationCircle,
} from 'ionicons/icons';
import { getAllBooks } from '../services/api';

const router = useRouter();
const totalBooks = ref(0);
const totalAuthors = ref(0);

onMounted(async () => {
  try {
    const books = await getAllBooks();
    totalBooks.value = books.length;
    
    const authors = new Set(books.map(book => book.author));
    totalAuthors.value = authors.size;
  } catch (error) {
    console.error('Error loading stats:', error);
  }
});

const goToBooks = () => {
  router.push('/books');
};

const goToAddBook = () => {
  router.push('/books/add');
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
  font-size: 24px;
  font-weight: 900;
  letter-spacing: -0.5px;
}

.title-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: white;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.title-content ion-icon {
  font-size: 28px;
  animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {
  0%, 100% { transform: rotate(0deg) scale(1); opacity: 1; }
  50% { transform: rotate(180deg) scale(1.2); opacity: 0.8; }
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
  background: 
    linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  z-index: -1;
  animation: gradientShift 10s ease infinite;
}

@keyframes gradientShift {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.9; }
}

.welcome-container {
  padding: 20px;
  max-width: 650px;
  margin: 0 auto;
  padding-bottom: 100px;
}

.hero-section {
  text-align: center;
  padding: 60px 20px 40px;
  position: relative;
}

.gradient-text {
  font-size: 56px;
  font-weight: 900;
  margin: 0 0 15px 0;
  background: linear-gradient(45deg, #fff, #ffd1ff, #fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -2px;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  animation: fadeIn 0.8s ease-out;
}

.subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
  animation: fadeIn 0.8s ease-out 0.2s backwards;
}

.decorative-line {
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  margin: 30px auto 0;
  border-radius: 2px;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.stats-wrapper {
  margin: 50px 0;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.stat-card-horizontal {
  position: relative;
  border-radius: 20px;
  padding: 25px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(15px);
  border: 2px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card-horizontal:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
}

.gradient-card-1 {
  background: linear-gradient(120deg, rgba(167, 112, 239, 0.85), rgba(207, 97, 183, 0.85));
}

.gradient-card-2 {
  background: linear-gradient(120deg, rgba(72, 149, 239, 0.85), rgba(111, 207, 238, 0.85));
}

.card-shine {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  animation: shine 3s ease-in-out infinite;
}

@keyframes shine {
  0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
  100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.stat-left {
  flex-shrink: 0;
}

.stat-right {
  flex: 1;
}

.icon-wrapper {
  width: 65px;
  height: 65px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(10px);
  position: relative;
}

.purple-glow::after,
.blue-glow::after {
  content: '';
  position: absolute;
  inset: -10px;
  border-radius: 25px;
  opacity: 0.6;
  z-index: -1;
  filter: blur(20px);
}

.purple-glow::after {
  background: rgba(167, 112, 239, 0.8);
}

.blue-glow::after {
  background: rgba(72, 149, 239, 0.8);
}

.stat-icon {
  font-size: 32px;
  color: white;
}

.count-number {
  font-size: 42px;
  font-weight: 900;
  color: white;
  margin: 0 0 5px 0;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  letter-spacing: -2px;
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.95);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0;
}

.animate-slide-up {
  animation: slideUp 0.6s ease-out;
}

.animate-slide-up-delay {
  animation: slideUp 0.6s ease-out 0.15s backwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.actions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin: 40px 0;
}

.action-card {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(20px);
  border-radius: 22px;
  padding: 30px 20px;
  text-align: center;
  border: 2px solid rgba(255, 255, 255, 0.25);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.action-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.action-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
  border-color: rgba(255, 255, 255, 0.4);
}

.action-card:hover::before {
  opacity: 1;
}

.action-card:active {
  transform: translateY(-5px) scale(0.99);
}

.action-icon-wrapper {
  width: 70px;
  height: 70px;
  margin: 0 auto 20px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.action-icon-wrapper ion-icon {
  font-size: 36px;
  color: white;
  z-index: 2;
  position: relative;
}

.gradient-1 {
  background: linear-gradient(135deg, #f093fb, #f5576c);
}

.gradient-2 {
  background: linear-gradient(135deg, #4facfe, #00f2fe);
}

.action-card h3 {
  font-size: 18px;
  font-weight: 800;
  color: white;
  margin: 0 0 10px 0;
  letter-spacing: 0.3px;
}

.action-card p {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
  font-weight: 500;
}

.quick-info {
  margin: 50px 0 30px;
  padding: 0 10px;
}

.info-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 18px 25px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(15px);
  border-radius: 18px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.9);
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  line-height: 1.5;
}

.info-text ion-icon {
  font-size: 22px;
  flex-shrink: 0;
  color: rgba(255, 255, 255, 0.9);
}

@media (max-width: 576px) {
  .gradient-text {
    font-size: 44px;
  }
  
  .subtitle {
    font-size: 14px;
  }
  
  .stats-wrapper {
    gap: 12px;
  }
  
  .stat-card-horizontal {
    padding: 20px;
    gap: 15px;
  }
  
  .icon-wrapper {
    width: 55px;
    height: 55px;
  }
  
  .stat-icon {
    font-size: 28px;
  }
  
  .count-number {
    font-size: 36px;
  }
  
  .stat-label {
    font-size: 12px;
  }
  
  .actions-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .action-card {
    padding: 28px 20px;
  }
  
  .info-text {
    font-size: 13px;
    padding: 15px 20px;
  }
}
</style>
