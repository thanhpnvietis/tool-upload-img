<template lang="">
  <div>
    <DataTable :value="posts" responsiveLayout="scroll" @row-click="rowClick($event)" >
      <Column field="id" header="ID"></Column>
      <Column field="title.rendered" header="title"></Column>
      <Column headerStyle="width: 4rem; text-align: center" bodyStyle="text-align: center; overflow: visible">
        <template #body="data">
            <Button v-if="data.data.can_upload" type="button" icon="pi pi-file" @click="showModal(data)" ></Button>
        </template>
      </Column>
    </DataTable>
    <div class="btn-load-more" >
      <Button v-if="!notData" :disabled="loading" label="Load more" @click="loadData()"/>
      <div v-else>
        Not found data
      </div>
    </div>
    <DialogUpload :display="display" :detail="detail" @updatePost="updatePost" />
    <Toast />
  </div>
</template>
<script>


export default {

  data() {
    return {
      posts : [],
      notData: false,
      page:1,
      loading:true,
      display:false,
      detail:null,
      postComplete:[]
    }
  },

  async mounted() {
    await this.getUploadComplete();
    this.loadData();
  },

  methods: {
    rowClick(event){},
    showModal(data){
      this.display = !this.display;
      this.detail = data.data;
    },
    async getUploadComplete(){
      let {result} = await $fetch('http://localhost:3000/api/post/query?col=posts');
      this.postComplete = result.map(e => e.post_id);
    },
    updatePost(data){
      let index = this.posts.findIndex(e=>{
        return e.id === data.postId;
      });
      this.posts[index]['can_upload'] = false;
    },
    loadData(){
      this.loading = true;
      $fetch('http://tooluploadfile.local/wp-json/wp/v2/posts?page='+this.page).then(data =>{
        for(let i = 0; i < data.length;i++){
          let item = data[i];
          if(this.postComplete.includes(item.id)){
            data[i]['can_upload'] = false;
          }else{
            data[i]['can_upload'] = true;
          }
        }
        this.loading = false;
        this.posts = [...this.posts ,...data];
        this.page++;
      }).catch(e=>{
        this.notData = true;
      })
    }
  },
}
</script>
<style >
.btn-load-more{
  margin-top: 10px;
    display: flex;
    justify-content: center;
}
</style>
