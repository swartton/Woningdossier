function updateTotalUnreadMessageCount(){$.ajax({url:window.location.origin+"/messages/count",type:"GET",success:function(o){$("#total-unread-message-count").html(o.count)},statusCode:{401:function(){window.location.href="/login"}}})}function pollForMessageCount(){var o=0;beenPolled&&(o=1e4),setTimeout(function(){beenPolled=!0,updateTotalUnreadMessageCount(),pollForMessageCount()},o)}function hoomdossierRound(o,n){return null!==o?(void 0===n&&(n=5),Math.round(o/n)*n):0}function hoomdossierNumberFormat(o,n,e){return null!==o?("string"==typeof o&&(o=parseFloat(o)),o.toLocaleString(n,{minimumFractionDigits:e})):0}var beenPolled=!1;