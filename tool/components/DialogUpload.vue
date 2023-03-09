<template lang="">
<Dialog v-model:visible="toggle" modal="true" @closeOnEscape="close()" :style="{width: '50vw'}">
  <template #header>
		<h3>{{detail.title.rendered}}({{detail.id}})</h3>
	</template>
  <div class="error">
    {{msg_error}}
  </div>
	<FileUpload name="demo[]" :multiple="true" :customUpload="true" @uploader="myUploader" />

  <div class="row g-3 align-items-center mt-1">

    <div class="col-2">
      <label for="link-to" class="col-form-label">LINK TO</label>
    </div>

    <div class="col-auto">
      <select  v-model="link_to"  class="form-select" id="image-size" >
        <option v-for="(item, index) in list_link_to" :key="index" :value="item.value" :selected="link_to === item.value" >{{item.label}}</option>
      </select>
    </div>

  </div>

  <div class="row g-3 align-items-center mt-1">
    <div class="col-2">
      <label for="image-size" class="col-form-label">IMAGE SIZE</label>
    </div>
    <div class="col-auto">
      <select v-model="size" class="form-select" id="image-size" >
        <option v-for="(item, index) in list_size" :key="index" :value="item.value" :selected="size === item.value" >{{item.label}}</option>
      </select>
    </div>
  </div>
  <div class="row g-3 align-items-center mt-1">
    <div class="col-2">
      <label for="image-column" class="col-form-label">COLUMNS</label>
    </div>
    <div class="col-auto">
      <select  v-model="columns"  class="form-select" id="image-column" >
        <option v-for="(item, index) in list_columns" :key="index" :value="item.value" :selected="columns === item.value" >{{item.label}}</option>
      </select>
    </div>
  </div>

  <div :class="`${uploading == true ? 'show' : '1'} loader`" >
    <div class="loading">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <template #footer>
    <Button label="close" @click="close()" accept="image/*" ></Button>
  </template>
</Dialog>
</template>
<script>
export default {
  props: {
    display: {
      type: Boolean,
      default: false
    },
    detail: {
      default: null
    },
  },
  watch: {
    display(newData, old) {
      this.toggle = !this.toggle
    }
  },
  created() {
    console.log(this.uploading);
  },
  data() {
    return {

      base_url_wp: this.$config.public.base_url_wp,
      base_url: this.$config.public.base_url,
      files:[],
      link:'',
      uploading: false,
      toggle:false,
      status_process :{
        none: 'none',
        processed: 'processed',
        error: 'error'
      },
      msg_error: '',

      list_columns: [
        {label: '', value: ''},
        {label: '1', value: '1'},
        {label: '2', value: '2'},
        {label: '3', value: '3'},
        {label: '4', value: '4'},
        {label: '5', value: '5'},
        {label: '6', value: '6'},
        {label: '7', value: '7'},
        {label: '8', value: '8'},
        {label: '9', value: '9'},
      ],

      list_link_to:[
        // {label: '', value: ''},
        {value: 'none', label: 'None'},
        {value: 'media', label: 'Media File'},
        {value: 'attachment', label: 'Attachment Page'},
      ],

      list_size:[
        {value: 'thumbnail', label: 'Thumbnail'},
        {value: 'medium', label: 'Medium'},
        {value: 'large', label: 'Large'},
        {value: 'full', label: 'Full Size'},
      ],

      link_to: "media",
      size: null,
      columns: null,

    }
  },

  methods: {
    async myUploader(event){
      this.uploading = true;
      this.error = '';
      let attachmentId = this.detail.attachment_id || {};
      attachmentId = [...attachmentId];
      let formData = new FormData();

      formData.append('delete_attachment_id', attachmentId);
      formData.append('post_id', this.detail.id);

      formData.append('link', this.link);
      formData.append('columns', this.columns);
      formData.append('large', this.large);

      for( let i = 0; i < event.files.length; i ++ ){
          formData.append('files['+i+']', event.files[i] );
      }

      $fetch( this.base_url_wp+'/wp-json/api_upload_file/v1/upload-file-to-post/', {
        method: 'POST',
        body: formData
      }).then(async e =>{
        console.log('res',e);
        let postComplete =  await this.addPostComplete({
          post_id:this.detail.id,
          status: this.status_process.processed,
          image_id:e.data.map(item=>item.ID),
          image_url:e.data.map(item=>item.guid), 
        });
        this.uploading = false;
        this.$toast.add({severity:'success', summary: 'Thành công', detail:'Tải ảnh lên thành công', life: 3000});
        this.$emit('updatePost', {postComplete});
      }).catch(async e=>{
        console.log('error',e);
        this.error = e.message;
        this.uploading = false
        let postComplete =  await this.addPostComplete({
          post_id:this.detail.id,
          status: this.status_process.error,
          image_id:e.data.map(e=>e.id),
          image_url:e.data.map(e=>e.guid), 
        });

        this.$toast.add({severity:'error', summary: 'Lỗi', detail:'Tải ảnh lên bị lỗi', life: 3000});
        this.$emit('updatePost', {postComplete});
      });
    },

    async addPostComplete(param){
      let body = {
        post_id:param.post_id,
        image_id:param.image_id,
        status:param.status,
        image_url:param.image_url,
      }
      let {result} = await $fetch( this.$config.public.base_url+'/api/post/add?col=posts', {
        method: 'POST',
        body
      })
      
      return result
    },

    close(){
      this.toggle = false;
    }
  },
}
</script>
<style >
  
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

.error{
  color: red;
}
</style>
