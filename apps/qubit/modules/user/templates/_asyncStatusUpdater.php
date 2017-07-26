<?php use_helper('Javascript') ?>
<?php echo javascript_tag(<<<EOF
jQuery(function ()
  {
    jQuery.ajax({
      url: '/user/status?slugs=' + Qubit.slugsDisplayed.join(','),
      dataType: 'json',
      success: function (results)
        {
          // Indicate which items are in the clipboard
          jQuery('button.clipboard').each(function ()
            {
              if (jQuery.inArray(jQuery(this).attr('data-clipboard-slug'), results.slugs) != -1)
                {
                  jQuery(this).addClass('added');
                }
            });

          // Show clipboard count in menu
          var spanExists = jQuery('#clipboard-menu > button > span').length;

          if (spanExists && results.count)
            {
              jQuery('#clipboard-menu > button > span').text(results.count);
            }
          else if (results.count)
            {
              var spanEl = jQuery('<span></span>');
              spanEl.text(results.count);
              jQuery('#clipboard-menu > button').append(spanEl);
            }
          else
            {
              jQuery('#clipboard-menu > button > span').remove();
            }

          // Add counts of each type of object in clipboard
          if (results.objectCountDescriptions.length)
            {
// TODO: fix escaping
              jQuery('#count-block').text(results.objectCountDescriptions.join('<br />'));
            }

          // Display appropriate user menu
          var userMenuId = results.hasOwnProperty('username') ? 'user-menu' : 'user-menu-unauth';
          jQuery('#' + userMenuId).show();

          // If user is authenticated, show username, etc.
          if (userMenuId == 'user-menu')
            {
              jQuery('#' + userMenuId + ' > button').text(results['username']);
              jQuery('#user-menu-username').text(results['username']);
            }
        }
    });
  });
EOF
) ?>
