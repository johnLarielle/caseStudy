<template>
  <ion-page>
    <ion-header class="ion-no-border" :translucent="true">
      <ion-toolbar class="gradient-toolbar">
        <ion-buttons slot="start">
          <ion-button class="glass-button">
            <ion-back-button default-href="/books" color="light"></ion-back-button>
          </ion-button>
        </ion-buttons>
        <ion-title class="custom-title">
          <div class="title-content">
            <ion-icon :icon="create"></ion-icon>
            {{ isEditMode ? 'Edit Book' : 'New Book' }}
          </div>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true" class="custom-content">
      <div class="animated-background"></div>
      
      <div class="form-container">
        <div class="form-card">
          <div class="card-header">
            <div class="header-icon-wrapper">
              <ion-icon :icon="bookSharp" class="header-icon"></ion-icon>
            </div>
            <h2 class="form-title">Book Information</h2>
            <p class="form-subtitle">{{ isEditMode ? 'Update the details below' : 'Fill in the details to add a new book' }}</p>
          </div>
          
          <form @submit.prevent="handleSubmit" class="modern-form">
            <!-- Title -->
            <div class="form-group" :class="{ 'has-error': errors.title && formTouched.title }">
              <label class="form-label">
                <ion-icon :icon="text"></ion-icon>
                Title <span class="required">*</span>
              </label>
              <input
                v-model="formData.title"
                type="text"
                placeholder="Enter book title"
                class="form-input"
                @blur="formTouched.title = true"
                required
              />
              <div v-if="errors.title && formTouched.title" class="error-message">
                <ion-icon :icon="alertCircle"></ion-icon>
                {{ errors.title }}
              </div>
            </div>

            <!-- Author -->
            <div class="form-group" :class="{ 'has-error': errors.author && formTouched.author }">
              <label class="form-label">
                <ion-icon :icon="person"></ion-icon>
                Author <span class="required">*</span>
              </label>
              <input
                v-model="formData.author"
                type="text"
                placeholder="Enter author name"
                class="form-input"
                @blur="formTouched.author = true"
                required
              />
              <div v-if="errors.author && formTouched.author" class="error-message">
                <ion-icon :icon="alertCircle"></ion-icon>
                {{ errors.author }}
              </div>
            </div>

            <div class="form-row">
              <!-- ISBN -->
              <div class="form-group half">
                <label class="form-label">
                  <ion-icon :icon="barcode"></ion-icon>
                  ISBN
                </label>
                <input
                  v-model="formData.isbn"
                  type="text"
                  placeholder="ISBN number"
                  class="form-input"
                />
              </div>

              <!-- Published Year -->
              <div class="form-group half" :class="{ 'has-error': errors.published_year && formTouched.published_year }">
                <label class="form-label">
                  <ion-icon :icon="calendar"></ion-icon>
                  Year
                </label>
                <input
                  v-model.number="formData.published_year"
                  type="number"
                  placeholder="2024"
                  class="form-input"
                  @blur="formTouched.published_year = true"
                />
                <div v-if="errors.published_year && formTouched.published_year" class="error-message">
                  <ion-icon :icon="alertCircle"></ion-icon>
                  {{ errors.published_year }}
                </div>
              </div>
            </div>

            <!-- Genre -->
            <div class="form-group">
              <label class="form-label">
                <ion-icon :icon="pricetag"></ion-icon>
                Genre
              </label>
              <select v-model="formData.genre" class="form-select">
                <option value="">Select a genre...</option>
                <option value="Fiction">Fiction</option>
                <option value="Non-Fiction">Non-Fiction</option>
                <option value="Science Fiction">Science Fiction</option>
                <option value="Fantasy">Fantasy</option>
                <option value="Mystery">Mystery</option>
                <option value="Thriller">Thriller</option>
                <option value="Romance">Romance</option>
                <option value="Horror">Horror</option>
                <option value="Biography">Biography</option>
                <option value="History">History</option>
                <option value="Self-Help">Self-Help</option>
                <option value="Educational">Educational</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label class="form-label">
                <ion-icon :icon="document"></ion-icon>
                Description
              </label>
              <textarea
                v-model="formData.description"
                placeholder="Enter book description..."
                class="form-textarea"
                rows="4"
              ></textarea>
            </div>

            <!-- Cover Image URL -->
            <div class="form-group">
              <label class="form-label">
                <ion-icon :icon="image"></ion-icon>
                Cover Image URL
              </label>
              <input
                v-model="formData.cover_image"
                type="url"
                placeholder="https://example.com/cover.jpg"
                class="form-input"
              />
            </div>

            <!-- Image Preview -->
            <div v-if="formData.cover_image" class="image-preview">
              <div class="preview-card">
                <p class="preview-label">
                  <ion-icon :icon="eye"></ion-icon>
                  Preview
                </p>
                <div class="preview-image-wrapper">
                  <img 
                    :src="formData.cover_image" 
                    alt="Cover preview"
                    @error="handleImageError"
                  />
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
              <button
                type="submit"
                :disabled="submitting || !isFormValid"
                class="submit-button"
              >
                <div class="button-content">
                  <ion-icon :icon="checkmarkCircle"></ion-icon>
                  <span>{{ submitting ? 'Saving...' : (isEditMode ? 'Update Book' : 'Add Book') }}</span>
                </div>
                <div class="button-shine"></div>
              </button>

              <button
                type="button"
                @click="handleCancel"
                :disabled="submitting"
                class="cancel-button"
              >
                <ion-icon :icon="close"></ion-icon>
                <span>Cancel</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
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
  toastController,
  loadingController,
} from '@ionic/vue';
import {
  bookSharp,
  create,
  checkmarkCircle,
  close,
  text,
  person,
  barcode,
  calendar,
  pricetag,
  document,
  image,
  eye,
  alertCircle,
} from 'ionicons/icons';
import { getBook, createBook, updateBook, type Book } from '../services/api';

const router = useRouter();
const route = useRoute();

const isEditMode = computed(() => !!route.params.id);
const bookId = computed(() => Number(route.params.id));

const formData = reactive<Partial<Book>>({
  title: '',
  author: '',
  isbn: '',
  published_year: undefined,
  genre: '',
  description: '',
  cover_image: '',
});

const formTouched = reactive({
  title: false,
  author: false,
  published_year: false,
});

const errors = reactive({
  title: '',
  author: '',
  published_year: '',
});

const submitting = ref(false);

onMounted(async () => {
  if (isEditMode.value) {
    await loadBook();
  }
});

const loadBook = async () => {
  const loading = await loadingController.create({
    message: 'Loading book...',
  });
  await loading.present();

  try {
    const book = await getBook(bookId.value);
    Object.assign(formData, book);
  } catch (error) {
    const toast = await toastController.create({
      message: 'Failed to load book',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
    router.push('/books');
  } finally {
    await loading.dismiss();
  }
};

const validateForm = (): boolean => {
  let isValid = true;

  // Validate title
  if (!formData.title || formData.title.trim() === '') {
    errors.title = 'Title is required';
    isValid = false;
  } else if (formData.title.length < 2) {
    errors.title = 'Title must be at least 2 characters';
    isValid = false;
  } else {
    errors.title = '';
  }

  // Validate author
  if (!formData.author || formData.author.trim() === '') {
    errors.author = 'Author is required';
    isValid = false;
  } else if (formData.author.length < 2) {
    errors.author = 'Author name must be at least 2 characters';
    isValid = false;
  } else {
    errors.author = '';
  }

  // Validate published year
  if (formData.published_year) {
    const currentYear = new Date().getFullYear();
    if (formData.published_year < 1000 || formData.published_year > currentYear + 10) {
      errors.published_year = `Year must be between 1000 and ${currentYear + 10}`;
      isValid = false;
    } else {
      errors.published_year = '';
    }
  } else {
    errors.published_year = '';
  }

  return isValid;
};

const isFormValid = computed(() => {
  return formData.title && formData.title.trim().length >= 2 &&
         formData.author && formData.author.trim().length >= 2 &&
         (!formData.published_year || (formData.published_year >= 1000 && formData.published_year <= new Date().getFullYear() + 10));
});

const handleSubmit = async () => {
  // Mark all fields as touched
  formTouched.title = true;
  formTouched.author = true;
  formTouched.published_year = true;

  if (!validateForm()) {
    const toast = await toastController.create({
      message: 'Please fix the errors in the form',
      duration: 2000,
      color: 'warning',
      position: 'top',
    });
    await toast.present();
    return;
  }

  submitting.value = true;

  try {
    // Clean up empty strings
    const bookData: Partial<Book> = {
      title: formData.title,
      author: formData.author,
      isbn: formData.isbn || undefined,
      published_year: formData.published_year || undefined,
      genre: formData.genre || undefined,
      description: formData.description || undefined,
      cover_image: formData.cover_image || undefined,
    };

    if (isEditMode.value) {
      await updateBook(bookId.value, bookData);
      const toast = await toastController.create({
        message: 'Book updated successfully!',
        duration: 2000,
        color: 'success',
        position: 'top',
      });
      await toast.present();
    } else {
      await createBook(bookData as Book);
      const toast = await toastController.create({
        message: 'Book added successfully!',
        duration: 2000,
        color: 'success',
        position: 'top',
      });
      await toast.present();
    }

    router.push('/books');
  } catch (error) {
    const toast = await toastController.create({
      message: `Failed to ${isEditMode.value ? 'update' : 'add'} book`,
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    submitting.value = false;
  }
};

const handleCancel = () => {
  router.push('/books');
};

const handleImageError = (event: any) => {
  event.target.style.display = 'none';
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

.form-container {
  padding: 20px;
  max-width: 700px;
  margin: 0 auto;
  padding-bottom: 100px;
}

.form-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border-radius: 30px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.card-header {
  text-align: center;
  padding: 40px 30px 30px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
}

.header-icon-wrapper {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  border-radius: 25px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.header-icon {
  font-size: 44px;
  color: white;
}

.form-title {
  font-size: 28px;
  font-weight: 900;
  color: white;
  margin: 0 0 10px 0;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.form-subtitle {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
  font-weight: 500;
}

.modern-form {
  padding: 30px;
}

.form-group {
  margin-bottom: 25px;
}

.form-group.half {
  flex: 1;
  margin-bottom: 0;
}

.form-row {
  display: flex;
  gap: 15px;
  margin-bottom: 25px;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 700;
  color: white;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-label ion-icon {
  font-size: 18px;
}

.required {
  color: #ff6b9d;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 15px 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  color: white;
  font-size: 16px;
  font-weight: 500;
  outline: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  border-color: rgba(255, 255, 255, 0.6);
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  transform: translateY(-2px);
}

.form-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='white' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 18px center;
  padding-right: 45px;
}

.form-select option {
  background: #764ba2;
  color: white;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.form-group.has-error .form-input,
.form-group.has-error .form-select,
.form-group.has-error .form-textarea {
  border-color: #ff6b9d;
  animation: shake 0.3s ease;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.error-message {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 8px;
  padding: 10px 15px;
  background: rgba(255, 107, 157, 0.2);
  border-left: 3px solid #ff6b9d;
  border-radius: 8px;
  color: #ff6b9d;
  font-size: 13px;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.error-message ion-icon {
  font-size: 18px;
}

.image-preview {
  margin: 30px 0;
}

.preview-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 20px;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.preview-label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 700;
  color: white;
  margin-bottom: 15px;
  font-size: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.preview-label ion-icon {
  font-size: 20px;
}

.preview-image-wrapper {
  display: flex;
  justify-content: center;
}

.preview-image-wrapper img {
  max-width: 200px;
  max-height: 280px;
  border-radius: 15px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  border: 3px solid rgba(255, 255, 255, 0.3);
}

.button-group {
  margin-top: 35px;
  display: flex;
  gap: 15px;
}

.submit-button,
.cancel-button {
  flex: 1;
  height: 56px;
  border-radius: 18px;
  border: none;
  font-size: 17px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.submit-button {
  background: linear-gradient(135deg, #10dc60, #05a845);
  color: white;
  box-shadow: 0 10px 30px rgba(16, 220, 96, 0.3);
}

.submit-button:not(:disabled):hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(16, 220, 96, 0.4);
}

.submit-button:not(:disabled):active {
  transform: translateY(-1px);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.button-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.button-content ion-icon {
  font-size: 24px;
}

.button-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s ease;
}

.submit-button:hover .button-shine {
  left: 100%;
}

.cancel-button {
  background: rgba(244, 67, 54, 0.3);
  border: 2px solid rgba(244, 67, 54, 0.5);
  color: #f44336;
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.cancel-button:hover {
  background: rgba(244, 67, 54, 0.4);
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(244, 67, 54, 0.3);
}

.cancel-button ion-icon {
  font-size: 22px;
}

@media (max-width: 576px) {
  .form-container {
    padding: 15px;
  }
  
  .modern-form {
    padding: 25px 20px;
  }
  
  .card-header {
    padding: 30px 20px 20px;
  }
  
  .form-title {
    font-size: 24px;
  }
  
  .form-subtitle {
    font-size: 14px;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  
  .form-group.half {
    margin-bottom: 25px;
  }
  
  .button-group {
    flex-direction: column;
  }
}
</style>

