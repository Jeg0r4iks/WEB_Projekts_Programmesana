<template>
    <div class="login">
        <h2>{{ isLogin ? 'Sign In' : 'Create an account' }}</h2>
        <form @submit.prevent="handleSubmit">
            <div class="input-group">
                <label for="email">Email</label>
                <input v-model="email" type="email" id="email" required />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" id="password" required />
            </div>
            <div v-if="!isLogin" class="input-group">
                <label for="confirmPassword">Confirm Password</label>
                <input v-model="confirmPassword" type="password" id="confirmPassword" required />
            </div>
            <button type="submit">{{ isLogin ? 'Enter' : 'Registration' }}</button>
        </form>
        <p @click="toggleMode" class="toggle-text">
            {{ isLogin ? 'Create an account' : 'Sign In' }}
        </p>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const isLogin = ref(true);
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const toggleMode = () => {
    isLogin.value = !isLogin.value;
};

const handleSubmit = async () => {
    if (!isLogin.value && password.value !== confirmPassword.value) {
        alert('Passwords do not match');
        return;
    }

    const formData = {
        email: email.value,
        password: password.value,
        password_confirmation: confirmPassword.value, // если регистрация
    };

    try {
        const response = await axios.post('/login', formData);
        console.log('User registered', response.data);
        // Можешь добавить редирект или успех
    } catch (error) {
        if (error.response && error.response.data) {
            console.error('Error:', error.response.data); // Ошибка валидации
        } else {
            console.error('Error registering user:', error);
        }
    }
};
</script>

<style scoped>
.login {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    text-align: center;
    border-radius: 10px;
    box-shadow: rgb(244, 153, 153) 1px 1px 10px;
    background-color: whitesmoke;
}

h2 {
    font-family: 'Perfectly Vintages';
    font-size: 40px;
    font-weight: normal;
    margin-bottom: 30px; /* Отступ снизу */
}

.input-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    display: block;
    font-family: 'Perfectly Vintages';
    font-size: 20px;
    margin-bottom: 10px;
    margin-left: 15px; /* Небольшой отступ слева от текста */
}

input {
    width: calc(100% - 30px); /* Уменьшаем ширину ввода, чтобы оставить отступ */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-left: 15px; /* Отступ слева */
    font-size: 16px;
}

button {
    width: 100%;
    padding: 10px;
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
}

.toggle-text {
    color: black;
    cursor: pointer;
    font-family: 'Perfectly Vintages';
    margin-top: 20px;
    font-size: 20px;
}

.toggle-text:hover {
    text-decoration: underline;
}
</style>
