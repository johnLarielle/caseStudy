<template>
  <ion-app>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      
      <ion-tab-bar slot="bottom" class="custom-tab-bar">
        <ion-tab-button tab="home" href="/home" class="custom-tab-button">
          <div class="tab-content">
            <div class="icon-wrapper">
              <ion-icon :icon="home"></ion-icon>
            </div>
            <ion-label>Home</ion-label>
          </div>
        </ion-tab-button>

        <ion-tab-button tab="books" href="/books" class="custom-tab-button">
          <div class="tab-content">
            <div class="icon-wrapper">
              <ion-icon :icon="library"></ion-icon>
            </div>
            <ion-label>Books</ion-label>
          </div>
        </ion-tab-button>

        <ion-tab-button tab="add" href="/books/add" class="custom-tab-button add-tab">
          <div class="tab-content">
            <div class="icon-wrapper special">
              <ion-icon :icon="addCircle"></ion-icon>
            </div>
            <ion-label>Add</ion-label>
          </div>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-app>
</template>

<script setup lang="ts">
import { 
  IonApp, 
  IonRouterOutlet, 
  IonTabs, 
  IonTabBar, 
  IonTabButton, 
  IonIcon, 
  IonLabel 
} from '@ionic/vue';
import { home, library, addCircle } from 'ionicons/icons';
</script>

<style>
ion-app {
  --ion-background-color: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.custom-tab-bar {
  --background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(30px);
  border-top: 2px solid rgba(255, 255, 255, 0.2);
  padding: 8px 0;
  box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
  height: 70px;
}

.custom-tab-button {
  --color: rgba(255, 255, 255, 0.6);
  --color-selected: white;
  position: relative;
}

.tab-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  position: relative;
  z-index: 2;
}

.icon-wrapper {
  width: 45px;
  height: 45px;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.1);
}

.custom-tab-button.tab-selected .icon-wrapper {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
  transform: translateY(-5px);
}

.icon-wrapper.special {
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
}

.custom-tab-button ion-icon {
  font-size: 26px;
}

.custom-tab-button ion-label {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.custom-tab-button::before {
  content: '';
  position: absolute;
  top: -3px;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 4px;
  background: linear-gradient(90deg, #f093fb, #f5576c);
  border-radius: 0 0 4px 4px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.custom-tab-button.tab-selected::before {
  opacity: 1;
}

.custom-tab-button:active .icon-wrapper {
  transform: scale(0.95);
}

/* Add ripple effect */
.custom-tab-button::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  transform: scale(0);
  transition: transform 0.4s ease;
}

.custom-tab-button:active::after {
  transform: scale(2);
  opacity: 0;
}
</style>
