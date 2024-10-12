/*
 *  Document   : compNestable.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Nestable Lists page
 */

var CompNestable = function() {

    /* Output serialised data function */
    var updateOutput = function(e) {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');

        if (window.JSON) {
            output.html(window.JSON.stringify(list.nestable('serialize')));
        } else {
            output.html('JSON browser support required!');
        }
    };

    return {
        init: function() {
            var nestLists = $('.dd');
            var nestList1 = $('#nestable1');
            var nestList2 = $('#nestable2');

            /* Initialize Nestable Lists with Nestable plugin, for more examples you can check out https://github.com/dbushell/Nestable */
            nestList1
                .nestable({
                    group: 1
                })
                .on('change', updateOutput);

            nestList2
                .nestable({
                    group: 1
                })
                .on('change', updateOutput);

            // Output initial serialised data
            updateOutput(nestList1.data('output', $('#nestable1-output')));
            updateOutput(nestList2.data('output', $('#nestable2-output')));

            // Collapse/Expand functionality
            $('#nestable-actions > a').on('click', function(e){
               var nestAction = $(this).data('action');

                if (nestAction == 'collapse') {
                   nestLists.nestable('collapseAll');
               } else if (nestAction == 'expand') {
                   nestLists.nestable('expandAll');
               }
            });
        }
    };
}();