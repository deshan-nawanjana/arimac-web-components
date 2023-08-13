// method to create background css rule
const toCSSBackground = (source, resize) => {
    // return if invalid source
    if (typeof source !== 'string') { return null }
    // check resize parameter
    if (resize) {
        // resize for post image
        if (source.includes('s72-')) { source = source.replace('s72-', `s${resize}-`) }
        // resize for author image
        if (source.includes('/s220/')) { source = source.replace('/s220/', `/s${resize}/`) }
    }
    // return background rule
    return { backgroundImage: `url(${source})` }
}

// method to ellipsis string
const ellipsisText = (input, length) => {
    // return if invalid input
    if (typeof input !== 'string') { return '' }
    // return same if shorter than length
    if (input.length < length) { return input }
    // get substring
    const output = input.substring(0, length)
    // check for char at length
    if ([' ', '.', ','].includes(input[length])) {
        // return same output
        return output + '...'
    } else {
        // remove word break
        return output.substring(0, output.lastIndexOf(' ')) + '...'
    }
}

// method to get date string
const toDateString = input => {
    // return if invalid input
    if (typeof input !== 'string') { return '' }
    // create date object
    const date = new Date(input)
    // split date string into items
    const data = date.toString().split(' ')
    // return date string
    return `${data[1]} ${data[2]}, ${data[3]}`
}

// method to get read time string
const toReadTime = input => {
    // return default time for invalid input
    if (typeof input !== 'number') { return '10 min read' }
    // return time in minutes
    return `${parseInt(input / 60)} min read`
}