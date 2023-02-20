<template lang="">
<Dialog v-model:visible="toggle" dismissableMask="false" modal="true" @closeOnEscape="close()" :style="{width: '50vw'}">
  <template #header>
		<h3>{{detail.title.rendered}}({{detail.id}})</h3>
	</template>
	<FileUpload name="demo[]" :multiple="true" :customUpload="true" @uploader="myUploader" />
	<template #footer>
    <Button label="close" @click="close()" accept="image/*" />
  </template>
</Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";
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
  data() {
    return {
      files:[],
      toast: useToast()
    }
  },
  watch: {
    display(newData, old) {
      this.toggle = !this.toggle
    }
  },
  
  data() {
    return {
      toggle:false
    }
  },

  methods: {
    async myUploader(event){
   
      let formData = new FormData();

      formData.append('post_id', this.detail.id);

      for( let i = 0; i < event.files.length; i ++ ){
          formData.append('files['+i+']', event.files[i] );
      }

      $fetch( 'http://tooluploadfile.local/wp-json/api_upload_file/v1/upload-file-to-post/', {
        method: 'POST',
        body: formData
      }).then(async e=>{
        await this.addPostComplete({
          post_id:this.detail.id,
          image_id:e.data
        })
        this.$emit('updatePost', {postId:this.detail.id});
        this.toggle = false;
      }).catch(e=>{

      });
    },

    async addPostComplete(param){
      let body = {
        post_id:param.post_id,
        image_id:param.image_id
      }
      let res = await $fetch( 'http://localhost:3000/api/post/add?col=posts', {
        method: 'POST',
        body
      })
    },

   

    showSuccess(){
      this.toast.add({severity:'success', summary: 'Success Message', detail:'Message Content', life: 3000});
    },
    showError(){
      this.toast.add({severity:'error', summary: 'Error Message', detail:'Message Content', life: 3000});
    },

    close(){
      this.toggle = false;
    }
  },
}
</script>
<style >
  
</style>
