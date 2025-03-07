
/**
 * 画像の遅延読み込み処理
 */
document.addEventListener('DOMContentLoaded', function() {
    // 遅延読み込み対象の画像を選択
    const lazyImages = document.querySelectorAll('img.lazy');
    
    // Intersection Observer APIをサポートしているかどうかをチェック
    if ('IntersectionObserver' in window) {
        // 画像が表示領域に入ったときの処理を設定
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                // 表示領域に入った場合
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    
                    // data-src属性の値をsrc属性に設定
                    if (lazyImage.dataset.src) {
                        lazyImage.src = lazyImage.dataset.src;
                    }
                    
                    // data-srcset属性の値をsrcset属性に設定
                    if (lazyImage.dataset.srcset) {
                        lazyImage.srcset = lazyImage.dataset.srcset;
                    }
                    
                    // lazyクラスを削除してloadedクラスを追加
                    lazyImage.classList.remove('lazy');
                    lazyImage.classList.add('loaded');
                    
                    // この画像の監視を解除
                    imageObserver.unobserve(lazyImage);
                }
            });
        }, {
            // ビューポートから50px手前に入った時点で読み込みを開始
            rootMargin: '50px 0px',
            threshold: 0.01
        });
        
        // 対象の画像を監視開始
        lazyImages.forEach(function(lazyImage) {
            imageObserver.observe(lazyImage);
        });
    } else {
        // Intersection Observerがサポートされていない場合のフォールバック
        // 一定間隔でスクロール位置をチェックして画像を読み込む
        let lazyLoadThrottleTimeout;
        
        function lazyLoad() {
            if (lazyLoadThrottleTimeout) {
                clearTimeout(lazyLoadThrottleTimeout);
            }
            
            lazyLoadThrottleTimeout = setTimeout(function() {
                const scrollTop = window.pageYOffset;
                
                lazyImages.forEach(function(lazyImage) {
                    if (lazyImage.offsetTop < (window.innerHeight + scrollTop)) {
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                        }
                        if (lazyImage.dataset.srcset) {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                        lazyImage.classList.remove('lazy');
                        lazyImage.classList.add('loaded');
                    }
                });
                
                // すべての画像が読み込まれたらイベントリスナーを削除
                if (lazyImages.length === 0) { 
                    document.removeEventListener('scroll', lazyLoad);
                    window.removeEventListener('resize', lazyLoad);
                    window.removeEventListener('orientationChange', lazyLoad);
                }
            }, 20);
        }
        
        // スクロール、リサイズ、画面回転時に処理を実行
        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationChange', lazyLoad);
        
        // 初回実行
        lazyLoad();
    }
    
    // 既に画面に表示されている画像に対してクラスを適用（フェードイン効果用）
    function handleVisibleImages() {
        const loadedImages = document.querySelectorAll('img.loaded');
        loadedImages.forEach(function(img) {
            img.style.opacity = '1';
        });
    }
    
    // DOMContentLoadedイベント後に実行
    handleVisibleImages();
});
