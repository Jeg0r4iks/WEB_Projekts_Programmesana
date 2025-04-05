<template>
    <nav :class="{ dark: isDarkMode }">
        <div class="left">
            <i class="fas fa-search"></i>
            <a href="/history" class="desktop-only">History</a>
            <a href="/runway" class="desktop-only">Runway</a>
            <i class="fas fa-bars mobile-only" @click="toggleMenu"></i>
        </div>

        <h1 @click="goHome">Mireqx</h1>

        <div class="right">
            <a href="/style" class="desktop-only">Style</a>
            <a href="/about" class="desktop-only">About</a>
            <i :class="isDarkMode ? 'fas fa-sun' : 'fas fa-moon'" @click="toggleDarkMode"></i>
            <a href="/login"><i class="fas fa-user"></i></a>
        </div>

        <div v-if="isMenuOpen" class="mobile-menu">
            <a href="/history">History</a>
            <a href="/runway">Runway</a>
            <a href="/style">Style</a>
            <a href="/about">About</a>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar',
    data() {
        return {
            isDarkMode: localStorage.getItem('isDarkMode') === 'true',
            isMenuOpen: false
        };
    },
    methods: {
        goHome() {
            this.$inertia.visit('/');
        },
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            localStorage.setItem('isDarkMode', this.isDarkMode);
            document.body.classList.toggle('dark', this.isDarkMode);
        },
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        }
    },
    mounted() {
        if (this.isDarkMode) {
            document.body.classList.add('dark');
        }
    }
};
</script>

<style scoped>
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    height: 60px;
    background-color: whitesmoke;
    box-shadow: rgb(244, 153, 153) 1px 1px 10px;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-family: "Akira Expanded";
    position: relative;
}

body.dark nav {
    background-color: #222;
    box-shadow: rgb(0, 0, 0) 1px 1px 10px;
}

.left, .right {
    display: flex;
    align-items: center;
    gap: 30px;
}

h1 {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 24px;
    cursor: pointer;
}

h1:hover {
    color: rgb(255, 0, 0);
}

a {
    font-size: 20px;
    text-decoration: none;
    color: black;
    font-weight: normal;
    transition: color 0.3s ease;
}

a:hover {
    color: rgb(255, 0, 0);
}

body.dark nav a {
    color: whitesmoke;
}

body.dark nav a:hover {
    color: rgb(255, 0, 0);
}

body.dark nav i {
    color: whitesmoke;
}

body.dark nav i:hover {
    color: rgb(255, 0, 0);
}

.mobile-menu {
    position: absolute;
    top: 60px;
    left: 0;
    width: 100%;
    background: whitesmoke;
    display: flex;
    flex-direction: column;
    text-align: center;
    padding: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

body.dark .mobile-menu {
    background: #333;
}

.mobile-menu a {
    padding: 10px;
    font-size: 18px;
}

@media (max-width: 768px) {
    .desktop-only {
        display: none;
    }
    .mobile-only {
        display: block;
        font-size: 30px;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .left, .right {
        gap: 10px;
    }
}

@media (min-width: 769px) {
    .mobile-only {
        display: none;
    }
}

nav i {
    font-size: 20px;
}

nav .fa-sun,
nav .fa-moon,
nav .fa-search,
nav .fa-bars {
    font-size: 20px;
}

body.dark nav .fa-sun,
body.dark nav .fa-moon,
body.dark nav .fa-search,
body.dark nav .fa-bars {
    color: whitesmoke;
}

body.dark nav .fa-sun:hover,
body.dark nav .fa-moon:hover,
body.dark nav .fa-search:hover,
body.dark nav .fa-bars:hover {
    color: rgb(255, 0, 0);
}

body nav i:hover {
    color: rgb(255, 0, 0);
}
</style>
