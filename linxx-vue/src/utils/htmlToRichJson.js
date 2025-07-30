export function htmlToRichJson(htmlString) {
    const parser = new DOMParser()
    const doc = parser.parseFromString(htmlString, 'text/html')
    const out = []
    let prevBreak = false

    const blocks = [...doc.body.childNodes]

    for (const node of blocks) {
        const isHeading = /^H[1-6]$/.test(node.nodeName)
        const isParagraph = node.nodeName === 'P'

        if (!isHeading && !isParagraph) continue

        const children = []

        for (const child of node.childNodes) {
            if (child.nodeName === 'A') {
                const t = child.textContent.trim()
                if (t) children.push({ type: 'link', href: child.getAttribute('href'), text: t })
            } else if (child.nodeName === 'IMG') {
                children.push({ type: 'emoji', src: child.getAttribute('src'), alt: child.getAttribute('alt') || '' })
            } else if (child.nodeType === Node.TEXT_NODE || child.nodeName === 'SPAN' || child.nodeName === 'STRONG') {
                const txt = child.textContent.trim()
                if (txt) children.push({ type: 'text', text: txt })
            }
        }

        const isEmptyPara = children.length === 0

        if (isEmptyPara) {
            if (!prevBreak) out.push({ type: 'line-break' })
            prevBreak = true
        } else {
            if (isHeading) {
                const level = Number(node.nodeName[1])
                out.push({ type: 'heading', level, children })
            } else {
                out.push({ type: 'paragraph', children })
            }
            prevBreak = false
        }
    }

    while (out.length && out[out.length - 1].type === 'line-break') out.pop()
    return out
}
