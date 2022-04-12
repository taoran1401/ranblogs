<template>
  <div>
    <!-- 头部 -->
    <mxg-header />
    <!-- 中间主体区域 -->
    <div class="mxg-main">
      <nuxt />
    </div>
    <!-- 底部 -->
    <mxg-footer />
    <!-- 回到顶部组件 bottom 是图标距离底部高度 -->
     <el-backtop :bottom="80"></el-backtop>
  </div>
</template>
<script>
import MxgHeader from '@/components/layout/Header'
import MxgFooter from '@/components/layout/Footer'
export default {
  components: {
    MxgHeader, // MxgHeader: MxgHeader
    MxgFooter
  }
}
</script>
<style>
.mxg-main {
  padding-top: 90px;
  max-width: 1140px;
  margin: 0 auto;
}
</style>
