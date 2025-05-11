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
        <button @click="goHome">Go to Home</button>
        <button @click="logout">Log Out</button>
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const logout = async () => {
    try {
        await axios.post('/logout');

        user.value = {
            username: '',
            email: ''
        };

        isEditing.value = false;

        router.visit('/login');
    } catch (error) {
        console.error('Error while logging out: ', error);
    }
};

const goHome = () => {
    router.visit('/');
};

const user = ref({
    username: '',
    email: ''
});

const isEditing = ref(false);

const editProfile = () => {
    isEditing.value = true;
};

 const cancelEdit = () => {
    isEditing.value = false;
};

const saveProfile = async () => {
    try {
        await axios.post('/update-profile', {
            username: user.value.username,
            email: user.value.email
        });
        console.log('Profile saved:', user.value);
        isEditing.value = false;
    } catch (error) {
        console.error('Error while loading profile', error);
    }
};

const fetchUser = async () => {
    try {
        const response = await axios.get('/user');
        user.value.username = response.data.username;
        user.value.email = response.data.email;
    } catch (error) {
        console.error("Error while loading user data", error);
        router.visit('/login');
    }
};

onMounted(() => {
    fetchUser();
});
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
    font-family: "Perfectly Vintages";
    font-size: 40px;
    margin-bottom: 20px;
}

.profile-info {
    font-size: 20px;
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

