let row = null
let input = null
let currentWrapper = null
let pressing = false

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
    requestAnimationFrame(() => {
        if (
            pressing ||
            input?.contains(document.activeElement) ||
            document.activeElement?.closest('.tb-row')
        ) {
            return
        }

        destroy()
    })
})

function isMobile() {
    return window.matchMedia('(pointer: coarse)').matches
}

function findInput(wrapper) {
    const codeMirrorEl = wrapper.querySelector('.CodeMirror')
    if (codeMirrorEl?.CodeMirror) {
        return codeMirrorEl
    }

    return (
        wrapper.querySelector('textarea') ||
        wrapper.querySelector('[contenteditable]') ||
        wrapper.querySelector('[role="textbox"]')
    )
}

function positionRow() {
    if (!row || !window.visualViewport) {
        return
    }

    const vv = window.visualViewport
    const keyboardHeight = window.innerHeight - vv.height - vv.offsetTop
    row.style.bottom = Math.max(0, keyboardHeight) + 'px'
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

    if (wrapper.dataset.typebarMobile === 'true') {
        container.dataset.mobileOnly = ''
    }

    const easyMDEContainer = wrapper.querySelector('.EasyMDEContainer')
    if (easyMDEContainer) {
        const isFullscreen = easyMDEContainer.classList.contains('tb-fullscreen')

        const fsButton = document.createElement('button')
        fsButton.type = 'button'
        fsButton.textContent = '⛶'
        fsButton.className = 'tb-fs-btn' + (isFullscreen ? ' tb-fs-active' : '')
        fsButton.setAttribute('aria-label', isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen')

        bindButton(fsButton, container, () => {
            const active = easyMDEContainer.classList.toggle('tb-fullscreen')
            document.body.classList.toggle('tb-fullscreen-active', active)
            fsButton.classList.toggle('tb-fs-active', active)
            fsButton.setAttribute('aria-label', active ? 'Exit fullscreen' : 'Enter fullscreen')
        })

        container.appendChild(fsButton)

        const sep = document.createElement('span')
        sep.className = 'tb-sep'
        sep.setAttribute('role', 'separator')
        sep.setAttribute('aria-orientation', 'vertical')
        container.appendChild(sep)
    }

    keys.forEach((key) => {
        const button = document.createElement('button')
        button.type = 'button'
        button.textContent = key
        button.setAttribute('aria-label', `Insert ${key}`)

        bindButton(button, container, () => insert(element, key, pairs[key]))

        container.appendChild(button)
    })

    document.body.appendChild(container)

    row = container
    input = element
    currentWrapper = wrapper

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', positionRow)
        window.visualViewport.addEventListener('scroll', positionRow)
        positionRow()
    }
}

function bindButton(button, scrollContainer, action) {
    let startX = 0
    let startScrollLeft = 0
    let moved = false

    // touchstart fires before iOS decides to move focus away from the input,
    // so preventDefault() here prevents keyboard dismissal on iPhone/iPad.
    // We record the scroll position so touchmove can drive it manually —
    // preventDefault() also cancels native scrolling, so we do it ourselves.
    button.addEventListener('touchstart', (event) => {
        event.preventDefault()
        pressing = true
        moved = false
        startX = event.touches[0].clientX
        startScrollLeft = scrollContainer.scrollLeft
    }, { passive: false })

    button.addEventListener('touchmove', (event) => {
        const dx = event.touches[0].clientX - startX
        if (Math.abs(dx) > 8) {
            moved = true
            scrollContainer.scrollLeft = startScrollLeft - dx
        }
    })

    button.addEventListener('touchend', () => {
        pressing = false
        if (!moved) {
            action()
        }
    })

    button.addEventListener('touchcancel', () => {
        pressing = false
        moved = false
    })

    // pointerdown handles mouse and pen input (touchstart doesn't fire for those).
    button.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'touch') return
        event.preventDefault()
        pressing = true
        action()
    })

    button.addEventListener('pointerup', () => { pressing = false })
    button.addEventListener('pointercancel', () => { pressing = false })
}

function insert(element, key, pair = null) {
    if (element.CodeMirror) {
        insertIntoCodeMirror(element.CodeMirror, key, pair)
        return
    }

    element.focus()

    if (element.tagName === 'TEXTAREA' || element.tagName === 'INPUT') {
        insertIntoInput(element, key, pair)
        return
    }

    insertIntoEditable(element, key)
}

function insertIntoCodeMirror(cm, key, pair = null) {
    if (!cm.hasFocus()) {
        cm.focus()
    }

    if (pair) {
        const selection = cm.getSelection()
        if (selection) {
            cm.replaceSelection(key + selection + pair)
        } else {
            cm.replaceSelection(key + pair)
            const cursor = cm.getCursor()
            cm.setCursor({ line: cursor.line, ch: cursor.ch - pair.length })
        }
    } else {
        cm.replaceSelection(key)
    }
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
    if (window.visualViewport) {
        window.visualViewport.removeEventListener('resize', positionRow)
        window.visualViewport.removeEventListener('scroll', positionRow)
    }

    row?.remove()
    row = null
    input = null
    currentWrapper = null
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
