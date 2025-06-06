<template>
    <div class="profile">
        <h1>Profile</h1>

        <div class="avatar">
            <img :src="user.profile_photo_url" alt="Avatar" />
        </div>

        <div class="profile-info">
            <div class="info-item">
                <label>Username:</label>
                <span>{{ user.username }}</span>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <span>{{ user.email }}</span>
            </div>
            <div class="info-item">
                <label>Description:</label>
                <span>{{ user.bio || 'Not added' }}</span>
            </div>
            <div class="info-item">
                <label>Total Posts:</label>
                <span>{{ postCount }}</span>
            </div>
            <div class="info-item">
                <label>Total Likes:</label>
                <span>{{ totalLikes }}</span>
            </div>
        </div>

        <button @click="editProfile">Edit Profile</button>
        <button @click="goHome">Go Home</button>
        <button @click="logout">Log Out</button>

        <transition name="fade">
            <div v-if="isEditing" class="edit-form">
                <h2>Edit Profile</h2>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input v-model="form.username" type="text" id="username" />
                    <p v-if="usernameError" class="error-text">{{ usernameError }}</p>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input v-model="form.email" type="email" id="email" />
                    <p v-if="emailError" class="error-text">{{ emailError }}</p>
                </div>

                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea v-model="form.bio" id="description" rows="4"></textarea>
                </div>

                <div class="input-group">
                    <label for="photo">Profile Photo</label>
                    <input @change="onFileChange" type="file" id="photo" accept="image/*" />
                </div>
                <div v-if="preview" class="input-group">
                    <label>Preview</label>
                    <img :src="preview" class="preview-img" />
                </div>

                <button @click="saveProfile" :disabled="!formIsValid">Save</button>
                <button @click="cancelEdit">Cancel</button>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="visible" class="error-notification">
                {{ message }}
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

// Regex rules
const usernameRegex = /^[A-Za-z0-9_]{7,}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// State
const user = ref({ id: null, username: '', email: '', bio: '', profile_photo_url: '' });
const form = ref({ username: '', email: '', bio: '' });
const isEditing = ref(false);
const file = ref(null);
const preview = ref(null);
const message = ref('');
const visible = ref(false);
const postCount = ref(0);
const totalLikes = ref(0);

// Validation
const usernameError = computed(() => {
    if (!isEditing.value) return '';
    if (!form.value.username) return 'Username is required';
    if (!usernameRegex.test(form.value.username)) return 'Username at least 7 characters';
    return '';
});
const emailError = computed(() => {
    if (!isEditing.value) return '';
    if (!form.value.email) return 'Email is required';
    if (!emailRegex.test(form.value.email)) return 'Invalid email format';
    return '';
});
const formIsValid = computed(() => {
    return !usernameError.value && !emailError.value;
});

function onFileChange(e) {
    const [f] = e.target.files;
    if (!f) return;
    file.value = f;
    preview.value = URL.createObjectURL(f);
}

async function fetchUser() {
    try {
        const { data } = await axios.get('/user');
        user.value = {
            id: data.id,
            username: data.username,
            email: data.email,
            bio: data.bio || '',
            profile_photo_url: data.profile_photo ? `/storage/${data.profile_photo}` : ''
        };
        form.value.username = data.username;
        form.value.email = data.email;
        form.value.bio = data.bio || '';
    } catch {
        router.visit('/login');
    }
}

async function fetchStats() {
    if (!user.value.id) return;

    try {
        const { data: posts } = await axios.get('/posts', {
            params: { user_id: user.value.id }
        });

        postCount.value = Array.isArray(posts) ? posts.length : 0;

        let likesSum = 0;
        for (const post of posts) {
            if (post.reactionCounts && typeof post.reactionCounts.like === 'number') {
                likesSum += post.reactionCounts.like;
            } else {
                const { data: reactions } = await axios.get(`/posts/${post.id}/reactions`);
                likesSum += reactions.like || 0;
            }
        }
        totalLikes.value = likesSum;
    } catch (e) {
        console.error('Error fetching stats:', e);
    }
}

function editProfile() {
    isEditing.value = true;
    preview.value = null;
    file.value = null;
}

function cancelEdit() {
    isEditing.value = false;
    preview.value = null;
    file.value = null;
}

function showMessage(msg) {
    message.value = msg;
    visible.value = true;
    setTimeout(() => (visible.value = false), 3000);
}

async function saveProfile() {
    if (!formIsValid.value) {
        showMessage('Provided data format is invalid');
        return;
    }
    try {
        const formData = new FormData();
        formData.append('username', form.value.username);
        formData.append('email', form.value.email);
        formData.append('bio', form.value.bio);
        if (file.value) formData.append('profile_photo', file.value);

        const { data } = await axios.post('/update-profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        user.value.username = data.user.username;
        user.value.email = data.user.email;
        user.value.bio = data.user.bio;
        user.value.profile_photo_url = data.user.profile_photo_url;

        isEditing.value = false;
        preview.value = null;
        file.value = null;
        // refresh stats after save
        await fetchStats();
    } catch (e) {
        console.error('Error saving profile:', e);
    }
}

const logout = async () => {
    await axios.post('/logout');
    router.visit('/login');
};
const goHome = () => router.visit('/');

onMounted(async () => {
    await fetchUser();
    await fetchStats();
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

.avatar {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.avatar img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    background-color: #fff;
}

label {
    font-family: "Perfectly Vintages";
    font-size: 25px;
}

.profile-info {
    font-size: 25px;
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

.input-group input,
.input-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.preview-img {
    max-width: 100px;
    max-height: 100px;
    border-radius: 4px;
    display: block;
    margin-top: 5px;
}

button {
    width: 30%;
    margin: 20px 10px 0 0;
    padding: 12px;
    border: none;
    background-color: black;
    color: white;
    border-radius: 5px;
    margin-top: 20px;
    cursor: pointer;
    font-family: 'Perfectly Vintages';
    font-size: 20px;
}

button:hover {
    background-color: rgb(244, 153, 153);
    border-color: rgb(244, 153, 153);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
