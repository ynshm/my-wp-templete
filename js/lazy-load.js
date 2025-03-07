/**
 * 画像の遅延読み込み機能
 * パフォーマンス向上のため画像の読み込みを必要なタイミングまで遅らせる
 */
(function() {
    'use strict';

    // 画像の遅延読み込み実装
    document.addEventListener('DOMContentLoaded', function() {
        if (!('IntersectionObserver' in window)) {
            // IntersectionObserver APIをサポートしていない場合のフォールバック
            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(function(img) {
                img.setAttribute('src', img.getAttribute('data-src'));
                if (img.getAttribute('data-srcset')) {
                    img.setAttribute('srcset', img.getAttribute('data-srcset'));
                }
                img.onload = function() {
                    img.classList.add('loaded');
                };
            });
            return;
        }

        // IntersectionObserver APIを使用した実装
        const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;

                    // レスポンシブ対応 - 現在の表示サイズに最適なサイズの画像を読み込む
                    const deviceWidth = window.innerWidth;
                    let imgSrc = lazyImage.getAttribute('data-src');

                    if (deviceWidth <= 576 && lazyImage.getAttribute('data-src-small')) {
                        imgSrc = lazyImage.getAttribute('data-src-small');
                    } else if (deviceWidth <= 992 && lazyImage.getAttribute('data-src-medium')) {
                        imgSrc = lazyImage.getAttribute('data-src-medium');
                    }

                    // 画像のソースを設定
                    lazyImage.setAttribute('src', imgSrc);

                    // srcsetがある場合は設定
                    if (lazyImage.getAttribute('data-srcset')) {
                        lazyImage.setAttribute('srcset', lazyImage.getAttribute('data-srcset'));
                    }

                    // 読み込み完了後にアニメーションクラスを適用
                    lazyImage.onload = function() {
                        lazyImage.classList.add('loaded');
                    };

                    observer.unobserve(lazyImage);
                }
            });
        }, {
            rootMargin: '200px 0px',  // ビューポートの200px手前で読み込みを開始
            threshold: 0.01
        });

        // すべての遅延読み込み画像を監視
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });

        // バックグラウンド画像の遅延読み込み
        const lazyBackgrounds = document.querySelectorAll('.lazy-background');

        const lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const lazyBackground = entry.target;
                    lazyBackground.style.backgroundImage = `url(${lazyBackground.getAttribute('data-background')})`;
                    lazyBackground.classList.add('loaded');
                    observer.unobserve(lazyBackground);
                }
            });
        }, {
            rootMargin: '200px 0px',
            threshold: 0.01
        });

        lazyBackgrounds.forEach(function(lazyBackground) {
            lazyBackgroundObserver.observe(lazyBackground);
        });
    });
})();