<template>
    <div class="login">
        <h2>{{ isLogin ? 'Log In' : 'Create an account' }}</h2>
        <form @submit.prevent="handleSubmit">
            <div v-if="!isLogin" class="input-group">
                <label for="username">Username</label>
                <input v-model="username" type="text" id="username" />
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input v-model="email" type="text" id="email" />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" id="password" />
            </div>
            <div v-if="!isLogin" class="input-group">
                <label for="confirmPassword">Confirm Password</label>
                <input v-model="confirmPassword" type="password" id="confirmPassword" />
            </div>
            <button type="submit">{{ isLogin ? 'Enter' : 'Registration' }}</button>
        </form>
        <p @click="toggleMode" class="toggle-text">
            {{ isLogin ? 'Create an account' : 'Log In' }}
        </p>
        <transition name="fade">
            <div v-if="visible" class="error-notification">
                {{ message }}
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordRegex = /^(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?])(?=.*\d).{8,}$/;
const isLogin = ref(true);
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const username = ref('');
const visible = ref(false);
const message = ref('');

const showMessage = (msg) => {
    message.value = msg;
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
    }, 3000);
};

const toggleMode = () => {
    isLogin.value = !isLogin.value;
};

const handleSubmit = async () => {
    if(!email.value || !password.value || (!isLogin.value && !confirmPassword.value)) {
        showMessage('Please fill the fields');
        return;
    }

    if(!emailRegex.test(email.value)) {
        showMessage('Email address is invalid');
        return;
    }

    if(!isLogin.value) {
        if(!passwordRegex.test(password.value)) {
            showMessage('Password should have 8 characters, special character and number');
            return;
        }
        if (password.value !== confirmPassword.value) {
            showMessage('Passwords do not match');
            return;
        }
    }

    const formData = {
        username: username.value,
        email: email.value,
        password: password.value,
        ...(isLogin.value ? {} : { password_confirmation: confirmPassword.value }),
    };

    try {
        const endpoint = isLogin.value ? '/login' : '/register';

        // Получаем CSRF cookie перед запросом
        await axios.get('/sanctum/csrf-cookie');

        // Отправляем запрос на регистрацию или логин
        const response = await axios.post(endpoint, formData);

        showMessage(isLogin.value ? 'Login successful' : 'Registration successful');

        // Перенаправление после успешной авторизации
        setTimeout(() => {
            window.location.href = '/profile';
        }, 1000);

        console.log('Success:', response.data);
    } catch (error) {
        if (error.response) {
            if (error.response.status === 401) {
                showMessage('Incorrect email or password');
            } else if (error.response.data?.message) {
                showMessage('An error occurred: ' + error.response.data.message);
            } else {
                showMessage('An error occurred');
            }
            console.error('Error:', error.response.data);
        } else {
            console.error('Unexpected error:', error);
            showMessage('Unexpected error occurred');
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
    margin-bottom: 30px;
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
    margin-left: 15px;
}

input {
    width: calc(100% - 30px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-left: 15px;
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

/* Стили для уведомления об ошибке/сообщения */
.error-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #f44336;
    color: white;
    padding: 15px 25px;
    border-radius: 4px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    font-family: 'Perfectly Vintages', sans-serif;
    font-size: 18px;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
