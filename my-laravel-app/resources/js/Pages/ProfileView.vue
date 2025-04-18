<template>
    <div class="profile">
        <h1>Profile</h1>

        <div class="profile-info">
            <div class="info-item">
                <label>Username:</label>
                <span v-if="!isEditing">{{ form.username }}</span>
                <input
                    v-else
                    v-model="form.username"
                    type="text"
                    class="input-group"
                />
            </div>

            <div class="info-item">
                <label>Email:</label>
                <span v-if="!isEditing">{{ form.email }}</span>
                <input
                    v-else
                    v-model="form.email"
                    type="email"
                    class="input-group"
                />
            </div>
        </div>

        <div v-if="!isEditing">
            <button @click="editProfile">Edit Profile</button>
        </div>

        <div v-else>
            <button @click="saveProfile" :disabled="form.processing">Save</button>
            <button @click="cancelEdit">Cancel</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object
});

const isEditing = ref(false);

const form = useForm({
    username: props.user.username,
    email: props.user.email
});

const editProfile = () => {
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.username = props.user.username;
    form.email = props.user.email;
};

const saveProfile = () => {
    form.post('/profile', {
        onSuccess: () => {
            isEditing.value = false;
        }
    });
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

