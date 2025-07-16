document.addEventListener('DOMContentLoaded', function() {
    const summaryArea = document.querySelector('.summary.entry-summary');
    if (!summaryArea) return;

    let whatsAppButton = null;
    let productTitleEl = null;

    const updateWhatsAppLink = () => {
        // We re-query the button each time in case the DOM is completely replaced.
        whatsAppButton = document.querySelector('.div_evowap_btn a.evowap_btn');
        productTitleEl = document.querySelector('h1.product_title.entry-title');

        if (!whatsAppButton || !productTitleEl) return;

        const finalPriceEl = document.querySelector('.booking-pricing-info .booking_cost li.total .price .woocommerce-Price-amount');
        const originalHref = whatsAppButton.getAttribute('href');
        const phoneMatch = originalHref.match(/wa.me\/(\d+)/);

        if (!phoneMatch) return;
        const phoneNumber = phoneMatch[1];
        
        const customMessage = document.querySelector('#woapp_message') ? document.querySelector('#woapp_message').value : "Olá! Tenho interesse neste item:";
        const productName = productTitleEl.innerText;
        const productLink = window.location.href;
        
        let priceText = '';

        // Check if the final calculated rental price is visible and has a value
        if (finalPriceEl && finalPriceEl.offsetParent !== null && finalPriceEl.innerText.trim() !== '') {
            priceText = 'Total: ' + finalPriceEl.innerText.trim();
        } else {
            // Fallback to the initial per-day price if the total isn't available
            const initialPriceEl = document.querySelector('p.price:not(.rnb-panel-header) .amount');
            if(initialPriceEl) priceText = initialPriceEl.innerText.trim().replace('/Diária', ' / diária');
        }

        if (priceText) {
            const fullMessage = `${customMessage}\n\n*Produto:* ${productName}\n*Valor:* ${priceText}\n\n*Link:* ${productLink}`;
            const newHref = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(fullMessage)}`;
            
            // Only update the href if it has actually changed to prevent infinite loops
            if (whatsAppButton.getAttribute('href') !== newHref) {
                whatsAppButton.setAttribute('href', newHref);
            }
        }
    };

    // This observer will watch for ANY change within the product summary area.
    const observer = new MutationObserver((mutationsList, observer) => {
        // A small delay ensures the rental plugin has finished its calculations before we read the price.
        setTimeout(updateWhatsAppLink, 150);
    });

    observer.observe(summaryArea, {
        childList: true,
        subtree: true,
        attributes: true
    });

    // Run the update function once after a short delay to catch the initial state.
    setTimeout(updateWhatsAppLink, 500);
});