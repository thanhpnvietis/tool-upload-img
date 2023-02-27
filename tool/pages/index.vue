<template lang="">
  <div class="text-center">
    <h1>Danh sách bài viết</h1>
  </div>
  <div>
    <DataTable :value="posts" responsiveLayout="scroll" @row-click="rowClick($event)" >
      <Column field="id" header="ID"></Column>
      <Column field="title.rendered" header="Tiêu đề"></Column>
      <Column header="Trạng thái ">
         <template #body="data">
            <span :class="status_process_label[data.data.status_process].class">
              {{status_process_label[data.data.status_process].label}}
            </span>
        </template>
      </Column>
      <Column header="Chức năng">
        <template #body="data">
            <div class="btn-action">
              <Button class="p-button-sm" label="Chèn ảnh" @click="addFile(data)" ></Button>
              <Button class="p-button-sm" label="Xem list ảnh" @click="showModalImage(data)" ></Button>
              <Button class="p-button-sm" label="Xem bài viết" @click="linkWp(data)" ></Button>
              
            </div>
        </template>
      </Column>
    </DataTable>
    <div class="btn-load-more" >
      <Button v-if="!notData" :disabled="loading" label="Load more" @click="loadData()"></Button>
      <div v-else>
        Không tìm thấy data
      </div>
    </div>
     <input type="file" id="input-file" @change="onFileSelected($event)" hidden multiple="multiple">
    <DialogUpload :display="display" :detail="detail" @updatePost="updatePost" />
    <DialogShowImg :display="displayListImage" :detail="detail" />
    <Toast />
  </div>
  <div :class="`${uploading ? 'show' : ''} loader`" >
    <div class="loading">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
</template>
<script>


export default {

  data() {
    return {
      base_url_wp: this.$config.public.base_url_wp,
      base_url: this.$config.public.base_url,
      uploading: false,
      posts : [],
      notData: false,
      page:1,
      loading:true,
      display:false,
      displayListImage:false,
      detail:null,
      postComplete:[],
      status_process :{
        none: 'none',
        processed: 'processed',
        error: 'error'
      },
      status_process_label :{
        none: {
          label: 'Chưa thêm ảnh',
          class: 'status_process_none'
        },
        processed: {
          label: 'Đã thêm ảnh',
          class: 'status_processed'
        },
        error: {
          label: 'Lỗi thêm ảnh',
          class: 'status_process_error'
        }
      }
    }
  },

  async mounted() {
    // await this.getUploadComplete();
    this.loadData();
  },

  methods: {
    rowClick(event){},
    showModal(data){
      this.display = !this.display;
      this.detail = data.data;
    },
    showModalImage(data){
      this.displayListImage = !this.displayListImage;
      this.detail = data.data;
    },
    async getUploadComplete(listId){
      let {result} = await $fetch(this.base_url+'/api/post/get',{
        method: 'POST',
        body: JSON.stringify({
          col: 'posts', 
          listId
        })
      });
      return result;
      // this.postComplete = result.map(e => e.post_id);
    },
    updatePost(data,dataTool){
      let index = this.posts.findIndex(e=>{
        return e.id === data.id;
      });
      this.posts[index]['status_process'] = dataTool.status;
      this.posts[index]['image_url'] = dataTool.image_url;
      this.posts[index]['attachment_id'] = dataTool.image_id;
    },
    linkWp(data){
      window.open(
        data.data.link,
        '_blank' // <- This is what makes it open in a new window.
      );
    },
    addFile(data){
      this.detail = data.data;
      document.getElementById("input-file").click();
    },
    onFileSelected(event){
      this.uploading = true
      let formData = new FormData();
     
      let attachmentId = this.detail.attachment_id || {};
      attachmentId = [...attachmentId];
      formData.append('post_id', this.detail.id);
      formData.append('delete_attachment_id', attachmentId);
      for( let i = 0; i < event.target.files.length; i ++ ){
          formData.append('files['+i+']', event.target.files[i] );
      }
      $fetch( this.base_url_wp+'/wp-json/api_upload_file/v1/upload-file-to-post/', {
        method: 'POST',
        body: formData
      }).then(async e =>{
        if(e.data){
          let postComplete =  await this.addPostComplete({
            post_id:this.detail.id,
            status: this.status_process.processed,
            image_id:e.data.map(item=>item.ID),
            image_url:e.data.map(item=>item.guid), 
          });
          this.uploading = false;
          this.updatePost(this.detail,postComplete);
        }else{
          let postComplete =  await this.addPostComplete({
            post_id:this.detail.id,
            status: this.status_process.error,
          });
          this.uploading = false;
          this.updatePost(this.detail,postComplete);
        }

      }).catch( async e => {
        console.log(e);
        let postComplete =  await this.addPostComplete({
          post_id:this.detail.id,
          status: this.status_process.error,
          image_id:e.data.map(e=>e.id),
          image_url:e.data.map(e=>e.guid), 
        })
        
        this.uploading = false;
        this.updatePost(this.detail,postComplete);
      });

      document.getElementById("input-file").value = "";
    },
    async addPostComplete(param){
      let body = {
        post_id:param.post_id,
        image_id:param.image_id,
        status:param.status,
        image_url:param.image_url,
      }
      let {result} = await $fetch( this.base_url+'/api/post/add?col=posts', {
        method: 'POST',
        body
      })
      
      return result
    },
    loadData(){
      this.loading = true;
      $fetch(this.base_url_wp+'/wp-json/wp/v2/posts?page='+this.page).then(async  (data) =>{
        let listId = data.map(e=>e.id);
        let listInTool = await this.getUploadComplete(listId);
        for(let i = 0; i < data.length;i++){
          data[i]['image_url'] = [];
          data[i]['attachment_id'] = [];
          let item = data[i];
          let find = listInTool.find(e => e.post_id == item.id);

          if(find){
            if(find.status === this.status_process.error){
              data[i]['status_process'] = this.status_process.error;
            }else if(find.status === this.status_process.processed){
              data[i]['status_process'] = this.status_process.processed;
            }else{
              data[i]['status_process'] = this.status_process.none;
            }
            data[i]['image_url'] = find.image_url || [];
            data[i]['attachment_id'] = find.image_id || [];
          }else{
            data[i]['status_process'] = this.status_process.none ;
          }
        }

        this.loading = false;
        this.posts = [...this.posts ,...data];
        this.page++;
      }).catch(e=>{
        console.log(e);
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
.btn-action{
  display: flex;
}
.btn-action button {
  margin-right:1rem;
}

.loader{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgb(0 255 60 / 2%);
  justify-content: center;
  align-items: center;
  display: none;
}

.loader.show{
  display: flex;
}


.loading {
 --speed-of-animation: 0.9s;
 --gap: 6px;
 --first-color: #4c86f9;
 --second-color: #49a84c;
 --third-color: #f6bb02;
 --fourth-color: #f6bb02;
 --fifth-color: #2196f3;
 display: flex;
 justify-content: center;
 align-items: center;
 width: 100px;
 gap: 6px;
 height: 100px;
}

.loading span {
 width: 4px;
 height: 50px;
 background: var(--first-color);
 animation: scale var(--speed-of-animation) ease-in-out infinite;
}

.loading span:nth-child(2) {
 background: var(--second-color);
 animation-delay: -0.8s;
}

.loading span:nth-child(3) {
 background: var(--third-color);
 animation-delay: -0.7s;
}

.loading span:nth-child(4) {
 background: var(--fourth-color);
 animation-delay: -0.6s;
}

.loading span:nth-child(5) {
 background: var(--fifth-color);
 animation-delay: -0.5s;
}

@keyframes scale {
 0%, 40%, 100% {
  transform: scaleY(0.05);
 }

 20% {
  transform: scaleY(1);
 }
}

.status_processed{
  color: #009d4f;

}
.status_process_error{
  color: #f44336;
}
.status_none{
  color: #fafafa;
}

</style>
