<?php
/**
 * RFQ (Request for Quotation) System for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_RFQ_System {

    public static function init() {
        add_action( 'wp_ajax_vh_submit_rfq', array( __CLASS__, 'handle_rfq_submission' ) );
        add_action( 'wp_ajax_nopriv_vh_submit_rfq', array( __CLASS__, 'handle_rfq_submission' ) );
        
        add_shortcode( 'vh_rfq_form', array( __CLASS__, 'render_rfq_form' ) );
    }

    /**
     * Render RFQ Form Shortcode
     */
    public static function render_rfq_form( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<div class="vh-container"><p>' . __( 'Please login as a Buyer to request a quote.', 'vendorhub' ) . '</p></div>';
        }

        ob_start();
        ?>
        <div class="vh-card vh-rfq-form-container">
            <h2><?php _e( 'Minta Penawaran (RFQ)', 'vendorhub' ); ?></h2>
            <p class="text-muted"><?php _e( 'Beritahu kami apa yang Anda butuhkan, dan vendor kami akan memberikan penawaran terbaik.', 'vendorhub' ); ?></p>
            
            <form id="vh-rfq-form" method="POST">
                <div class="vh-form-group" style="margin-bottom:1rem;">
                    <label><?php _e( 'Apa yang Anda cari?', 'vendorhub' ); ?></label>
                    <input type="text" name="rfq_subject" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--vh-border);" placeholder="Contoh: Laptop Office 20 Unit">
                </div>
                
                <div class="vh-form-group" style="margin-bottom:1rem;">
                    <label><?php _e( 'Detail Kebutuhan', 'vendorhub' ); ?></label>
                    <textarea name="rfq_details" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--vh-border); min-height:120px;" placeholder="Sebutkan spesifikasi, jumlah, dan deadline..."></textarea>
                </div>

                <div class="vh-grid" style="grid-template-columns: 1fr 1fr; margin-bottom:1rem;">
                    <div class="vh-form-group">
                        <label><?php _e( 'Estimasi Budget', 'vendorhub' ); ?></label>
                        <input type="number" name="rfq_budget" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--vh-border);">
                    </div>
                    <div class="vh-form-group">
                        <label><?php _e( 'Kuantitas', 'vendorhub' ); ?></label>
                        <input type="text" name="rfq_quantity" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--vh-border);" placeholder="Ex: 50 Box">
                    </div>
                </div>

                <button type="submit" class="vh-btn vh-btn-action" style="width:100%;"><?php _e( 'Kirim Permintaan', 'vendorhub' ); ?></button>
                <div id="vh-rfq-msg" style="margin-top:1rem; font-weight:600;"></div>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#vh-rfq-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $('#vh-rfq-msg').text('<?php _e( 'Mengirim...', 'vendorhub' ); ?>');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData + '&action=vh_submit_rfq',
                    success: function(response) {
                        if(response.success) {
                            $('#vh-rfq-msg').css('color', 'green').text(response.data);
                            $('#vh-rfq-form')[0].reset();
                        } else {
                            $('#vh-rfq-msg').css('color', 'red').text(response.data);
                        }
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle AJAX Submission
     */
    public static function handle_rfq_submission() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'Unauthorized login required.', 'vendorhub' ) );
        }

        $subject = sanitize_text_field( $_POST['rfq_subject'] );
        $details = sanitize_textarea_field( $_POST['rfq_details'] );
        $budget = sanitize_text_field( $_POST['rfq_budget'] );
        $quantity = sanitize_text_field( $_POST['rfq_quantity'] );

        $post_id = wp_insert_post( array(
            'post_title'   => $subject,
            'post_content' => $details,
            'post_status'  => 'publish',
            'post_type'    => 'vh_rfq',
        ) );

        if ( $post_id ) {
            update_post_meta( $post_id, '_vh_rfq_budget', $budget );
            update_post_meta( $post_id, '_vh_rfq_quantity', $quantity );
            wp_send_json_success( __( 'Permintaan Anda telah berhasil dikirim! Vendor akan segera menghubungi Anda.', 'vendorhub' ) );
        } else {
            wp_send_json_error( __( 'Gagal mengirim permintaan. Silakan coba lagi.', 'vendorhub' ) );
        }
    }
}

VH_RFQ_System::init();
