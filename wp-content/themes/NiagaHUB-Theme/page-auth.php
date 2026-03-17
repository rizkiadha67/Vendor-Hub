<?php
/**
 * Template Name: NiagaHUB Auth (Login/Register)
 */

get_header(); ?>

<style>
    .nh-auth-section {
        background: #f8fafc;
        min-height: calc(100vh - 100px);
        display: flex;
        align-items: center;
        padding: 4rem 1.5rem;
    }
    .nh-auth-card {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
    }
    .nh-auth-branding {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        padding: 4rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }
    .nh-auth-branding::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, var(--primary-color) 0%, transparent 70%);
        opacity: 0.1;
        filter: blur(40px);
    }
    .nh-auth-branding h2 {
        color: white;
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 2rem;
        line-height: 1.2;
    }
    .nh-auth-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .nh-auth-features li {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
        font-size: 15px;
        opacity: 0.9;
    }
    .nh-auth-features .dashicons {
        color: var(--primary-color);
        background: rgba(255,255,255,0.1);
        padding: 8px;
        border-radius: 50%;
        font-size: 16px;
        width: 16px;
        height: 16px;
    }
    .nh-auth-form-wrap {
        padding: 4rem;
    }
    .nh-auth-tabs {
        display: flex;
        gap: 2rem;
        margin-bottom: 2.5rem;
        border-bottom: 2px solid #f1f5f9;
        position: relative;
    }
    .auth-tab, .auth-tabactive {
        background: none;
        border: none;
        padding: 12px 0;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #64748b;
        position: relative;
    }
    .auth-tabactive {
        color: var(--dark-color);
    }
    .auth-tabactive::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    @media (max-width: 992px) {
        .nh-auth-branding { padding: 3rem; }
        .nh-auth-form-wrap { padding: 3rem; }
        .nh-auth-branding h2 { font-size: 1.8rem; }
    }

    @media (max-width: 768px) {
        .nh-auth-section { padding: 2rem 1rem; }
        .nh-auth-card { grid-template-columns: 1fr; border-radius: 20px; }
        .nh-auth-branding { 
            padding: 3rem 2rem; 
            text-align: center;
            align-items: center;
        }
        .nh-auth-branding h2 { font-size: 1.5rem; margin-bottom: 1.5rem; }
        .nh-auth-features { display: none; } /* Hide features on small mobile to save vertical space */
        .nh-auth-form-wrap { padding: 2.5rem 1.5rem; }
        .nh-auth-tabs { gap: 1.5rem; margin-bottom: 2rem; }
        .auth-tab, .auth-tabactive { font-size: 1rem; }
    }
</style>

<div class="nh-auth-section">
    <div class="nh-auth-card">
        <!-- Left Side: Branding/Info -->
        <div class="nh-auth-branding">
            <h2><?php _e('Bergabung dengan Jaringan B2B Terbesar', 'niagahub-theme'); ?></h2>
            <ul class="nh-auth-features">
                <li><span class="dashicons dashicons-yes"></span> <?php _e('Akses ke ribuan vendor terverifikasi', 'niagahub-theme'); ?></li>
                <li><span class="dashicons dashicons-yes"></span> <?php _e('Sistem Tender & RFQ transparan', 'niagahub-theme'); ?></li>
                <li><span class="dashicons dashicons-yes"></span> <?php _e('Manajemen pengadaan yang efisien', 'niagahub-theme'); ?></li>
            </ul>
        </div>

        <!-- Right Side: The Form -->
        <div class="nh-auth-form-wrap">
            <div id="auth-tabs" class="nh-auth-tabs">
                <button class="auth-tabactive" data-target="login-form"><?php _e('Masuk', 'niagahub-theme'); ?></button>
                <button class="auth-tab" data-target="register-form"><?php _e('Daftar Akun', 'niagahub-theme'); ?></button>
            </div>

            <!-- Auth Message Message -->
            <div id="auth-msg" style="margin-bottom: 1.5rem; font-weight: 700; text-align: center; font-size: 14px;"></div>

                <!-- Login Form -->
                <form id="login-form" class="auth-form">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Email atau Username', 'niagahub-theme'); ?></label>
                        <input type="text" name="log" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Password', 'niagahub-theme'); ?></label>
                        <input type="password" name="pwd" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <button type="submit" class="vh-btn vh-btn-primary" style="width: 100%; padding: 12px;"><?php _e('Masuk Sekarang', 'niagahub-theme'); ?></button>
                </form>

                <!-- Register Form (Hidden by default) -->
                <form id="register-form" class="auth-form" style="display: none;">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Username Bisnis', 'niagahub-theme'); ?></label>
                        <input type="text" name="user_login" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Email Bisnis', 'niagahub-theme'); ?></label>
                        <input type="email" name="user_email" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Nama Perusahaan', 'niagahub-theme'); ?></label>
                        <input type="text" name="vh_company_name" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;" placeholder="Contoh: PT Niaga Jaya">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Lokasi Bisnis', 'niagahub-theme'); ?></label>
                        <input type="text" name="vh_location" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;" placeholder="Contoh: Jakarta Selatan">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;"><?php _e('Daftar Sebagai:', 'niagahub-theme'); ?></label>
                        <div class="auth-role-selector" style="display: flex; gap: 1rem;">
                            <label class="role-option active" style="flex: 1; padding: 15px; border: 2px solid var(--primary-color); border-radius: 12px; text-align: center; cursor: pointer; transition: 0.3s; background: #fff9f2;">
                                <input type="radio" name="user_role" value="buyer" checked style="display: none;">
                                <span class="dashicons dashicons-cart" style="display: block; font-size: 24px; width: 24px; height: 24px; margin: 0 auto 5px;"></span>
                                <strong><?php _e('Cari Vendor', 'niagahub-theme'); ?></strong>
                                <small style="display: block; font-size: 11px; opacity: 0.7;"><?php _e('(Buyer)', 'niagahub-theme'); ?></small>
                            </label>
                            <label class="role-option" style="flex: 1; padding: 15px; border: 2px solid #f1f5f9; border-radius: 12px; text-align: center; cursor: pointer; transition: 0.3s;">
                                <input type="radio" name="user_role" value="vendor" style="display: none;">
                                <span class="dashicons dashicons-businessman" style="display: block; font-size: 24px; width: 24px; height: 24px; margin: 0 auto 5px;"></span>
                                <strong><?php _e('Jadi Vendor', 'niagahub-theme'); ?></strong>
                                <small style="display: block; font-size: 11px; opacity: 0.7;"><?php _e('(Supplier)', 'niagahub-theme'); ?></small>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="vh-btn vh-btn-action" style="width: 100%; padding: 12px;"><?php _e('Buat Akun Baru', 'niagahub-theme'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.auth-tab, .auth-tabactive').click(function() {
        var target = $(this).data('target');
        $('.auth-form').hide();
        $('#' + target).fadeIn();
        $('#auth-msg').text('');
        
        $(this).siblings().css({
            'border-bottom': 'none',
            'color': 'var(--vh-text-muted)'
        }).removeClass('auth-tabactive').addClass('auth-tab');
        
        $(this).css({
            'border-bottom': '3px solid var(--primary-color)',
            'color': 'var(--vh-text-dark)'
        }).removeClass('auth-tab').addClass('auth-tabactive');
    });

    // Role Selector UI Toggle
    $('.role-option').click(function() {
        $(this).siblings().removeClass('active').css({
            'border-color': '#f1f5f9',
            'background': 'white'
        });
        $(this).addClass('active').css({
            'border-color': 'var(--primary-color)',
            'background': '#fff9f2'
        });
    });


    // Handle Login
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $('#auth-msg').text('<?php _e('Checking credentials...', 'niagahub-theme'); ?>').css('color', 'var(--vh-text-dark)');

        $.ajax({
            url: vh_auth_obj.ajax_url,
            type: 'POST',
            data: data + '&action=vh_ajax_login&security=' + vh_auth_obj.nonce,
            success: function(res) {
                if(res.success) {
                    $('#auth-msg').text(res.data.message).css('color', 'green');
                    window.location.href = res.data.redirect;
                } else {
                    $('#auth-msg').text(res.data).css('color', 'red');
                }
            }
        });
    });

    // Handle Registration
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $('#auth-msg').text('<?php _e('Creating account...', 'niagahub-theme'); ?>').css('color', 'var(--vh-text-dark)');

        $.ajax({
            url: vh_auth_obj.ajax_url,
            type: 'POST',
            data: data + '&action=vh_ajax_register&security=' + vh_auth_obj.nonce,
            success: function(res) {
                if(res.success) {
                    $('#auth-msg').text(res.data.message).css('color', 'green');
                    window.location.href = res.data.redirect;
                } else {
                    $('#auth-msg').text(res.data).css('color', 'red');
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
