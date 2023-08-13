new Vue({
    // container element
    el: '#container',
    // app data
    data: {
        // current theme
        theme: 'light',
        // articles array
        articles: [null, null]
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
        },
        // method to view article page
        viewArticlesPage() {
            // navigate to articles page
            window.location.href = '../articles-list'
        }
    },
    async mounted() {
        const data = await API.getArticles(1, 2)
        setTimeout(() => this.articles = data.entry, 500)
    }
})