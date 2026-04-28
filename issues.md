1. That's Safari's input accessory bar (the ↑↓ navigation + Done row). It sits between our bar and the QWERTY keys, and                     
   visualViewport.height only accounts for the QWERTY portion — so our keyboardHeight calculation doesn't include it, leaving our bar       
   underneath. The cleanest fix: start the bar hidden on mobile, let visualViewport resize events update the position during the keyboard animation, and
only reveal once the keyboard height is substantial. That way we never show the bar in a wrong position — it pops in already correctly  
placed. A fallback timeout covers external keyboards and edge cases.
2. fullscreen toggling doesn't keep the editor field on screen. when entering fullscreen mode i have to scroll up to the top of the editor to see where the cursor is. and when exiting fullscreen mode the field is no longer at the same scroll position on the page.
3. would it be better to have the bar be fixed to the bottom of the editor field instead of floating in the page? This would require adjusting the positioning logic to ensure the bar remains visible and accessible regardless of scroll position.
