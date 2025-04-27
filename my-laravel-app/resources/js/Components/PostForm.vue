<template>
    <div class="post-form">
        <h2>Создать публикацию</h2>
        <form @submit.prevent="submitPost">
            <div>
                <label>Заголовок:</label>
                <input type="text" v-model="post.title" required>
            </div>
            <div>
                <label>Текст:</label>
                <textarea v-model="post.content" required></textarea>
            </div>
            <button type="submit">Опубликовать</button>
        </form>
        <p v-if="message" class="success-message">{{ message }}</p>

        <h2>Публикации</h2>
        <div v-if="posts.length">
            <div v-for="post in posts" :key="post.id" class="post">
                <h3>{{ post.title }}</h3>
                <p>{{ post.content }}</p>
            </div>
        </div>
        <p v-else>Публикаций пока нет.</p>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            post: {
                title: '',
                content: ''
            },
            message: '',
            posts: [] // Храним список публикаций
        };
    },
    methods: {
        // Отправка нового поста в API
        async submitPost() {
            try {
                const response = await axios.post('/posts', {
                    title: this.post.title,
                    content: this.post.content
                });
                this.message = response.data.message;

                await this.fetchPosts(); // Дождаться обновления списка

                this.post.title = '';
                this.post.content = '';
            } catch (error) {
                console.error("Ошибка при отправке:", error);
            }
        },
        // Получение списка публикаций
        async fetchPosts() {
            try {
                const response = await axios.get('/posts');
                this.posts = response.data;
            } catch (error) {
                console.error("Ошибка при загрузке постов:", error);
            }
        }
    },
    mounted() {
        this.fetchPosts(); // Загружаем публикации при загрузке страницы
    }
}
</script>

<style scoped>
.post-form {
    max-width: 500px;
    margin: auto;
    padding: 20px;
    background: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 10px;
}

input, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    margin-top: 15px;
    padding: 10px 15px;
    background: blue;
    color: white;
    border: none;
    cursor: pointer;
}

.success-message {
    color: green;
    margin-top: 10px;
}

.post {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}
</style>
