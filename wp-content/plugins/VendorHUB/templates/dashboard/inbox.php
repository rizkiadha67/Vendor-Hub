<?php
/**
 * Inbox Template - NiagaHUB
 * Redesigned for the fi-dashboard light theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_user_id = get_current_user_id();
$messages = get_posts( array(
    'post_type'      => 'vh_message',
    'meta_query'     => array( array(
        'key'   => '_vh_msg_to',
        'value' => $current_user_id,
    ) ),
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$total = count( $messages );
?>

<style>
.fi-inbox-wrap { display: grid; grid-template-columns: 320px 1fr; gap: 0; min-height: 520px; }

/* Left: message list */
.fi-inbox-list { border-right: 1px solid #e8ecf0; overflow-y: auto; }
.fi-inbox-list-header {
    padding: 14px 20px;
    border-bottom: 1px solid #e8ecf0;
    display: flex; align-items: center; justify-content: space-between;
}
.fi-inbox-list-header h3 { margin: 0; font-size: 13px; font-weight: 700; color: #374151; }
.fi-inbox-count { background: #2563eb; color: white; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }

.fi-msg-item {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background 0.15s;
    display: flex; gap: 12px; align-items: flex-start;
}
.fi-msg-item:hover { background: #f8fafc; }
.fi-msg-item.active { background: #eff6ff; border-left: 3px solid #2563eb; }

.fi-msg-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: #e2e8f0; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-weight: 700; font-size: 15px; color: #64748b;
}
.fi-msg-meta { flex: 1; min-width: 0; }
.fi-msg-sender { font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fi-msg-preview { font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.fi-msg-time { font-size: 10px; color: #94a3b8; white-space: nowrap; margin-top: 2px; }

/* Right: message detail */
.fi-inbox-detail { display: flex; flex-direction: column; }
.fi-inbox-empty-detail {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; padding: 60px;
    text-align: center;
}
.fi-inbox-empty-detail .dashicons { font-size: 48px; width: 48px; height: 48px; opacity: 0.15; margin-bottom: 16px; display: block; }
.fi-inbox-empty-detail p { font-size: 13px; margin: 0; }

.fi-msg-detail-header {
    padding: 16px 24px;
    border-bottom: 1px solid #e8ecf0;
    display: flex; align-items: center; gap: 14px;
}
.fi-msg-detail-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: #dbeafe; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 18px; color: #2563eb; flex-shrink: 0;
}
.fi-msg-detail-name { font-weight: 700; font-size: 15px; color: #1e293b; }
.fi-msg-detail-time { font-size: 12px; color: #94a3b8; margin-top: 2px; }

.fi-msg-detail-body {
    flex: 1; padding: 24px;
    font-size: 14px; line-height: 1.7; color: #374151;
    background: #fafbfc;
}
.fi-msg-detail-body .fi-bubble {
    background: white; border: 1px solid #e8ecf0;
    border-radius: 14px 14px 14px 4px;
    padding: 16px 20px;
    max-width: 80%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.fi-msg-thread-badge {
    display: inline-block; margin-bottom: 12px;
    background: #eff6ff; color: #2563eb;
    font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
    text-transform: uppercase; letter-spacing: 0.06em;
}

.fi-msg-reply-bar {
    padding: 16px 20px;
    border-top: 1px solid #e8ecf0;
    background: white;
    display: flex; gap: 10px; align-items: flex-end;
}
.fi-msg-reply-bar textarea {
    flex: 1; border: 1px solid #d1d5db; border-radius: 10px;
    padding: 10px 14px; font-size: 13px; resize: none; height: 44px;
    font-family: inherit; color: #1e293b; transition: border-color 0.15s;
}
.fi-msg-reply-bar textarea:focus { outline: none; border-color: #2563eb; }
.fi-msg-reply-btn {
    background: #2563eb; color: white; border: none; border-radius: 10px;
    padding: 10px 18px; font-size: 13px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; gap: 6px;
    transition: background 0.15s; white-space: nowrap;
}
.fi-msg-reply-btn:hover { background: #1d4ed8; }
.fi-msg-reply-btn .dashicons { font-size: 15px; width: 15px; height: 15px; }

/* Full empty state (no messages at all) */
.fi-inbox-zero {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 80px 40px; text-align: center;
}
.fi-inbox-zero .dashicons { font-size: 56px; width: 56px; height: 56px; color: #e2e8f0; margin-bottom: 20px; display: block; }
.fi-inbox-zero h4 { font-size: 16px; font-weight: 700; color: #374151; margin: 0 0 8px; }
.fi-inbox-zero p { font-size: 13px; color: #94a3b8; margin: 0; }

@media (max-width: 768px) {
    .fi-inbox-wrap { grid-template-columns: 1fr; }
    .fi-inbox-list { border-right: none; border-bottom: 1px solid #e8ecf0; max-height: 280px; }
}
</style>

<div class="fi-inbox-wrap">

    <?php if ( empty( $messages ) ) : ?>
        <!-- Zero state - no messages at all -->
        <div class="fi-inbox-zero" style="grid-column: span 2;">
            <span class="dashicons dashicons-email-alt"></span>
            <h4><?php _e('Kotak Masuk Kosong', 'vendorhub'); ?></h4>
            <p><?php _e('Belum ada pesan yang masuk. Pesan dari vendor atau buyer akan muncul di sini.', 'vendorhub'); ?></p>
        </div>

    <?php else : ?>

        <!-- Left: List -->
        <div class="fi-inbox-list">
            <div class="fi-inbox-list-header">
                <h3><?php _e('Semua Pesan', 'vendorhub'); ?></h3>
                <span class="fi-inbox-count"><?php echo $total; ?></span>
            </div>
            <?php foreach ( $messages as $i => $msg ) :
                $from    = get_userdata( $msg->post_author );
                $initial = $from ? strtoupper( substr( $from->display_name, 0, 1 ) ) : '?';
                $preview = wp_trim_words( $msg->post_content, 8 );
                $date    = get_the_date( 'd M', $msg->ID );
            ?>
            <div class="fi-msg-item <?php echo $i === 0 ? 'active' : ''; ?>"
                 onclick="fi_show_msg(<?php echo $msg->ID; ?>, this)">
                <div class="fi-msg-avatar"><?php echo esc_html( $initial ); ?></div>
                <div class="fi-msg-meta">
                    <div class="fi-msg-sender"><?php echo $from ? esc_html($from->display_name) : 'Sistem'; ?></div>
                    <div class="fi-msg-preview"><?php echo esc_html( $preview ); ?></div>
                    <div class="fi-msg-time"><?php echo esc_html( $date ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Right: Detail -->
        <div class="fi-inbox-detail" id="fi-msg-detail">
            <?php
            $first   = $messages[0];
            $f_from  = get_userdata( $first->post_author );
            $f_init  = $f_from ? strtoupper( substr( $f_from->display_name, 0, 1 ) ) : '?';
            $f_thrd  = get_post_meta( $first->ID, '_vh_msg_thread', true );
            ?>
            <div class="fi-msg-detail-header">
                <div class="fi-msg-detail-avatar"><?php echo esc_html( $f_init ); ?></div>
                <div>
                    <div class="fi-msg-detail-name"><?php echo $f_from ? esc_html($f_from->display_name) : 'Sistem'; ?></div>
                    <div class="fi-msg-detail-time"><?php echo get_the_date('d F Y, H:i', $first->ID); ?></div>
                </div>
            </div>
            <div class="fi-msg-detail-body">
                <?php if ( $f_thrd ): ?>
                <span class="fi-msg-thread-badge"><?php echo esc_html( str_replace('_',' ',$f_thrd) ); ?></span>
                <?php endif; ?>
                <div class="fi-bubble">
                    <?php echo nl2br( esc_html( $first->post_content ) ); ?>
                </div>
            </div>
            <div class="fi-msg-reply-bar">
                <textarea placeholder="<?php _e('Tulis balasan...', 'vendorhub'); ?>"></textarea>
                <button class="fi-msg-reply-btn">
                    <span class="dashicons dashicons-paperplane"></span>
                    <?php _e('Kirim', 'vendorhub'); ?>
                </button>
            </div>
        </div>

        <script>
        const fi_msgs = <?php
            $json_msgs = [];
            foreach ($messages as $m) {
                $fu = get_userdata($m->post_author);
                $ft = get_post_meta($m->ID, '_vh_msg_thread', true);
                $json_msgs[] = [
                    'id'         => $m->ID,
                    'author_id'  => $m->post_author,
                    'from'       => $fu ? esc_js($fu->display_name) : 'Sistem',
                    'initial'    => $fu ? strtoupper(substr($fu->display_name, 0, 1)) : 'S',
                    'date'       => get_the_date('d F Y, H:i', $m->ID),
                    'thread'     => $ft ? esc_js(str_replace('_',' ',$ft)) : '',
                    'thread_raw' => $ft ? esc_js($ft) : '',
                    'body'       => esc_js($m->post_content),
                ];
            }
            echo json_encode($json_msgs);
        ?>;

        window.current_msg_id = fi_msgs[0] ? fi_msgs[0].id : null;

        function fi_show_msg(id, el) {
            window.current_msg_id = id;
            document.querySelectorAll('.fi-msg-item').forEach(i => i.classList.remove('active'));
            if (el) el.classList.add('active');
            const m = fi_msgs.find(x => x.id == id);
            if (!m) return;
            const d = document.getElementById('fi-msg-detail');
            d.querySelector('.fi-msg-detail-avatar').textContent = m.initial;
            d.querySelector('.fi-msg-detail-name').textContent = m.from;
            d.querySelector('.fi-msg-detail-time').textContent = m.date;
            const badge = d.querySelector('.fi-msg-thread-badge');
            if (badge) {
                badge.style.display = m.thread ? 'inline-block' : 'none';
                badge.textContent = m.thread;
            }
            var safeBody = jQuery('<div>').text(m.body).html().replace(/\n/g,'<br>');
            d.querySelector('.fi-bubble').innerHTML = safeBody;
            // Reset textarea
            document.querySelector('.fi-msg-reply-bar textarea').value = '';
        }
        </script>

    <?php endif; ?>
</div>
