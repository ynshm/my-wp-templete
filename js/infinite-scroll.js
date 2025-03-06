
/**
 * 無限スクロール機能
 * ページ移動なしで記事をロードする
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        const $container = $(newsPortalInfiniteScroll.posts_container);
        const $pagination = $(newsPortalInfiniteScroll.pagination);
        const $nextLink = $(newsPortalInfiniteScroll.next_selector);
        const $statusContainer = $('.infinite-scroll-status');
        const $loadingText = $('.infinite-scroll-request');
        const $endText = $('.infinite-scroll-last');
        const $errorText = $('.infinite-scroll-error');
        
        // 無限スクロールの状態
        let loading = false;
        let hasNextPage = $nextLink.length > 0;
        let nextPageUrl = hasNextPage ? $nextLink.attr('href') : '';
        
        // 初期状態設定
        $statusContainer.show();
        $loadingText.hide();
        $endText.hide();
        $errorText.hide();
        
        // スクロール位置の監視
        $(window).on('scroll', debounce(function() {
            checkScroll();
        }, 100));
        
        /**
         * スクロール位置をチェックし、必要に応じて新規コンテンツをロード
         */
        function checkScroll() {
            if (loading || !hasNextPage) return;
            
            const scrollHeight = $(document).height();
            const scrollPosition = $(window).height() + $(window).scrollTop();
            
            // ページ下部に近づいたらコンテンツをロード（スクロール位置がページ高さの80%を超えたとき）
            if (scrollPosition > scrollHeight * 0.8) {
                loadMorePosts();
            }
        }
        
        /**
         * 次ページの投稿をロード
         */
        function loadMorePosts() {
            if (loading) return;
            
            loading = true;
            $loadingText.show();
            
            $.ajax({
                url: nextPageUrl,
                type: 'GET',
                dataType: 'html',
                beforeSend: function() {
                    $container.addClass('loading-more');
                }
            })
            .done(function(response) {
                // DOM要素の作成とフィルタリング
                const $html = $(response);
                const $newPosts = $html.find(newsPortalInfiniteScroll.post_selector);
                
                // ないなら終了
                if ($newPosts.length === 0) {
                    hasNextPage = false;
                    showEndMessage();
                    return;
                }
                
                // 新規投稿の追加（アニメーション付き）
                $newPosts.each(function(index) {
                    const $post = $(this);
                    $post.css('opacity', 0);
                    $container.append($post);
                    
                    // フェードインアニメーション（各要素に遅延を付ける）
                    setTimeout(function() {
                        $post.animate({ opacity: 1 }, 300);
                    }, index * 100);
                });
                
                // 次ページリンクの更新
                const $newPagination = $html.find(newsPortalInfiniteScroll.pagination);
                const $newNextLink = $newPagination.find(newsPortalInfiniteScroll.next_selector);
                
                if ($newNextLink.length > 0) {
                    nextPageUrl = $newNextLink.attr('href');
                } else {
                    hasNextPage = false;
                    showEndMessage();
                }
                
                // 遅延読み込み画像の処理
                initNewImages();
                
                // カスタムイベントの発火（他のJSで使用可能）
                $(document).trigger('news_portal_posts_loaded', [$newPosts]);
            })
            .fail(function() {
                $errorText.show();
                hasNextPage = false;
            })
            .always(function() {
                loading = false;
                $loadingText.hide();
                $container.removeClass('loading-more');
            });
        }
        
        /**
         * 新しくロードされた画像の遅延読み込み設定
         */
        function initNewImages() {
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('img.lazy-load:not(.loaded)');
                
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                            }
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            }
        }
        
        /**
         * 終了メッセージの表示
         */
        function showEndMessage() {
            $endText.fadeIn(300);
            setTimeout(function() {
                $endText.fadeOut(500);
            }, 3000);
        }
        
        /**
         * デバウンス関数（スクロールイベントの最適化）
         */
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }
    });
})(jQuery);
