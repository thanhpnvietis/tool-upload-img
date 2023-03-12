<template lang="">
  <Dialog
    v-model:visible="toggle"
    modal="true"
    @closeOnEscape="close()"
    :style="{ width: '90vw' }"
  >
    <template #header>
      <h3>{{ detail.title.rendered }}({{ detail.id }})</h3>
    </template>
    <div class="error">
      {{ msg_error }}
    </div>
    <div class="row">
      <div class="col-9">
        <TabView ref="tabview1" v-model:activeIndex="tabIndex">
          <TabPanel header="Tải ảnh lên">
            <FileUpload
              name="demo[]"
              :multiple="true"
              :customUpload="true"
              :showUploadButton="false"
              @select="uploadFile($event)"
              @clear="clearFile($event)"
              @remove="removeFile($event)"

              />
            <div>

              <div class="row g-3 align-items-center mt-1">
                <div class="col-2">
                  <label for="link-to" class="col-form-label">Tên thư mục</label>
                </div>
                <div class="col-auto">
                  <input type=""  v-model="folder_name"  class="form-control" />
                </div>
              </div>
            </div>
          </TabPanel>
          <TabPanel header="Chọn ảnh từ wordpress">
            <div class="box-img">
              
              <div  class="item-img" v-for="(item, index) in listImageWp" :key="index" v-on:click="selectImg(item.id)">

              <img :src="(item.media_details.sizes.medium||item.media_details.sizes.full).source_url" alt="">

                <div class="cntr" >
                  <input v-model="list_image_id" :value="item.id" :id="'cbx'+item.id" :true-value="[]"  type="checkbox" class=" ccbx hidden-xs-up">
                  <label :for="'cbx'+item.id" class="cbx"></label>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center">
              <span v-if="notFoundImage">
                Không tìm thấy ảnh
              </span>
              <Button v-else :disabled="loadListImage"  label="Load more" @click="loadMoreImage()" ></Button>
            </div>
          </TabPanel>
   
        </TabView>
      </div>
      <div class="col-3">
        <div class="row g-3 align-items-center mt-1">
          <div class="col-4">
            <label for="link-to" class="col-form-label">Link to</label>
          </div>
          <div class="col-auto">
            <select v-model="link_to" class="form-select" id="image-size">
              <option
                v-for="(item, index) in list_link_to"
                :key="index"
                :value="item.value"
                :selected="link_to === item.value"
              >
                {{ item.label }}
              </option>
            </select>
          </div>
        </div>
        <div class="row g-3 align-items-center mt-1">
          <div class="col-4">
            <label for="image-size" class="col-form-label">Image size</label>
          </div>
          <div class="col-auto">
            <select v-model="size" class="form-select" id="image-size">
              <option
                v-for="(item, index) in list_size"
                :key="index"
                :value="item.value"
                :selected="size === item.value"
              >
                {{ item.label }}
              </option>
            </select>
          </div>
        </div>
        <div class="row g-3 align-items-center mt-1">
          <div class="col-4">
            <label for="image-column" class="col-form-label">Columns</label>
          </div>
          <div class="col-auto">
            <select v-model="columns" class="form-select" id="image-column">
              <option
                v-for="(item, index) in list_columns"
                :key="index"
                :value="item.value"
                :selected="columns === item.value"
              >
                {{ item.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div :class="`${uploading == true ? 'show' : '1'} loader`">
      <div class="loading">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <template #footer>
      <Button label="Submit" @click="submit()" ></Button>
      <Button label="Close" @click="close()" ></Button>
    </template>
  </Dialog>
</template>
<script>
import { postStore } from "@/stores/posts";
const store = postStore();
export default {
  props: {
    display: {
      type: Boolean,
      default: false,
    },
    detail: {
      default: null,
    },
  },
  watch: {
    display(newData, old) {
      this.toggle = !this.toggle;
      if (this.toggle) {
        this.tabIndex = 0;
        this.loadImage();
      }else{
        this.closeModel();
      }
    },
    tabIndex(newData, old) {
      if(newData == 0){
        this.list_image_id = []
      }
      if(newData == 1){
        this.files = [];
        this.folder_name = '';
      }
    },
  },
  computed: {
    listImageWp() {
      return store.getListImage;
    },
    loadListImage() {
      return store.getLoadListImage;
    },
    notFoundImage() {
      return store.getNotFoundImage;
    },
  },

  data() {
    return {
      base_url_wp: this.$config.public.base_url_wp,
      base_url: this.$config.public.base_url,
      link: "",
      uploading: false,
      toggle: false,
      status_process: {
        none: "none",
        processed: "processed",
        error: "error",
      },
      msg_error: "",

      list_columns: [
        { label: "", value: "" },
        { label: "1", value: "1" },
        { label: "2", value: "2" },
        { label: "3", value: "3" },
        { label: "4", value: "4" },
        { label: "5", value: "5" },
        { label: "6", value: "6" },
        { label: "7", value: "7" },
        { label: "8", value: "8" },
        { label: "9", value: "9" },
      ],

      list_link_to: [
        // {label: '', value: ''},
        { value: "none", label: "None" },
        { value: "media", label: "Media File" },
        { value: "attachment", label: "Attachment Page" },
      ],

      list_size: [
        { value: "thumbnail", label: "Thumbnail" },
        { value: "medium", label: "Medium" },
        { value: "large", label: "Large" },
        { value: "full", label: "Full Size" },
      ],
      tabIndex:0,
      pageImg:1,
      list_image_id:[],
      files: [],
      files: [],
      link_to: "media",
      size: "",
      columns: "",
      folder_name: "",
      not_image:false
    };
  },
  created() {
  },
  methods: {
    submit(){},
    closeModel(){
      store.listImage = [];
      this.tabIndex = 0;
      this.pageImg = 0;
      this.list_image_id = [];
      this.files = [];
      this.link_to = "media";
      this.size = '';
      this.columns = '';
      this.folder_name = '';
    },
    loadMoreImage(){
      this.pageImg++;
      this.loadImage();
    },  
    async loadImage() {
      await store.acListImage({ page: this.pageImg });
    },
    uploadFile(event){
      for (let i = 0; i < event.files.length; i++) {
        this.files.push(event.files[i]);
      }
    },
    clearFile(event){
      this.files = [];
    },
    removeFile(event){
      this.files = [];
      for (let i = 0; i < event.files.length; i++) {
        this.files.push(event.files[i]);
      }
    },
    async submit(event) {
      this.uploading = true;
      this.error = "";
      let attachmentId = this.detail.attachment_id || {};
      attachmentId = [...attachmentId];
      let formData = new FormData();

      formData.append("delete_attachment_id", attachmentId);
      formData.append("list_image_id", this.list_image_id);
      formData.append("folder_name", this.folder_name);
      formData.append("link_to", this.link_to);
      formData.append("columns", this.columns);
      formData.append("size", this.size);
      formData.append("post_id", this.detail.id);

      for (let i = 0; i < this.files.length; i++) {
        formData.append("files[" + i + "]", this.files[i]);
      }

      $fetch(
        this.base_url_wp + "/wp-json/api_upload_file/v1/upload-file-to-post/",
        {
          method: "POST",
          body: formData,
        }
      ).then(async (e) => {
          let postComplete = await this.addPostComplete({
            post_id: this.detail.id,
            status: this.status_process.processed,
            image_id: e.data.map((item) => item.ID),
            image_url: e.data.map((item) => item.guid),
          });
          this.uploading = false;
          this.$toast.add({
            severity: "success",
            summary: "Thành công",
            detail: "Tải ảnh lên thành công",
            life: 3000,
          });
          this.$emit("updatePost", { postComplete });
          this.close();
        })
        .catch(async (e) => {

          this.error = e.message;
          this.uploading = false;
          let postComplete = await this.addPostComplete({
            post_id: this.detail.id,
            status: this.status_process.error,
            image_id: e.data.map((e) => e.id),
            image_url: e.data.map((e) => e.guid),
          });

          this.$toast.add({
            severity: "error",
            summary: "Lỗi",
            detail: "Tải ảnh lên bị lỗi",
            life: 3000,
          });
          this.$emit("updatePost", { postComplete });
          
        });
    },
    selectImg(id){
      document.querySelector('#cbx'+id).click()
    }, 
    async addPostComplete(param) {
      let body = {
        post_id: param.post_id,
        image_id: param.image_id,
        status: param.status,
        image_url: param.image_url,
      };
      let { result } = await $fetch(
        this.$config.public.base_url + "/api/post/add?col=posts",
        {
          method: "POST",
          body,
        }
      );

      return result;
    },

    close() {
      this.closeModel();
      this.toggle = false;
    },
  },
};
</script>
<style>
.loader.show {
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
  0%,
  40%,
  100% {
    transform: scaleY(0.05);
  }

  20% {
    transform: scaleY(1);
  }
}

.error {
  color: red;
}

.cbx {
 position: relative;
 top: 1px;
 width: 27px;
 height: 27px;
 /* border: 1px solid #c8ccd4; */
 border-radius: 3px;
 vertical-align: middle;
 transition: background 0.1s ease;
 cursor: pointer;
 display: block;
}

.cbx:after {
  content: '';
  position: absolute;
  top: 4px;
  left: 11px;
  width: 7px;
  height: 14px;
  opacity: 0;
  transform: rotate(45deg) scale(0);
  border-right: 2px solid #fff;
  border-bottom: 2px solid #fff;
  transition: all 0.3s ease;
  transition-delay: 0.15s;
}

.lbl {
 margin-left: 5px;
 vertical-align: middle;
 cursor: pointer;
}

.ccbx:checked ~ .cbx {
 border-color: transparent;
 background: #6871f1;
 animation: jelly 0.6s ease;
}

.ccbx:checked ~ .cbx:after {
 opacity: 1;
 transform: rotate(45deg) scale(1);
}

.cntr {
    position: absolute;
    top: 0;
    left:0;
}

@keyframes jelly {
 from {
  transform: scale(1, 1);
 }

 30% {
  transform: scale(1.25, 0.75);
 }

 40% {
  transform: scale(0.75, 1.25);
 }

 50% {
  transform: scale(1.15, 0.85);
 }

 65% {
  transform: scale(0.95, 1.05);
 }

 75% {
  transform: scale(1.05, 0.95);
 }

 to {
  transform: scale(1, 1);
 }
}

.hidden-xs-up {
 display: none!important;
}


.box-img{
  display: block;
}

.item-img img{
  object-fit: none;
}
.item-img {
    display: inline-block;
    position: relative;
    justify-content: center;
    width: 9rem;
    overflow: hidden;
    height: 10rem;
    margin-bottom: 2rem;
}

.box-img {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

</style>


