<template lang="">
  <Dialog v-model:visible="toggle" dismissableMask="false" modal="true" @closeOnEscape="close()" :style="{width: '50vw'}">
    <template #header>
      <h2> Danh sách ảnh ({{detail.id}})</h2>
    </template>
    <div class="item-img mb-3" v-if="detail.image_url.length" v-for="(item, index) in detail.image_url" :key="index">
      <img :src="item" alt="">
    </div>
    <div v-else>
       Không có dữ liệu
    </div>
    <template #footer>
      <Button class="mt-2" label="Đóng" @click="close()" ></Button>
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
    data() {
      return {
        toggle:false
      }
    },
    methods: {
      close(){
        this.toggle = false;
      }
    },
  }
  </script>
  <style >
  .item-img img{
    max-width: 100%;
    
  }
  .item-img {
    display: flex;
    justify-content: center;
  }
  </style>
  