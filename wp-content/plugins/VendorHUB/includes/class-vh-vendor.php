<?php
/**
 * Vendor and Product Management for NiagaHUB
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VH_Vendor_Manager {

    public static function init() {
        add_action( 'show_user_profile', array( __CLASS__, 'add_vendor_fields' ) );
        add_action( 'edit_user_profile', array( __CLASS__, 'add_vendor_fields' ) );
        add_action( 'personal_options_update', array( __CLASS__, 'save_vendor_fields' ) );
        add_action( 'edit_user_profile_update', array( __CLASS__, 'save_vendor_fields' ) );

        // Add Tender meta fields
        add_action( 'add_meta_boxes_vh_tender', array( __CLASS__, 'add_tender_metaboxes' ) );
        add_action( 'save_post_vh_tender', array( __CLASS__, 'save_tender_meta' ) );

        add_action( 'add_meta_boxes_vh_product', array( __CLASS__, 'add_product_metaboxes' ) );
        add_action( 'save_post_vh_product', array( __CLASS__, 'save_product_meta' ) );
    }

    /**
     * Tender Meta Boxes (Budget, Deadline)
     */
    public static function add_tender_metaboxes() {
        add_meta_box( 'vh_tender_settings', __( 'Tender Details', 'vendorhub' ), array( __CLASS__, 'render_tender_metabox' ), 'vh_tender', 'side' );
    }

    public static function render_tender_metabox( $post ) {
        $budget = get_post_meta( $post->ID, '_vh_tender_budget', true );
        $deadline = get_post_meta( $post->ID, '_vh_tender_deadline', true );
        ?>
        <p>
            <label for="vh_tender_budget"><?php _e('Estimated Budget (Rp)', 'vendorhub'); ?></label>
            <input type="number" name="vh_tender_budget" id="vh_tender_budget" value="<?php echo esc_attr($budget); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="vh_tender_deadline"><?php _e('Deadline Date', 'vendorhub'); ?></label>
            <input type="date" name="vh_tender_deadline" id="vh_tender_deadline" value="<?php echo esc_attr($deadline); ?>" style="width:100%;" />
        </p>
        <?php
    }

    public static function save_tender_meta( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( isset( $_POST['vh_tender_budget'] ) ) update_post_meta( $post_id, '_vh_tender_budget', sanitize_text_field( $_POST['vh_tender_budget'] ) );
        if ( isset( $_POST['vh_tender_deadline'] ) ) update_post_meta( $post_id, '_vh_tender_deadline', sanitize_text_field( $_POST['vh_tender_deadline'] ) );
    }

    public static function add_product_metaboxes_init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_product_metaboxes' ) );
        add_action( 'save_post_vh_product', array( __CLASS__, 'save_product_meta' ) );
    }

    /**
     * Add Vendor specific fields to User Profile
     */
    public static function add_vendor_fields( $user ) {
        if ( ! in_array( 'vendor', (array) $user->roles ) && ! in_array( 'administrator', (array) $user->roles ) ) {
            return;
        }

        $verified      = get_user_meta( $user->ID, 'vh_verified', true );
        $company_name  = get_user_meta( $user->ID, 'vh_company_name', true );
        $tagline       = get_user_meta( $user->ID, 'vh_tagline', true );
        $description   = get_user_meta( $user->ID, 'vh_description', true );
        $business_type = get_user_meta( $user->ID, 'vh_business_type', true );
        $nib           = get_user_meta( $user->ID, 'vh_nib', true );
        $location      = get_user_meta( $user->ID, 'vh_location', true );
        $address       = get_user_meta( $user->ID, 'vh_address', true );
        $wa_number     = get_user_meta( $user->ID, 'vh_wa_number', true );
        $business_email= get_user_meta( $user->ID, 'vh_business_email', true );
        $website       = get_user_meta( $user->ID, 'vh_website', true );
        $est_year      = get_user_meta( $user->ID, 'vh_est_year', true );
        $biz_scale     = get_user_meta( $user->ID, 'vh_biz_scale', true );
        $industry      = get_user_meta( $user->ID, 'vh_industry', true );
        ?>
        <h3><?php _e( 'NiagaHUB Vendor Information', 'vendorhub' ); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="vh_vendor_logo"><?php _e( 'Company Logo', 'vendorhub' ); ?></label></th>
                <td>
                    <?php 
                    $logo_id = get_user_meta( $user->ID, 'vh_vendor_logo', true );
                    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
                    ?>
                    <div id="vh-logo-preview" style="margin-bottom: 10px;">
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" style="max-width: 100px; border: 1px solid #ccc; border-radius: 8px;" />
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="vh_vendor_logo" id="vh_vendor_logo" value="<?php echo esc_attr( $logo_id ); ?>" />
                    <button type="button" class="button vh-upload-button" id="vh_upload_logo_btn"><?php _e( 'Select Logo', 'vendorhub' ); ?></button>
                    <button type="button" class="button vh-remove-button" id="vh_remove_logo_btn" <?php echo ! $logo_id ? 'style="display:none;"' : ''; ?>><?php _e( 'Remove', 'vendorhub' ); ?></button>
                </td>
            </tr>
            <tr>
                <th><label for="vh_company_name"><?php _e( 'Company Name', 'vendorhub' ); ?></label></th>
                <td><input type="text" name="vh_company_name" id="vh_company_name" value="<?php echo esc_attr( $company_name ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_tagline"><?php _e( 'Tagline', 'vendorhub' ); ?></label></th>
                <td><input type="text" name="vh_tagline" id="vh_tagline" value="<?php echo esc_attr( $tagline ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_description"><?php _e( 'Company Description', 'vendorhub' ); ?></label></th>
                <td><textarea name="vh_description" id="vh_description" rows="5" cols="30" class="regular-text"><?php echo esc_textarea( $description ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="vh_business_type"><?php _e( 'Business Type', 'vendorhub' ); ?></label></th>
                <td>
                    <select name="vh_business_type" id="vh_business_type">
                        <option value="PT" <?php selected($business_type, 'PT'); ?>>PT</option>
                        <option value="CV" <?php selected($business_type, 'CV'); ?>>CV</option>
                        <option value="Koperasi" <?php selected($business_type, 'Koperasi'); ?>>Koperasi</option>
                        <option value="UMKM" <?php selected($business_type, 'UMKM'); ?>>UMKM</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vh_nib"><?php _e( 'NIB (Nomor Induk Berusaha)', 'vendorhub' ); ?></label></th>
                <td><input type="text" name="vh_nib" id="vh_nib" value="<?php echo esc_attr( $nib ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_nib_file"><?php _e( 'NIB Document', 'vendorhub' ); ?></label></th>
                <td>
                    <?php 
                    $nib_id = get_user_meta( $user->ID, 'vh_nib_file', true );
                    if ( $nib_id ) echo '<a href="'.wp_get_attachment_url($nib_id).'" target="_blank">'.__('View Document', 'vendorhub').'</a><br>';
                    ?>
                    <input type="hidden" name="vh_nib_file" id="vh_nib_file" value="<?php echo esc_attr( $nib_id ); ?>" />
                    <button type="button" class="button vh-upload-button" id="vh_upload_nib_btn"><?php _e( 'Upload NIB', 'vendorhub' ); ?></button>
                </td>
            </tr>
            <tr>
                <th><label for="vh_industry"><?php _e( 'Main Industry', 'vendorhub' ); ?></label></th>
                <td>
                    <select name="vh_industry" id="vh_industry">
                        <?php 
                        $terms = get_terms(array('taxonomy' => 'vh_industry', 'hide_empty' => false));
                        foreach ($terms as $term) {
                            echo '<option value="'.esc_attr($term->slug).'" '.selected($industry, $term->slug, false).'>'.esc_html($term->name).'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vh_location"><?php _e( 'Location (Province/City)', 'vendorhub' ); ?></label></th>
                <td><input type="text" name="vh_location" id="vh_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_address"><?php _e( 'Full Address', 'vendorhub' ); ?></label></th>
                <td><textarea name="vh_address" id="vh_address" rows="3" cols="30" class="regular-text"><?php echo esc_textarea( $address ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="vh_wa_number"><?php _e( 'WhatsApp Number', 'vendorhub' ); ?></label></th>
                <td><input type="text" name="vh_wa_number" id="vh_wa_number" value="<?php echo esc_attr( $wa_number ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_business_email"><?php _e( 'Business Email', 'vendorhub' ); ?></label></th>
                <td><input type="email" name="vh_business_email" id="vh_business_email" value="<?php echo esc_attr( $business_email ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_website"><?php _e( 'Website', 'vendorhub' ); ?></label></th>
                <td><input type="url" name="vh_website" id="vh_website" value="<?php echo esc_attr( $website ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_est_year"><?php _e( 'Established Year', 'vendorhub' ); ?></label></th>
                <td><input type="number" name="vh_est_year" id="vh_est_year" value="<?php echo esc_attr( $est_year ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="vh_biz_scale"><?php _e( 'Business Scale', 'vendorhub' ); ?></label></th>
                <td>
                    <select name="vh_biz_scale" id="vh_biz_scale">
                        <option value="Kecil" <?php selected($biz_scale, 'Kecil'); ?>>Kecil</option>
                        <option value="Menengah" <?php selected($biz_scale, 'Menengah'); ?>>Menengah</option>
                        <option value="Besar" <?php selected($biz_scale, 'Besar'); ?>>Besar</option>
                    </select>
                </td>
            </tr>
            <?php if ( current_user_can( 'manage_options' ) ) : ?>
            <tr>
                <th><label for="vh_verified"><?php _e( 'Verified Vendor', 'vendorhub' ); ?></label></th>
                <td>
                    <input type="checkbox" name="vh_verified" id="vh_verified" value="1" <?php checked( $verified, '1' ); ?> />
                    <span class="description"><?php _e( 'Check to mark this vendor as verified.', 'vendorhub' ); ?></span>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <script>
        jQuery(document).ready(function($) {
            function vh_media_upload(btn_selector, input_selector, preview_selector = null) {
                $(btn_selector).click(function(e) {
                    e.preventDefault();
                    var custom_uploader = wp.media({
                        title: 'Select File',
                        button: { text: 'Use this file' },
                        multiple: false
                    }).on('select', function() {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        $(input_selector).val(attachment.id);
                        if (preview_selector) {
                            if (attachment.type === 'image') {
                                $(preview_selector).html('<img src="' + attachment.url + '" style="max-width: 100px; border: 1px solid #ccc; border-radius: 8px;" />');
                            } else {
                                $(preview_selector).html('<span>File: ' + attachment.filename + '</span>');
                            }
                        }
                    }).open();
                });
            }

            vh_media_upload('#vh_upload_logo_btn', '#vh_vendor_logo', '#vh-logo-preview');
            vh_media_upload('#vh_upload_nib_btn', '#vh_nib_file');

            $('#vh_remove_logo_btn').click(function() {
                $('#vh-logo-preview').html('');
                $('#vh_vendor_logo').val('');
            });
        });
        </script>
        <?php
    }

    public static function save_vendor_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return;
        }
        
        $fields = array(
            'vh_company_name', 'vh_tagline', 'vh_description', 'vh_business_type', 
            'vh_nib', 'vh_nib_file', 'vh_industry', 'vh_location', 'vh_address', 
            'vh_wa_number', 'vh_business_email', 'vh_website', 'vh_est_year', 
            'vh_biz_scale', 'vh_vendor_logo'
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                update_user_meta( $user_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        }

        if ( current_user_can( 'manage_options' ) ) {
            update_user_meta( $user_id, 'vh_verified', isset( $_POST['vh_verified'] ) ? '1' : '0' );
        }
    }

    /**
     * Product Meta Boxes (MOQ, Unit)
     */
    public static function add_product_metaboxes() {
        add_meta_box( 'vh_product_settings', __( 'B2B Settings', 'vendorhub' ), array( __CLASS__, 'render_product_metabox' ), 'vh_product', 'side' );
    }

    public static function render_product_metabox( $post ) {
        $moq = get_post_meta( $post->ID, '_vh_moq', true );
        $unit = get_post_meta( $post->ID, '_vh_unit', true );
        ?>
        <p>
            <label for="vh_moq"><?php _e( 'Min Order Quantity (MOQ)', 'vendorhub' ); ?></label>
            <input type="number" name="vh_moq" id="vh_moq" value="<?php echo esc_attr( $moq ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="vh_unit"><?php _e( 'Unit (e.g. Box, Pcs, Kg)', 'vendorhub' ); ?></label>
            <input type="text" name="vh_unit" id="vh_unit" value="<?php echo esc_attr( $unit ); ?>" style="width:100%;" />
        </p>
        <?php
    }

    public static function save_product_meta( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( isset( $_POST['vh_moq'] ) ) update_post_meta( $post_id, '_vh_moq', sanitize_text_field( $_POST['vh_moq'] ) );
        if ( isset( $_POST['vh_unit'] ) ) update_post_meta( $post_id, '_vh_unit', sanitize_text_field( $_POST['vh_unit'] ) );
    }
}

VH_Vendor_Manager::init();

/**
 * Helper: Check if vendor is verified
 */
function vh_is_verified( $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    return get_user_meta( $user_id, 'vh_verified', true ) === '1';
}

/**
 * Helper: Check if profile is complete (Company Name, Location, NIB, NIB File)
 */
function vh_profile_is_complete( $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    
    $name = get_user_meta( $user_id, 'vh_company_name', true );
    $loc = get_user_meta( $user_id, 'vh_location', true );
    $nib = get_user_meta( $user_id, 'vh_nib', true );
    $nib_file = get_user_meta( $user_id, 'vh_nib_file', true );

    return ! empty( $name ) && ! empty( $loc ) && ! empty( $nib ) && ! empty( $nib_file );
}

/**
 * Helper: Get all verified user IDs
 */
function vh_get_verified_user_ids( $role = null ) {
    $meta_query = array(
        'relation' => 'AND',
        array( 'key' => 'vh_verified', 'value' => '1' ),
        array( 'key' => 'vh_company_name', 'value' => '', 'compare' => '!=' ),
        array( 'key' => 'vh_nib', 'value' => '', 'compare' => '!=' ),
        array( 'key' => 'vh_nib_file', 'value' => '', 'compare' => '!=' ),
    );

    if ( $role ) {
        $meta_query[] = array( 'key' => 'vh_role', 'value' => $role );
    }

    $args = array(
        'meta_query' => $meta_query,
        'fields' => 'ID'
    );
    return get_users( $args );
}

/**
 * Helper: Get all verified vendor IDs
 */
function vh_get_verified_vendor_ids() {
    return vh_get_verified_user_ids( 'vendor' );
}

/**
 * Helper: Display Verified Badge
 */
function vh_verified_badge( $user_id ) {
    if ( vh_is_verified( $user_id ) ) {
        return '<span class="vh-badge vh-badge-verified"><span class="dashicons dashicons-yes"></span> ' . __( 'Verified', 'vendorhub' ) . '</span>';
    }
    return '';
}
