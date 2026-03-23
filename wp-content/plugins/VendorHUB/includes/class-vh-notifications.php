<?php
/**
 * Notification System for NiagaHUB
 * In-app notifications untuk vendor dan buyer
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class VH_Notifications {

    public static function init() {
        add_action('wp_ajax_vh_get_notifications',   [__CLASS__, 'get_notifications']);
        add_action('wp_ajax_vh_mark_notif_read',     [__CLASS__, 'mark_all_read']);
        // add_action('wp_footer',                      [__CLASS__, 'render_notif_bell']);

        // Hook ke event-event plugin
        add_action('vh_proposal_submitted', [__CLASS__, 'on_proposal_submitted'], 10, 3);
        add_action('vh_winner_selected',    [__CLASS__, 'on_winner_selected'], 10, 2);
    }

    /**
     * Create a notification
     */
    public static function create($user_id, $title, $message, $link = '', $type = 'info') {
        $notifs = get_user_meta($user_id, 'vh_notifications', true) ?: [];
        array_unshift($notifs, [
            'id'      => uniqid(),
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'type'    => $type,
            'read'    => false,
            'time'    => current_time('mysql'),
        ]);
        // Keep max 50 notifications
        $notifs = array_slice($notifs, 0, 50);
        update_user_meta($user_id, 'vh_notifications', $notifs);
    }

    /**
     * Get unread count
     */
    public static function get_unread_count($user_id = null) {
        if (!$user_id) $user_id = get_current_user_id();
        $notifs = get_user_meta($user_id, 'vh_notifications', true) ?: [];
        return count(array_filter($notifs, fn($n) => !$n['read']));
    }

    /**
     * Event: Proposal submitted → notify buyer
     */
    public static function on_proposal_submitted($proposal_id, $tender_id, $vendor_id) {
        $tender = get_post($tender_id);
        if (!$tender) return;
        $buyer_id     = $tender->post_author;
        $company_name = get_user_meta($vendor_id, 'vh_company_name', true) ?: get_userdata($vendor_id)->display_name;
        self::create(
            $buyer_id,
            '📬 Proposal Baru Masuk',
            $company_name . ' mengirimkan proposal untuk tender "' . $tender->post_title . '"',
            get_permalink($tender_id),
            'proposal'
        );
    }

    /**
     * Event: Winner selected → notify winning vendor
     */
    public static function on_winner_selected($proposal_id, $tender_id) {
        $proposal  = get_post($proposal_id);
        $tender    = get_post($tender_id);
        if (!$proposal || !$tender) return;
        self::create(
            $proposal->post_author,
            '🏆 Selamat! Proposal Anda Terpilih',
            'Proposal Anda untuk tender "' . $tender->post_title . '" telah dipilih sebagai pemenang!',
            get_permalink($tender_id),
            'winner'
        );
        // Also notify losing vendors
        $losers = get_posts(['post_type' => 'vh_proposal', 'numberposts' => -1, 'meta_query' => [
            ['key' => '_vh_proposal_tender_id', 'value' => $tender_id],
            ['key' => '_vh_proposal_status', 'value' => 'rejected'],
        ]]);
        foreach ($losers as $loser) {
            self::create($loser->post_author, '📋 Update Tender', 'Tender "' . $tender->post_title . '" telah memilih vendor lain.', get_permalink($tender_id), 'info');
        }
    }

    /**
     * AJAX: get notifications JSON
     */
    public static function get_notifications() {
        if (!is_user_logged_in()) wp_send_json_error('Login required.');
        $user_id = get_current_user_id();
        $notifs  = get_user_meta($user_id, 'vh_notifications', true) ?: [];
        wp_send_json_success(['notifications' => array_slice($notifs, 0, 20), 'unread' => self::get_unread_count($user_id)]);
    }

    /**
     * AJAX: mark all as read
     */
    public static function mark_all_read() {
        if (!is_user_logged_in()) wp_send_json_error();
        $user_id = get_current_user_id();
        $notifs  = get_user_meta($user_id, 'vh_notifications', true) ?: [];
        $notifs  = array_map(fn($n) => array_merge($n, ['read' => true]), $notifs);
        update_user_meta($user_id, 'vh_notifications', $notifs);
        wp_send_json_success();
    }

    /**
     * Render notification bell (Manual call)
     */
    public static function render_navbar_bell() {
        if (!is_user_logged_in()) return;
        $count = self::get_unread_count();
        ?>
        <div id="vh-navbar-notif" style="position:relative;">
            <button id="vh-notif-btn" onclick="vhToggleNotif()" class="fi-topbar-link" style="border:none; background:transparent; font-size:18px; padding:6px; color:#64748b; position:relative;">
                <span class="dashicons dashicons-bell"></span>
                <?php if ($count > 0): ?>
                <span id="vh-notif-count" style="position:absolute; top:-2px; right:-2px; background:#ef4444; color:white; font-size:10px; font-weight:700; min-width:16px; height:16px; border-radius:10px; display:flex; align-items:center; justify-content:center; padding:0 4px; border:2px solid white; box-sizing:content-box;">
                    <?= min($count, 99) ?>
                </span>
                <?php endif; ?>
            </button>

            <div id="vh-notif-panel" style="display:none; position:absolute; top:45px; right:0; width:340px; max-height:420px; background:white; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.12); overflow:hidden; z-index:999; border:1px solid #e2e8f0;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fff;">
                    <strong style="font-size:14px; color:#1e293b;">Notifikasi</strong>
                    <button onclick="vhMarkRead()" style="background:none; border:none; color:#2563eb; cursor:pointer; font-size:12px; font-weight:600;">Tandai semua dibaca</button>
                </div>
                <div id="vh-notif-list" style="overflow-y:auto; max-height:350px;"></div>
                <div style="padding:10px; text-align:center; border-top:1px solid #f1f5f9; background:#f8fafc;">
                    <a href="#" style="font-size:12px; color:#64748b; text-decoration:none; font-weight:500;">Lihat Semua</a>
                </div>
            </div>
        </div>

        <script>
        var vhNotifOpen = false;
        function vhToggleNotif(e) {
            if(e) e.stopPropagation();
            vhNotifOpen = !vhNotifOpen;
            document.getElementById('vh-notif-panel').style.display = vhNotifOpen ? 'block' : 'none';
            if (vhNotifOpen) vhLoadNotifs();
        }
        function vhLoadNotifs() {
            jQuery.post('<?= admin_url('admin-ajax.php') ?>', {action: 'vh_get_notifications'}, function(res) {
                if (!res.success) return;
                var html = '';
                if (!res.data.notifications.length) {
                    html = '<div style="text-align:center; color:#94a3b8; padding:3rem 2rem;"><span class="dashicons dashicons-bell" style="font-size:32px; width:32px; height:32px; margin-bottom:12px; opacity:0.3;"></span><p style="font-size:13px; margin:0;">Tidak ada notifikasi baru</p></div>';
                } else {
                    res.data.notifications.forEach(function(n) {
                        var icons = {proposal:'📬', winner:'🏆', info:'ℹ️'};
                        var safeTitle = jQuery('<div>').text(n.title).html();
                        var safeMsg   = jQuery('<div>').text(n.message).html();
                        
                        html += '<a href="'+(n.link||'#')+'" style="display:block; padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9; text-decoration:none; background:'+(n.read?'#fff':'#f0f7ff')+'">';
                        html += '<div style="display:flex; gap:12px; align-items:flex-start;">';
                        html += '<span style="font-size:1.25rem; flex-shrink:0;">'+(icons[n.type]||'🔔')+'</span>';
                        html += '<div><p style="margin:0 0 4px 0; font-size:13px; font-weight:600; color:#1e293b; line-height:1.4;">'+safeTitle+'</p>';
                        html += '<p style="margin:0; font-size:12px; color:#64748b; line-height:1.5;">'+safeMsg+'</p>';
                        html += '<p style="margin:6px 0 0 0; font-size:10px; color:#94a3b8;">'+(n.time||'')+'</p></div></div></a>';
                    });
                }
                jQuery('#vh-notif-list').html(html);
                if (res.data.unread) {
                    jQuery('#vh-notif-count').text(res.data.unread > 99 ? '99+' : res.data.unread).show();
                } else {
                    jQuery('#vh-notif-count').hide();
                }
            });
        }
        function vhMarkRead() {
            jQuery.post('<?= admin_url('admin-ajax.php') ?>', {action: 'vh_mark_notif_read'}, function() {
                jQuery('#vh-notif-count').hide();
                vhLoadNotifs();
            });
        }
        jQuery(document).on('click', function(e) {
            if (!jQuery('#vh-navbar-notif').is(e.target) && jQuery('#vh-navbar-notif').has(e.target).length === 0) {
                if (vhNotifOpen) { vhNotifOpen = false; jQuery('#vh-notif-panel').hide(); }
            }
        });
        </script>
        <?php
    }
}

VH_Notifications::init();
