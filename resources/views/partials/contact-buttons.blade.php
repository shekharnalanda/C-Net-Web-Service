@if(!request()->is('admin*'))
<div class="cnet-contact-buttons" aria-label="Quick contact options">
    <a
        class="cnet-contact-button cnet-whatsapp"
        href="https://wa.me/917782801846?text={{ urlencode('नमस्कार, मुझे C-Net Web Services की सेवाओं के बारे में जानकारी चाहिए।') }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="WhatsApp पर संपर्क करें"
    >
        <span aria-hidden="true">💬</span>
        <span>WhatsApp</span>
    </a>

    <a
        class="cnet-contact-button cnet-call"
        href="tel:+917782801846"
        aria-label="फोन करें"
    >
        <span aria-hidden="true">☎</span>
        <span>Call Now</span>
    </a>
</div>

<style>
.cnet-contact-buttons {
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.cnet-contact-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-width: 132px;
    padding: 11px 16px;
    border-radius: 999px;
    color: #fff !important;
    font: 700 14px/1.2 Arial, sans-serif;
    text-decoration: none !important;
    box-shadow: 0 5px 18px rgba(0,0,0,.24);
    transition: transform .2s ease, box-shadow .2s ease;
}
.cnet-contact-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(0,0,0,.3);
}
.cnet-whatsapp { background: #128c4b; }
.cnet-call { background: #1558d6; }

@media (max-width: 600px) {
    .cnet-contact-buttons {
        right: 10px;
        bottom: 10px;
        flex-direction: row;
    }
    .cnet-contact-button {
        min-width: auto;
        padding: 11px 13px;
    }
    .cnet-contact-button span:last-child {
        display: none;
    }
}
</style>
@endif
