
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
            // Generate lighter version for gradient
            const lighter = adjustBrightness(to, 20);
            document.documentElement.style.setProperty('--gradient-primary', `linear-gradient(135deg, ${to}, ${lighter})`);
        });
    });

    // セカンダリーカラー
    wp.customize('secondary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--secondary-color', to);
            // Generate lighter version for gradient
            const lighter = adjustBrightness(to, 20);
            document.documentElement.style.setProperty('--gradient-secondary', `linear-gradient(135deg, ${to}, ${lighter})`);
        });
    });
    
    // Helper function to adjust color brightness
    function adjustBrightness(hex, steps) {
        // Remove hash if present
        hex = hex.replace('#', '');
        
        // Parse RGB
        let r = parseInt(hex.substr(0, 2), 16);
        let g = parseInt(hex.substr(2, 2), 16);
        let b = parseInt(hex.substr(4, 2), 16);
        
        // Adjust brightness
        r = Math.max(0, Math.min(255, r + steps));
        g = Math.max(0, Math.min(255, g + steps));
        b = Math.max(0, Math.min(255, b + steps));
        
        // Convert back to hex
        return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    }

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
