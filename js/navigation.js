/**
 * ナビゲーション機能のJavaScript
 */
(function() {
    const siteNavigation = document.getElementById('site-navigation');

    // 対象の要素がなければ終了
    if (!siteNavigation) {
        return;
    }

    const button = siteNavigation.querySelector('.menu-toggle');
    const menu = siteNavigation.querySelector('.menu-container');

    // メニューか切り替えボタンがなければ終了
    if (!button || !menu) {
        return;
    }

    // メニューを非表示にする
    menu.setAttribute('aria-hidden', 'true');

    // ボタンがクリックされていなければ非表示のままにする
    if (button.getAttribute('aria-expanded') !== 'true') {
        menu.classList.remove('toggled');
    }

    button.addEventListener('click', function() {
        menu.classList.toggle('toggled');

        if (menu.classList.contains('toggled')) {
            button.setAttribute('aria-expanded', 'true');
            menu.setAttribute('aria-hidden', 'false');
        } else {
            button.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-hidden', 'true');
        }
    });

    // ウィンドウがリサイズされた時に、メニューの状態をリセット
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            button.setAttribute('aria-expanded', 'false');
            menu.classList.remove('toggled');
            menu.setAttribute('aria-hidden', 'true');
        }
    });

    // メニュー内のリンクをフォーカスできるようにする
    const links = menu.getElementsByTagName('a');
    const linksWithChildren = menu.querySelectorAll('.menu-item-has-children > a');

    // すべてのサブメニューを閉じた状態にする
    for (const link of linksWithChildren) {
        link.addEventListener('click', function(event) {
            const parent = this.parentNode;
            parent.classList.toggle('focus');

            // 1階層目のメニューの場合は、イベントをキャンセルしてサブメニューを表示
            if (parent.parentNode === menu) {
                event.preventDefault();
            }
        });
    }
})();