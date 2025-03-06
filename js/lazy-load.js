
/**
 * 画像の遅延読み込み機能
 * パフォーマンス向上のため画像の読み込みを必要なタイミングまで遅らせる
 */
(function() {
    'use strict';
    
    // IntersectionObserverが利用可能かチェック
    if ('IntersectionObserver' in window) {
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = [].slice.call(document.querySelectorAll('img.lazy-load'));
            
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        
                        // data-srcがある場合はsrcに設定
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                        }
                        
                        // data-srcsetがある場合はsrcsetに設定
                        if (lazyImage.dataset.srcset) {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                        
                        lazyImage.classList.remove('lazy-load');
                        lazyImage.classList.add('loaded');
                        lazyImageObserver.unobserve(lazyImage);
                        
                        // イベントの発火（他のスクリプトで使用可能）
                        const event = new Event('lazyloaded');
                        lazyImage.dispatchEvent(event);
                    }
                });
            });
            
            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
            
            // Mutationオブザーバーを使ってDOM変更を監視、新しい遅延読み込み画像を検出
            const contentContainer = document.querySelector('#content');
            if (contentContainer) {
                const mutationObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // 要素ノードのみ
                                // 追加された要素内の遅延読み込み画像を探す
                                const newLazyImages = [].slice.call(node.querySelectorAll('img.lazy-load'));
                                if (newLazyImages.length > 0) {
                                    newLazyImages.forEach(function(lazyImage) {
                                        lazyImageObserver.observe(lazyImage);
                                    });
                                }
                                
                                // 追加された要素自体が遅延読み込み画像の場合
                                if (node.classList && node.classList.contains('lazy-load') && node.tagName === 'IMG') {
                                    lazyImageObserver.observe(node);
                                }
                            }
                        });
                    });
                });
                
                mutationObserver.observe(contentContainer, {
                    childList: true,
                    subtree: true
                });
            }
        });
    } else {
        // IntersectionObserverがサポートされていない場合のフォールバック
        document.addEventListener('DOMContentLoaded', function() {
            const lazyLoad = function() {
                const lazyImages = [].slice.call(document.querySelectorAll('img.lazy-load'));
                const activeArea = window.innerHeight + window.pageYOffset + 200;
                
                lazyImages.forEach(function(lazyImage) {
                    if ((lazyImage.getBoundingClientRect().top + window.pageYOffset) < activeArea) {
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                        }
                        if (lazyImage.dataset.srcset) {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                        lazyImage.classList.remove('lazy-load');
                        lazyImage.classList.add('loaded');
                        
                        // イベントの発火
                        const event = new Event('lazyloaded');
                        lazyImage.dispatchEvent(event);
                    }
                });
                
                if (lazyImages.length === 0) {
                    document.removeEventListener('scroll', lazyLoad);
                    window.removeEventListener('resize', lazyLoad);
                    window.removeEventListener('orientationchange', lazyLoad);
                }
            };
            
            lazyLoad();
            
            document.addEventListener('scroll', lazyLoad);
            window.addEventListener('resize', lazyLoad);
            window.addEventListener('orientationchange', lazyLoad);
        });
    }
})();
