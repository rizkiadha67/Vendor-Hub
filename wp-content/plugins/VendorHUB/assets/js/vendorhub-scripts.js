/**
 * NiagaHUB Main Scripts
 */
jQuery(document).ready(function($) {
    // Slider Functionality
    let slideIndex = 1;
    const slides = $('.nh-slide');
    const dots = $('.dot');

    function showSlides(n) {
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        
        slides.removeClass('active');
        dots.removeClass('active');
        
        slides.eq(slideIndex-1).addClass('active');
        dots.eq(slideIndex-1).addClass('active');
    }

    // Auto Advance
    let slideTimer = setInterval(function() {
        slideIndex++;
        showSlides(slideIndex);
    }, 5000);

    // Dot Click
    window.currentSlide = function(n) {
        clearInterval(slideTimer);
        slideIndex = n;
        showSlides(slideIndex);
        // Restart timer
        slideTimer = setInterval(function() {
            slideIndex++;
            showSlides(slideIndex);
        }, 5000);
    }

    // Enquiry / Message Handle
    $('.vh-enquiry-btn').on('click', function() {
        if(!confirm('Kirim pesan otomatis ke vendor ini?')) return;
        
        const btn = $(this);
        const vendorId = btn.data('vendor');
        const productName = btn.data('product');
        
        btn.prop('disabled', true).text('Mengirim...');

        $.ajax({
            url: vh_auth_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'vh_send_message',
                to_user_id: vendorId,
                message: 'Halo, saya tertarik dengan produk "' + productName + '". Bisakah kita berdiskusi lebih lanjut?',
                thread_id: 'product_inquiry'
            },
            success: function(res) {
                if(res.success) {
                    alert('Pesan berhasil dikirim! Vendor akan membalas di Dashboard Anda.');
                    btn.text('Terkirim').css('background', 'green');
                } else {
                    alert('Gagal: ' + res.data);
                    btn.prop('disabled', false).text('Coba Lagi');
                }
            }
        });
    });
});
