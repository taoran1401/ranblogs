<template>
    <div>
        <!-- 左（上下）右  -->
        <el-row type="flex">
            <el-col :md="18" :xs="24" :sm="24" >
                <div class="article-left">
                    <el-card>
                        <!-- 标题 -->
                        <div class="article-title">
                            <h1>{{data.title}}</h1>
                            <div class="article-count">
                                <nuxt-link :to="`/user/${data.userId}`" target="_blank" class="nickname">
                                    <i class="el-icon-user-solid"></i> {{data.nickName}}
                                </nuxt-link>
                                <span>
                                    <i class="el-icon-date"></i>  {{ getDateFormat(data.updateDate) }}
                                    <i class="el-icon-thumb"></i> {{data.thumhup}}
                                    <i class="el-icon-view"></i> {{data.viewCount}}
                                </span>
                                <nuxt-link v-if="$store.state.userInfo && $store.state.userInfo.uid === data.userId "
                                :to="{path: '/article/edit', query: {id: data.id} }" class="nickname">
                                    &nbsp; &nbsp; 编辑
                                </nuxt-link>
                            </div>
                            <el-tag style="margin: 5px;" v-for="item in data.labelList" :key="item.id" size="small">
                                {{item.name}}
                            </el-tag>
                            
                        </div>
                        <!-- 文章内容 -->
                        <div class="article-content">
                            <div class="markdown-body" v-html="data.htmlContent"></div>
                        </div>
                        <!-- plain为true，背景不是深蓝色，为false是深蓝色 -->
                        <el-button @click="handleThumb" icon="el-icon-thumb" 
                        type="primary" size="medium" :plain="!isThumb">
                            赞
                        </el-button>
                    </el-card>
                    <div>
                        <h2>评论区</h2>
                        <el-card v-if="!$store.state.userInfo">
                            <h4>登录后参与交流、获取后续更新提醒</h4>
                            <div>
                                <el-button @click="$store.dispatch('LoginPage')" type="primary" size="small">登录</el-button>
                                <el-button  @click="$store.dispatch('LoginPage')" type="primary" size="small" plain>注册</el-button>
                            </div>
                        </el-card>

                        <el-card>
                            <mxg-comment :userId="userId" :userImage="userImage"
                                :authorId="data.userId" 
                                :showComment="$store.state.userInfo ? true: false"
                                :commentList="commentList"
                                @doSend="doSend" @doChildSend="doChildSend" @doRemove="doRemove"
                            >
                            </mxg-comment>
                        </el-card>
                    </div>
                </div>
            </el-col>
            <el-col class="hidden-sm-and-down" :md="6">
                <el-row>
                    <el-col>
                        <mxg-affix :offset="80">
                           <mxg-directory parentClass="article-content"></mxg-directory>
                        </mxg-affix>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
    </div>
</template>
<script>
// 高度显示样式
import '@/assets/css/md/github-markdown.css'
import '@/assets/css/md/github-min.css'

import {dateFormat} from '@/utils/date.js'

// 固钉
import MxgAffix from '@/components/common/Affix/index.vue'
// 文章目录
import MxgDirectory from '@/components/common/Directory/index.vue'

// 评论组件
import MxgComment from '@/components/common/Comment'  
export default {
    components: {MxgAffix, MxgDirectory, MxgComment},

    // 校验id为数值才可访问此组件
   validate({params}) {
       return /^\d+$/.test(params.id)
   },

   head() {
       return {
           title: this.data.title // 浏览器中的标题
       }
   },

   data() {
       return {
           // 是否点赞
           isThumb: this.$cookies.get(`article-thumb-${this.$route.params.id}`) || false,
        //    当前登录用户id
           userId: this.$store.state.userInfo && this.$store.state.userInfo.uid,
        //    当前登录用户头像url
           userImage: this.$store.state.userInfo && this.$store.state.userInfo.imageUrl,
        //    commentList: []

       }
   },

   methods: {
       getDateFormat(date) {
           return dateFormat(date)
       },

       // 点赞
       async handleThumb() {
           // 取消点赞或者点赞
           this.isThumb = !this.isThumb
           // 1. 点赞，-1取消赞
           const count = this.isThumb ? 1: -1
           // 获取文章
           const articleId = this.$route.params.id
           const {code} = await this.$updateArticleThumb(articleId, count)
           if(code === 20000) {
               // 更新下当前文章页面显示的点赞数
              this.data.thumhup = this.data.thumhup+count
              // 保存cookie，永久保存
              this.$cookies.set(`article-thumb-${this.$route.params.id}`, this.isThumb, {
                  maxAge: 60*60*24*365*5 // 保存5年
              })
           }
       },
        // 公布评论
       doSend(content) {
        //    console.log('公布评论', content)
           this.doChildSend(content)
       },
        // 发布回复评论（回复内容，父评论id)
       doChildSend(content, parentId = "-1") {
            // console.log('发布回复评论（回复内容，父评论id', content, parentId)
            const data = {
               content,
               parentId,
               articleId: this.$route.params.id,
               userId: this.userId,
               userImage: this.userImage,
               nickName: this.$store.state.userInfo && this.$store.state.userInfo.nickName
           }
           this.$addComment(data).then(response => {
               // 新增评论成功
               if(response.code === 20000) {
                   // 刷新评论信息
                   this.refreshComment()
               }
           })
       },

       async doRemove(id) {
           const {code} = await this.$deleteCommentById(id)
           if( code === 20000 ) {
               // 删除成功，刷新评论
               this.refreshComment()
           }
        },

      // 查询评论列表数据
       async refreshComment() {
        //    console.log('refreshComment')
            const {data} = await this.$getCommentListByArticleId(this.$route.params.id)
            this.commentList = data
       }


       
   },

   async asyncData({params, app}) {
       // 1. 查询文章详情
        const {data} = await app.$getArticleById(params.id)

       // 2. 更新文章浏览数: 将本此查询文章的id保存到cookie, 
       // 如果说cookie存在此文章id，则不更新浏览器，如果不存在则更新，
       // 当浏览器关闭自动将此文章cookie的值把它删除，再它下次打开浏览器的时候，再去访问文章，文章浏览数就+1
       // a. 判断当前cookie是否存在此文章id
       const isView = app.$cookies.get(`aritcle-view-${params.id}`)
       if(!isView) {
           // 没有值 ，可以更新浏览数
           const {code} = await app.$updateArticleViewCount(params.id)
           if(code === 20000) {
               // 将此文章浏览数+1
               data.viewCount++
               // 保存cookie中, 关闭浏览器后会被删除
               app.$cookies.set(`aritcle-view-${params.id}`, true)
           }
       }

    //    console.log('文章详情', data)
      // 通过文章id查询所有评论列表信息
      const {data: commentList} = await app.$getCommentListByArticleId(params.id)
      return { data, commentList }
   }
}
</script>

<style scoped>
    @import '@/assets/css/blog/article.css';
</style>