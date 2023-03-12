import { defineStore } from 'pinia'

// You can name the return value of `defineStore()` anything you want,
// but it's best to use the name of the store and surround it with `use`
// and `Store` (e.g. `useUserStore`, `useCartStore`, `useProductStore`)
// the first argument is a unique id of the store across your application
export const postStore = defineStore('posts', {
  state: () => ({ 
    listImage: [],
    loadListImage: false,
  }),
  getters: {
    getListImage: (state) => state.listImage,
    getLoadListImage: (state) => state.loadListImage,
  },
  actions: {
    async acListImage(params) {
      this.loadListImage = true;
      $fetch(useRuntimeConfig().public.base_url_wp+'/wp-json/wp/v2/media?page='+params.page).then(async (data) =>{
        console.log(data);
        if(params.page == 1){
          this.listImage = data;
        }else{
          this.listImage = [...this.listImage,...data];
        }
        console.log(this.listImage);
        this.loadListImage = false;
      }).catch(e=>{
        this.loadListImage = false;
      })
    }
  },
})
