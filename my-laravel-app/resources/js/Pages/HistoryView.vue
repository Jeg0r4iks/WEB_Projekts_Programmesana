<template>
    <div class="history">
        <navbar @toggle="closeSidebar"/>
        <h1>History of Fashion</h1>

        <div class="content">
            <span class="filters-button" @click="toggleSidebar">
                Filters
            </span>

            <div class="sidebar" v-if="showSidebar">
                <div v-if="showFilters" class="filter-container">
                    <span class="close-filter" @click="closeSidebar">&times;</span>

                    <input v-model="filter" type="text" placeholder="Search designers" class="search-box"/>
                    <div class="designer-list">
                        <button v-for="designer in filteredDesigners"
                                :key="designer.id"
                                class="designer-item"
                                @click="selectDesigner(designer)">
                            {{ designer.name }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <div v-if="selectedDesigner" class="designer-details">
                    <h2>{{ selectedDesigner.name }}</h2>
                    <p>{{ selectedDesigner.description }}</p>
                </div>
            </div>
        </div>
        <post-form :hide-categories="true"/>
        <AppFooter/>
    </div>
</template>

<script>
import navbar from "@/Components/navbar.vue";
import AppFooter from "@/Components/footer.vue";

export default {
    name: 'HistoryView',
    components: { navbar, AppFooter },
    data() {
        return {
            designers: [
                { id: 1, name: 'Coco Chanel', description: 'Founder of Chanel, known for timeless elegance.' },
                { id: 2, name: 'Yves Saint Laurent', description: 'Revolutionized fashion with the tuxedo suit for women.' },
                { id: 3, name: 'Gianni Versace', description: 'Bold colors and extravagant styles define Versace.' },
                { id: 4, name: 'Karl Lagerfeld', description: 'Iconic designer behind Chanel and Fendi.' },
                { id: 5, name: 'Vivienne Westwood', description: 'Punk and rebellious spirit in fashion.' },
                { id: 6, name: 'Alexander McQueen', description: 'British fashion designer known for his unconventional designs.'},
                { id: 7, name: 'Balenciaga', description: 'Spanish luxury fashion house founded by Cristóbal Balenciaga.' },
                { id: 8, name: 'Comme des Garçons', description: 'Japanese fashion label founded by Rei Kawakubo.' },
                { id: 9, name: 'Dsquared2', description: 'Canadian fashion label founded by twin brothers Dean and Dan Caten.'},
                { id: 10, name: 'Rick Owens', description: ''},
            ],
            filter: '',
            selectedDesigner: null,
            showSidebar: false,
            showFilters: false,
        };
    },
    computed: {
        filteredDesigners() {
            return this.designers.filter((designer) =>
                designer.name.toLowerCase().includes(this.filter.toLowerCase())
            );
        }
    },
    methods: {
        selectDesigner(designer) {
            this.selectedDesigner = designer;
        },
        toggleSidebar() {
            this.showSidebar = !this.showSidebar;
            this.showFilters = this.showSidebar;
        },
        closeSidebar() {
            this.showSidebar = false;
            this.showFilters = false;
        }
    },
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
