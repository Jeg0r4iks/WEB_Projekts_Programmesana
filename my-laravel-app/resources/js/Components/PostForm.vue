<template>
    <div class="post-form">
        <!-- Filter Bar -->
        <div v-if="categories.length" class="filter-bar">
            <div class="search-box">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search posts..."
                />
                <button @click="fetchPosts">Search</button>
            </div>

            <div class="category-box" v-if="!hideCategories">
                <label for="categoryFilter">Filter by Category:</label>
                <select v-model="selectedCategory" @change="fetchPosts">
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div>
                <label for="sortBy">Sort By:</label>
                <select v-model="sortBy" @change="fetchPosts">
                    <option value="">Default</option>
                    <option value="likes">Likes</option>
                    <option value="date">Date</option>
                </select>
            </div>
        </div>

        <!-- Posts List -->
        <div class="post-container">
            <div v-if="posts.length">
                <div v-for="post in posts" :key="post.id" class="post">
                    <h3 v-html="highlight(post.title)"></h3>
                    <img
                        v-if="post.image_url"
                        :src="post.image_url"
                        class="post-uploaded-image"
                    />

                    <div class="user_name">
                        <p><strong>Author: </strong>{{ post.user?.username || 'Unknown' }}</p>
                        <p>{{ post.created_date }}</p>
                    </div>
                    <p v-html="highlight(post.content)"></p>

                    <div class="reaction-icons">
                        <button @click="addReaction('like', post.id)">👍 Like</button>
                        <span>{{ post.reactionCounts?.like }}</span>
                        <button @click="addReaction('dislike', post.id)">👎 Dislike</button>
                        <span>{{ post.reactionCounts?.dislike }}</span>
                        <button @click="addReaction('heart', post.id)">❤️ Heart</button>
                        <span>{{ post.reactionCounts?.heart }}</span>
                    </div>

                    <!-- Admin/Owner Actions -->
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

                    <!-- Open Modal -->
                    <button @click="openModal(post)">Open Full Post</button>

                    <!-- Inline Comments -->
                    <div v-if="post.id === selectedPost?.id">
                        <div v-for="comment in selectedPost.comments" :key="comment.id" class="comment">
                            <p><strong>{{ comment.user.username }}:</strong> {{ comment.content }}</p>
                        </div>
                        <div v-if="isLoggedIn">
                            <textarea v-model="newCommentContent" placeholder="Add a comment..."></textarea>
                            <button @click="addComment(post.id)">Post Comment</button>
                        </div>
                        <p v-else>Please log in to comment.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Post -->
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
                        <select v-model="post.category_ids" multiple>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label>Content:</label>
                        <textarea v-model="post.content" required></textarea>
                        <label>Photo:</label>
                        <input type="file" accept="image/*" @change="handleImageUpload" ref="fileInput"/>
                        <button type="submit">Publish Post</button>
                    </div>
                </form>
                <p v-if="message" class="success-message">{{ message }}</p>
            </div>
        </div>
        <p v-else>Log in to add post</p>

        <!-- Full Post Modal -->
        <div v-if="isModalOpen" class="modal" @click="closeModal">
            <div class="modal-content" @click.stop>
                <h2>{{ selectedPost.user.username + ' post' }}</h2>
                <div v-if="selectedPost.comments?.length">
                    <div v-for="cm in selectedPost.comments" :key="cm.id" class="comment">
                        <div class="comment-header">
                            <strong>{{ cm.user.username }}: {{ cm.content }}</strong>
                            <span class="comment-date">{{ cm.created_date }}</span>
                        </div>
                    </div>
                </div>
                <div v-if="isLoggedIn">
                    <textarea v-model="newCommentContent" placeholder="Add a comment..."></textarea>
                    <button @click="addComment(selectedPost.id)">Post Comment</button>
                </div>
                <p v-else>Please log in to comment.</p>
                <button @click="closeModal">Close</button>
            </div>
        </div>

        <!-- Edit Post Modal -->
        <div v-if="editingPost" class="edit-overlay">
            <div class="edit-container">
                <h3>Edit Post</h3>
                <input v-model="editingPost.title" placeholder="Заголовок" />
                <textarea v-model="editingPost.content" placeholder="Содержимое"></textarea>
                <label>Select Categories:</label>
                <select v-model="editingPost.category_ids" multiple>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
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
            selectedCategory: '',
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
            const esc = this.searchQuery.replace(/[.*+?^${}()|[\\]\\]/g, '\\$&');
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
                if (this.selectedCategory) params.push(`category_id=${this.selectedCategory}`);
                if (this.searchQuery.trim()) params.push(`search=${encodeURIComponent(this.searchQuery.trim())}`);
                if (this.sortBy === 'likes') params.push('sort=likes');
                else if (this.sortBy === 'date') params.push('sort=date');
                if (params.length) url += '?' + params.join('&');
                const response = await axios.get(url);
                this.posts = response.data;
                for (let post of this.posts) {
                    const reactionResponse = await axios.get(`/posts/${post.id}/reactions`);
                    post.reactionCounts = reactionResponse.data || { like: 0, dislike: 0, heart: 0 };
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
            } catch {};
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
            } catch {};
        },
        async addComment(postId) {
            if (!this.newCommentContent) return;
            try {
                await axios.post(`/posts/${postId}/comments`, { content: this.newCommentContent });
                this.newCommentContent = '';
                this.fetchComments(postId);
            } catch {};
        },
        async addReaction(type, postId) {
            try {
                await axios.post(`/posts/${postId}/reactions`, { type });
                this.fetchPosts();
            } catch {};
        },
        async fetchCategories() {
            try {
                const res = await axios.get('/categories');
                this.categories = res.data;
            } catch {};
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
                const payload = {
                    title: this.editingPost.title,
                    content: this.editingPost.content,
                    category_ids: this.editingPost.category_ids
                };
                await axios.put(`/posts/${this.editingPost.id}`, payload);
                this.editingPost = null;
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
h2 {
    text-align: center;
    font-family: "Perfectly Vintages";
    font-size: 35px;
}

h3 {
    font-family: "Akira Expanded";
    font-size: 25px;
}

.post p {
    margin: 5px 0;
    font-size: 15px;
    font-family: sans-serif;
}

.user_name p {
    font-family: "Perfectly Vintages";
    font-size: 15px;
    font-weight: normal;
}
.user_name {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.post {
    color: #333;
    background-color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    flex: 0 0 30%;
}

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

.post-form {
    background: transparent;
    box-shadow: none;
}

h2 {
    color: #333;
}

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

button {
    margin-top: 15px;
    padding: 10px 15px;
    background: red;
    color: white;
    border: none;
    cursor: pointer;
}

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

.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

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

mark {
    background-color: yellow;
    padding: 0 2px;
    border-radius: 2px;
}

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

.post-uploaded-image {
    width: 100%;
    margin: 10px 0;
    border-radius: 4px;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-date {
    font-size: 0.85em;
    color: #666;
}

/* Добавьте в <style scoped> */
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
