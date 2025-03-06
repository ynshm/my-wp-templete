
/**
 * カスタマイザーライブプレビュー用のJavaScript
 *
 * @package News_Portal
 */
(function($) {
    // サイトタイトル
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });

    // サイトの説明
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // ヘッダーのテキストカラー
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });

    // プライマリーカラー
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--primary-color', to);
        });
    });

    // セカンダリーカラー
    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--secondary-color', to);
        });
    });

    // ロゴの幅
    wp.customize('custom_logo_width', function(value) {
        value.bind(function(to) {
            $('.site-branding img.custom-logo').css('max-width', to + 'px');
        });
    });

    // コピーライトテキスト
    wp.customize('footer_copyright', function(value) {
        value.bind(function(to) {
            $('.site-info').html(to);
        });
    });
})(jQuery);
