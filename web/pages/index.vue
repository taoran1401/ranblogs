<template>
  <div>
    <el-row type="flex" justify="space-between">
      <!-- 左侧 -->
      <el-col class="hidden-sm-and-down" :md="3">
        <el-divider content-position="left">技术频道</el-divider>
        <el-menu
            :default-active="$route.path"
            active-text-color="#ffffff"
            router
            >
            <el-menu-item index="/">
              推荐
            </el-menu-item>
            <el-menu-item v-for="item in categoryList" :key="item.id" :index="'/'+item.id">
              {{item.name}}
            </el-menu-item>
          </el-menu>
      </el-col>
      <!-- 中间 -->
      <el-col :md="16">
        <div class="blog-center">
          <div class="banner">
            <el-carousel height="230px">
              <el-carousel-item v-for="item in mainAdvertList" :key="item.id">
                <a :href="item.advertUrl" :target="item.advertTarget">
                  <img :src="item.imageUrl" >
                </a>
              </el-carousel-item>
            </el-carousel>
          </div>
          <!-- 文章列表 -->
          <nuxt-child />
        </div>
      </el-col>
      <!-- 右侧广告区 -->
      <el-col class="hidden-sm-and-down" :md="5">
         <el-card class="right-card" shadow="hover" :body-style="{padding: '10px'}">
            <p>课程推荐</p>
            <el-carousel height="210px">
              <el-carousel-item v-for="item in courseAdvertList" :key="item.id">
                <a :href="item.advertUrl" :target="item.advertTarget">
                  <img :src="item.imageUrl" >
                  <span>{{item.title}}</span>
                </a>
              </el-carousel-item>
            </el-carousel>
          </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  
  async asyncData({app}) {
    // 1. 查询技术频道
    const {data: categoryList} = await app.$getCategoryList()
    
    // 2. 查询滚动图广告
    const {data: mainAdvertList} = await app.$getAdvertList(1)
    // 3. 查询课程推荐广告
    const {data: courseAdvertList} = await app.$getAdvertList(2)
    return {categoryList, mainAdvertList, courseAdvertList}
  },
  
}
</script>
<style scoped>
/* 局部引入外部样式文件 */
  @import '@/assets/css/blog/index.css';
</style>
