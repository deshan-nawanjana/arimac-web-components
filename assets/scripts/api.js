// backend root path
const root = 'https://apis.dnjs.info/arimac-web'

// api object
const API = {}

// method to get articles
API.getArticles = async (page, limit, search = "", category = "") => {
    // request articles
    const resp = await fetch(root + '/posts?' + new URLSearchParams({
        page, limit, search, category: category
    }))
    // return parsed response
    return await resp.json()
}