<?php
/**
 * Rating & Review System for NiagaHUB
 * Buyer bisa beri rating ke vendor setelah proyek selesai
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Rating {

    public static function init() {
        add_action('wp_ajax_vh_submit_rating', [__CLASS__, 'handle_submit_rating']);
        add_shortcode('vh_rating_form',  [__CLASS__, 'render_rating_form']);
        add_shortcode('vh_vendor_reviews', [__CLASS__, 'render_reviews']);
        add_action('the_content', [__CLASS__, 'append_reviews_to_vendor']);
    }

    /**
     * Auto-inject reviews on vendor profile pages (author archive)
     */
    public static function append_reviews_to_vendor($content) {
        if (!is_author()) return $content;
        $vendor_id = get_queried_object_id();
        if (!vh_is_vendor($vendor_id)) return $content;
        return $content . self::render_reviews(['user_id' => $vendor_id]);
    }

    /**
     * Get average rating for a vendor
     */
    public static function get_average_rating($vendor_id) {
        $reviews = get_posts([
            'post_type'   => 'vh_review',
            'numberposts' => -1,
            'meta_query'  => [['key' => '_vh_review_vendor_id', 'value' => $vendor_id]],
        ]);
        if (empty($reviews)) return null;
        $total = array_sum(array_map(fn($r) => (float)get_post_meta($r->ID, '_vh_review_rating', true), $reviews));
        return round($total / count($reviews), 1);
    }

    public static function get_review_count($vendor_id) {
        return count(get_posts(['post_type' => 'vh_review', 'numberposts' => -1, 'meta_query' => [['key' => '_vh_review_vendor_id', 'value' => $vendor_id]]]));
    }

    public static function render_stars($rating, $max = 5) {
        $html = '<span style="color:#f59e0b;font-size:1.1rem;">';
        for ($i = 1; $i <= $max; $i++) {
            $html .= $i <= round($rating) ? '★' : '☆';
        }
        return $html . '</span>';
    }

    /**
     * Render rating form shortcode: [vh_rating_form vendor_id="123"]
     */
    public static function render_rating_form($atts = []) {
        if (!is_user_logged_in() || !vh_is_buyer()) return '';
        $atts      = shortcode_atts(['vendor_id' => 0, 'tender_id' => 0], $atts);
        $vendor_id = intval($atts['vendor_id']);
        $tender_id = intval($atts['tender_id']);
        if (!$vendor_id) return '';

        $user_id  = get_current_user_id();
        $existing = get_posts([
            'post_type'  => 'vh_review',
            'author'     => $user_id,
            'meta_query' => [['key' => '_vh_review_vendor_id', 'value' => $vendor_id]],
            'numberposts'=> 1,
        ]);

        ob_start(); ?>
        <div class="vh-card" style="padding:2rem;margin-top:2rem;">
            <h3 style="margin:0 0 1.25rem;">⭐ Beri Ulasan</h3>

            <?php if ($existing): ?>
                <p style="color:#065f46;background:#d1fae5;padding:1rem;border-radius:8px;">✅ Anda sudah memberikan ulasan untuk vendor ini.</p>
            <?php else: ?>
            <form id="vh-rating-form">
                <input type="hidden" name="vendor_id" value="<?= $vendor_id ?>">
                <input type="hidden" name="tender_id" value="<?= $tender_id ?>">

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-weight:600;margin-bottom:0.5rem;">Rating</label>
                    <div class="vh-star-rating" style="font-size:2rem;cursor:pointer;color:#d1d5db;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="vh-star" data-val="<?= $i ?>" style="transition:color 0.1s;">★</span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="vh-rating-val" required>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-weight:600;margin-bottom:0.5rem;">Ulasan</label>
                    <textarea name="review" rows="4" required placeholder="Bagikan pengalaman Anda bekerja sama dengan vendor ini..."
                              style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>

                <button type="submit" class="vh-btn vh-btn-action" style="padding:10px 24px;">Kirim Ulasan</button>
                <div id="vh-rating-msg" style="margin-top:1rem;font-weight:600;"></div>
            </form>

            <style>
            .vh-star-rating .vh-star:hover, .vh-star-rating .vh-star.active { color: #f59e0b; }
            </style>
            <script>
            jQuery(function($) {
                var rated = 0;
                $('.vh-star').on('mouseover', function() {
                    var val = $(this).data('val');
                    $('.vh-star').each(function() { $(this).css('color', $(this).data('val') <= val ? '#f59e0b' : '#d1d5db'); });
                }).on('mouseout', function() {
                    $('.vh-star').each(function() { $(this).css('color', $(this).data('val') <= rated ? '#f59e0b' : '#d1d5db'); });
                }).on('click', function() {
                    rated = $(this).data('val');
                    $('#vh-rating-val').val(rated);
                    $('.vh-star').removeClass('active').each(function() {
                        if ($(this).data('val') <= rated) $(this).addClass('active');
                    });
                });

                $('#vh-rating-form').on('submit', function(e) {
                    e.preventDefault();
                    if (!$('#vh-rating-val').val()) { $('#vh-rating-msg').css('color','#991b1b').text('Pilih rating bintang terlebih dahulu.'); return; }
                    jQuery.post('<?= admin_url('admin-ajax.php') ?>', {
                        action: 'vh_submit_rating',
                        ...Object.fromEntries(new FormData(this))
                    }, function(res) {
                        if (res.success) {
                            $('#vh-rating-msg').css('color','#065f46').text(res.data);
                            $('#vh-rating-form').slideUp();
                        } else {
                            $('#vh-rating-msg').css('color','#991b1b').text(res.data);
                        }
                    });
                });
            });
            </script>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render reviews list: [vh_vendor_reviews user_id="123"]
     */
    public static function render_reviews($atts = []) {
        $atts      = shortcode_atts(['user_id' => get_queried_object_id()], $atts);
        $vendor_id = intval($atts['user_id']);
        $reviews   = get_posts([
            'post_type'   => 'vh_review',
            'numberposts' => -1,
            'meta_query'  => [['key' => '_vh_review_vendor_id', 'value' => $vendor_id]],
            'orderby'     => 'date',
            'order'       => 'DESC',
        ]);

        $avg   = self::get_average_rating($vendor_id);
        $count = count($reviews);

        ob_start(); ?>
        <div class="vh-card" style="padding:2rem;margin-top:2rem;">
            <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                <div>
                    <h3 style="margin:0 0 0.25rem;font-size:1.25rem;">Ulasan & Rating</h3>
                    <?php if ($avg): ?>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <?= self::render_stars($avg) ?>
                        <span style="font-size:1.5rem;font-weight:700;"><?= $avg ?></span>
                        <span style="color:#6b7280;font-size:0.9rem;">(<?= $count ?> ulasan)</span>
                    </div>
                    <?php else: ?>
                    <p style="color:#9ca3af;margin:0;">Belum ada ulasan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($reviews): foreach ($reviews as $review):
                $rating     = get_post_meta($review->ID, '_vh_review_rating', true);
                $buyer_id   = $review->post_author;
                $buyer_name = get_userdata($buyer_id)->display_name;
            ?>
            <div style="border-top:1px solid #f3f4f6;padding:1rem 0;">
                <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                    <strong><?= esc_html($buyer_name) ?></strong>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <?= self::render_stars($rating) ?>
                        <span style="font-size:0.8rem;color:#6b7280;"><?= date('d M Y', strtotime($review->post_date)) ?></span>
                    </div>
                </div>
                <p style="margin:0;color:#374151;font-size:0.9rem;line-height:1.6;"><?= nl2br(esc_html($review->post_content)) ?></p>
            </div>
            <?php endforeach;
            else: ?>
            <p style="color:#9ca3af;text-align:center;padding:1rem 0;">Belum ada ulasan untuk vendor ini.</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX: Submit Rating
     */
    public static function handle_submit_rating() {
        if (!is_user_logged_in() || !vh_is_buyer()) {
            wp_send_json_error('Login sebagai Buyer diperlukan.');
        }
        $vendor_id = intval($_POST['vendor_id']);
        $tender_id = intval($_POST['tender_id'] ?? 0);
        $rating    = intval($_POST['rating']);
        $review    = sanitize_textarea_field($_POST['review']);
        $user_id   = get_current_user_id();

        if (!$vendor_id || $rating < 1 || $rating > 5 || !$review) {
            wp_send_json_error('Data tidak valid.');
        }

        // Verify transaction if tender_id is provided
        if ($tender_id) {
            $winner = get_post_meta($tender_id, '_vh_tender_winner_id', true);
            if ($winner != $vendor_id) {
                wp_send_json_error('Vendor ini bukanlah pemenang dari tender tersebut.');
            }
        }

        $existing = get_posts(['post_type' => 'vh_review', 'author' => $user_id, 'meta_query' => [['key' => '_vh_review_vendor_id', 'value' => $vendor_id]], 'numberposts' => 1]);
        if ($existing) {
            wp_send_json_error('Anda sudah memberikan ulasan.');
        }

        $id = wp_insert_post([
            'post_title'   => 'Review for Vendor #' . $vendor_id,
            'post_content' => $review,
            'post_type'    => 'vh_review',
            'post_status'  => 'publish',
            'post_author'  => $user_id,
        ]);

        if ($id) {
            update_post_meta($id, '_vh_review_vendor_id', $vendor_id);
            update_post_meta($id, '_vh_review_rating',    $rating);
            if ($tender_id) update_post_meta($id, '_vh_review_tender_id', $tender_id);
            wp_send_json_success('✅ Ulasan berhasil dikirim. Terima kasih!');
        } else {
            wp_send_json_error('Gagal mengirim ulasan.');
        }
    }
}

VH_Rating::init();
