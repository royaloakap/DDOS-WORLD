window.onload = function() {
    var e = document.createElement("div");
    e.setAttribute("id", "sellix-container"),
    document.getElementsByTagName("body")[0].appendChild(e),
    setTimeout(function e(t) {
        if (document.querySelector("[data-sellix-product]") && !t) {
            SellixButtons = document.querySelectorAll("[data-sellix-product]");
            var pointerEventsStyleFix = '<style>' + '[data-sellix-product] * {\n' + '    pointer-events: none;\n' + '}' + '</style>';
            var pointerFix = document.createElement("div");
            pointerFix.setAttribute("id", "sellix-buttons-pointers-fix");
            pointerFix.innerHTML = pointerEventsStyleFix;
            document.getElementById("sellix-container").appendChild(pointerFix);
            for (var n = 0; n < SellixButtons.length; n++)
                SellixButtons[n].addEventListener("click", function(e) {
                    console.log("Sellix Product ID: ", e.target.getAttribute("data-sellix-product"));
                    for (var t, n = e.target.getAttribute("data-sellix-product"), l = "?", i = 0, o = e.target.attributes, a = o.length; i < a; i++)
                        (t = o[i]).nodeName.indexOf("data-sellix-custom") > -1 && (l += t.nodeName.replace("data-sellix-custom-", "") + "=" + t.nodeValue + "&");
                    var r, d = "https://embed.sellix.io/product/" + n + l, s = '<div class="sellix-fallback-button-container"><a class="sellix-fallback-button" href="' + d + '" target="_blank" >Go to product</a></div>';
                    (r = document.createElement("div")).setAttribute("id", "sellix-modal-" + n),
                    r.setAttribute("style", "display:none; position:fixed; top: 0; left:0; width: 100%; height:100%; z-index:-1050"),
                    r.innerHTML = '<div id="backdrop" class="sellix-backdrop"></div><style>.sellix-iframe {\n    width:100%;\n    height:100%;\n    border:none;\n}\n\n.sellix-iframe-content {\n    height: 100%;\n}\n\n.sellix-iframe-wrapper {\n    top:20px;\n    margin:auto;\n    width: 100%;\n    height:100%;\n    z-index: 1;\n}\n\n.sellix-iframe-loader-container {\n    z-index: 1;\n    position: absolute;\n    top: 30%;\n    left: 50%;\n    transform: translate(-50%);\n    display: flex;\n    flex-direction: column;\n    align-items: center;\n}\n\n.sellix-backdrop {\n    background: #00000075;\n    backdrop-filter: blur(3px);\n    width:100%;\n    height:100%;\n    position:absolute;\n}\n\n.sellix-fallback-button {\n    font-family: "Open Sans";\n    font-size: 14px;\n    font-weight: 600;\n    color: white;\n    text-decoration: none;\n}\n\n.sellix-fallback-button-container {\n    position: absolute;\n    z-index: 2;\n    display: none;\n    top: 50%;\n    height: 50px;\n    line-height: 40px;\n    max-height: 50px;\n    box-sizing: border-box;\n    left: 50%;\n    transform: translate(-50%, -50%);\n    background: #613bea;\n    padding: .375rem .75rem;\n    border-radius: 3px;\n}</style><div class="sellix-iframe-loader-container"><img src="https://cdn.sellix.io/static/embed/loader.png" alt="Loader" class="sellix-iframe-loader" style="width: 35px;" /></div>' + s + '<div class="sellix-iframe-wrapper"><div class="sellix-iframe-content"><iframe scrolling="auto" src="' + d + '" class="sellix-iframe" id="sellix-iframe" onerror="(e) => console.log(e)"></div></div>',
                    document.getElementById("sellix-container").appendChild(r),
                    (r = document.getElementById("sellix-modal-" + n)).style.display = "flex",
                    r.style.zIndex = "99999",
                    document.querySelector("#sellix-iframe").addEventListener("load", function(e) {
                        document.querySelector(".sellix-iframe-loader").style.display = "none"
                    }),
                    document.querySelector("#sellix-iframe").addEventListener("error", function(e) {
                        document.querySelector(".sellix-iframe-loader").style.display = "none",
                        document.querySelector(".sellix-fallback-button-container").style.display = "flex"
                    }),
                    window.addEventListener("message", function(e) {
                        "close-embed" == e.data && (r.style.display = "none",
                        r.style.zIndex = "-1050",
                        r.parentNode.removeChild(r)),
                        console.log(e)
                    }, !1)
                });
            t = !0
        }
        document.querySelector("[data-sellix-product]") || (t = !1),
        setTimeout(e, 100, t)
    }, 100, !1)
}
;
