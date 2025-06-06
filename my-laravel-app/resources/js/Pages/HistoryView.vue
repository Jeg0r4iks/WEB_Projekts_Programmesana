<template>
    <div class="history">
        <navbar @toggle="closeSidebar"/>
        <h1>History of Fashion</h1>
        <post-form :hide-categories="true" filter-category-name="History" />
        <AppFooter/>
    </div>
</template>

<script>
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";

export default {
    name: 'HistoryView',
    components: { navbar, AppFooter }
};
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

.content {
    display: flex;
    justify-content: space-between;
}

/* Filters Button Styling */
.filters-button {
    display: inline-block;
    font-size: 18px;
    font-weight: bold;
    color: black;
    cursor: pointer;
    margin: 20px;
}

.filters-button:hover {
    text-decoration: underline;
}

.sidebar {
    width: 250px;
    padding: 20px;
    background-color: #f4f4f4;
    border-left: 1px solid #ddd;
    position: absolute;
    right: 0;
    top: 100px;
    height: calc(100vh - 100px);
    overflow-y: auto;
}

.close-filter {
    font-size: 30px;
    font-weight: bold;
    color: black;
    cursor: pointer;
    position: absolute;
    top: 10px;
    left: 10px;
}

.close-filter:hover {
    color: red;
}

.search-box {
    width: 100%;
    padding: 10px;
    font-size: 18px;
}

.designer-list {
    margin-top: 20px;
}

.designer-item {
    display: block;
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    background-color: whitesmoke;
    border: 1px solid black;
    border-radius: 5px;
    text-align: left;
    cursor: pointer;
    font-weight: bold;
}

.main-content {
    flex-grow: 1;
    padding: 20px;
}

h2 {
    text-align: center;
    color: black;
    font-size: 35px;
    font-family: 'Gill Sans';
}
</style>
