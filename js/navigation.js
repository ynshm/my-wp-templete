
/**
 * モバイルナビゲーションのJS
 * パフォーマンスとアクセシビリティ対応
 */
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.querySelector('.menu-toggle');
  const menuContainer = document.querySelector('.menu-container');
  const primaryMenu = document.getElementById('primary-menu');
  const subMenus = document.querySelectorAll('.sub-menu');
  
  if (!menuToggle || !menuContainer) {
    return;
  }
  
  // メニュー開閉の操作
  menuToggle.addEventListener('click', function() {
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
    
    menuToggle.setAttribute('aria-expanded', !isExpanded);
    menuContainer.classList.toggle('toggled');
    
    if (!isExpanded) {
      setTimeout(() => {
        // フォーカスをメニューの最初の項目に移動
        const firstItem = primaryMenu.querySelector('a');
        if (firstItem) {
          firstItem.focus();
        }
      }, 100);
    }
  });
  
  // Escキーでメニューを閉じる
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && menuContainer.classList.contains('toggled')) {
      menuToggle.setAttribute('aria-expanded', 'false');
      menuContainer.classList.remove('toggled');
      menuToggle.focus();
    }
  });
  
  // サブメニューのアクセシビリティ対応
  if (subMenus.length > 0) {
    // 親メニューの項目にaria属性を追加
    const parentMenuItems = document.querySelectorAll('.menu-item-has-children > a');
    
    parentMenuItems.forEach(item => {
      // 親メニューアイテムにドロップダウン表示用のインジケータを追加
      const dropdownIndicator = document.createElement('span');
      dropdownIndicator.className = 'dropdown-icon';
      dropdownIndicator.setAttribute('aria-hidden', 'true');
      item.appendChild(dropdownIndicator);
      
      // 親メニューのアクセシビリティ属性設定
      item.setAttribute('aria-haspopup', 'true');
      item.setAttribute('aria-expanded', 'false');
      
      // ドロップダウン開閉のイベント
      item.addEventListener('click', function(e) {
        // 既存のリンク機能を維持したまま、ドロップダウン機能を追加
        if (window.innerWidth > 768) {
          return; // デスクトップではデフォルト動作のまま
        }
        
        e.preventDefault();
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        
        const subMenu = this.nextElementSibling;
        if (subMenu && subMenu.classList.contains('sub-menu')) {
          subMenu.classList.toggle('toggled');
        }
      });
    });
  }
  
  // ブラウザリサイズ対応
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 768) {
        menuContainer.classList.remove('toggled');
        menuToggle.setAttribute('aria-expanded', 'false');
        
        // サブメニューのリセット
        document.querySelectorAll('.sub-menu.toggled').forEach(subMenu => {
          subMenu.classList.remove('toggled');
        });
        
        document.querySelectorAll('.menu-item-has-children > a[aria-expanded="true"]').forEach(item => {
          item.setAttribute('aria-expanded', 'false');
        });
      }
    }, 50);
  });
  
  // 遅延読み込みのための対応
  document.addEventListener('lazyloaded', function(e) {
    // 遅延読み込み画像のロード完了後の処理
    // 必要に応じてレイアウト調整を行う
  });
});

/**
 * スクロールトップボタンの実装
 */
document.addEventListener('DOMContentLoaded', function() {
  // スクロールトップボタンの作成
  const scrollTopBtn = document.createElement('button');
  scrollTopBtn.id = 'scroll-top';
  scrollTopBtn.className = 'scroll-top-btn';
  scrollTopBtn.setAttribute('aria-label', 'ページの先頭へ戻る');
  scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up" aria-hidden="true"></i>';
  document.body.appendChild(scrollTopBtn);
  
  // スクロール位置によるボタンの表示/非表示
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      scrollTopBtn.classList.add('show');
    } else {
      scrollTopBtn.classList.remove('show');
    }
  });
  
  // ボタンクリック時の処理
  scrollTopBtn.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});
