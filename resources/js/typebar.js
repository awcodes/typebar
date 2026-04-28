let row = null
let input = null

document.addEventListener('focusin', (event) => {
    const wrapper = event.target.closest('[data-typebar]')

    if (!wrapper) {
        return
    }

    if (wrapper.dataset.typebarMobile === 'true' && !isMobile()) {
        return
    }

    const element = findInput(wrapper)

    if (!element) {
        return
    }

    render(wrapper, element)
})

document.addEventListener('focusout', () => {
    setTimeout(() => {
        if (
            document.activeElement === input ||
            document.activeElement?.closest('.tb-row')
        ) {
            return
        }

        destroy()
    }, 150)
})

function isMobile() {
    return window.matchMedia('(pointer: coarse)').matches
}

function findInput(wrapper) {
    return wrapper.querySelector('textarea')
        || wrapper.querySelector('[contenteditable]')
        || wrapper.querySelector('[role="textbox"]')
}

function render(wrapper, element) {
    destroy()

    const keys = parse(wrapper.dataset.typebarKeys, [])
    const pairs = parse(wrapper.dataset.typebarPairs, {})

    if (!keys.length) {
        return
    }

    const container = document.createElement('div')

    container.className = 'tb-row'
    container.setAttribute('role', 'toolbar')
    container.setAttribute('aria-label', 'Typebar')

    keys.forEach((key) => {
        const button = document.createElement('button')

        button.type = 'button'
        button.textContent = key
        button.setAttribute('aria-label', `Insert ${key}`)

        button.addEventListener('pointerdown', (event) => {
            event.preventDefault()

            insert(element, key, pairs[key])
        })

        container.appendChild(button)
    })

    document.body.appendChild(container)

    row = container
    input = element
}

function insert(element, key, pair = null) {
    element.focus()

    if (element.tagName === 'TEXTAREA' || element.tagName === 'INPUT') {
        insertIntoInput(element, key, pair)

        return
    }

    insertIntoEditable(element, key)
}

function insertIntoInput(element, key, pair = null) {
    const start = element.selectionStart
    const end = element.selectionEnd
    const value = pair ? key + pair : key

    element.setRangeText(value, start, end, pair ? 'start' : 'end')

    if (pair) {
        element.selectionStart = start + key.length
        element.selectionEnd = start + key.length
    }

    element.dispatchEvent(new Event('input', { bubbles: true }))
}

function insertIntoEditable(element, key) {
    const selection = window.getSelection()

    if (!selection || selection.rangeCount === 0) {
        return
    }

    const range = selection.getRangeAt(0)

    range.deleteContents()
    range.insertNode(document.createTextNode(key))
    range.collapse(false)

    selection.removeAllRanges()
    selection.addRange(range)

    element.dispatchEvent(new Event('input', { bubbles: true }))
}

function destroy() {
    row?.remove()

    row = null
    input = null
}

function parse(value, fallback) {
    if (!value) {
        return fallback
    }

    try {
        return JSON.parse(value)
    } catch {
        return fallback
    }
}
