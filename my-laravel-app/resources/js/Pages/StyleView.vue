<template>
    <div class="style">
        <navbar />
        <h1>Find your style</h1>
        <post-form :hide-categories="true"/>
        <AppFooter />
    </div>
</template>

<script>
import axios from 'axios';
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";

export default {
    name: 'StyleView',
    components: { navbar, AppFooter },
    data() {
        return {
            info: []
        };
    },
    mounted() {
        this.fetchInfo();
    },
    methods: {
        async fetchInfo() {
            try {
                const response = await axios.get("/info");
                this.info = response.data;
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }
    }
}
</script>

<style scoped>

h1 {
    text-align: center;
    color: black;
    font-size: 45px;
    font-family: 'Perfectly Vintages';
    font-weight: normal;
    background-color: rgb(241, 211, 211);
    padding: 30px;
}

.info-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.info-item {
    display: inline-block;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 400px;
    min-width: 150px;
    font-family: 'Perfectly Vintages'; /* Применение нового шрифта */
}

.info-item h2 {
    font-size: 26px; /* Увеличил заголовок */
    font-weight: bold;
    margin-bottom: 15px; /* Увеличил отступ после заголовка */
}

.info-item p {
    font-size: 16px;
    margin-bottom: 20px; /* Добавил больше пространства после текста */
}

.info-item img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-top: 15px; /* Увеличил отступ перед картинкой */
}
</style>

