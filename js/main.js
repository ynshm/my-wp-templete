// メインのJavaScriptファイル
document.addEventListener('DOMContentLoaded', function() {
    // ヘッダースクロール効果
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // メニュートグル
    const menuToggle = document.querySelector('.menu-toggle');
    const menuContainer = document.querySelector('.menu-container');

    if (menuToggle && menuContainer) {
        menuToggle.addEventListener('click', function() {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            menuContainer.classList.toggle('toggled');
        });
    }

    // 検索フォームトグル
    const searchToggle = document.querySelector('.search-toggle');
    const searchForm = document.querySelector('.header-search-form');

    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function() {
            const isExpanded = searchToggle.getAttribute('aria-expanded') === 'true';
            searchToggle.setAttribute('aria-expanded', !isExpanded);
            searchForm.classList.toggle('active');
        });

        // 検索フォーム以外をクリックしたら閉じる
        document.addEventListener('click', function(event) {
            if (!searchToggle.contains(event.target) && !searchForm.contains(event.target)) {
                searchToggle.setAttribute('aria-expanded', 'false');
                searchForm.classList.remove('active');
            }
        });
    }

    // ダークモード切り替え
    const themeSwitch = document.getElementById('theme-switch');
    const body = document.body;

    // ローカルストレージから現在のテーマを取得
    const currentTheme = localStorage.getItem('theme');

    // 現在のテーマを適用
    if (currentTheme) {
        body.classList.add(currentTheme);

        if (currentTheme === 'dark-theme') {
            if (themeSwitch) {
                themeSwitch.innerHTML = '<i class="fas fa-sun"></i>';
            }
        }
    }

    // テーマ切り替えボタンのイベントリスナー
    if (themeSwitch) {
        themeSwitch.addEventListener('click', function() {
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                body.classList.add('light-theme');
                themeSwitch.innerHTML = '<i class="fas fa-moon"></i>';
                localStorage.setItem('theme', 'light-theme');
            } else {
                body.classList.remove('light-theme');
                body.classList.add('dark-theme');
                themeSwitch.innerHTML = '<i class="fas fa-sun"></i>';
                localStorage.setItem('theme', 'dark-theme');
            }
        });
    }

    // トップに戻るボタン
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.className = 'scroll-top-btn';
    scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollTopBtn.setAttribute('aria-label', 'トップに戻る');
    document.body.appendChild(scrollTopBtn);

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add('show');
        } else {
            scrollTopBtn.classList.remove('show');
        }
    });

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // ドロップダウンメニューのアクセシビリティ対応
    const dropdownLinks = document.querySelectorAll('#primary-menu .menu-item-has-children > a');

    if (dropdownLinks.length > 0) {
        dropdownLinks.forEach(function(link) {
            // ドロップダウンアイコンの作成
            const dropdownIcon = document.createElement('span');
            dropdownIcon.className = 'dropdown-icon';
            link.appendChild(dropdownIcon);

            // ARIA属性の追加
            link.setAttribute('aria-haspopup', 'true');
            link.setAttribute('aria-expanded', 'false');

            // モバイル用のドロップダウントグル
            link.addEventListener('click', function(e) {
                if (window.innerWidth < 768) {
                    e.preventDefault();
                    const expanded = link.getAttribute('aria-expanded') === 'true';
                    link.setAttribute('aria-expanded', !expanded);
                    const subMenu = link.nextElementSibling;
                    if (subMenu) {
                        subMenu.style.display = expanded ? 'none' : 'block';
                    }
                }
            });
        });
    }

    // 画像の遅延読み込み
    const lazyImages = document.querySelectorAll('img.lazy-load');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    if (lazyImage.dataset.srcset) {
                        lazyImage.srcset = lazyImage.dataset.srcset;
                    }
                    lazyImage.classList.add('loaded');
                    imageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function(lazyImage) {
            imageObserver.observe(lazyImage);
        });
    } else {
        // フォールバック（古いブラウザ用）
        lazyImages.forEach(function(lazyImage) {
            lazyImage.src = lazyImage.dataset.src;
            if (lazyImage.dataset.srcset) {
                lazyImage.srcset = lazyImage.dataset.srcset;
            }
            lazyImage.classList.add('loaded');
        });
    }

    // タッチデバイス検出
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    if (isTouchDevice) {
        document.body.classList.add('touch-device');
    }
});