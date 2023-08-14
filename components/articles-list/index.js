const app = new Vue({
    // container element
    el: '#container',
    // app data
    data: {
        // current theme
        theme: 'light',
        // busy flag
        busy: false,
        // articles array
        articles: [null, null, null],
        // current page
        page: 1,
        // limit per page
        limit: 3,
        // end of results flag
        ended: false,
        // total articles
        total: 3,
        // all categories
        categories: ['All Categories'],
        // search queries
        search: {
            // keyword filter
            keyword: '',
            // category filter
            category: 'All Categories'
        }
    },
    // methods
    methods: {
        // method to generate css background rule
        background(...args) {
            return toCSSBackground(...args)
        },
        // method to generate ellipsized text
        ellipsis(...args) {
            return ellipsisText(...args)
        },
        // method to generate date and read time string
        date(item) {
            // return if null item
            if (item === null) { return '' }
            // get date
            const date = toDateString(item?.updated?.$t)
            // get read time string
            const read = toReadTime(item?.duration)
            // return string
            return `${date} • ${read}`
        },
        // method to load articles
        async loadArticles(reset = false) {
            // return if busy or set busy
            if (this.busy) { return } else { this.busy = true }
            // check reset flag
            if (reset) {
                // set loading items on array
                this.articles = [null, null, null]
                // scroll into top
                this.$el.scrollIntoView()
            }
            // reset ended flag
            this.edned = false
            // increase current page
            this.page = reset ? 1 : this.page + 1
            // get current category
            const category = this.search.category !== 'All Categories'
                ? this.search.category : ''
            // get articles
            const data = await API.getArticles(this.page, this.limit, this.search.keyword, category)
            // set empty array if no entry node
            if('entry' in data === false) { data.entry = [] }
            // loading delay
            setTimeout(() => {
                // check reset mode
                if (reset) {
                    // set on articles
                    this.articles = data?.entry
                    // check categories
                    if ('category' in data) {
                        // set all categories
                        this.categories = [
                            // all category mode
                            'All Categories',
                            // map categories
                            ...data?.category?.map(item => item.term)
                        ]
                    }
                } else {
                    // push to articles
                    this.articles.push(...data?.entry)
                }
                // update ended flag
                this.ended = this.limit > data?.entry.length
                // clear busy state
                this.busy = false
            }, 500)
        },
        // method to search articles
        searchArticles(event) {
            // return if keydown for typing
            if (event.type === 'keydown' && event.key !== 'Enter') { return }
            // load articles
            this.loadArticles(true)
        },
        // method to view article page
        viewArticle(item) {
            // return if invalid item
            if (item === null) { return }
            // find self link
            const link = item.link.find(item => item.rel === 'self')
            // return if no link
            if (link === undefined) { return }
            // get link data
            const data = link.href.split('/')
            // navigate to article
            window.location.href = `../article-view?id=${data[7]}`
        }
    },
    async mounted() {
        // load first articles
        this.loadArticles(true)
    }
})