import { defineStore } from 'pinia'


export const useGeneralStore = defineStore('general', {
    state: () => ({
        menuOpen: true,
        menuType: 'expanded',
        themeMode: 'dark',
        sort: [
            20,
            100,
            500,
        ],
        status:[
            { title: 'Active', value: 1 },
            { title: 'Deactive', value: 0 },
        ],
        PaymentStatus:[
            { title: 'Paid', value: 1 },
            { title: 'UnPaid', value: 0 },
        ],
    }),

    getters: {
        // userName: (state) => state.user?.name || 'Guest',
    },

    actions: {
        toggleThemeMode(theme) {
             if (theme.global.name == "adminDark") {
                theme.change("adminLight");
            } else {
                theme.change("adminDark");
            }
        },
        toggleMenuType() {
            this.menuType = this.menuType === 'collapsed' ? 'expanded' : 'collapsed'
        },
        toggleMenu() {
            this.menuOpen = this.menuOpen === true ? false : true;
        },
        setMenuType(type) {
            this.menuType = this.menuType === type;
        },
        startLoading() {
            this.loading = true;
            console.log('Theme Store Loading',this.loading);
        },
        endLoading() {
            this.loading = false;
             console.log('Theme Store Loading',this.loading);
        },
        toggleTheme() {
            if (this.vuetify.global.name == "adminDark") {
                this.vuetify.change("adminLight");
            } else {
                this.vuetify.change("adminDark");
            }
        },
    },
})
