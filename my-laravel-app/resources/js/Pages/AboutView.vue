<template>
    <navbar/>
    <h1>About Mireqx</h1>
    <AppFooter/>
</template>

<script>
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";
import PostForm from "@/Components/PostForm.vue";
export default {
    name: 'AboutView',
    components: {PostForm, navbar, AppFooter},
};
</script>

<style scoped>
h1 {
    text-align: center;
    color: black;
    font-size: 45px;
    font-family: 'Perfectly Vintages';
    font-weight: normal;
    padding: 30px;
    background-color: rgb(241, 211, 211);
}
</style>
