<template>
    <div>
        <el-tabs value="hot" type="card" @tab-click="handleClick">
            <el-tab-pane label="热门回答" name="hot">
                <List name="hot" :listData="listData" :page="page" @fetch-data="fetchData"/>
            </el-tab-pane>
            <el-tab-pane label="最新问答" name="new">
               <List name="new" :listData="listData" :page="page" @fetch-data="fetchData"/>
            </el-tab-pane>
            <el-tab-pane label="等待回答" name="wait">
               <List name="wait" :listData="listData" :page="page" @fetch-data="fetchData"/>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
import List from '@/components/question/List'

export default {
    components: {List},

    methods: {
        handleClick(tab) {
            // console.log(tab.paneName)
            this.fetchData(tab.paneName, 1)
        },

        // 获取分页数据（标签名，当前页码）
        async fetchData(paneName, current) {
            // 分页查询数据
            this.page.current = current
            let response = null
            switch (paneName) {
                case 'hot':
                    // 查询热门
                    response = await this.$getHotList(this.page)
                    break;
                case 'new':
                    response = await this.$getNewList(this.page)
                    break;
                case 'wait':
                    response = await this.$getWaitList(this.page)
                    break;
                default:
                    break;
            }

            if(response && response.code === 20000) {
                // 更新总记录数
                this.page.total = response.data.total
                // 列表数据
                this.listData = response.data.records
            }
        }
    },

    async asyncData({app}) {
      const page = {
          total: 0, // 总记录数
          current: 1, // 当前页码 
          size: 20 // 每页显示20条数据
      }
      const {data} = await app.$getHotList(page)
      // 赋值总记录数
      page.total = data.total
      
      return {page, listData: data.records}
    },
}
</script>