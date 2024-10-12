/*
 *  Document   : appSocial.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Social Net page
 */

var AppSocial = function() {

    return {
        init: function() {
            /*
             * Chat Widget Functionality
             */
            var chatTalk    = $('.chatui-talk');
            var chatForm    = $('.chatui-form');
            var chatInput   = $('#chat-message');
            var chatMsg     = '';

            // Add a message to the chat
            chatForm
                .find('form')
                .submit(function(e){
                    // Get text from chat input
                    chatMsg = chatInput.val();

                    // If the user typed a message
                    if (chatMsg) {
                        // Add it to the message list
                        chatTalk
                            .find('ul')
                            .append('<li class="chatui-talk-msg chatui-talk-msg-right animation-expandUp">'
                                    + '<img src="img/placeholders/avatars/avatar9.jpg" alt="Avatar" class="img-circle chatui-talk-msg-avatar">'
                                    + $('<div />').text(chatMsg).html()
                                    + '</li>');

                        // Scroll the message list to the bottom
                        chatTalk
                            .animate({
                                scrollTop: chatTalk[0].scrollHeight
                            }, 200);

                        // Reset the chat input
                        chatInput.val('');
                    }

                    // Don't submit the message form
                    e.preventDefault();
                });

            // Open Chat window
            $('.chatui-action-open').on('click', function(){
                $(this)
                    .parents('.chatui')
                    .removeClass('chatui-window-close');

                // Scroll the message list to the bottom
                chatTalk
                    .animate({
                        scrollTop: chatTalk[0].scrollHeight
                    }, 0);

                // Focus chat input
                chatInput.focus();
            });

            // Close Chat window
            $('.chatui-action-close').on('click', function(){
                $(this)
                    .parents('.chatui')
                    .addClass('chatui-window-close');
            });

            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Initialize map
            new GMaps({
                div: '#gmap-checkin',
                lat: 59.32,
                lng: 17.97,
                zoom: 15,
                scrollwheel: false
            }).addMarkers([
                {lat: 59.32, lng: 17.97, title: 'Cafe-Bar', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Cafe-Bar</strong>'}}
            ]);
        }
    };
}();