<?php
/**
 * Membership System for NiagaHUB Vendors
 * Paket: Free / Basic / Professional / Premium
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Membership {

    const PLANS = [
        'free'         => ['name' => 'Free',         'price' => 0,       'products' => 3,   'services' => 1,  'featured' => false, 'badge' => '',    'color' => '#6b7280'],
        'basic'        => ['name' => 'Basic',        'price' => 99000,   'products' => 10,  'services' => 5,  'featured' => false, 'badge' => '🥈', 'color' => '#6b7280'],
        'professional' => ['name' => 'Professional', 'price' => 299000,  'products' => 50,  'services' => 20, 'featured' => true,  'badge' => '🥇', 'color' => '#1a56db'],
        'premium'      => ['name' => 'Premium',      'price' => 799000,  'products' => -1,  'services' => -1, 'featured' => true,  'badge' => '💎', 'color' => '#7e3af2'],
    ];

    public static function init() {
        add_action('wp_ajax_vh_upgrade_membership', [__CLASS__, 'handle_upgrade']);
        add_shortcode('vh_membership_plans', [__CLASS__, 'render_plans']);
        add_action('add_meta_boxes', [__CLASS__, 'add_member_admin_box']);
    }

    public static function get_plan($user_id = null) {
        if (!$user_id) $user_id = get_current_user_id();
        $plan = get_user_meta($user_id, 'vh_membership_plan', true);
        return isset(self::PLANS[$plan]) ? $plan : 'free';
    }

    public static function get_plan_data($plan = null) {
        if (!$plan) $plan = self::get_plan();
        return self::PLANS[$plan] ?? self::PLANS['free'];
    }

    public static function can_add_product($user_id = null) {
        if (!$user_id) $user_id = get_current_user_id();
        $plan  = self::get_plan($user_id);
        $data  = self::PLANS[$plan];
        if ($data['products'] === -1) return true;
        $count = count(get_posts(['post_type' => 'vh_product', 'author' => $user_id, 'numberposts' => -1, 'post_status' => 'publish']));
        return $count < $data['products'];
    }

    public static function render_plans($atts = []) {
        $current_user = get_current_user_id();
        $current_plan = self::get_plan($current_user);
        ob_start();
        ?>
        <div class="vh-container">
            <div style="text-align:center;margin-bottom:2.5rem;">
                <h2 style="font-size:2rem;font-weight:700;margin:0 0 0.5rem;">💼 Paket Membership Vendor</h2>
                <p style="color:#6b7280;margin:0;">Tingkatkan paket Anda untuk akses lebih banyak fitur</p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
            <?php foreach (self::PLANS as $key => $plan):
                $is_current = ($key === $current_plan);
                $is_popular = ($key === 'professional');
            ?>
            <div class="vh-card" style="padding:2rem;text-align:center;position:relative;border:2px solid <?= $is_current ? $plan['color'] : '#f3f4f6' ?>;<?= $is_popular ? 'transform:scale(1.04)' : '' ?>">
                <?php if ($is_popular): ?>
                <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:#1a56db;color:white;padding:4px 16px;border-radius:20px;font-size:0.75rem;font-weight:700;">POPULER</div>
                <?php endif; ?>
                <?php if ($is_current): ?>
                <div style="position:absolute;top:-12px;right:16px;background:<?= $plan['color'] ?>;color:white;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:700;">Aktif</div>
                <?php endif; ?>

                <div style="font-size:2rem;margin-bottom:0.5rem;"><?= $plan['badge'] ?: '🆓' ?></div>
                <h3 style="margin:0 0 0.25rem;font-size:1.25rem;color:<?= $plan['color'] ?>"><?= $plan['name'] ?></h3>

                <div style="margin:1rem 0;">
                    <?php if ($plan['price'] === 0): ?>
                        <span style="font-size:1.75rem;font-weight:700;">Gratis</span>
                    <?php else: ?>
                        <span style="font-size:1.75rem;font-weight:700;">Rp <?= number_format($plan['price'],0,',','.') ?></span>
                        <span style="color:#9ca3af;font-size:0.85rem;">/bulan</span>
                    <?php endif; ?>
                </div>

                <ul style="text-align:left;list-style:none;padding:0;margin:0 0 1.5rem;font-size:0.9rem;color:#374151;">
                    <li style="padding:5px 0;border-bottom:1px solid #f3f4f6;">📦 <?= $plan['products'] === -1 ? 'Produk Unlimited' : $plan['products'].' Produk' ?></li>
                    <li style="padding:5px 0;border-bottom:1px solid #f3f4f6;">🛠 <?= $plan['services'] === -1 ? 'Jasa Unlimited' : $plan['services'].' Jasa' ?></li>
                    <li style="padding:5px 0;border-bottom:1px solid #f3f4f6;"><?= $plan['featured'] ? '⭐ Featured Listing' : '— No Featured' ?></li>
                    <li style="padding:5px 0;">📋 Akses Tender & RFQ</li>
                </ul>

                <?php if ($is_current): ?>
                    <button disabled style="width:100%;padding:10px;background:#f3f4f6;color:#9ca3af;border:none;border-radius:8px;font-weight:600;">Paket Saat Ini</button>
                <?php elseif ($plan['price'] === 0): ?>
                    <button onclick="vhUpgrade('<?= $key ?>')" style="width:100%;padding:10px;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-weight:600;cursor:pointer;">Downgrade ke Free</button>
                <?php else: ?>
                    <button onclick="vhUpgrade('<?= $key ?>')" style="width:100%;padding:10px;background:<?= $plan['color'] ?>;color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Pilih <?= $plan['name'] ?></button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            </div>
            <div id="vh-membership-msg" style="margin-top:1.5rem;text-align:center;font-weight:600;"></div>
        </div>

        <script>
        function vhUpgrade(plan) {
            if (!confirm('Upgrade ke paket ' + plan + '?')) return;
            jQuery.post('<?= admin_url('admin-ajax.php') ?>', {
                action: 'vh_upgrade_membership', plan: plan
            }, function(res) {
                jQuery('#vh-membership-msg').css('color', res.success ? '#065f46' : '#991b1b').text(res.data);
                if (res.success) setTimeout(() => location.reload(), 1500);
            });
        }
        </script>
        <?php
        return ob_get_clean();
    }

    public static function handle_upgrade() {
        if (!is_user_logged_in() || !vh_is_vendor()) {
            wp_send_json_error('Login sebagai Vendor diperlukan.');
        }
        $plan = sanitize_text_field($_POST['plan']);
        if (!isset(self::PLANS[$plan])) {
            wp_send_json_error('Paket tidak valid.');
        }
        // In real app: integrate with payment gateway here
        update_user_meta(get_current_user_id(), 'vh_membership_plan', $plan);
        update_user_meta(get_current_user_id(), 'vh_membership_since', current_time('mysql'));
        wp_send_json_success('✅ Berhasil upgrade ke paket ' . self::PLANS[$plan]['name'] . '!');
    }

    public static function add_member_admin_box() {
        // Show membership plan in WP admin user profile (handled in vendor fields)
    }
}

VH_Membership::init();

// Helper
function vh_get_membership_badge($user_id = null) {
    $plan = VH_Membership::get_plan($user_id);
    $data = VH_Membership::get_plan_data($plan);
    if ($data['badge']) {
        return '<span title="'.esc_attr($data['name']).'" style="font-size:1rem;">'.$data['badge'].'</span>';
    }
    return '';
}
