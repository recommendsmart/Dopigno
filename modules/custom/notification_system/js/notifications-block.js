(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.notificationSystemBlock = {
    attach: function (context, settings) {

      $('.notification-block', context).once('loadNotifications').each(function () {
        var $block = $(this);

        // Lazy load data for the notification block.
        var $placeholder = $block.find('.notification-block__placeholder');

        var getEndpoint = drupalSettings.notificationSystem.getEndpointUrl;
        var display_mode = $block.data('notificationblock-display-mode');
        var show_read = $block.data('notificationblock-show-read');
        getEndpoint = getEndpoint.replace('DISPLAY_MODE', display_mode);

        if (show_read === 'yes') {
          getEndpoint = getEndpoint + '?showRead';
        }

        $.get(getEndpoint, function (data) {
          $placeholder.before(data).remove();

          addEventListeners($block);
          updateCounters();
        });
      });


      function addEventListeners($context) {
        // Mark as Read.
        $context.find('.notification-markasread-trigger').once('markAsRead').click(function (e) {
          var $button = $(this);
          var markAsReadEndpoint = drupalSettings.notificationSystem.markAsReadEndpointUrl;
          var providerId = $button.data('notification-provider');
          var notificationId = $button.data('notification-id');
          markAsReadEndpoint = markAsReadEndpoint.replace('PROVIDER_ID', providerId);
          markAsReadEndpoint = markAsReadEndpoint.replace('NOTIFICATION_ID', notificationId);

          $.ajax({
            url: markAsReadEndpoint,
            type: 'GET',
          }).done(function (data) {
            var $notificationItem = $button.parents('.notification-item');

            $notificationItem.addClass('is-read');
            updateCounters();
          }).fail(function (data) {
            if (data.responseJSON.hasOwnProperty('message')) {
              console.error('Error while marking notification as read: ' + data.responseJSON.message);
            }
            else {
              console.error('Unknown error while marking notification as read.');
            }
          });
        });
      }

      function updateCounters() {
        $('.notification-block').each(function () {
          var $block = $(this);

          var displayMode = $block.data('notificationblock-display-mode');

          if (displayMode === 'simple') {

            var $counter = $block.find('.notification-counter');
            var count = $block.find('.notification-item:not(.is-read)').length;

            $counter.text('(' + count + ')');

            if (count > 0) {
              $counter.removeClass('is-hidden');
            }
            else {
              $counter.addClass('is-hidden');
            }
          }

          if (displayMode === 'bundled') {
            $block.find('.notification-group').each(function () {
              var $group = $(this);
              var $counter = $group.find('.notification-counter');
              var count = $group.find('.notification-item:not(.is-read)').length;

              $counter.text(count);

              if (count > 0) {
                $counter.removeClass('is-hidden');
              }
              else {
                $counter.addClass('is-hidden');
              }
            })
          }
        });
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
