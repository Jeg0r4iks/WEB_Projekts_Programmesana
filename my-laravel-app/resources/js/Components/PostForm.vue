<template>
    <div class="post-form">
        <div v-if="categories.length">
            <label for="categoryFilter">Filter by Category:</label>
            <select v-model="selectedCategory" @change="fetchPosts">
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
        </div>

        <div v-if="posts.length">
            <div v-for="post in posts" :key="post.id" class="post">
                <h3>{{ post.title }}</h3>
                <div class="user_name">
                    <p><strong>Author: </strong>{{ post.user ? post.user.username : 'Unknown' }}</p>
                </div>
                <p>{{ post.content }}</p>

                <!-- Reaction buttons and counts -->
                <div class="reaction-icons">
                    <button @click="addReaction('like', post.id)">👍 Like</button>
                    <span>{{ post.reactionCounts.like }}</span>

                    <button @click="addReaction('dislike', post.id)">👎 Dislike</button>
                    <span>{{ post.reactionCounts.dislike }}</span>

                    <button @click="addReaction('hearts', post.id)">❤️ Heart</button>
                    <span>{{ post.reactionCounts.hearts }}</span>
                </div>

                <button v-if="post.user && post.user.id === currentUserId" @click="deletePost(post.id)">Delete Post</button>
                <button @click="openModal(post)">Open Full Post</button>

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

        <div v-if="isLoggedIn">
            <form @submit.prevent="submitPost">
                <div>
                    <label>Post Title:</label>
                    <input type="text" v-model="post.title" required />
                </div>
                <div>
                    <label>Select Categories:</label>
                    <select v-model="post.category_ids" multiple>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label>Content:</label>
                    <textarea v-model="post.content" required></textarea>
                    <button type="submit">Publish Post</button>
                </div>
            </form>
            <p v-if="message" class="success-message">{{ message }}</p>
        </div>
        <p v-else>There are no posts yet.</p>
    </div>

    <div v-if="isModalOpen" class="modal" @click="closeModal">
        <div class="modal-content" @click.stop>
            <h2>{{ selectedPost.title }}</h2>
            <p><strong>Author: </strong>{{ selectedPost.user ? selectedPost.user.username : 'Unknown' }}</p>
            <p>{{ selectedPost.content }}</p>

            <div v-if="selectedPost.comments && selectedPost.comments.length > 0">
                <div v-for="comment in selectedPost.comments" :key="comment.id" class="comment">
                    <p><strong>{{ comment.user.username }}:</strong> {{ comment.content }}</p>
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
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            post: {
                title: '',
                content: '',
                category_ids: [],
            },
            selectedCategory: '',
            message: '',
            posts: [],
            categories: [],
            isLoggedIn: false,
            currentUserId: null,
            isModalOpen: false,
            selectedPost: null,
            newCommentContent: "",
        };
    },
    methods: {
        async submitPost() {
            try {
                const response = await axios.post('/posts', {
                    title: this.post.title,
                    content: this.post.content,
                    category_ids: this.post.category_ids,
                });
                this.message = response.data.message;
                await this.fetchPosts();
                this.post.title = '';
                this.post.content = '';
                this.post.category_ids = [];
            } catch (error) {
                console.error("Error while sending posts:", error);
            }
        },
        async fetchPosts() {
            try {
                let url = '/posts';
                if (this.selectedCategory) {
                    url += `?category_id=${this.selectedCategory}`;
                }
                const response = await axios.get(url);
                this.posts = response.data;
                for (let post of this.posts) {
                    const reactionResponse = await axios.get(`/posts/${post.id}/reactions`);
                    post.reactionCounts = reactionResponse.data || { like: 0, dislike: 0, hearts: 0 };
                }
            } catch (error) {
                console.error("Error while loading posts:", error);
            }
        },
        async checkLoginStatus() {
            try {
                const response = await axios.get('/user');
                this.isLoggedIn = response.data ? true : false;
                if (this.isLoggedIn) {
                    this.currentUserId = response.data.id;
                }
            } catch (error) {
                console.error("Error checking login status:", error);
            }
        },
        async deletePost(postId) {
            try {
                const response = await axios.delete(`/posts/${postId}`);
                this.message = response.data.message;
                await this.fetchPosts();
            } catch (error) {
                console.error("Error while deleting post:", error);
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
                const response = await axios.get(`/posts/${postId}/comments`);
                this.selectedPost.comments = response.data;
            } catch (error) {
                console.error("Error fetching comments:", error);
            }
        },
        async addComment(postId) {
            if (!this.newCommentContent) return;
            try {
                const response = await axios.post(`/posts/${postId}/comments`, { content: this.newCommentContent });
                this.newCommentContent = "";
                this.fetchComments(postId);
            } catch (error) {
                console.error("Error submitting comment:", error);
            }
        },

        async addReaction(reactionType, postId) {
            try {
                await axios.post(`/posts/${postId}/reactions`, { type: reactionType });
                this.fetchPosts();
            } catch (error) {
                console.error("Error adding reaction:", error);
            }
        },

        async fetchCategories() {
            try {
                const response = await axios.get('/categories');
                this.categories = response.data;
            } catch (error) {
                console.error("Error while fetching categories:", error);
            }
        },
    },
    mounted() {
        this.checkLoginStatus();
        this.fetchCategories();
        this.fetchPosts();
    }
}
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
    font-size: 20px;
    text-align: center;
    font-weight: normal;
}
.user_name strong {
    font-weight: normal;
}

.post {
    color: #333;
    background-color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
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
</style>
