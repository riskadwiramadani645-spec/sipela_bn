// Dashboard Fix Script
document.addEventListener('DOMContentLoaded', function() {
    // Force hide spinner immediately
    hideSpinner();
    
    // Enable all interactive elements
    enableInteractions();
    
    // Remove any blocking overlays
    removeBlockingElements();
});

// Additional safety checks
window.addEventListener('load', function() {
    setTimeout(hideSpinner, 100);
    setTimeout(enableInteractions, 200);
});

// Force hide spinner on any page interaction
document.addEventListener('click', function() {
    hideSpinner();
    enableInteractions();
});

function hideSpinner() {
    const spinner = document.getElementById('spinner');
    if (spinner) {
        spinner.classList.remove('show');
        spinner.style.display = 'none';
        spinner.style.visibility = 'hidden';
        spinner.style.opacity = '0';
    }
}

function enableInteractions() {
    // Enable body interactions
    document.body.style.pointerEvents = 'auto';
    document.body.style.overflow = 'auto';
    
    // Enable all interactive elements
    const interactiveElements = document.querySelectorAll('button, a, .btn, input, select, textarea');
    interactiveElements.forEach(element => {
        element.style.pointerEvents = 'auto';
        if (element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
            element.disabled = false;
        }
    });
}

function removeBlockingElements() {
    // Remove any overlay that might be blocking interactions
    const overlays = document.querySelectorAll('.overlay, .loading-overlay, .modal-backdrop.show');
    overlays.forEach(overlay => {
        if (!overlay.closest('.modal.show')) {
            overlay.remove();
        }
    });
}

// Debug function for troubleshooting
function debugPageState() {
    console.log('=== DEBUG PAGE STATE ===');
    console.log('Spinner:', document.getElementById('spinner'));
    console.log('Body pointer events:', getComputedStyle(document.body).pointerEvents);
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('jQuery available:', typeof $ !== 'undefined');
    console.log('Modal elements:', document.querySelectorAll('.modal').length);
    console.log('========================');
}

// Call debug on console
window.debugPageState = debugPageState;