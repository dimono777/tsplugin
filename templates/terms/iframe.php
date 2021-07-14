<script>
    var GLOBAL = GLOBAL || {};
    GLOBAL.pageInfo = { id: 'terms' };
    
    var seenStartOfPage, seenEndOfPage;
    
    (function($) {
      $(document).ready(function() {
    
        seenStartOfPage = true;
        seenEndOfPage = (window.innerHeight + $(window).scrollTop()) >= document.body.offsetHeight;
    
        window.onscroll = function(event) {
          if (
              !seenEndOfPage
              && (window.innerHeight + $(window).scrollTop()) >= document.body.offsetHeight
          ) {
            seenEndOfPage = true;
            dispachPopupEvent();
          }
        };
    
        dispachPopupEvent();
    
        function dispachPopupEvent() {
      
          if (seenStartOfPage && self != self.top) {
        
            self.postMessage(
                {'name': 'popuper', 'event': 'hasSeenTCPage'},
                '*'
            );
            parent.postMessage(
                {'name': 'popuper', 'event': 'hasSeenTCPage'},
                '*'
            );
          }
      
          if (seenEndOfPage && self != self.top) {
        
            self.postMessage(
                {'name': 'popuper', 'event': 'hasSeenTCPageEnd'},
                '*'
            );
            parent.postMessage(
                {'name': 'popuper', 'event': 'hasSeenTCPageEnd'},
                '*'
            );
          }
        }
        
      });
    })(window.jQuery);
</script>