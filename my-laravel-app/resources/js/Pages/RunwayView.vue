<template>
    <div class="runway">
        <navbar/>
        <h1>Explore the runway</h1>

        <!-- Поиск -->
        <div class="search-container">
            <input type="text" placeholder="Search..." class="search-box"/>
            <span class="close-search" @click="clearSearch">&times;</span>
        </div>

        <AppFooter/>
    </div>
</template>

<script>
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";

export default {
    name: 'RunwayView',
    components: { navbar, AppFooter },
    data() {
        return {
            searchQuery: '',
        };
    },
    methods: {
        clearSearch() {
            this.searchQuery = '';
        },
    },
};
</script>

<style scoped>
h1 {
    background-color: transparent;
    text-align: center;
    color: black;
    font-size: 45px;
    font-family: 'Perfectly Vintages';
    font-weight: normal;
    padding: 30px;
}

/* Контейнер для поиска */
.search-container {
    position: relative;
    width: 300px; /* Задайте нужную ширину поля поиска */
    margin: 20px auto;
}

/* Поле ввода */
.search-box {
    width: 100%;
    padding: 10px;
    font-size: 18px;
    padding-right: 30px; /* Сделайте пространство для крестика */
}

/* Крестик для очистки поля */
.close-search {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    font-size: 20px;
    color: black;
    cursor: pointer;
}

.close-search:hover {
    color: red; /* Цвет при наведении */
}
</style>
