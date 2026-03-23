<?php
/**
 * Vendor Dashboard Template
 * Shortcode: [vh_vendor_dashboard]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$user_id = get_current_user_id();
$company_name  = get_user_meta( $user_id, 'vh_company_name', true ) ?: wp_get_current_user()->display_name;
$verified      = get_user_meta( $user_id, 'vh_verified', true ) === '1';
$profile_done  = vh_profile_is_complete( $user_id );
$logo_id       = get_user_meta( $user_id, 'vh_vendor_logo', true );
$logo_url      = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : VENDORHUB_URL . 'assets/img/default-logo.png';

// Stats
$my_products = new WP_Query(['post_type' => 'vh_product', 'author' => $user_id, 'posts_per_page' => -1]);
$my_services = new WP_Query(['post_type' => 'vh_service', 'author' => $user_id, 'posts_per_page' => -1]);
$my_proposals = get_posts(['post_type' => 'vh_proposal', 'author' => $user_id, 'numberposts' => -1]);

// Active tenders user could bid on
$open_tenders = new WP_Query(['post_type' => 'vh_tender', 'posts_per_page' => 5, 'meta_query' => [['key' => '_vh_tender_status', 'value' => 'closed', 'compare' => '!=']]]);
?>

<div class="vh-vendor-dashboard vh-container">

    <!-- Header -->
    <div class="vh-dashboard-header" style="display:flex;align-items:center;gap:1.5rem;background:linear-gradient(135deg,#1a56db,#7e3af2);color:white;border-radius:16px;padding:2rem;margin-bottom:2rem;">
        <img src="<?= esc_url($logo_url) ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.4);" alt="Logo">
        <div>
            <h2 style="margin:0;font-size:1.5rem;"><?= esc_html($company_name) ?>
                <?php if ($verified): ?> <span style="background:rgba(255,255,255,0.2);padding:2px 10px;border-radius:20px;font-size:0.75rem;vertical-align:middle;">✓ Terverifikasi</span><?php endif; ?>
            </h2>
            <p style="margin:0.25rem 0 0;opacity:0.85;">Vendor Dashboard</p>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <?php if (!$profile_done): ?>
                <a href="<?= esc_url(get_edit_profile_url()) ?>" style="background:rgba(255,255,255,0.2);color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-size:14px;">⚠️ Lengkapi Profil</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="vh-grid" style="grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
        <div class="vh-card" style="text-align:center;padding:1.5rem;">
            <div style="font-size:2rem;font-weight:700;color:#1a56db;"><?= $my_products->found_posts ?></div>
            <div style="color:#6b7280;margin-top:0.25rem;">📦 Produk</div>
        </div>
        <div class="vh-card" style="text-align:center;padding:1.5rem;">
            <div style="font-size:2rem;font-weight:700;color:#7e3af2;"><?= $my_services->found_posts ?></div>
            <div style="color:#6b7280;margin-top:0.25rem;">🛠 Jasa</div>
        </div>
        <div class="vh-card" style="text-align:center;padding:1.5rem;">
            <div style="font-size:2rem;font-weight:700;color:#0e9f6e;"><?= count($my_proposals) ?></div>
            <div style="color:#6b7280;margin-top:0.25rem;">📋 Proposal Terkirim</div>
        </div>
    </div>

    <div class="vh-grid" style="grid-template-columns:1fr 1fr;gap:1.5rem;">

        <!-- Open Tenders -->
        <div>
            <h3 style="margin-bottom:1rem;">🔔 Tender Terbuka</h3>
            <?php if ($open_tenders->have_posts()): while ($open_tenders->have_posts()): $open_tenders->the_post(); 
                $budget   = get_post_meta(get_the_ID(), '_vh_tender_budget', true);
                $deadline = get_post_meta(get_the_ID(), '_vh_tender_deadline', true);
                $status   = get_post_meta(get_the_ID(), '_vh_tender_status', true) ?: 'open';
                // Check if already submitted proposal
                $already = get_posts(['post_type'=>'vh_proposal','meta_query'=>[['key'=>'_vh_proposal_tender_id','value'=>get_the_ID()]],'author'=>$user_id,'numberposts'=>1]);
            ?>
            <div class="vh-card" style="margin-bottom:1rem;padding:1.25rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div>
                        <h4 style="margin:0 0 0.5rem;"><a href="<?= get_permalink() ?>" style="color:inherit;text-decoration:none;"><?= get_the_title() ?></a></h4>
                        <?php if ($budget): ?><p style="margin:0;color:#6b7280;font-size:0.85rem;">💰 Budget: Rp <?= number_format($budget,0,',','.') ?></p><?php endif; ?>
                        <?php if ($deadline): ?><p style="margin:0;color:#6b7280;font-size:0.85rem;">⏰ Deadline: <?= date('d M Y', strtotime($deadline)) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <?php if ($already): ?>
                            <span style="background:#d1fae5;color:#065f46;padding:4px 10px;border-radius:6px;font-size:0.8rem;">✓ Terkirim</span>
                        <?php elseif ($status === 'open'): ?>
                            <a href="<?= get_permalink() ?>#vh-proposal-form" class="vh-btn vh-btn-action" style="font-size:0.85rem;padding:6px 14px;white-space:nowrap;">Kirim Proposal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); 
            else: ?>
            <div class="vh-card" style="padding:1.5rem;text-align:center;color:#6b7280;">Tidak ada tender terbuka saat ini.</div>
            <?php endif; ?>
        </div>

        <!-- My Proposals -->
        <div>
            <h3 style="margin-bottom:1rem;">📋 Proposal Saya</h3>
            <?php if (!empty($my_proposals)): foreach (array_slice($my_proposals, 0, 5) as $prop):
                $tender_id    = get_post_meta($prop->ID, '_vh_proposal_tender_id', true);
                $prop_budget  = get_post_meta($prop->ID, '_vh_proposal_budget', true);
                $prop_status  = get_post_meta($prop->ID, '_vh_proposal_status', true) ?: 'pending';
                $tender_title = $tender_id ? get_the_title($tender_id) : '—';
                $badge_colors = ['pending'=>'#fef3c7;color:#92400e','accepted'=>'#d1fae5;color:#065f46','rejected'=>'#fee2e2;color:#991b1b'];
                $badge = $badge_colors[$prop_status] ?? $badge_colors['pending'];
            ?>
            <div class="vh-card" style="margin-bottom:0.75rem;padding:1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <p style="margin:0;font-weight:600;font-size:0.9rem;"><?= esc_html($tender_title) ?></p>
                        <?php if ($prop_budget): ?><p style="margin:0;color:#6b7280;font-size:0.8rem;">💰 Penawaran: Rp <?= number_format($prop_budget,0,',','.') ?></p><?php endif; ?>
                    </div>
                    <span style="background:<?= $badge ?>;padding:3px 10px;border-radius:6px;font-size:0.78rem;font-weight:600;"><?= ucfirst($prop_status) ?></span>
                </div>
            </div>
            <?php endforeach;
            else: ?>
            <div class="vh-card" style="padding:1.5rem;text-align:center;color:#6b7280;">Belum ada proposal yang dikirim.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Links -->
    <div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
        <a href="<?= esc_url(admin_url('post-new.php?post_type=vh_product')) ?>" class="vh-btn" style="background:#1a56db;color:white;">+ Tambah Produk</a>
        <a href="<?= esc_url(admin_url('post-new.php?post_type=vh_service')) ?>" class="vh-btn" style="background:#7e3af2;color:white;">+ Tambah Jasa</a>
        <a href="<?= esc_url(get_edit_profile_url()) ?>" class="vh-btn" style="background:#374151;color:white;">⚙ Edit Profil</a>
    </div>

</div>
