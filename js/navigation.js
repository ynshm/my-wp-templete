
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens.
 */
(function() {
  const siteNavigation = document.getElementById('site-navigation');

  // Return early if the navigation doesn't exist.
  if (!siteNavigation) {
    return;
  }

  const button = siteNavigation.getElementsByTagName('button')[0];

  // Return early if the button doesn't exist.
  if ('undefined' === typeof button) {
    return;
  }

  const menu = siteNavigation.getElementsByTagName('ul')[0];

  // Hide menu toggle button if menu is empty and return early.
  if ('undefined' === typeof menu) {
    button.style.display = 'none';
    return;
  }

  if (!menu.classList.contains('nav-menu')) {
    menu.classList.add('nav-menu');
  }

  // Toggle the .toggled class and the aria-expanded value each time the button is clicked.
  button.addEventListener('click', function() {
    siteNavigation.classList.toggle('toggled');

    if (button.getAttribute('aria-expanded') === 'true') {
      button.setAttribute('aria-expanded', 'false');
    } else {
      button.setAttribute('aria-expanded', 'true');
    }
  });

  // Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
  document.addEventListener('click', function(event) {
    const isClickInside = siteNavigation.contains(event.target);

    if (!isClickInside) {
      siteNavigation.classList.remove('toggled');
      button.setAttribute('aria-expanded', 'false');
    }
  });
})();
/**
 * ナビゲーションメニューの機能
 *
 * @package News_Portal
 */
(function() {
    const siteNavigation = document.getElementById('site-navigation');

    // サイトナビゲーション要素が存在しない場合は早期に終了
    if (!siteNavigation) {
        return;
    }

    const button = siteNavigation.getElementsByTagName('button')[0];

    // メニューボタンが存在しない場合は早期に終了
    if ('undefined' === typeof button) {
        return;
    }

    const menu = siteNavigation.getElementsByTagName('ul')[0];

    // メニューが存在しない場合はボタンを非表示にして終了
    if ('undefined' === typeof menu) {
        button.style.display = 'none';
        return;
    }

    if (!menu.classList.contains('nav-menu')) {
        menu.classList.add('nav-menu');
    }

    // メニューボタンのクリックイベント
    button.addEventListener('click', function() {
        siteNavigation.classList.toggle('toggled');

        if (button.getAttribute('aria-expanded') === 'true') {
            button.setAttribute('aria-expanded', 'false');
        } else {
            button.setAttribute('aria-expanded', 'true');
        }
    });

    // メニュー内のリンクがクリックされたときの処理
    document.addEventListener('click', function(event) {
        const isClickInside = siteNavigation.contains(event.target);

        if (!isClickInside) {
            siteNavigation.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
        }
    });

    // サブメニューの処理
    const links = menu.getElementsByTagName('a');
    const linksWithChildren = menu.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');

    for (const link of linksWithChildren) {
        link.addEventListener('click', function(event) {
            const closestLi = link.closest('li');
            
            if (closestLi.classList.contains('focus')) {
                closestLi.classList.remove('focus');
            } else {
                // 他の開いているサブメニューを閉じる
                const openMenus = menu.querySelectorAll('.focus');
                for (const openMenu of openMenus) {
                    openMenu.classList.remove('focus');
                }
                
                closestLi.classList.add('focus');
            }
            
            // クリックイベントが伝播するのを防止
            // ただし、サブメニューを持つ親メニューのリンク機能は維持
            if (!link.classList.contains('allow-click')) {
                event.preventDefault();
            }
        });
    }

    // Escキーが押されたときの処理
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openMenus = menu.querySelectorAll('.focus');
            for (const openMenu of openMenus) {
                openMenu.classList.remove('focus');
            }
            siteNavigation.classList.remove('toggled');
            button.setAttribute('aria-expanded', 'false');
        }
    });
})();
