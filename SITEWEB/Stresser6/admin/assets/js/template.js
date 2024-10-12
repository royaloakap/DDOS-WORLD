
(function($) {
  'use strict';
  $(function() {
    var body = $('body');
    var mainWrapper = $('.main-wrapper');
    var footer = $('footer');
    var sidebar = $('.sidebar');
    var navbar = $('.navbar').not('.top-navbar');

    // Enable feather-icons with SVG markup
    feather.replace();

    // initializing bootstrap tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // initialize clipboard plugin
    if ($('.btn-clipboard').length) {
      var clipboard = new ClipboardJS('.btn-clipboard');

      // Enabling tooltip to all clipboard buttons
      $('.btn-clipboard').attr('data-toggle', 'tooltip').attr('title', 'Copy to clipboard');

      // initializing bootstrap tooltip
      $('[data-toggle="tooltip"]').tooltip();

      // initially hide btn-clipboard and show after copy
      clipboard.on('success', function(e) {
        e.trigger.classList.value = 'btn btn-clipboard btn-current'
        $('.btn-current').tooltip('hide');
        e.trigger.dataset.originalTitle = 'Copied';
        $('.btn-current').tooltip('show');
        setTimeout(function(){
            $('.btn-current').tooltip('hide');
            e.trigger.dataset.originalTitle = 'Copy to clipboard';
            e.trigger.classList.value = 'btn btn-clipboard'
        },1000);
        e.clearSelection();
      });
    }


    // Applying perfect-scrollbar 
    if ($('.sidebar .sidebar-body').length) {
      const sidebarBodyScroll = new PerfectScrollbar('.sidebar-body');
    }
    if ($('.content-nav-wrapper').length) {
      const contentNavWrapper = new PerfectScrollbar('.content-nav-wrapper');
    }

    // Sidebar toggle to sidebar-folded
    $('.sidebar-toggler').on('click', function(e) {
      $(this).toggleClass('active');
      $(this).toggleClass('not-active');
      if (window.matchMedia('(min-width: 992px)').matches) {
        e.preventDefault();
        body.toggleClass('sidebar-folded');
      } else if (window.matchMedia('(max-width: 991px)').matches) {
        e.preventDefault();
        body.toggleClass('sidebar-open');
      }
    });


    // Settings sidebar toggle
    $('.settings-sidebar-toggler').on('click', function(e) {
      $('body').toggleClass('settings-open');
    });

    // Sidebar theme settings
    $("input:radio[name=sidebarThemeSettings]").click(function() {
      $('body').removeClass('sidebar-light sidebar-dark');
      $('body').addClass($(this).val());
     })


    // sidebar-folded on large devices
    function iconSidebar(e) {
      if (e.matches) {
        body.addClass('sidebar-folded');
      } else {
        body.removeClass('sidebar-folded');
      }
    }
    var desktopMedium = window.matchMedia('(min-width:992px) and (max-width: 1199px)');
    desktopMedium.addListener(iconSidebar);
    iconSidebar(desktopMedium);


    //  open sidebar-folded when hover
    $(".sidebar .sidebar-body").hover(
    function () {
      if (body.hasClass('sidebar-folded')){
        body.addClass("open-sidebar-folded");
      }
    },
    function () {
      if (body.hasClass('sidebar-folded')){
        body.removeClass("open-sidebar-folded");
      }
    });

  // close sidebar when click outside on mobile/table    
    $(document).on('click touchstart', function(e){
      e.stopPropagation();

      // closing of sidebar menu when clicking outside of it
      if (!$(e.target).closest('.sidebar-toggler').length) {
        var sidebar = $(e.target).closest('.sidebar').length;
        var sidebarBody = $(e.target).closest('.sidebar-body').length;
        if (!sidebar && !sidebarBody) {
        if ($('body').hasClass('sidebar-open')) {
          $('body').removeClass('sidebar-open');
        }
        }
      }
    });

    //Close other submenu in sidebar on opening any
    $(".sidebar-body > .nav > .nav-item > a[data-toggle='collapse']").on("click", function () {
      $(".sidebar-body > .nav > .nav-item").find('.collapse.show').collapse('hide');
    });

    // initializing popover
    $('[data-toggle="popover"]').popover();

    //checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-frame"></i>');

  });
})(jQuery);