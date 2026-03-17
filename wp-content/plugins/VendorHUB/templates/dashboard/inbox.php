<?php
/**
 * Inbox Template - NiagaHUB
 */

$current_user_id = get_current_user_id();
$messages = get_posts( array(
    'post_type'   => 'vh_message',
    'meta_query'  => array(
        array(
            'key'     => '_vh_msg_to',
            'value'   => $current_user_id,
        ),
    ),
    'posts_per_page' => -1,
) );
?>

<div class="vh-inbox-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin: 0;"><?php _e('Pesan Masuk', 'vendorhub'); ?></h2>
        <span class="vh-badge" style="background: var(--primary-color); color: white;">
            <?php echo count($messages); ?> <?php _e('Pesan', 'vendorhub'); ?>
        </span>
    </div>

    <?php if ( ! empty( $messages ) ) : ?>
        <div class="vh-message-list">
            <?php foreach ( $messages as $msg ) : 
                $from_user_id = $msg->post_author;
                $from_user = get_userdata($from_user_id);
                $thread = get_post_meta($msg->ID, '_vh_msg_thread', true);
            ?>
                <div class="vh-card vh-message-item" style="margin-bottom: 1rem; border-left: 4px solid var(--primary-color); padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <span class="dashicons dashicons-admin-users" style="color: #94a3b8;"></span>
                            </div>
                            <div>
                                <strong style="display: block; font-size: 1rem;"><?php echo esc_html($from_user->display_name); ?></strong>
                                <span class="text-muted" style="font-size: 11px; text-transform: uppercase;"><?php echo get_the_date('', $msg->ID); ?></span>
                            </div>
                        </div>
                        <span class="vh-badge" style="background: #f1f5f9; color: #475569; font-size: 10px;">
                            <?php echo esc_html(strtoupper(str_replace('_', ' ', $thread))); ?>
                        </span>
                    </div>

                    <div class="message-body" style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 14px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                        <?php echo nl2br(esc_html($msg->post_content)); ?>
                    </div>

                    <div style="text-align: right;">
                        <button class="vh-btn vh-btn-primary" style="padding: 6px 15px; font-size: 13px;">
                            <span class="dashicons dashicons-undo" style="font-size: 16px; width: 16px; height: 16px;"></span>
                            <?php _e('Balas Pesan', 'vendorhub'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="vh-card" style="text-align: center; padding: 4rem;">
            <span class="dashicons dashicons-email-alt" style="font-size: 3rem; width: 3rem; height: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></span>
            <p class="text-muted"><?php _e('Belum ada pesan masuk.', 'vendorhub'); ?></p>
        </div>
    <?php endif; ?>
</div>
