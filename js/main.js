
/**
 * メインJavaScriptファイル
 * モダンな機能とインタラクションを実装
 */
document.addEventListener('DOMContentLoaded', function() {
    // ヘッダースクロール効果
    const siteHeader = document.querySelector('.site-header');
    
    if (siteHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        });
    }
    
    // スライダー初期化
    initFeaturedSlider();
    
    // 画像の遅延読み込み
    initLazyLoading();
    
    // リンク要素のホバーエフェクト最適化
    optimizeHoverEffects();
    
    // テーマ切り替え（ダークモード/ライトモード）
    initThemeToggle();
});

/**
 * フィーチャードスライダーの初期化
 */
function initFeaturedSlider() {
    const slider = document.querySelector('.featured-slider');
    
    if (!slider) return;
    
    const sliderWrapper = slider.querySelector('.slider-wrapper');
    const sliderItems = slider.querySelectorAll('.slider-item');
    const sliderNav = slider.querySelector('.slider-nav');
    const sliderDots = slider.querySelector('.slider-dots');
    const prevBtn = slider.querySelector('.slider-prev');
    const nextBtn = slider.querySelector('.slider-next');
    
    let currentIndex = 0;
    let intervalId;
    const itemCount = sliderItems.length;
    
    // スライダードットの生成
    for (let i = 0; i < itemCount; i++) {
        const dot = document.createElement('button');
        dot.classList.add('slider-dot');
        dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
        if (i === 0) dot.classList.add('active');
        
        dot.addEventListener('click', function() {
            goToSlide(i);
        });
        
        sliderDots.appendChild(dot);
    }
    
    // スライダーアイテムの初期表示
    sliderItems.forEach((item, index) => {
        item.style.display = index === 0 ? 'flex' : 'none';
    });
    
    // 次のスライドへ
    function nextSlide() {
        let next = currentIndex + 1;
        if (next >= itemCount) next = 0;
        goToSlide(next);
    }
    
    // 前のスライドへ
    function prevSlide() {
        let prev = currentIndex - 1;
        if (prev < 0) prev = itemCount - 1;
        goToSlide(prev);
    }
    
    // 特定のスライドへ移動
    function goToSlide(index) {
        // 現在のスライドを非表示
        sliderItems[currentIndex].style.display = 'none';
        sliderDots.children[currentIndex].classList.remove('active');
        
        // 新しいスライドを表示
        currentIndex = index;
        sliderItems[currentIndex].style.display = 'flex';
        sliderDots.children[currentIndex].classList.add('active');
        
        // 自動再生をリセット
        resetInterval();
    }
    
    // 自動再生の設定
    function startInterval() {
        intervalId = setInterval(nextSlide, 5000);
    }
    
    // 自動再生のリセット
    function resetInterval() {
        clearInterval(intervalId);
        startInterval();
    }
    
    // ボタンイベントのバインド
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
    
    // スワイプ対応
    let touchStartX = 0;
    let touchEndX = 0;
    
    sliderWrapper.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    sliderWrapper.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        if (touchEndX < touchStartX - 50) {
            nextSlide();
        } else if (touchEndX > touchStartX + 50) {
            prevSlide();
        }
    }
    
    // 自動再生の開始
    startInterval();
    
    // 一時停止（マウスオーバー時）
    sliderWrapper.addEventListener('mouseenter', function() {
        clearInterval(intervalId);
    });
    
    sliderWrapper.addEventListener('mouseleave', function() {
        startInterval();
    });
}

/**
 * 画像の遅延読み込み機能
 */
function initLazyLoading() {
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img.lazy-load');
        
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                    
                    // イベント発火（他のスクリプトで使用可能）
                    const event = new Event('lazyloaded');
                    img.dispatchEvent(event);
                }
            });
        });
        
        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    } else {
        // Intersection Observerが利用できない場合のフォールバック
        const lazyLoad = function() {
            const lazyImages = document.querySelectorAll('img.lazy-load');
            const scrollTop = window.pageYOffset;
            
            lazyImages.forEach(function(img) {
                if (img.offsetTop < window.innerHeight + scrollTop && !img.classList.contains('loaded')) {
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    img.classList.add('loaded');
                    
                    // イベント発火
                    const event = new Event('lazyloaded');
                    img.dispatchEvent(event);
                }
            });
        };
        
        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationChange', lazyLoad);
        lazyLoad();
    }
}

/**
 * リンク要素のホバーエフェクト最適化
 */
function optimizeHoverEffects() {
    const links = document.querySelectorAll('a');
    
    links.forEach(function(link) {
        // ホバーは意図的なものだけに適用（タップデバイスでの偶発的なホバーを回避）
        link.addEventListener('mouseenter', function() {
            this.classList.add('hover-intent');
        });
        
        link.addEventListener('mouseleave', function() {
            this.classList.remove('hover-intent');
        });
    });
}

/**
 * テーマ切り替え機能の初期化
 */
function initThemeToggle() {
    // テーマ切り替えボタンを追加
    const themeToggle = document.createElement('button');
    themeToggle.id = 'theme-toggle';
    themeToggle.className = 'theme-toggle-btn';
    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    themeToggle.setAttribute('aria-label', 'Toggle dark mode');
    document.body.appendChild(themeToggle);
    
    // ユーザー設定かシステム設定からテーマを取得
    const userTheme = localStorage.getItem('theme');
    const systemDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // テーマの初期設定
    if (userTheme === 'dark' || (!userTheme && systemDarkMode)) {
        document.body.classList.add('dark-theme');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        document.body.classList.add('light-theme');
    }
    
    // テーマ切り替え処理
    themeToggle.addEventListener('click', function() {
        if (document.body.classList.contains('dark-theme')) {
            document.body.classList.remove('dark-theme');
            document.body.classList.add('light-theme');
            localStorage.setItem('theme', 'light');
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        } else {
            document.body.classList.remove('light-theme');
            document.body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
    });
    
    // システムテーマ変更の監視
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.body.classList.remove('light-theme');
                document.body.classList.add('dark-theme');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.body.classList.remove('dark-theme');
                document.body.classList.add('light-theme');
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            }
        }
    });
}
