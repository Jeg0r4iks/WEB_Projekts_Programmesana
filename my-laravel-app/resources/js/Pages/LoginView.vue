<template>
    <div class="login-wrapper">
        <div class="login">
            <h2>{{ isLogin ? 'Log In' : 'Create an account' }}</h2>
            <form @submit.prevent="handleSubmit">
                <div v-if="!isLogin" class="input-group">
                    <label for="username">Username</label>
                    <input v-model="username" type="text" id="username" />
                    <p v-if="usernameError" class="error-text">{{ usernameError }}</p>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input v-model="email" type="text" id="email" />
                    <p v-if="emailError" class="error-text">{{ emailError }}</p>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input v-model="password" type="password" id="password" />
                    <p v-if="passwordError" class="error-text">{{ passwordError }}</p>
                </div>

                <div v-if="!isLogin" class="input-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input v-model="confirmPassword" type="password" id="confirmPassword" />
                    <p v-if="confirmPasswordError" class="error-text">{{ confirmPasswordError }}</p>
                </div>

                <button type="submit" :disabled="!formIsValid">
                    {{ isLogin ? 'Enter' : 'Registration' }}
                </button>
            </form>

            <p @click="toggleMode" class="toggle-text">
                {{ isLogin ? 'Create an account' : 'Log In' }}
            </p>

            <transition name="fade">
                <div v-if="visible" class="error-notification">
                    {{ message }}
                </div>
            </transition>

            <button @click="goHome" class="home-button">Go to Home</button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

// Regex rules
const usernameRegex = /^[A-Za-z0-9_]{7,}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordRegex = /^(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?])(?=.*\d).{8,}$/;

// State
const isLogin = ref(true);
const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const visible = ref(false);
const message = ref('');

// Navigation
const goHome = () => {
    router.visit('/');
};

// Toggle login/register mode
const toggleMode = () => {
    isLogin.value = !isLogin.value;
    // Reset fields and messages
    username.value = '';
    email.value = '';
    password.value = '';
    confirmPassword.value = '';
};

// Real-time validation messages
const usernameError = computed(() => {
    if (isLogin.value) return '';
    if (!username.value) return 'Username is required';
    if (!usernameRegex.test(username.value)) return 'At least 7 characters, without special characters';
    return '';
});

const emailError = computed(() => {
    if (!email.value) return 'Email is required';
    if (!emailRegex.test(email.value)) return 'Invalid email format';
    return '';
});

const passwordError = computed(() => {
    if (!password.value) return 'Password is required';
    if (!passwordRegex.test(password.value)) return 'Minimum of 8 chars, including special character and number';
    return '';
});

const confirmPasswordError = computed(() => {
    if (isLogin.value) return '';
    if (confirmPassword.value !== password.value) return 'Passwords do not match';
    return '';
});

// Overall form validity
const formIsValid = computed(() => {
    const baseValid = !emailError.value && !passwordError.value;
    if (isLogin.value) {
        return baseValid;
    }
    return baseValid && !usernameError.value && !confirmPasswordError.value;
});

// Show error notification
const showMessage = (msg) => {
    message.value = msg;
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
    }, 3000);
};

// Form submission
const handleSubmit = async () => {
    if (!formIsValid.value) {
        showMessage('Please correct the errors above');
        return;
    }

    const formData = {
        ...(isLogin.value ? {} : { username: username.value }),
        email: email.value,
        password: password.value,
        ...(isLogin.value ? {} : { password_confirmation: confirmPassword.value }),
    };

    try {
        const endpoint = isLogin.value ? '/login' : '/register';
        await axios.get('/sanctum/csrf-cookie');
        await axios.post(endpoint, formData, { withCredentials: true });
        showMessage(isLogin.value ? 'Login successful' : 'Registration successful');
        setTimeout(() => window.location.href = '/profile', 1000);
    } catch (error) {
        const res = error.response;
        if (res && res.status === 401) {
            showMessage('Incorrect email or password');
        } else if (res?.data?.message) {
            showMessage('Error: ' + res.data.message);
        } else {
            showMessage('Unexpected error occurred');
        }
        console.error(error);
    }
};
</script>
<style scoped>
.login-wrapper {
    background-image: url('https://theplanetapp.com/wp-content/uploads/2022/08/fast-fashion-1-scaled-1-scaled.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.login {
    max-width: 600px;
    width: 90vw;
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
    max-width: none;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-left: 15px;
    font-size: 16px;
}

button {
    width: 100%;
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

.home-button {
    position: fixed;
    top: 10px;
    left: 20px;
    padding: 10px 10px;
    border: 1px solid black;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Perfectly Vintages';
    font-size: 20px;
    z-index: 1000;
    width: auto;
    height: auto;
}
</style>
