
<?php
/**
 * カスタム投稿タイプの定義 - 競馬情報サイト専用
 *
 * @package Horse_Racing_Portal
 */

/**
 * 競馬情報サイト用のカスタム投稿タイプを登録
 */
function news_portal_register_custom_post_types() {
    // レース情報
    register_post_type('race', array(
        'labels' => array(
            'name' => __('レース', 'news-portal'),
            'singular_name' => __('レース', 'news-portal'),
            'add_new' => __('新規追加', 'news-portal'),
            'add_new_item' => __('新しいレースを追加', 'news-portal'),
            'edit_item' => __('レースを編集', 'news-portal'),
            'new_item' => __('新しいレース', 'news-portal'),
            'view_item' => __('レースを表示', 'news-portal'),
            'search_items' => __('レースを検索', 'news-portal'),
            'not_found' => __('レースが見つかりません', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-flag',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'race'),
        'show_in_rest' => true,
        'menu_position' => 5,
    ));

    // 競走馬情報
    register_post_type('horse', array(
        'labels' => array(
            'name' => __('競走馬', 'news-portal'),
            'singular_name' => __('競走馬', 'news-portal'),
            'add_new' => __('新規追加', 'news-portal'),
            'add_new_item' => __('新しい競走馬を追加', 'news-portal'),
            'edit_item' => __('競走馬を編集', 'news-portal'),
            'new_item' => __('新しい競走馬', 'news-portal'),
            'view_item' => __('競走馬を表示', 'news-portal'),
            'search_items' => __('競走馬を検索', 'news-portal'),
            'not_found' => __('競走馬が見つかりません', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-pets',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'horse'),
        'show_in_rest' => true,
        'menu_position' => 6,
    ));

    // 騎手情報
    register_post_type('jockey', array(
        'labels' => array(
            'name' => __('騎手', 'news-portal'),
            'singular_name' => __('騎手', 'news-portal'),
            'add_new' => __('新規追加', 'news-portal'),
            'add_new_item' => __('新しい騎手を追加', 'news-portal'),
            'edit_item' => __('騎手を編集', 'news-portal'),
            'new_item' => __('新しい騎手', 'news-portal'),
            'view_item' => __('騎手を表示', 'news-portal'),
            'search_items' => __('騎手を検索', 'news-portal'),
            'not_found' => __('騎手が見つかりません', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'),
        'rewrite' => array('slug' => 'jockey'),
        'show_in_rest' => true,
        'menu_position' => 7,
    ));

    // 予想記事
    register_post_type('prediction', array(
        'labels' => array(
            'name' => __('予想記事', 'news-portal'),
            'singular_name' => __('予想記事', 'news-portal'),
            'add_new' => __('新規追加', 'news-portal'),
            'add_new_item' => __('新しい予想記事を追加', 'news-portal'),
            'edit_item' => __('予想記事を編集', 'news-portal'),
            'new_item' => __('新しい予想記事', 'news-portal'),
            'view_item' => __('予想記事を表示', 'news-portal'),
            'search_items' => __('予想記事を検索', 'news-portal'),
            'not_found' => __('予想記事が見つかりません', 'news-portal'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'comments'),
        'rewrite' => array('slug' => 'prediction'),
        'show_in_rest' => true,
        'menu_position' => 8,
    ));
}
add_action('init', 'news_portal_register_custom_post_types');

/**
 * 競馬情報サイト用のカスタムタクソノミーを登録
 */
function news_portal_register_custom_taxonomies() {
    // 競馬場
    register_taxonomy('racecourse', array('race', 'prediction'), array(
        'labels' => array(
            'name' => __('競馬場', 'news-portal'),
            'singular_name' => __('競馬場', 'news-portal'),
            'search_items' => __('競馬場を検索', 'news-portal'),
            'all_items' => __('すべての競馬場', 'news-portal'),
            'edit_item' => __('競馬場を編集', 'news-portal'),
            'add_new_item' => __('新しい競馬場を追加', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'racecourse'),
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));

    // レースグレード（G1, G2, G3など）
    register_taxonomy('race_grade', array('race', 'prediction'), array(
        'labels' => array(
            'name' => __('レースグレード', 'news-portal'),
            'singular_name' => __('レースグレード', 'news-portal'),
            'search_items' => __('グレードを検索', 'news-portal'),
            'all_items' => __('すべてのグレード', 'news-portal'),
            'edit_item' => __('グレードを編集', 'news-portal'),
            'add_new_item' => __('新しいグレードを追加', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'race-grade'),
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));

    // 馬場状態
    register_taxonomy('track_condition', array('race'), array(
        'labels' => array(
            'name' => __('馬場状態', 'news-portal'),
            'singular_name' => __('馬場状態', 'news-portal'),
            'search_items' => __('馬場状態を検索', 'news-portal'),
            'all_items' => __('すべての馬場状態', 'news-portal'),
        ),
        'hierarchical' => false,
        'rewrite' => array('slug' => 'track-condition'),
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));

    // 距離カテゴリー
    register_taxonomy('distance_category', array('race', 'horse'), array(
        'labels' => array(
            'name' => __('距離カテゴリー', 'news-portal'),
            'singular_name' => __('距離カテゴリー', 'news-portal'),
            'search_items' => __('距離カテゴリーを検索', 'news-portal'),
            'all_items' => __('すべての距離カテゴリー', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'distance'),
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));

    // 馬の血統（父系）
    register_taxonomy('bloodline', array('horse'), array(
        'labels' => array(
            'name' => __('血統', 'news-portal'),
            'singular_name' => __('血統', 'news-portal'),
            'search_items' => __('血統を検索', 'news-portal'),
            'all_items' => __('すべての血統', 'news-portal'),
        ),
        'hierarchical' => true,
        'rewrite' => array('slug' => 'bloodline'),
        'show_in_rest' => true,
        'show_admin_column' => true,
    ));
}
add_action('init', 'news_portal_register_custom_taxonomies');

/**
 * カスタム投稿タイプのメタボックスを追加
 */
function news_portal_add_meta_boxes() {
    // レース情報用メタボックス
    add_meta_box(
        'race_details',
        __('レース詳細情報', 'news-portal'),
        'news_portal_race_meta_box_callback',
        'race',
        'normal',
        'high'
    );

    // 競走馬情報用メタボックス
    add_meta_box(
        'horse_details',
        __('競走馬詳細情報', 'news-portal'),
        'news_portal_horse_meta_box_callback',
        'horse',
        'normal',
        'high'
    );

    // 騎手情報用メタボックス
    add_meta_box(
        'jockey_details',
        __('騎手詳細情報', 'news-portal'),
        'news_portal_jockey_meta_box_callback',
        'jockey',
        'normal',
        'high'
    );

    // 予想記事用メタボックス
    add_meta_box(
        'prediction_details',
        __('予想詳細', 'news-portal'),
        'news_portal_prediction_meta_box_callback',
        'prediction',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'news_portal_add_meta_boxes');

/**
 * レース情報のメタボックスコールバック
 */
function news_portal_race_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');

    $race_date = get_post_meta($post->ID, '_race_date', true);
    $race_time = get_post_meta($post->ID, '_race_time', true);
    $race_distance = get_post_meta($post->ID, '_race_distance', true);
    $race_surface = get_post_meta($post->ID, '_race_surface', true);
    $race_prize = get_post_meta($post->ID, '_race_prize', true);
    $race_number = get_post_meta($post->ID, '_race_number', true);
    $race_entries = get_post_meta($post->ID, '_race_entries', true);

    ?>
    <p>
        <label for="race_date"><?php _e('開催日:', 'news-portal'); ?></label><br />
        <input type="date" id="race_date" name="race_date" value="<?php echo esc_attr($race_date); ?>" style="width:100%" />
    </p>
    <p>
        <label for="race_time"><?php _e('発走時刻:', 'news-portal'); ?></label><br />
        <input type="time" id="race_time" name="race_time" value="<?php echo esc_attr($race_time); ?>" />
    </p>
    <p>
        <label for="race_distance"><?php _e('距離 (m):', 'news-portal'); ?></label><br />
        <input type="number" id="race_distance" name="race_distance" value="<?php echo esc_attr($race_distance); ?>" min="800" max="4000" />
        <span class="description">例: 2000 (メートル)</span>
    </p>
    <p>
        <label for="race_surface"><?php _e('コース:', 'news-portal'); ?></label><br />
        <select id="race_surface" name="race_surface">
            <option value="">選択してください</option>
            <option value="芝" <?php selected($race_surface, '芝'); ?>>芝</option>
            <option value="ダート" <?php selected($race_surface, 'ダート'); ?>>ダート</option>
            <option value="障害" <?php selected($race_surface, '障害'); ?>>障害</option>
        </select>
    </p>
    <p>
        <label for="race_prize"><?php _e('賞金 (万円):', 'news-portal'); ?></label><br />
        <input type="number" id="race_prize" name="race_prize" value="<?php echo esc_attr($race_prize); ?>" min="0" />
        <span class="description">例: 15000 (1着賞金)</span>
    </p>
    <p>
        <label for="race_number"><?php _e('レース番号:', 'news-portal'); ?></label><br />
        <input type="number" id="race_number" name="race_number" value="<?php echo esc_attr($race_number); ?>" min="1" max="12" />
        <span class="description">例: 11 (第11レース)</span>
    </p>
    <p>
        <label for="race_entries"><?php _e('出走頭数:', 'news-portal'); ?></label><br />
        <input type="number" id="race_entries" name="race_entries" value="<?php echo esc_attr($race_entries); ?>" min="1" max="18" />
    </p>
    <?php
}

/**
 * 競走馬情報のメタボックスコールバック
 */
function news_portal_horse_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');

    $horse_birth = get_post_meta($post->ID, '_horse_birth', true);
    $horse_gender = get_post_meta($post->ID, '_horse_gender', true);
    $horse_color = get_post_meta($post->ID, '_horse_color', true);
    $horse_sire = get_post_meta($post->ID, '_horse_sire', true);
    $horse_dam = get_post_meta($post->ID, '_horse_dam', true);
    $horse_trainer = get_post_meta($post->ID, '_horse_trainer', true);
    $horse_owner = get_post_meta($post->ID, '_horse_owner', true);
    $horse_wins = get_post_meta($post->ID, '_horse_wins', true);
    $horse_places = get_post_meta($post->ID, '_horse_places', true);

    ?>
    <p>
        <label for="horse_birth"><?php _e('生年月日:', 'news-portal'); ?></label><br />
        <input type="date" id="horse_birth" name="horse_birth" value="<?php echo esc_attr($horse_birth); ?>" />
    </p>
    <p>
        <label for="horse_gender"><?php _e('性別:', 'news-portal'); ?></label><br />
        <select id="horse_gender" name="horse_gender">
            <option value="">選択してください</option>
            <option value="牡馬" <?php selected($horse_gender, '牡馬'); ?>>牡馬</option>
            <option value="牝馬" <?php selected($horse_gender, '牝馬'); ?>>牝馬</option>
            <option value="セン馬" <?php selected($horse_gender, 'セン馬'); ?>>セン馬</option>
        </select>
    </p>
    <p>
        <label for="horse_color"><?php _e('毛色:', 'news-portal'); ?></label><br />
        <select id="horse_color" name="horse_color">
            <option value="">選択してください</option>
            <option value="栗毛" <?php selected($horse_color, '栗毛'); ?>>栗毛</option>
            <option value="鹿毛" <?php selected($horse_color, '鹿毛'); ?>>鹿毛</option>
            <option value="青鹿毛" <?php selected($horse_color, '青鹿毛'); ?>>青鹿毛</option>
            <option value="青毛" <?php selected($horse_color, '青毛'); ?>>青毛</option>
            <option value="黒鹿毛" <?php selected($horse_color, '黒鹿毛'); ?>>黒鹿毛</option>
            <option value="芦毛" <?php selected($horse_color, '芦毛'); ?>>芦毛</option>
            <option value="白毛" <?php selected($horse_color, '白毛'); ?>>白毛</option>
        </select>
    </p>
    <p>
        <label for="horse_sire"><?php _e('父:', 'news-portal'); ?></label><br />
        <input type="text" id="horse_sire" name="horse_sire" value="<?php echo esc_attr($horse_sire); ?>" style="width:100%" />
    </p>
    <p>
        <label for="horse_dam"><?php _e('母:', 'news-portal'); ?></label><br />
        <input type="text" id="horse_dam" name="horse_dam" value="<?php echo esc_attr($horse_dam); ?>" style="width:100%" />
    </p>
    <p>
        <label for="horse_trainer"><?php _e('調教師:', 'news-portal'); ?></label><br />
        <input type="text" id="horse_trainer" name="horse_trainer" value="<?php echo esc_attr($horse_trainer); ?>" style="width:100%" />
    </p>
    <p>
        <label for="horse_owner"><?php _e('馬主:', 'news-portal'); ?></label><br />
        <input type="text" id="horse_owner" name="horse_owner" value="<?php echo esc_attr($horse_owner); ?>" style="width:100%" />
    </p>
    <p>
        <label for="horse_wins"><?php _e('勝利数:', 'news-portal'); ?></label><br />
        <input type="number" id="horse_wins" name="horse_wins" value="<?php echo esc_attr($horse_wins); ?>" min="0" />
    </p>
    <p>
        <label for="horse_places"><?php _e('出走回数:', 'news-portal'); ?></label><br />
        <input type="number" id="horse_places" name="horse_places" value="<?php echo esc_attr($horse_places); ?>" min="0" />
    </p>
    <?php
}

/**
 * 騎手情報のメタボックスコールバック
 */
function news_portal_jockey_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');

    $jockey_birth = get_post_meta($post->ID, '_jockey_birth', true);
    $jockey_debut = get_post_meta($post->ID, '_jockey_debut', true);
    $jockey_weight = get_post_meta($post->ID, '_jockey_weight', true);
    $jockey_stable = get_post_meta($post->ID, '_jockey_stable', true);
    $jockey_wins = get_post_meta($post->ID, '_jockey_wins', true);
    $jockey_win_rate = get_post_meta($post->ID, '_jockey_win_rate', true);

    ?>
    <p>
        <label for="jockey_birth"><?php _e('生年月日:', 'news-portal'); ?></label><br />
        <input type="date" id="jockey_birth" name="jockey_birth" value="<?php echo esc_attr($jockey_birth); ?>" />
    </p>
    <p>
        <label for="jockey_debut"><?php _e('デビュー年:', 'news-portal'); ?></label><br />
        <input type="number" id="jockey_debut" name="jockey_debut" value="<?php echo esc_attr($jockey_debut); ?>" min="1900" max="2030" />
    </p>
    <p>
        <label for="jockey_weight"><?php _e('体重 (kg):', 'news-portal'); ?></label><br />
        <input type="number" id="jockey_weight" name="jockey_weight" value="<?php echo esc_attr($jockey_weight); ?>" min="40" max="60" step="0.1" />
    </p>
    <p>
        <label for="jockey_stable"><?php _e('所属:', 'news-portal'); ?></label><br />
        <select id="jockey_stable" name="jockey_stable">
            <option value="">選択してください</option>
            <option value="美浦" <?php selected($jockey_stable, '美浦'); ?>>美浦</option>
            <option value="栗東" <?php selected($jockey_stable, '栗東'); ?>>栗東</option>
            <option value="地方" <?php selected($jockey_stable, '地方'); ?>>地方</option>
        </select>
    </p>
    <p>
        <label for="jockey_wins"><?php _e('通算勝利数:', 'news-portal'); ?></label><br />
        <input type="number" id="jockey_wins" name="jockey_wins" value="<?php echo esc_attr($jockey_wins); ?>" min="0" />
    </p>
    <p>
        <label for="jockey_win_rate"><?php _e('勝率 (%):', 'news-portal'); ?></label><br />
        <input type="number" id="jockey_win_rate" name="jockey_win_rate" value="<?php echo esc_attr($jockey_win_rate); ?>" min="0" max="100" step="0.1" />
    </p>
    <?php
}

/**
 * 予想記事のメタボックスコールバック
 */
function news_portal_prediction_meta_box_callback($post) {
    wp_nonce_field('news_portal_save_meta_box_data', 'news_portal_meta_box_nonce');

    $prediction_race = get_post_meta($post->ID, '_prediction_race', true);
    $prediction_main_pick = get_post_meta($post->ID, '_prediction_main_pick', true);
    $prediction_confidence = get_post_meta($post->ID, '_prediction_confidence', true);
    $prediction_result = get_post_meta($post->ID, '_prediction_result', true);

    // レースの一覧を取得
    $races = get_posts(array(
        'post_type' => 'race',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    ?>
    <p>
        <label for="prediction_race"><?php _e('対象レース:', 'news-portal'); ?></label><br />
        <select id="prediction_race" name="prediction_race" style="width:100%">
            <option value="">選択してください</option>
            <?php foreach ($races as $race) : ?>
                <option value="<?php echo $race->ID; ?>" <?php selected($prediction_race, $race->ID); ?>>
                    <?php echo esc_html($race->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="prediction_main_pick"><?php _e('本命馬:', 'news-portal'); ?></label><br />
        <input type="text" id="prediction_main_pick" name="prediction_main_pick" value="<?php echo esc_attr($prediction_main_pick); ?>" style="width:100%" />
    </p>
    <p>
        <label for="prediction_confidence"><?php _e('自信度 (1-5):', 'news-portal'); ?></label><br />
        <select id="prediction_confidence" name="prediction_confidence">
            <option value="">選択してください</option>
            <option value="1" <?php selected($prediction_confidence, '1'); ?>>★☆☆☆☆</option>
            <option value="2" <?php selected($prediction_confidence, '2'); ?>>★★☆☆☆</option>
            <option value="3" <?php selected($prediction_confidence, '3'); ?>>★★★☆☆</option>
            <option value="4" <?php selected($prediction_confidence, '4'); ?>>★★★★☆</option>
            <option value="5" <?php selected($prediction_confidence, '5'); ?>>★★★★★</option>
        </select>
    </p>
    <p>
        <label for="prediction_result"><?php _e('予想結果:', 'news-portal'); ?></label><br />
        <select id="prediction_result" name="prediction_result">
            <option value="">未確定</option>
            <option value="的中" <?php selected($prediction_result, '的中'); ?>>的中</option>
            <option value="外れ" <?php selected($prediction_result, '外れ'); ?>>外れ</option>
        </select>
    </p>
    <?php
}

/**
 * メタボックスのデータを保存
 */
function news_portal_save_meta_box_data($post_id) {
    if (!isset($_POST['news_portal_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['news_portal_meta_box_nonce'], 'news_portal_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // レース情報のメタデータ保存
    $race_fields = array('race_date', 'race_time', 'race_distance', 'race_surface', 'race_prize', 'race_number', 'race_entries');
    foreach ($race_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // 競走馬情報のメタデータ保存
    $horse_fields = array('horse_birth', 'horse_gender', 'horse_color', 'horse_sire', 'horse_dam', 'horse_trainer', 'horse_owner', 'horse_wins', 'horse_places');
    foreach ($horse_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // 騎手情報のメタデータ保存
    $jockey_fields = array('jockey_birth', 'jockey_debut', 'jockey_weight', 'jockey_stable', 'jockey_wins', 'jockey_win_rate');
    foreach ($jockey_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // 予想記事のメタデータ保存
    $prediction_fields = array('prediction_race', 'prediction_main_pick', 'prediction_confidence', 'prediction_result');
    foreach ($prediction_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'news_portal_save_meta_box_data');
