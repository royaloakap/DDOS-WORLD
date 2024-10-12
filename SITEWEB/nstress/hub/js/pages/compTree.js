/*
 *  Document   : compTree.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tree View Lists page
 */

var CompTree = function() {

    return {
        init: function() {
            /*
             *  Easy Tree plugin, for more examples you can check out http://www.easyjstree.com/
             */

            // Initialize default tree view list
            $('#easy-tree1').easytree();

            // Initialize tree view list with extra options
            $('#easy-tree2').easytree({
                disableIcons: true,
                enableDnd: true
            });
        }
    };
}();