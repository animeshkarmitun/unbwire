"use strict";

// ChartJS
if(window.Chart) {
  Chart.defaults.global.defaultFontFamily = "'Nunito', 'Segoe UI', 'Arial'";
  Chart.defaults.global.defaultFontSize = 11;
  Chart.defaults.global.defaultFontStyle = 500;
  Chart.defaults.global.defaultFontColor = "#999";
  Chart.defaults.global.tooltips.backgroundColor = '#000';
  Chart.defaults.global.tooltips.titleFontFamily = "'Nunito', 'Segoe UI', 'Arial'";
  Chart.defaults.global.tooltips.titleFontColor = '#fff';
  Chart.defaults.global.tooltips.titleFontSize = 20;
  Chart.defaults.global.tooltips.xPadding = 10;
  Chart.defaults.global.tooltips.yPadding = 10;
  Chart.defaults.global.tooltips.cornerRadius = 3;
}

// DropzoneJS
if(window.Dropzone) {
  Dropzone.autoDiscover = false;
}

// Basic confirm box
$('[data-confirm]').each(function() {
  var me = $(this),
      me_data = me.data('confirm');

  me_data = me_data.split("|");
  me.fireModal({
    title: me_data[0],
    body: me_data[1],
    buttons: [
      {
        text: me.data('confirm-text-yes') || 'Yes',
        class: 'btn btn-danger btn-shadow',
        handler: function() {
          eval(me.data('confirm-yes'));
        }
      },
      {
        text: me.data('confirm-text-cancel') || 'Cancel',
        class: 'btn btn-secondary',
        handler: function(modal) {
          $.destroyModal(modal);
          eval(me.data('confirm-no'));
        }
      }
    ]
  })
});

// Global event listener for Summernote picture button - Use native addEventListener with capture phase
// jQuery doesn't support capture phase, so we use native JavaScript
document.addEventListener('click', function(e) {
  // Check if clicked element is the picture button
  var $target = $(e.target);
  var isPictureButton = $target.closest('.note-btn[data-event="showImageDialog"]').length > 0 ||
                        $target.closest('button[data-event="showImageDialog"]').length > 0 ||
                        $target.closest('[data-event="showImageDialog"]').length > 0;
  
  if (isPictureButton) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    // Find the editor
    var $button = $target.closest('[data-event="showImageDialog"]');
    var $editor = $button.closest('.note-editor').prev('.summernote-simple');
    if (!$editor.length) {
      var $noteEditor = $button.closest('.note-editor');
      if ($noteEditor.length) {
        $editor = $noteEditor.siblings('.summernote-simple');
      }
    }
    if (!$editor.length) {
      $editor = $button.closest('.note-toolbar').siblings('.note-editing-area').find('.summernote-simple');
    }
    if (!$editor.length) {
      // Last resort: find any summernote-simple on the page
      $editor = $('.summernote-simple').first();
    }
    
    if ($editor.length) {
      if (typeof window.openMediaLibrary === 'function') {
        window.openMediaLibrary($editor);
      } else if (typeof showImageDialog === 'function') {
        showImageDialog($editor);
      }
    }
    return false;
  }
}, true); // Use capture phase to run BEFORE Summernote's handlers

// Global
$(function() {
  let sidebar_nicescroll_opts = {
    cursoropacitymin: 0,
    cursoropacitymax: .8,
    zindex: 892
  }, now_layout_class = null;

  var sidebar_sticky = function() {
    if($("body").hasClass('layout-2')) {    
      $("body.layout-2 #sidebar-wrapper").stick_in_parent({
        parent: $('body')
      });
      $("body.layout-2 #sidebar-wrapper").stick_in_parent({recalc_every: 1});
    }
  }
  sidebar_sticky();

  var sidebar_nicescroll;
  var update_sidebar_nicescroll = function() {
    let a = setInterval(function() {
      if(sidebar_nicescroll != null)
        sidebar_nicescroll.resize();
    }, 10);

    setTimeout(function() {
      clearInterval(a);
    }, 600);
  }

  var sidebar_dropdown = function() {
    if($(".main-sidebar").length) {
      $(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
      sidebar_nicescroll = $(".main-sidebar").getNiceScroll();

      $(".main-sidebar .sidebar-menu li a.has-dropdown").off('click').on('click', function() {
        var me = $(this);

        me.parent().find('> .dropdown-menu').slideToggle(500, function() {
          update_sidebar_nicescroll();
          return false;
        });
        return false;
      });
    }
  }
  sidebar_dropdown();

  if($("#top-5-scroll").length) {
    $("#top-5-scroll").css({
      height: 315
    }).niceScroll();
  }

  $(".main-content").css({
    minHeight: $(window).outerHeight() - 95
  })

  $(".nav-collapse-toggle").click(function() {
    $(this).parent().find('.navbar-nav').toggleClass('show');
    return false;
  });

  $(document).on('click', function(e) {
    $(".nav-collapse .navbar-nav").removeClass('show');
  });

  var toggle_sidebar_mini = function(mini) {
    let body = $('body');

    if(!mini) {
      body.removeClass('sidebar-mini');
      $(".main-sidebar").css({
        overflow: 'hidden'
      });
      setTimeout(function() {
        $(".main-sidebar").niceScroll(sidebar_nicescroll_opts);
        sidebar_nicescroll = $(".main-sidebar").getNiceScroll();
      }, 500);
      $(".main-sidebar .sidebar-menu > li > ul .dropdown-title").remove();
      $(".main-sidebar .sidebar-menu > li > a").removeAttr('data-toggle');
      $(".main-sidebar .sidebar-menu > li > a").removeAttr('data-original-title');
      $(".main-sidebar .sidebar-menu > li > a").removeAttr('title');
    }else{
      body.addClass('sidebar-mini');
      body.removeClass('sidebar-show');
      sidebar_nicescroll.remove();
      sidebar_nicescroll = null;
      $(".main-sidebar .sidebar-menu > li").each(function() {
        let me = $(this);

        if(me.find('> .dropdown-menu').length) {
          me.find('> .dropdown-menu').hide();
          me.find('> .dropdown-menu').prepend('<li class="dropdown-title pt-3">'+ me.find('> a').text() +'</li>');
        }else{
          me.find('> a').attr('data-toggle', 'tooltip');
          me.find('> a').attr('data-original-title', me.find('> a').text());
          $("[data-toggle='tooltip']").tooltip({
            placement: 'right'
          });
        }
      });
    }
  }

  $("[data-toggle='sidebar']").click(function() {
    var body = $("body"),
      w = $(window);

    if(w.outerWidth() <= 1024) {
      body.removeClass('search-show search-gone');
      if(body.hasClass('sidebar-gone')) {
        body.removeClass('sidebar-gone');
        body.addClass('sidebar-show');
      }else{
        body.addClass('sidebar-gone');
        body.removeClass('sidebar-show');
      }

      update_sidebar_nicescroll();
    }else{
      body.removeClass('search-show search-gone');
      if(body.hasClass('sidebar-mini')) {
        toggle_sidebar_mini(false);
      }else{
        toggle_sidebar_mini(true);
      }
    }

    return false;
  });

  var toggleLayout = function() {
    var w = $(window),
      layout_class = $('body').attr('class') || '',
      layout_classes = (layout_class.trim().length > 0 ? layout_class.split(' ') : '');

    if(layout_classes.length > 0) {
      layout_classes.forEach(function(item) {
        if(item.indexOf('layout-') != -1) {
          now_layout_class = item;
        }
      });
    }

    if(w.outerWidth() <= 1024) {
      if($('body').hasClass('sidebar-mini')) {
        toggle_sidebar_mini(false);
        $('.main-sidebar').niceScroll(sidebar_nicescroll_opts);
        sidebar_nicescroll = $(".main-sidebar").getNiceScroll();
      }

      $("body").addClass("sidebar-gone");
      $("body").removeClass("layout-2 layout-3 sidebar-mini sidebar-show");
      $("body").off('click').on('click', function(e) {
        if($(e.target).hasClass('sidebar-show') || $(e.target).hasClass('search-show')) {
          $("body").removeClass("sidebar-show");
          $("body").addClass("sidebar-gone");
          $("body").removeClass("search-show");

          update_sidebar_nicescroll();
        }
      });

      update_sidebar_nicescroll();

      if(now_layout_class == 'layout-3') {
        let nav_second_classes = $(".navbar-secondary").attr('class'),
          nav_second = $(".navbar-secondary");

        nav_second.attr('data-nav-classes', nav_second_classes);
        nav_second.removeAttr('class');
        nav_second.addClass('main-sidebar');

        let main_sidebar = $(".main-sidebar");
        main_sidebar.find('.container').addClass('sidebar-wrapper').removeClass('container');
        main_sidebar.find('.navbar-nav').addClass('sidebar-menu').removeClass('navbar-nav');
        main_sidebar.find('.sidebar-menu .nav-item.dropdown.show a').click();
        main_sidebar.find('.sidebar-brand').remove();
        main_sidebar.find('.sidebar-menu').before($('<div>', {
          class: 'sidebar-brand'
        }).append(
          $('<a>', {
            href: $('.navbar-brand').attr('href'),
          }).html($('.navbar-brand').html())
        ));
        setTimeout(function() {
          sidebar_nicescroll = main_sidebar.niceScroll(sidebar_nicescroll_opts);
          sidebar_nicescroll = main_sidebar.getNiceScroll();
        }, 700);

        sidebar_dropdown();
        $(".main-wrapper").removeClass("container");
      }
    }else{
      $("body").removeClass("sidebar-gone sidebar-show");
      if(now_layout_class)
        $("body").addClass(now_layout_class);

      let nav_second_classes = $(".main-sidebar").attr('data-nav-classes'),
        nav_second = $(".main-sidebar");

      if(now_layout_class == 'layout-3' && nav_second.hasClass('main-sidebar')) {
        nav_second.find(".sidebar-menu li a.has-dropdown").off('click');
        nav_second.find('.sidebar-brand').remove();
        nav_second.removeAttr('class');
        nav_second.addClass(nav_second_classes);
  
        let main_sidebar = $(".navbar-secondary");
        main_sidebar.find('.sidebar-wrapper').addClass('container').removeClass('sidebar-wrapper');
        main_sidebar.find('.sidebar-menu').addClass('navbar-nav').removeClass('sidebar-menu');
        main_sidebar.find('.dropdown-menu').hide();
        main_sidebar.removeAttr('style');
        main_sidebar.removeAttr('tabindex');
        main_sidebar.removeAttr('data-nav-classes');
        $(".main-wrapper").addClass("container");
        // if(sidebar_nicescroll != null)
        //   sidebar_nicescroll.remove();
      }else if(now_layout_class == 'layout-2') {
        $("body").addClass("layout-2");
      }else{
        update_sidebar_nicescroll();
      }
    }
  }
  toggleLayout();
  $(window).resize(toggleLayout);

  $("[data-toggle='search']").click(function() {
    var body = $("body");

    if(body.hasClass('search-gone')) {
      body.addClass('search-gone');
      body.removeClass('search-show');
    }else{
      body.removeClass('search-gone');
      body.addClass('search-show');
    }
  });

  // tooltip
  $("[data-toggle='tooltip']").tooltip();

  // popover
  $('[data-toggle="popover"]').popover({
    container: 'body'
  });

  // Select2
  if(jQuery().select2) {
    $(".select2").select2();
  }

  // Selectric
  if(jQuery().selectric) {
    $(".selectric").selectric({
      disableOnMobile: false,
      nativeOnMobile: false
    });
  }

  $(".notification-toggle").dropdown();
  $(".notification-toggle").parent().on('shown.bs.dropdown', function() {
    $(".dropdown-list-icons").niceScroll({
      cursoropacitymin: .3,
      cursoropacitymax: .8,
      cursorwidth: 7
    });
  });

  $(".message-toggle").dropdown();
  $(".message-toggle").parent().on('shown.bs.dropdown', function() {
    $(".dropdown-list-message").niceScroll({
      cursoropacitymin: .3,
      cursoropacitymax: .8,
      cursorwidth: 7
    });
  });

  if($(".chat-content").length) { 
    $(".chat-content").niceScroll({
        cursoropacitymin: .3,
        cursoropacitymax: .8,
    });
    $('.chat-content').getNiceScroll(0).doScrollTop($('.chat-content').height());
  }

  // Custom functions for Image ALT and Caption
  function addImageAltText($editor) {
    var $node = $($editor.summernote('getSelectedNode'));
    var $img = $node.is('img') ? $node : $node.find('img');
    
    if ($img.length && $img.is('img')) {
      var currentAlt = $img.attr('alt') || '';
      var altText = prompt('Enter ALT text for image:', currentAlt);
      if (altText !== null) {
        $img.attr('alt', altText);
        $editor.summernote('focus');
      }
    } else {
      alert('Please select an image first');
    }
  }

  function addImageCaption($editor) {
    var $node = $($editor.summernote('getSelectedNode'));
    var $img = $node.is('img') ? $node : $node.find('img');
    
    if ($img.length && $img.is('img')) {
      var $figure = $img.closest('figure');
      if (!$figure.length) {
        // Wrap image in figure if not already wrapped
        $figure = $('<figure>');
        $img.wrap($figure);
        $figure = $img.parent();
      }
      
      var $figcaption = $figure.find('figcaption');
      var currentCaption = $figcaption.length ? $figcaption.text() : '';
      var captionText = prompt('Enter image caption:', currentCaption);
      
      if (captionText !== null) {
        if (captionText.trim() === '') {
          // Remove caption if empty
          if ($figcaption.length) {
            $figcaption.remove();
          }
        } else {
          if ($figcaption.length) {
            $figcaption.text(captionText);
          } else {
            $figure.append($('<figcaption>').text(captionText));
          }
        }
        $editor.summernote('focus');
      }
    } else {
      alert('Please select an image first');
    }
  }

  if(jQuery().summernote) {   
    $(".summernote").summernote({
      dialogsInBody: true,
      minHeight: 300,
      maxHeight: 500,
      focus: true,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'mediaLibrary', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['height', ['height']]
      ],
      buttons: {
        mediaLibrary: function(context) {
          var ui = $.summernote.ui;
          return ui.button({
            contents: '<i class="note-icon-picture"/>',
            tooltip: 'Insert Image',
            click: function() {
              var $textarea = $(context.layoutInfo.editor).siblings('.summernote');
              if (!$textarea.length) {
                $textarea = $('.summernote').first();
              }
              if (typeof window.openMediaLibrary === 'function') {
                window.openMediaLibrary($textarea);
              } else if (typeof showImageDialog === 'function') {
                showImageDialog($textarea);
              }
            }
          }).render();
        }
      },
      fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Nunito'],
      fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48', '64', '82', '150'],
      popover: {
        image: [
          ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
          ['float', ['floatLeft', 'floatRight', 'floatNone']],
          ['custom', ['imageAlt', 'imageCaption']],
          ['remove', ['removeMedia']]
        ],
        link: [
          ['link', ['linkDialogShow', 'unlink']]
        ],
        table: [
          ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
          ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
        ],
        air: [
          ['color', ['color']],
          ['font', ['bold', 'underline', 'clear']],
          ['para', ['ul', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture']]
        ]
      },
      callbacks: {
        onImageUpload: function(files) {
          // Handle image upload if needed
          for (let i = 0; i < files.length; i++) {
            uploadImage(files[i], this);
          }
        },
        onInit: function() {
          var $editor = $(this);
          // Watch for image popover and add custom buttons
          var checkPopover = setInterval(function() {
            var $popover = $('.note-popover');
            var $imagePopover = $popover.find('.note-image');
            if ($imagePopover.length && $imagePopover.find('.note-btn-image-alt').length === 0) {
              // Add ALT button
              var $altBtn = $('<button type="button" class="note-btn btn btn-sm btn-light" title="Add ALT text" style="margin: 2px;"><i class="fa fa-tag"></i> ALT</button>');
              $altBtn.on('click', function(e) {
                e.preventDefault();
                addImageAltText($editor);
              });
              $imagePopover.find('.note-btn-group').last().after($('<div class="note-btn-group">').append($altBtn));
              
              // Add Caption button
              var $captionBtn = $('<button type="button" class="note-btn btn btn-sm btn-light" title="Add caption" style="margin: 2px;"><i class="fa fa-quote-left"></i> Caption</button>');
              $captionBtn.on('click', function(e) {
                e.preventDefault();
                addImageCaption($editor);
              });
              $imagePopover.find('.note-btn-group').last().after($('<div class="note-btn-group">').append($captionBtn));
            }
          }, 200);
          
          // Clean up interval when editor is destroyed
          $editor.on('summernote.destroy', function() {
            clearInterval(checkPopover);
          });
        }
      }
    });
    
    // CRITICAL: Override Summernote's image dialog BEFORE initializing editors
    if (typeof $.summernote !== 'undefined') {
      // Override imageDialog module to completely disable it
      if ($.summernote.modules) {
        $.summernote.modules.imageDialog = function(context) {
          return {
            initialize: function() {
              // Completely disable - do nothing
            },
            destroy: function() {}
          };
        };
      }
      
      // Override the image button plugin
      if ($.summernote.plugins) {
        $.summernote.plugins.image = function(context) {
          var ui = $.summernote.ui;
          
          context.memo('button.image', function() {
            return ui.button({
              contents: '<i class="note-icon-picture"/>',
              tooltip: 'Insert Image',
              click: function() {
                // Find the editor
                var $editor = $('.summernote-simple').first();
                var $noteEditor = $(context.layoutInfo.editor);
                if ($noteEditor.length) {
                  $editor = $noteEditor.siblings('.summernote-simple');
                }
                if (!$editor.length) {
                  $editor = $('.summernote-simple').first();
                }
                
                // Open our media library
                if (typeof window.openMediaLibrary === 'function') {
                  window.openMediaLibrary($editor);
                }
              }
            }).render();
          });
        };
      }
    }

    $(".summernote-simple").summernote({
      dialogsInBody: true,
      minHeight: 300,
      maxHeight: 500,
      focus: true,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'mediaLibrary', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['height', ['height']]
      ],
      buttons: {
        mediaLibrary: function(context) {
          var ui = $.summernote.ui;
          return ui.button({
            contents: '<i class="note-icon-picture"/>',
            tooltip: 'Insert Image',
            click: function() {
              var $textarea = $(context.layoutInfo.editor).siblings('.summernote-simple');
              if (!$textarea.length) {
                $textarea = $('.summernote-simple').first();
              }
              if (typeof window.openMediaLibrary === 'function') {
                window.openMediaLibrary($textarea);
              } else if (typeof showImageDialog === 'function') {
                showImageDialog($textarea);
              }
            }
          }).render();
        }
      },
      fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana', 'Nunito'],
      fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48', '64', '82', '150'],
      popover: {
        image: [
          ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
          ['float', ['floatLeft', 'floatRight', 'floatNone']],
          ['remove', ['removeMedia']]
        ],
        link: [
          ['link', ['linkDialogShow', 'unlink']]
        ],
        table: [
          ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
          ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
        ],
        air: [
          ['color', ['color']],
          ['font', ['bold', 'underline', 'clear']],
          ['para', ['ul', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture']]
        ]
      },
      callbacks: {
        onImageUpload: function(files) {
          // Prevent default upload, show custom dialog instead
          var $editor = $(this);
          if (typeof window.openMediaLibrary === 'function') {
            window.openMediaLibrary($editor);
          } else {
            showImageDialog($editor);
          }
        },
        onInit: function() {
          var $editor = $(this);
          
          // Completely replace picture button handler
          var replacePictureButton = function() {
            try {
              // Find the editor container
              var $noteEditor = $editor.parent().find('.note-editor');
              if (!$noteEditor || !$noteEditor.length) {
                $noteEditor = $editor.closest('.note-editor');
              }
              if (!$noteEditor || !$noteEditor.length) {
                $noteEditor = $editor.parent().parent().find('.note-editor');
              }
              
              if ($noteEditor && $noteEditor.length > 0) {
                // Find picture button - try multiple selectors
                var $pictureBtn = $noteEditor.find('.note-btn[data-event="showImageDialog"]');
                if (!$pictureBtn || !$pictureBtn.length) {
                  $pictureBtn = $noteEditor.find('button[data-event="showImageDialog"]');
                }
                if (!$pictureBtn || !$pictureBtn.length) {
                  $pictureBtn = $noteEditor.find('[data-event="showImageDialog"]');
                }
                
                if ($pictureBtn && $pictureBtn.length > 0) {
                  // Completely remove ALL handlers and events
                  $pictureBtn.off();
                  $pictureBtn.unbind();
                  
                  // Remove any data attributes that might trigger default behavior
                  $pictureBtn.removeAttr('data-event');
                  
                  // Add our custom handler with highest priority
                  $pictureBtn.on('click.mediaLibrary', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // Open media library
                    if (typeof window.openMediaLibrary === 'function') {
                      window.openMediaLibrary($editor);
                    } else if (typeof showImageDialog === 'function') {
                      showImageDialog($editor);
                    }
                    return false;
                  });
                  
                  // Also handle mousedown to catch it even earlier
                  $pictureBtn.on('mousedown.mediaLibrary', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                  });
                }
              }
            } catch (error) {
              console.warn('Error replacing picture button:', error);
            }
          };
          
          // Try multiple times to catch the button
          replacePictureButton();
          setTimeout(replacePictureButton, 50);
          setTimeout(replacePictureButton, 100);
          setTimeout(replacePictureButton, 200);
          setTimeout(replacePictureButton, 300);
          setTimeout(replacePictureButton, 500);
          setTimeout(replacePictureButton, 1000);
          
          // Watch for image popover and add edit button
          var checkPopover = setInterval(function() {
            var $popover = $('.note-popover');
            var $imagePopover = $popover.find('.note-image');
            if ($imagePopover.length && $imagePopover.find('.note-btn-edit-image').length === 0) {
              // Add Edit button to open dialog with current values
              var $editBtn = $('<button type="button" class="note-btn btn btn-sm btn-light" title="Edit Image (Alt & Caption)" style="margin: 2px;"><i class="fa fa-edit"></i> Edit</button>');
              $editBtn.on('click', function(e) {
                e.preventDefault();
                if (typeof window.openMediaLibrary === 'function') {
                  window.openMediaLibrary($editor);
                } else {
                  showImageDialog($editor);
                }
              });
              $imagePopover.find('.note-btn-group').last().after($('<div class="note-btn-group">').append($editBtn));
            }
          }, 200);
          
          // Clean up interval when editor is destroyed
          $editor.on('summernote.destroy', function() {
            clearInterval(checkPopover);
          });
        }
      }
    });
  }

  // Image upload function for Summernote with Alt and Caption
  // callback: optional function to call with HTML string instead of inserting directly
  function uploadImage(file, editor, altText, caption, callback) {
    var data = new FormData();
    data.append("file", file);
    data.append("alt", altText || '');
    data.append("caption", caption || '');
    data.append("_token", $('meta[name="csrf-token"]').attr('content'));
    
    $.ajax({
      data: data,
      type: "POST",
      url: "/admin/upload-image",
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {
        if (response.url) {
          // Ensure alt text is always set (even if empty, for accessibility)
          var altAttr = altText ? altText.trim() : '';
          
          // Build HTML string for reliable insertion
          var imageHtml = '';
          if (caption && caption.trim() !== '') {
            // Wrap image in figure with figcaption
            imageHtml = '<figure>' +
              '<img src="' + response.url + '" alt="' + escapeHtml(altAttr) + '" style="max-width: 100%; height: auto;">' +
              '<figcaption>' + escapeHtml(caption.trim()) + '</figcaption>' +
              '</figure>';
          } else {
            // Just insert image with alt text
            imageHtml = '<img src="' + response.url + '" alt="' + escapeHtml(altAttr) + '" style="max-width: 100%; height: auto;">';
          }
          
          // If callback provided, call it with HTML (for editing scenarios)
          // Otherwise, insert directly into editor
          if (typeof callback === 'function') {
            callback(imageHtml);
          } else {
            // Use pasteHTML for reliable HTML insertion
            $(editor).summernote('pasteHTML', imageHtml);
          }
        } else {
          alert('Error: No image URL returned');
        }
      },
      error: function(xhr) {
        var errorMessage = 'Error uploading image';
        if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMessage = xhr.responseJSON.error;
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          var errors = xhr.responseJSON.errors;
          errorMessage = Object.values(errors).flat().join(', ');
        }
        alert(errorMessage);
        console.error('Upload error:', xhr);
      }
    });
  }
  
  // Helper function to escape HTML to prevent XSS
  function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
  }

  // Custom image dialog function
  function showImageDialog($editor) {
    // Use media library modal if available, otherwise fall back to old dialog
    if (typeof window.openMediaLibrary === 'function') {
      window.openMediaLibrary($editor);
      return false; // Prevent default
    }
    
    // Fallback to old dialog (for backward compatibility)
    // Create modal HTML
    var modalHtml = `
      <div class="modal fade" id="imageDialogModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Insert Image</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="imageFile">Image File</label>
                <input type="file" class="form-control-file" id="imageFile" accept="image/*">
                <small class="form-text text-muted">Select an image file to upload</small>
              </div>
              <div class="form-group">
                <label for="imageAlt">Alt Text</label>
                <input type="text" class="form-control" id="imageAlt" placeholder="Enter alt text for accessibility">
                <small class="form-text text-muted">Describe the image for screen readers and SEO</small>
              </div>
              <div class="form-group">
                <label for="imageCaption">Caption</label>
                <input type="text" class="form-control" id="imageCaption" placeholder="Enter image caption (optional)">
                <small class="form-text text-muted">Caption will appear below the image</small>
              </div>
              <div id="imagePreview" class="form-group" style="display: none;">
                <label>Preview</label>
                <div class="border p-2 text-center">
                  <img id="previewImg" src="" alt="" style="max-width: 100%; max-height: 200px;">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="insertImageBtn">Insert Image</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Remove existing modal if any
    $('#imageDialogModal').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    var $modal = $('#imageDialogModal');
    var $fileInput = $('#imageFile');
    var $altInput = $('#imageAlt');
    var $captionInput = $('#imageCaption');
    var $preview = $('#imagePreview');
    var $previewImg = $('#previewImg');
    var $insertBtn = $('#insertImageBtn');
    
    // Check if editing existing image
    var $node = $($editor.summernote('getSelectedNode'));
    var $img = $node.is('img') ? $node : $node.find('img');
    var isEditing = $img.length && $img.is('img');
    
    if (isEditing) {
      $modal.find('.modal-title').text('Edit Image');
      $insertBtn.text('Update Image');
      $altInput.val($img.attr('alt') || '');
      
      var $figure = $img.closest('figure');
      if ($figure.length) {
        var $figcaption = $figure.find('figcaption');
        if ($figcaption.length) {
          $captionInput.val($figcaption.text());
        }
      }
    }
    
    // Preview image on file select
    $fileInput.on('change', function(e) {
      var file = e.target.files[0];
      if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $previewImg.attr('src', e.target.result);
          $preview.show();
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Handle insert/update
    $insertBtn.on('click', function() {
      if (isEditing) {
        // Check if a new file was selected
        var file = $fileInput[0].files[0];
        var altText = $altInput.val().trim();
        var captionText = $captionInput.val().trim();
        
        if (file) {
          // New file selected - upload and replace image
          uploadImage(file, $editor[0], altText, captionText, function(newImageHtml) {
            // Convert HTML string to jQuery object for proper replacement
            var $newContent = $(newImageHtml);
            var $figure = $img.closest('figure');
            
            if ($figure.length) {
              // Replace entire figure structure
              $figure.replaceWith($newContent);
            } else {
              // Just replace the img element
              $img.replaceWith($newContent);
            }
            
            $editor.summernote('focus');
            $modal.modal('hide');
          });
        } else {
          // No new file - just update alt text and caption
          $img.attr('alt', altText);
          
          var $figure = $img.closest('figure');
          if (captionText !== '') {
            if (!$figure.length) {
              $figure = $('<figure>');
              $img.wrap($figure);
              $figure = $img.parent();
            }
            var $figcaption = $figure.find('figcaption');
            if ($figcaption.length) {
              $figcaption.text(captionText);
            } else {
              $figure.append($('<figcaption>').text(captionText));
            }
          } else {
            if ($figure.length) {
              $figure.find('figcaption').remove();
              if ($figure.children().length === 1) {
                $img.unwrap();
              }
            }
          }
          
          $editor.summernote('focus');
          $modal.modal('hide');
        }
      } else {
        // Insert new image
        var file = $fileInput[0].files[0];
        if (!file) {
          alert('Please select an image file');
          return;
        }
        
        var altText = $altInput.val().trim();
        var captionText = $captionInput.val().trim();
        
        uploadImage(file, $editor[0], altText, captionText);
        $modal.modal('hide');
      }
    });
    
    // Show modal
    $modal.modal('show');
    
    // Clean up on close
    $modal.on('hidden.bs.modal', function() {
      $modal.remove();
    });
  }


  if(window.CodeMirror) {
    $(".codeeditor").each(function() {
      let editor = CodeMirror.fromTextArea(this, {
        lineNumbers: true,
        theme: "duotone-dark",
        mode: 'javascript',
        height: 200
      });
      editor.setSize("100%", 200);
    });
  }

  // Follow function
  $('.follow-btn, .following-btn').each(function() {
    var me = $(this),
        follow_text = 'Follow',
        unfollow_text = 'Following';

    me.click(function() {
      if(me.hasClass('following-btn')) {
        me.removeClass('btn-danger');
        me.removeClass('following-btn');
        me.addClass('btn-primary');
        me.html(follow_text);

        eval(me.data('unfollow-action'));
      }else{
        me.removeClass('btn-primary');
        me.addClass('btn-danger');
        me.addClass('following-btn');
        me.html(unfollow_text);

        eval(me.data('follow-action'));
      }
      return false;
    });
  });

  // Dismiss function
  $("[data-dismiss]").each(function() {
    var me = $(this),
        target = me.data('dismiss');

    me.click(function() {
      $(target).fadeOut(function() {
        $(target).remove();
      });
      return false;
    });
  });

  // Collapsable
  $("[data-collapse]").each(function() {
    var me = $(this),
        target = me.data('collapse');

    me.click(function() {
      $(target).collapse('toggle');
      $(target).on('shown.bs.collapse', function() {
        me.html('<i class="fas fa-minus"></i>');
      });
      $(target).on('hidden.bs.collapse', function() {
        me.html('<i class="fas fa-plus"></i>');
      });
      return false;
    });
  });

  // Gallery
  $(".gallery .gallery-item").each(function() {
    var me = $(this);

    me.attr('href', me.data('image'));
    me.attr('title', me.data('title'));
    if(me.parent().hasClass('gallery-fw')) {
      me.css({
        height: me.parent().data('item-height'),
      });
      me.find('div').css({
        lineHeight: me.parent().data('item-height') + 'px'
      });
    }
    me.css({
      backgroundImage: 'url("'+ me.data('image') +'")'
    });
  });
  if(jQuery().Chocolat) { 
    $(".gallery").Chocolat({
      className: 'gallery',
      imageSelector: '.gallery-item',
    });
  }

  // Background
  $("[data-background]").each(function() {
    var me = $(this);
    me.css({
      backgroundImage: 'url(' + me.data('background') + ')'
    });
  });

  // Custom Tab
  $("[data-tab]").each(function() {
    var me = $(this);

    me.click(function() {
      if(!me.hasClass('active')) {
        var tab_group = $('[data-tab-group="' + me.data('tab') + '"]'),
            tab_group_active = $('[data-tab-group="' + me.data('tab') + '"].active'),
            target = $(me.attr('href')),
            links = $('[data-tab="'+me.data('tab') +'"]');

        links.removeClass('active');
        me.addClass('active');
        target.addClass('active');
        tab_group_active.removeClass('active');
      }
      return false;
    });
  });

  // Bootstrap 4 Validation
  $(".needs-validation").submit(function() {
    var form = $(this);
    if (form[0].checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.addClass('was-validated');
  });

  // alert dismissible
  $(".alert-dismissible").each(function() {
    var me = $(this);

    me.find('.close').click(function() {
      me.alert('close');
    });
  });

  if($('.main-navbar').length) {
  }

  // Image cropper
  $('[data-crop-image]').each(function(e) {
    $(this).css({
      overflow: 'hidden',
      position: 'relative',
      height: $(this).data('crop-image')
    });
  });

  // Slide Toggle
  $('[data-toggle-slide]').click(function() {
    let target = $(this).data('toggle-slide');

    $(target).slideToggle();
    return false;
  });

  // Dismiss modal
  $("[data-dismiss=modal]").click(function() {
    $(this).closest('.modal').modal('hide');

    return false;
  });

  // Width attribute
  $('[data-width]').each(function() {
    $(this).css({
      width: $(this).data('width')
    });
  });
  
  // Height attribute
  $('[data-height]').each(function() {
    $(this).css({
      height: $(this).data('height')
    });
  });

  // Chocolat
  if($('.chocolat-parent').length && jQuery().Chocolat) {
    $('.chocolat-parent').Chocolat();
  }

  // Sortable card
  if($('.sortable-card').length && jQuery().sortable) {
    $('.sortable-card').sortable({
      handle: '.card-header',
      opacity: .8,
      tolerance: 'pointer'
    });
  }

  // Daterangepicker
  if(jQuery().daterangepicker) {
    if($(".datepicker").length) {
      $('.datepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        singleDatePicker: true,
      });
    }
    if($(".datetimepicker").length) {
      $('.datetimepicker').daterangepicker({
        locale: {format: 'YYYY-MM-DD hh:mm'},
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
      });
    }
    if($(".daterange").length) {
      $('.daterange').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        drops: 'down',
        opens: 'right'
      });
    }
  }

  // Timepicker
  if(jQuery().timepicker && $(".timepicker").length) {
    $(".timepicker").timepicker({
      icons: {
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down'
      }
    });
  }
});