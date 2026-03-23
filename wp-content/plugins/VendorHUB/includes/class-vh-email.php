<?php
/**
 * Email & SMTP Helper for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Email {

    public static function init() {
        add_action('phpmailer_init', [__CLASS__, 'configure_smtp']);
        add_filter('wp_mail_from', [__CLASS__, 'get_from_email']);
        add_filter('wp_mail_from_name', [__CLASS__, 'get_from_name']);
        add_filter('wp_mail_content_type', function() { return 'text/html'; });

        // Hooks for specific events
        add_action('vh_proposal_submitted', [__CLASS__, 'send_notif_proposal'], 10, 3);
        add_action('vh_winner_selected',    [__CLASS__, 'send_notif_winner'], 10, 2);
    }

    /**
     * Configure PHPMailer to use custom SMTP
     */
    public static function configure_smtp($phpmailer) {
        $host = get_option('vh_smtp_host');
        if (empty($host)) return;

        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = get_option('vh_smtp_port', 587);
        $phpmailer->Username   = get_option('vh_smtp_user');
        $phpmailer->Password   = get_option('vh_smtp_pass');
        $phpmailer->SMTPSecure = get_option('vh_smtp_encryption', 'tls');
        if ($phpmailer->SMTPSecure === 'none') $phpmailer->SMTPSecure = '';
    }

    public static function get_from_email($original) {
        return get_option('vh_from_email', $original);
    }

    public static function get_from_name($original) {
        return get_option('vh_from_name', $original);
    }

    /**
     * Base Template Wrapper
     */
    public static function wrap_template($title, $content) {
        $logo_url = 'https://pilarteknologi.co.id/wp-content/uploads/2021/04/Logo-Pilar-Teknologi-Solusi.png'; // Fallback
        return '
        <div style="background:#f1f5f9; padding:40px 20px; font-family:Sans-Serif;">
            <div style="max-width:600px; margin:0 auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                <div style="background:#1e293b; padding:24px; text-align:center;">
                    <h1 style="color:white; margin:0; font-size:24px;">Niaga<span style="color:#f97316;">HUB</span></h1>
                </div>
                <div style="padding:40px;">
                    <h2 style="margin-top:0; color:#1e293b;">'.$title.'</h2>
                    <div style="color:#475569; line-height:1.6; font-size:16px;">
                        '.$content.'
                    </div>
                    <div style="margin-top:32px; padding-top:24px; border-top:1px solid #e2e8f0; text-align:center;">
                        <a href="'.home_url('/dashboard').'" style="display:inline-block; background:#f97316; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:700;">Buka Dashboard</a>
                    </div>
                </div>
                <div style="background:#f8fafc; padding:20px; text-align:center; color:#94a3b8; font-size:12px;">
                    &copy; '.date('Y').' NiagaHUB. PT. Pilar Teknologi Solusi.<br>
                    Pesan ini dikirimkan secara otomatis, mohon tidak membalas email ini.
                </div>
            </div>
        </div>';
    }

    /**
     * Send: New Proposal Notification
     */
    public static function send_notif_proposal($proposal_id, $tender_id, $vendor_id) {
        $tender = get_post($tender_id);
        if (!$tender) return;
        
        $buyer    = get_userdata($tender->post_author);
        $vendor   = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_userdata($vendor_id)->display_name;
        
        $title    = '📬 Proposal Baru: ' . esc_html($tender->post_title);
        $content  = 'Halo <strong>' . esc_html($buyer->display_name) . '</strong>,<br><br>
                    Vendor <strong>' . esc_html($vendor) . '</strong> baru saja mengirimkan penawaran/proposal untuk proyek tender Anda: 
                    <br><strong>"' . esc_html($tender->post_title) . '"</strong>.<br><br>
                    Silakan lihat rincian penawaran, spesifikasi, dan harga melalui dashboard Anda.';
        
        $html = self::wrap_template('Proposal Baru Masuk', $content);
        wp_mail($buyer->user_email, $title, $html);
    }

    /**
     * Send: Winner Selected Notification
     */
    public static function send_notif_winner($proposal_id, $tender_id) {
        $proposal = get_post($proposal_id);
        $tender   = get_post($tender_id);
        if (!$proposal || !$tender) return;

        $vendor_user = get_userdata($proposal->post_author);
        
        $title   = '🏆 Selamat! Anda Terpilih Pemenang';
        $content = 'Halo <strong>' . esc_html($vendor_user->display_name) . '</strong>,<br><br>
                   Kami dengan senang hati menginformasikan bahwa proposal Anda telah dipilih sebagai <strong>Pemenang</strong> 
                   untuk proyek tender:<br><strong>"' . esc_html($tender->post_title) . '"</strong>.<br><br>
                   Segera hubungi buyer melalui detail informasi di dashboard untuk koordinasi lebih lanjut.';
        
        $html = self::wrap_template('Selamat! Proposal Anda Terpilih', $content);
        wp_mail($vendor_user->user_email, $title, $html);
    }
}
