    <footer class="nh-footer">
        <div class="nh-container">
            <div class="nh-footer-top">
                <div class="nh-footer-col nh-footer-brand">
                    <a href="<?php echo home_url(); ?>" class="nh-logo">Niaga<span>HUB</span></a>
                    <p class="nh-footer-desc">
                        <?php _e('NiagaHUB adalah platform marketplace B2B dan pengadaan (procurement) nasional yang menghubungkan vendor terpercaya dengan perusahaan untuk efisiensi bisnis Anda.', 'niagahub-theme'); ?>
                    </p>
                    <div class="nh-social-links">
                        <a href="#"><span class="dashicons dashicons-facebook"></span></a>
                        <a href="#"><span class="dashicons dashicons-twitter"></span></a>
                        <a href="#"><span class="dashicons dashicons-instagram"></span></a>
                        <a href="#"><span class="dashicons dashicons-linkedin"></span></a>
                    </div>
                </div>

                <div class="nh-footer-col">
                    <h4><?php _e('Solusi Vendor', 'niagahub-theme'); ?></h4>
                    <ul class="nh-footer-links">
                        <li><a href="<?php echo home_url('/vendor/'); ?>"><?php _e('Direktori Vendor', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/produk/'); ?>"><?php _e('Marketplace Jasa', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/dashboard/?tab=adm-vendors'); ?>"><?php _e('Verifikasi Vendor', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/dashboard/?tab=settings'); ?>"><?php _e('Paket Membership', 'niagahub-theme'); ?></a></li>
                    </ul>
                </div>

                <div class="nh-footer-col">
                    <h4><?php _e('Solusi Buyer', 'niagahub-theme'); ?></h4>
                    <ul class="nh-footer-links">
                        <li><a href="<?php echo home_url('/tender/'); ?>"><?php _e('Pusat Tender', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/minta-penawaran/'); ?>"><?php _e('Sistem RFQ', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/dashboard/'); ?>"><?php _e('Manajemen Vendor', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/dashboard/'); ?>"><?php _e('Laporan Pengadaan', 'niagahub-theme'); ?></a></li>
                    </ul>
                </div>

                <div class="nh-footer-col">
                    <h4><?php _e('Bantuan & Kontak', 'niagahub-theme'); ?></h4>
                    <ul class="nh-footer-links">
                        <li><a href="<?php echo home_url('/#cara-kerja'); ?>"><?php _e('Cara Kerja', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/pusat-bantuan/'); ?>"><?php _e('Pusat Bantuan', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/kebijakan-privasi/'); ?>"><?php _e('Kebijakan Privasi', 'niagahub-theme'); ?></a></li>
                        <li><a href="<?php echo home_url('/#kontak'); ?>"><?php _e('Kontak Kami', 'niagahub-theme'); ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="nh-footer-middle">
                <div class="nh-partner-logos">
                    <span><?php _e('Partner Strategis & Sistem Pembayaran:', 'niagahub-theme'); ?></span>
                    <div class="nh-logos-grid">
                        <!-- Placeholders for actual logos later -->
                        <div class="nh-logo-placeholder"></div>
                        <div class="nh-logo-placeholder"></div>
                        <div class="nh-logo-placeholder"></div>
                        <div class="nh-logo-placeholder"></div>
                    </div>
                </div>
            </div>

            <div class="nh-footer-bottom">
                <div class="nh-copyright">
                    <p>&copy; <?php echo date('Y'); ?> <strong>NiagaHUB</strong>. <?php _e('Platform Marketplace & Procurement Terpadu. All rights reserved.', 'niagahub-theme'); ?></p>
                </div>
                <div class="nh-footer-meta">
                    <p><?php _e('Developed by', 'niagahub-theme'); ?> <a href="https://www.pilarteknologi.co.id/" target="_blank" style="color: inherit; font-weight: 700;">PT. Pilar Teknologi Solusi</a></p>
                </div>
            </div>
        </div>
    </footer>
    <script>
    var slideIndex = 1;
    showSlides(slideIndex);

    function currentSlide(n) {
      showSlides(slideIndex = n);
    }

    function showSlides(n) {
      var i;
      var wrapper = document.querySelector(".nh-slider-wrapper");
      var dots = document.getElementsByClassName("dot");
      var slides = document.getElementsByClassName("nh-slide");
      
      if (n > slides.length) {slideIndex = 1}    
      if (n < 1) {slideIndex = slides.length}
      
      if (wrapper) {
        wrapper.style.transform = "translateX(-" + ((slideIndex - 1) * 100) + "%)";
      }
      
      for (i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
      }
      if (dots.length > 0) {
        dots[slideIndex-1].classList.add("active");
      }
    }
    
    // Auto slide
    var slideInterval = setInterval(function() {
      slideIndex++;
      showSlides(slideIndex);
    }, 5000);

    // Pause on hover
    var slider = document.querySelector('.nh-main-slider');
    if (slider) {
        slider.addEventListener('mouseenter', function() {
            clearInterval(slideInterval);
        });
        slider.addEventListener('mouseleave', function() {
            slideInterval = setInterval(function() {
                slideIndex++;
                showSlides(slideIndex);
            }, 5000);
        });
    }
    </script>
    <?php wp_footer(); ?>
</body>
</html>
