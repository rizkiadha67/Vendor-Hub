<?php
/**
 * Proposal System for NiagaHUB
 * Vendor kirim proposal ke tender, buyer bisa pilih pemenang
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Proposal_System {

    public static function init() {
        add_action( 'wp_ajax_vh_submit_proposal', [ __CLASS__, 'handle_submit_proposal' ] );
        add_action( 'wp_ajax_vh_select_winner',   [ __CLASS__, 'handle_select_winner' ] );
        add_action( 'wp_ajax_vh_close_tender',    [ __CLASS__, 'handle_close_tender' ] );
        add_action( 'the_content',                [ __CLASS__, 'append_proposal_form' ] );
        add_shortcode( 'vh_proposal_form',        [ __CLASS__, 'render_proposal_form' ] );
    }

    /**
     * Auto-inject proposal form at bottom of single tender page
     */
    public static function append_proposal_form( $content ) {
        if ( ! is_singular('vh_tender') || ! in_the_loop() ) return $content;
        if ( vh_is_vendor() ) {
            $content .= self::render_proposal_form();
        }
        if ( vh_is_buyer() || current_user_can('administrator') ) {
            $content .= self::render_proposal_list( get_the_ID() );
        }
        return $content;
    }

    /**
     * Render proposal submission form for vendors
     */
    public static function render_proposal_form( $atts = [] ) {
        if ( ! is_user_logged_in() || ! vh_is_vendor() ) return '';

        $tender_id = get_the_ID();
        $status    = get_post_meta( $tender_id, '_vh_tender_status', true ) ?: 'open';
        $user_id   = get_current_user_id();

        // Check if already submitted
        $existing = get_posts([
            'post_type'   => 'vh_proposal',
            'author'      => $user_id,
            'meta_query'  => [[ 'key' => '_vh_proposal_tender_id', 'value' => $tender_id ]],
            'numberposts' => 1,
        ]);

        ob_start();
        ?>
        <div id="vh-proposal-form" class="vh-card" style="margin-top:2rem;padding:2rem;">
            <h3 style="margin:0 0 1.25rem;font-size:1.25rem;">📝 Kirim Proposal Anda</h3>

            <?php if ($status !== 'open'): ?>
                <div style="background:#fee2e2;color:#991b1b;padding:1rem;border-radius:8px;">Tender ini sudah ditutup dan tidak menerima proposal baru.</div>

            <?php elseif ($existing): 
                $prop_status = get_post_meta($existing[0]->ID, '_vh_proposal_status', true) ?: 'pending';
                $badges = ['pending'=>['#fef3c7','#92400e','⏳ Menunggu'],'accepted'=>['#d1fae5','#065f46','✅ Diterima'],'rejected'=>['#fee2e2','#991b1b','❌ Ditolak']];
                $b = $badges[$prop_status] ?? $badges['pending']; ?>
                <div style="background:<?= $b[0] ?>;color:<?= $b[1] ?>;padding:1rem;border-radius:8px;">
                    <?= $b[2] ?> — Proposal Anda sudah terkirim dan sedang dalam proses evaluasi.
                    <?php $budget = get_post_meta($existing[0]->ID, '_vh_proposal_budget', true); if ($budget): ?>
                    <br><strong>Penawaran Anda: Rp <?= number_format($budget,0,',','.') ?></strong>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <form id="vh-proposal-form-el">
                    <?php wp_nonce_field('vh_proposal_nonce','vh_proposal_nonce_field'); ?>
                    <input type="hidden" name="tender_id" value="<?= $tender_id ?>">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                        <div>
                            <label style="display:block;font-weight:600;margin-bottom:0.4rem;">💰 Nilai Penawaran (Rp)</label>
                            <input type="number" name="proposal_budget" required placeholder="cth: 15000000"
                                   style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block;font-weight:600;margin-bottom:0.4rem;">⏱ Estimasi Pengerjaan</label>
                            <input type="text" name="proposal_timeline" placeholder="cth: 14 hari kerja"
                                   style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                        </div>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-weight:600;margin-bottom:0.4rem;">📄 Deskripsi Penawaran</label>
                        <textarea name="proposal_content" rows="5" required placeholder="Jelaskan solusi, metodologi, dan keunggulan penawaran Anda..."
                                  style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-weight:600;margin-bottom:0.4rem;">🏆 Pengalaman Relevan (opsional)</label>
                        <textarea name="proposal_experience" rows="3" placeholder="Sebutkan proyek serupa yang pernah dikerjakan..."
                                  style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;background:#f8fafc;padding:1.25rem;border-radius:12px;border:1px dashed #e2e8f0;">
                         <div>
                            <label style="display:block;font-weight:600;margin-bottom:0.4rem;font-size:0.9rem;">📂 Lampiran Dokumen (Compro/Spek)</label>
                            <input type="file" name="proposal_file" style="font-size:12px;width:100%;">
                            <small style="color:#64748b;display:block;margin-top:4px;">PDF, DOCX Maks 5MB</small>
                         </div>
                         <div>
                            <label style="display:block;font-weight:600;margin-bottom:0.4rem;font-size:0.9rem;">🔗 Hubungkan Produk Katalog</label>
                            <select name="proposal_products[]" multiple style="width:100%;padding:8px;border-radius:8px;border:1px solid #e2e8f0;font-size:13px;min-height:60px;">
                                <?php
                                $my_products = get_posts(['post_type'=>'vh_product','author'=>$user_id,'posts_per_page'=>-1]);
                                if ($my_products) {
                                    foreach ($my_products as $p) {
                                        echo '<option value="'.$p->ID.'">'.esc_html($p->post_title).'</option>';
                                    }
                                } else {
                                    echo '<option disabled>Belum ada produk di katalog</option>';
                                }
                                ?>
                            </select>
                            <small style="color:#64748b;display:block;margin-top:4px;">Tahan CTRL/CMD untuk pilih lebih dari satu</small>
                         </div>
                    </div>

                    <button type="submit" class="vh-btn vh-btn-action" style="width:100%;padding:12px;">🚀 Kirim Proposal</button>
                    <div id="vh-proposal-msg" style="margin-top:1rem;font-weight:600;text-align:center;"></div>
                </form>

                <script>
                jQuery('#vh-proposal-form-el').on('submit', function(e) {
                    e.preventDefault();
                    var $btn = jQuery(this).find('button[type=submit]');
                    var formData = new FormData(this);
                    formData.append('action', 'vh_submit_proposal');

                    jQuery.ajax({
                        url: '<?= admin_url('admin-ajax.php') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                        if (res.success) {
                            jQuery('#vh-proposal-msg').css('color','#065f46').text(res.data);
                            jQuery('#vh-proposal-form-el')[0].reset();
                            $btn.hide();
                        } else {
                            jQuery('#vh-proposal-msg').css('color','#991b1b').text(res.data);
                            $btn.text('Kirim Proposal').prop('disabled', false);
                        }
                    });
                });
                </script>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render list of proposals for buyer/admin on tender page
     */
    public static function render_proposal_list( $tender_id ) {
        $proposals = get_posts([
            'post_type'   => 'vh_proposal',
            'numberposts' => -1,
            'meta_query'  => [[ 'key' => '_vh_proposal_tender_id', 'value' => $tender_id ]],
            'post_status' => 'publish',
        ]);

        $tender_status = get_post_meta($tender_id, '_vh_tender_status', true) ?: 'open';
        $winner_id     = get_post_meta($tender_id, '_vh_tender_winner_id', true);

        ob_start();
        ?>
        <div class="vh-card" style="margin-top:2rem;padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h3 style="margin:0;font-size:1.25rem;">📬 Proposal Masuk (<?= count($proposals) ?>)</h3>
                <?php if ($tender_status === 'open' && current_user_can('administrator')): ?>
                <button onclick="vhCloseTender(<?= $tender_id ?>)" class="vh-btn" style="background:#ef4444;color:white;font-size:0.85rem;padding:8px 16px;">🔒 Tutup Tender</button>
                <?php endif; ?>
            </div>

            <?php if (empty($proposals)): ?>
                <p style="color:#9ca3af;text-align:center;padding:2rem 0;">Belum ada proposal yang masuk.</p>
            <?php else: foreach ($proposals as $prop):
                $vendor_id    = $prop->post_author;
                $company_name = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_userdata($vendor_id)->display_name;
                $verified     = get_user_meta($vendor_id, 'vh_verified', true) === '1';
                $logo_id      = get_user_meta($vendor_id, 'vh_vendor_logo', true);
                $logo_url     = $logo_id ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';
                $timeline     = get_post_meta($prop->ID, '_vh_proposal_timeline', true);
                $file_url     = get_post_meta($prop->ID, '_vh_proposal_file_url', true);
                $linked_prods = get_post_meta($prop->ID, '_vh_proposal_products', true) ?: [];
                $prop_status  = get_post_meta($prop->ID, '_vh_proposal_status', true) ?: 'pending';
                $is_winner    = ($winner_id == $prop->ID);
            ?>
            <div class="vh-card" style="margin-bottom:1rem;padding:1.5rem;border:2px solid <?= $is_winner ? '#0e9f6e' : '#f3f4f6' ?>;">
                <div style="display:flex;align-items:flex-start;gap:1rem;">
                    <?php if ($logo_url): ?>
                    <img src="<?= esc_url($logo_url) ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    <?php endif; ?>

                    <div style="flex:1;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.5rem;">
                            <div>
                                <strong><?= esc_html($company_name) ?></strong>
                                <?php if ($verified): ?><span style="color:#0e9f6e;font-size:0.85rem;">✓ Terverifikasi</span><?php endif; ?>
                                <?php if ($is_winner): ?><span style="background:#d1fae5;color:#065f46;padding:2px 10px;border-radius:20px;font-size:0.8rem;margin-left:0.5rem;">🏆 Pemenang</span><?php endif; ?>
                            </div>
                            <div style="text-align:right;">
                                <?php if ($budget): ?><div style="font-size:1.1rem;font-weight:700;color:#1a56db;">Rp <?= number_format($budget,0,',','.') ?></div><?php endif; ?>
                                <?php if ($timeline): ?><div style="font-size:0.8rem;color:#6b7280;">⏱ <?= esc_html($timeline) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div style="margin-top:0.75rem;color:#374151;font-size:0.9rem;line-height:1.6;"><?= nl2br(esc_html($prop->post_content)) ?></div>

                        <?php $exp = get_post_meta($prop->ID, '_vh_proposal_experience', true); if ($exp): ?>
                        <div style="margin-top:0.75rem;background:#f9fafb;padding:0.75rem;border-radius:8px;">
                            <strong style="font-size:0.85rem;">Pengalaman:</strong>
                            <p style="margin:0.25rem 0 0;font-size:0.85rem;color:#6b7280;"><?= nl2br(esc_html($exp)) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($file_url || !empty($linked_prods)): ?>
                        <div style="margin-top:1rem; display:flex; flex-direction:column; gap:10px;">
                            <?php if ($file_url): ?>
                            <a href="<?= esc_url($file_url) ?>" target="_blank" class="vh-btn" style="background:#f3f4f6; color:#374151; border:1px solid #d1d5db; font-size:12px; display:inline-flex; align-items:center; gap:6px; width:fit-content; padding:8px 14px;">
                                <span class="dashicons dashicons-pdf" style="font-size:16px; width:16px; height:16px;"></span> Lihat Dokumen (Compro/Spek)
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($linked_prods)): ?>
                            <div style="border-top:1px solid #f3f4f6; padding-top:10px;">
                                <strong style="font-size:12px; color:#4b5563; display:block; margin-bottom:8px;">Produk Terkait:</strong>
                                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                    <?php foreach ($linked_prods as $lp_id): ?>
                                    <a href="<?= get_permalink($lp_id) ?>" target="_blank" style="display:flex; align-items:center; gap:8px; background:#fff; border:1px solid #e5e7eb; padding:6px 12px; border-radius:8px; text-decoration:none; color:#1f2937; font-size:11px; font-weight:600; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                                        <?php if (has_post_thumbnail($lp_id)): ?>
                                            <?= get_the_post_thumbnail($lp_id, [24, 24], ['style'=>'border-radius:4px;']) ?>
                                        <?php else: ?>
                                            <span class="dashicons dashicons-format-image" style="font-size:16px; width:16px; height:16px; color:#9ca3af;"></span>
                                        <?php endif; ?>
                                        <?= esc_html(get_the_title($lp_id)) ?>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!$is_winner && $tender_status === 'open' && (vh_is_buyer() || current_user_can('administrator'))): ?>
                        <div style="margin-top:1rem;">
                            <button onclick="vhSelectWinner(<?= $tender_id ?>, <?= $prop->ID ?>)" class="vh-btn" style="background:#0e9f6e;color:white;font-size:0.85rem;padding:8px 18px;">
                                🏆 Pilih sebagai Pemenang
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <script>
        function vhSelectWinner(tenderId, proposalId) {
            if (!confirm('Yakin ingin memilih proposal ini sebagai pemenang?')) return;
            jQuery.post('<?= admin_url('admin-ajax.php') ?>', {
                action: 'vh_select_winner',
                tender_id: tenderId,
                proposal_id: proposalId
            }, function(res) {
                if (res.success) { location.reload(); }
                else { alert(res.data); }
            });
        }
        function vhCloseTender(tenderId) {
            if (!confirm('Tutup tender ini? Vendor tidak bisa lagi mengirim proposal.')) return;
            jQuery.post('<?= admin_url('admin-ajax.php') ?>', {
                action: 'vh_close_tender',
                tender_id: tenderId
            }, function(res) {
                if (res.success) { location.reload(); }
                else { alert(res.data); }
            });
        }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX: Submit Proposal
     */
    public static function handle_submit_proposal() {
        if ( ! is_user_logged_in() || ! vh_is_vendor() ) {
            wp_send_json_error('Login sebagai Vendor diperlukan.');
        }

        $tender_id = intval($_POST['tender_id']);
        $budget    = sanitize_text_field($_POST['proposal_budget']);
        $timeline  = sanitize_text_field($_POST['proposal_timeline']);
        $content   = sanitize_textarea_field($_POST['proposal_content']);
        $exp       = sanitize_textarea_field($_POST['proposal_experience']);
        $products  = isset($_POST['proposal_products']) ? array_map('absint', $_POST['proposal_products']) : [];
        $user_id   = get_current_user_id();

        if (!$tender_id || !$content || !$budget) {
            wp_send_json_error('Lengkapi semua field yang wajib diisi.');
        }

        $tender = get_post($tender_id);
        if (!$tender || $tender->post_type !== 'vh_tender') {
            wp_send_json_error('Tender tidak ditemukan.');
        }

        $status = get_post_meta($tender_id, '_vh_tender_status', true) ?: 'open';
        if ($status !== 'open') {
            wp_send_json_error('Tender sudah ditutup.');
        }

        // Check duplicate
        $existing = get_posts(['post_type' => 'vh_proposal', 'author' => $user_id, 'meta_query' => [['key' => '_vh_proposal_tender_id', 'value' => $tender_id]], 'numberposts' => 1]);
        if ($existing) {
            wp_send_json_error('Anda sudah mengirimkan proposal untuk tender ini.');
        }

        $company = get_user_meta($user_id, 'vh_company_name', true) ?: wp_get_current_user()->display_name;

        $prop_id = wp_insert_post([
            'post_title'   => 'Proposal dari ' . $company . ' untuk ' . $tender->post_title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'vh_proposal',
            'post_author'  => $user_id,
        ]);

        if ($prop_id) {
            update_post_meta($prop_id, '_vh_proposal_tender_id', $tender_id);
            update_post_meta($prop_id, '_vh_proposal_budget',    $budget);
            update_post_meta($prop_id, '_vh_proposal_timeline',  $timeline);
            update_post_meta($prop_id, '_vh_proposal_experience',$exp);
            update_post_meta($prop_id, '_vh_proposal_status',    'pending');
            
            if (!empty($products)) {
                update_post_meta($prop_id, '_vh_proposal_products', $products);
            }

            // Handle File Upload
            if (!empty($_FILES['proposal_file']['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                $uploadedfile = $_FILES['proposal_file'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    update_post_meta($prop_id, '_vh_proposal_file_url', $movefile['url']);
                }
            }

            // Notify buyer
            do_action('vh_proposal_submitted', $prop_id, $tender_id, $user_id);

            wp_send_json_success('✅ Proposal berhasil dikirim! Buyer akan menghubungi Anda jika terpilih.');
        } else {
            wp_send_json_error('Gagal mengirim proposal. Silakan coba lagi.');
        }
    }

    /**
     * AJAX: Select Winner
     */
    public static function handle_select_winner() {
        if ( ! is_user_logged_in() || ( ! vh_is_buyer() && ! current_user_can('administrator') ) ) {
            wp_send_json_error('Akses ditolak.');
        }

        $tender_id   = intval($_POST['tender_id']);
        $proposal_id = intval($_POST['proposal_id']);

        $proposal = get_post($proposal_id);
        update_post_meta($tender_id, '_vh_tender_status', 'awarded');
        update_post_meta($tender_id, '_vh_tender_winner_id', $proposal->post_author);
        update_post_meta($proposal_id, '_vh_proposal_status', 'accepted');

        // Reject all others
        $others = get_posts(['post_type' => 'vh_proposal', 'numberposts' => -1, 'meta_query' => [['key' => '_vh_proposal_tender_id', 'value' => $tender_id]]]);
        foreach ($others as $p) {
            if ($p->ID !== $proposal_id) {
                update_post_meta($p->ID, '_vh_proposal_status', 'rejected');
            }
        }

        // Notify winner
        do_action('vh_winner_selected', $proposal_id, $tender_id);

        wp_send_json_success('🏆 Pemenang berhasil dipilih!');
    }

    /**
     * AJAX: Close Tender
     */
    public static function handle_close_tender() {
        if ( ! is_user_logged_in() || ! current_user_can('administrator') ) {
            wp_send_json_error('Akses ditolak.');
        }
        $tender_id = intval($_POST['tender_id']);
        update_post_meta($tender_id, '_vh_tender_status', 'closed');
        wp_send_json_success('Tender berhasil ditutup.');
    }
}

VH_Proposal_System::init();
