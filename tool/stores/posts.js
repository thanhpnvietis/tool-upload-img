import { defineStore } from 'pinia'

// You can name the return value of `defineStore()` anything you want,
// but it's best to use the name of the store and surround it with `use`
// and `Store` (e.g. `useUserStore`, `useCartStore`, `useProductStore`)
// the first argument is a unique id of the store across your application
export const postStore = defineStore('posts', {
  state: () => ({ 
    listImage: [],
    loadListImage: false,
    notFoundImage: false,
  }),
  getters: {
    getListImage: (state) => state.listImage,
    getLoadListImage: (state) => state.loadListImage,
    getNotFoundImage: (state) => state.notFoundImage,
  },
  actions: {
    async acListImage(params) {
      this.loadListImage = true;
      this.notFoundImage = false;
      $fetch(useRuntimeConfig().public.base_url_wp+'/wp-json/wp/v2/media?page='+params.page+'&per_page=35').then(async (data) =>{
        if(params.page == 1){
          this.listImage = data;
        }else{
          this.listImage = [...this.listImage,...data];
        }
        this.loadListImage = false;
      }).catch(e=>{
        this.loadListImage = false;
        this.notFoundImage = true;
      })
    }
  },
})
