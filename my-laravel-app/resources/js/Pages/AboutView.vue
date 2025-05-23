<template>
    <navbar/>
    <h1>About Mireqx</h1>
    <h2>Mireqx is a digital fashion platform where style meets individuality. This website is designed to inspire, express, and explore the latest trends through community-driven content and creative freedom.

        What you’ll find on Mireqx:

        💫 Trend Spotlights – Stay updated with the latest seasonal styles and color palettes.

        📸 User-Generated Fashion – Share your looks, react to others, and be part of a style-forward community.

        💬 Fashion Discussions – Connect with others to exchange ideas, inspiration, and personal style journeys.

        🖤 Authentic Expression – There’s no right or wrong here—only your unique fashion voice.

        Mireqx is the space where you can be unapologetically you—bold, creative, and stylish.
    </h2>
    <AppFooter/>
</template>

<script>
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";
export default {
    name: 'AboutView',
    components: {navbar, AppFooter},
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

h2 {
    font-family: "Perfectly Vintages";
    font-size: 20px;
}
</style>
