<template>
    <div class="profile">
        <h1>Profile</h1>
        <div class="profile-info">
            <div class="info-item">
                <label>Username:</label>
                <span>{{ user.username }}</span>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <span>{{ user.email }}</span>
            </div>
        </div>
        <button @click="editProfile">Edit Profile</button>

        <transition name="fade">
            <div v-if="isEditing" class="edit-form">
                <h2>Edit Profile</h2>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input v-model="user.username" type="text" id="username" />
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input v-model="user.email" type="email" id="email" />
                </div>
                <button @click="saveProfile">Save</button>
                <button @click="cancelEdit">Cancel</button>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';

// Заготовленные данные пользователя (можно заменить на реальные данные, полученные с сервера)
const user = ref({
    username: '',
    email: ''
});

// Флаг режима редактирования профиля
const isEditing = ref(false);

// Открыть форму редактирования
const editProfile = () => {
    isEditing.value = true;
};

// Отмена редактирования
const cancelEdit = () => {
    isEditing.value = false;
    // Здесь можно добавить восстановление исходных данных, если это необходимо
};

// Сохранение изменений профиля
const saveProfile = async () => {
    // Реализуйте логику сохранения профиля. Например, можно использовать axios:
    //
    // try {
    //   const response = await axios.post('/api/profile', user.value);
    //   console.log('Profile saved:', response.data);
    // } catch(error) {
    //   console.error('Error saving profile', error);
    // }
    console.log('Profile saved:', user.value);
    isEditing.value = false;
};
</script>

<style scoped>
.profile {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    text-align: center;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-family: sans-serif;
}

.profile h1 {
    font-size: 2rem;
    margin-bottom: 20px;
}

.profile-info {
    text-align: left;
    margin-bottom: 20px;
}

.info-item {
    margin-bottom: 10px;
}

.info-item label {
    font-weight: bold;
    margin-right: 10px;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    padding: 10px 15px;
    margin: 5px;
    border: none;
    background-color: #007BFF;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

/* Плавный переход для формы редактирования */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>

