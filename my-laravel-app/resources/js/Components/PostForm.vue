<template>
    <div class="post-form">
        <div v-if="categories.length" class="filter-bar">
            <div class="search-box">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search posts..."
                />
            </div>

            <div class="category-box" v-if="!hideCategories">
                <label>Select Categories:</label>
                <div class="categories-checkboxes">
                    <label
                        v-for="c in categories"
                        :key="c.id"
                        class="checkbox-item"
                    >
                        <span class="category-label">{{ c.name }}</span>
                        <input
                            type="checkbox"
                            :value="c.id"
                            v-model="selectedCategories"
                            @change="fetchPosts"
                            class="category-checkbox"
                        />
                    </label>
                </div>
            </div>

            <div>
                <label for="sortBy">Sort By:</label>
                <select v-model="sortBy" @change="fetchPosts">
                    <option value="">Default</option>
                    <option value="reactions">Reactions</option>
                    <option value="date">Date</option>
                </select>
            </div>
        </div>

        <div class="post-container">
            <div v-if="posts.length">
                <div v-for="post in posts" :key="post.id" class="post">
                    <h3 v-html="highlight(post.title)"></h3>
                    <h4>
                        {{ post.categories.map(c => c.name).join(', ') }}
                    </h4>
                    <img
                        v-if="post.image_url"
                        :src="post.image_url"
                        class="post-uploaded-image"
                    />
                    <div class="user-name">
                        <div class="author-info">
                            <img
                                v-if="post.user?.profile_photo_url"
                                :src="post.user.profile_photo_url"
                                alt="Author photo"
                                class="profile-photo-post"
                            />
                            <p class="author-text">
                                <strong>{{ post.user?.username || 'Unknown' }}</strong>
                            </p>
                        </div>
                        <p class="post-date">
                            {{ post.created_date }}
                        </p>
                    </div>
                    <p v-html="highlight(post.content)"></p>

                    <div class="reaction-icons">
                        <button @click="addReaction('like', post.id)">👍</button>
                        <span>{{ post.reactionCounts?.like }}</span>
                        <button @click="addReaction('dislike', post.id)">👎</button>
                        <span>{{ post.reactionCounts?.dislike }}</span>
                        <button @click="addReaction('heart', post.id)">❤️</button>
                        <span>{{ post.reactionCounts?.heart }}</span>
                    </div>

                    <button
                        v-if="(post.user && post.user.id === currentUserId) || isAdmin"
                        @click="deletePost(post.id)"
                        class="btn-delete"
                    >
                        Delete Post
                    </button>
                    <button
                        v-if="isAdmin || (post.user && post.user.id === currentUserId)"
                        @click="startEdit(post)"
                        class="btn-edit"
                    >
                        Edit Post
                    </button>

                    <button @click="openModal(post)">Open Full Post</button>

                    <div v-if="post.id === selectedPost?.id">
                        <div
                            v-for="comment in selectedPost.comments"
                            :key="comment.id"
                            class="comment"
                        >
                            <p>
                                <strong>{{ comment.user.username }}:</strong>
                                {{ comment.content }}
                            </p>
                        </div>
                        <div v-if="isLoggedIn">
              <textarea
                  v-model="newCommentContent"
                  placeholder="Add a comment..."
              ></textarea>
                            <button @click="addComment(post.id)">Post Comment</button>
                        </div>
                        <p v-else>Log in to comment</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isLoggedIn" class="create-post-wrapper">
            <div class="create-post-container">
                <h2 class="create-title">Write your own thoughts</h2>
                <form @submit.prevent="submitPost" enctype="multipart/form-data">
                    <div>
                        <label>Post Title:</label>
                        <input type="text" v-model="post.title" required />
                    </div>
                    <div>
                        <label>Select Categories:</label>
                        <div class="categories-checkboxes">
                            <label
                                v-for="c in categories"
                                :key="c.id"
                                class="checkbox-item"
                            >
                                <span class="category-label">{{ c.name }}</span>
                                <input
                                    type="checkbox"
                                    :value="c.id"
                                    v-model="post.category_ids"
                                    class="category-checkbox"
                                />
                            </label>
                        </div>
                    </div>
                    <div>
                        <label>Content:</label>
                        <textarea v-model="post.content" required></textarea>
                        <label>Photo:</label>
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                            ref="fileInput"
                        />
                        <button type="submit">Publish Post</button>
                    </div>
                </form>
                <p v-if="message" class="success-message">{{ message }}</p>
            </div>
        </div>

        <div v-if="isModalOpen" class="modal" @click="closeModal">
            <div class="modal-content" @click.stop>
                <h2>{{ selectedPost.user.username + ' post' }}</h2>
                <div v-if="selectedPost.comments?.length">
                    <div
                        v-for="cm in selectedPost.comments"
                        :key="cm.id"
                        class="comment"
                    >
                        <div class="comment-header">
                            <strong>{{ cm.user.username }}: {{ cm.content }}</strong>
                            <span class="comment-date">{{ cm.created_date }}</span>
                        </div>
                    </div>
                </div>
                <div v-if="isLoggedIn">
          <textarea
              v-model="newCommentContent"
              placeholder="Add a comment..."
          ></textarea>
                    <button @click="addComment(selectedPost.id)">Post Comment</button>
                </div>
                <p v-else>Log in to comment</p>
                <button @click="closeModal">Close</button>
            </div>
        </div>

        <div v-if="editingPost" class="edit-overlay">
            <div class="edit-container">
                <h3>Edit Post</h3>
                <input v-model="editingPost.title" placeholder="Title" />
                <textarea v-model="editingPost.content" placeholder="Content"></textarea>

                <label>Select Categories:</label>
                <div class="categories-checkboxes">
                    <label
                        v-for="c in categories"
                        :key="c.id"
                        class="checkbox-item"
                    >
                        <span class="category-label">{{ c.name }}</span>
                        <input
                            type="checkbox"
                            :value="c.id"
                            v-model="editingPost.category_ids"
                            class="category-checkbox"
                        />
                    </label>
                </div>

                <label>Change Photo:</label>
                <input type="file" accept="image/*" @change="handleImageUpload" />

                <button @click="updatePost">Save</button>
                <button @click="cancelEdit">Cancel</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'PostForm',
    props: {
        hideCategories: Boolean
    },
    data() {
        return {
            post: { title: '', content: '', category_ids: [] },
            selectedCategories: [],
            searchQuery: '',
            sortBy: '',
            message: '',
            posts: [],
            categories: [],
            isLoggedIn: false,
            isAdmin: false,
            currentUserId: null,
            isModalOpen: false,
            selectedPost: null,
            newCommentContent: '',
            imageFile: null,
            editingPost: null
        };
    },
    methods: {
        highlight(text) {
            if (!this.searchQuery) return text;
            const esc = this.searchQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.replace(new RegExp(`(${esc})`, 'gi'), '<mark>$1</mark>');
        },
        handleImageUpload(e) {
            this.imageFile = e.target.files[0];
        },
        async submitPost() {
            const fd = new FormData();
            fd.append('title', this.post.title);
            fd.append('content', this.post.content);
            this.post.category_ids.forEach(id => fd.append('category_ids[]', id));
            if (this.imageFile) fd.append('image', this.imageFile);
            const res = await axios.post('/posts', fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            this.message = res.data.message;
            this.fetchPosts();
            this.post.title = '';
            this.post.content = '';
            this.post.category_ids = [];
            this.imageFile = null;
            this.$refs.fileInput.value = '';
        },
        async fetchPosts() {
            try {
                let url = '/posts';
                const params = [];
                if (this.selectedCategories.length) {
                    this.selectedCategories.forEach(id =>
                        params.push(`category_ids[]=${id}`)
                    );
                }
                if (this.searchQuery.trim()) {
                    params.push(`search=${encodeURIComponent(this.searchQuery.trim())}`);
                }
                if (this.sortBy === 'date') {
                    params.push('sort=date');
                }
                if (params.length) url += '?' + params.join('&');

                const response = await axios.get(url);
                this.posts = response.data;

                for (let post of this.posts) {
                    const reactionResponse = await axios.get(
                        `/posts/${post.id}/reactions`
                    );
                    post.reactionCounts = reactionResponse.data || {
                        like: 0,
                        dislike: 0,
                        heart: 0
                    };
                }

                if (this.sortBy === 'reactions') {
                    this.posts.sort((a, b) => {
                        const sumA =
                            (a.reactionCounts.like || 0) +
                            (a.reactionCounts.dislike || 0) +
                            (a.reactionCounts.heart || 0);
                        const sumB =
                            (b.reactionCounts.like || 0) +
                            (b.reactionCounts.dislike || 0) +
                            (b.reactionCounts.heart || 0);
                        return sumB - sumA;
                    });
                }
            } catch (error) {
                console.error(error);
            }
        },
        async checkLoginStatus() {
            try {
                const response = await axios.get('/user');
                this.isLoggedIn = !!response.data;
                if (this.isLoggedIn) {
                    this.currentUserId = response.data.id;
                    this.isAdmin = response.data.is_admin;
                }
            } catch {}
        },
        async deletePost(postId) {
            try {
                await axios.delete(`/posts/${postId}`);
                this.fetchPosts();
            } catch (e) {
                console.error(e);
            }
        },
        openModal(post) {
            this.selectedPost = post;
            this.isModalOpen = true;
            this.fetchComments(post.id);
        },
        closeModal() {
            this.isModalOpen = false;
            this.selectedPost = null;
        },
        async fetchComments(postId) {
            try {
                const res = await axios.get(`/posts/${postId}/comments`);
                this.selectedPost.comments = res.data;
            } catch {}
        },
        async addComment(postId) {
            if (!this.newCommentContent) return;
            try {
                await axios.post(`/posts/${postId}/comments`, {
                    content: this.newCommentContent
                });
                this.newCommentContent = '';
                this.fetchComments(postId);
            } catch {}
        },
        async addReaction(type, postId) {
            if (!this.isLoggedIn) {
                alert('Log in to add reaction');
                return;
            }
            try {
                await axios.post(`/posts/${postId}/reactions`, { type });
                this.fetchPosts();
            } catch (e) {
                console.error(e);
            }
        },
        async fetchCategories() {
            try {
                const res = await axios.get('/categories');
                this.categories = res.data;
            } catch {}
        },
        startEdit(post) {
            this.editingPost = {
                id: post.id,
                title: post.title,
                content: post.content,
                category_ids: post.categories.map(c => c.id)
            };
        },
        cancelEdit() {
            this.editingPost = null;
        },
        async updatePost() {
            try {
                const fd = new FormData();
                fd.append('title', this.editingPost.title);
                fd.append('content', this.editingPost.content);
                this.editingPost.category_ids.forEach(id =>
                    fd.append('category_ids[]', id)
                );
                if (this.imageFile) fd.append('image', this.imageFile);
                await axios.post(
                    `/posts/${this.editingPost.id}?_method=PUT`,
                    fd,
                    {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    }
                );
                this.cancelEdit();
                this.fetchPosts();
            } catch (e) {
                console.error(e);
            }
        }
    },
    mounted() {
        this.checkLoginStatus();
        this.fetchCategories().then(this.fetchPosts);
    }
};
</script>
<style scoped>
.categories-checkboxes {
    width: 100%;
    margin-top: 8px;
}

.checkbox-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    padding: 0;
    border: none;
    background: transparent;
}

.category-label {
    margin-right: 8px;
}

.category-checkbox {
    width: 18px;
    height: 18px;
    /* accent-color: #000; */
}

.user-name {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 0;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-photo-post {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.author-text {
    margin: 0;
    font-size: 15px;
}

.post-date {
    margin: 0;
    font-size: 15px;
    color: #666;
    text-align: right;
}

.edit-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.edit-container {
    background-color: #fff;
    padding: 20px;
    max-width: 600px;
    width: 90%;
    border-radius: 8px;
}

/* Модалка */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: white;
    padding: 20px;
    max-width: 800px;
    width: 100%;
    border-radius: 8px;
    overflow-y: auto;
}

/* Заголовки */
h2 {
    text-align: center;
    font-family: "Perfectly Vintages";
    font-size: 35px;
}

h3 {
    font-family: "Akira Expanded";
    font-size: 25px;
}

h4 {
    font-family: "Perfectly Vintages";
    font-size: 20px;
}

/* Текст поста */
.post p {
    margin: 5px 0;
    font-size: 15px;
    font-family: sans-serif;
}

/* Карточка поста */
.post {
    color: #333;
    background-color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    flex: 0 0 30%;
}

/* Форма создания */
.post-form {
    max-width: 500px;
    margin: auto;
    padding: 20px;
    background: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Общие label/input/textarea */
label {
    display: block;
    font-weight: bold;
    margin-top: 10px;
}

input,
textarea {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Кнопки */
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

/* Сообщение об успехе */
.success-message {
    color: green;
    margin-top: 10px;
}

/* Убираем фон у обёртки формы (если нужно) */
.post-form {
    background: transparent;
    box-shadow: none;
}

/* Реакции */
.reaction-icons {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.reaction-icons button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

/* Фильтр-панель */
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

/* Поиск */
.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-box input {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.search-box button {
    padding: 6px 12px;
    background: #222;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-box button:hover {
    background: red;
}

/* Подсветка поиска */
mark {
    background-color: yellow;
    padding: 0 2px;
    border-radius: 2px;
}

/* Сетка постов */
.post-container > div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
    align-items: start;
}

@media (max-width: 960px) {
    .post-container > div {
        grid-template-columns: 1fr;
    }
}

.post-container {
    position: relative;
    left: 50%;
    transform: translateX(-50%);
    width: 90vw !important;
    max-width: 1200px !important;
}

/* Изображение в посте */
.post-uploaded-image {
    width: 100%;
    margin: 10px 0;
    border-radius: 4px;
}

/* Комментарии */
.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-date {
    font-size: 0.85em;
    color: #666;
}

/* Заголовок формы создания */
.create-post-container {
    margin-top: 60px;
}

.create-post-container .create-title {
    text-align: center;
    font-family: "Perfectly Vintages";
    font-size: 35px;
    margin-bottom: 20px;
}
</style>
