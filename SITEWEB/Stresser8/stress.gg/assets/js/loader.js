function documentReady(callback=function(){loadDependencies();}){if(document.readyState!='loading'){callback();}else if(document.addEventListener){document.addEventListener('DOMContentLoaded',callback);}else{document.attachEvent('onreadystatechange',function(){if(document.readyState=='complete'){callback();}});}}
function loadDependencies(){const resources=['websocket-define-0.11.js','anime.min.js','msgpack-1.12.0.min.js','sweetalert2.all.min.js','vivus-0.4.5.min.js','site.js'];const load=function(index=0){if(index>=resources.length){return;}
const script=document.createElement('script');script.setAttribute('src','assets/js/'+resources[index]);script.setAttribute('type','text/javascript');script.onload=function(){load(index+1);}
document.getElementsByTagName('head')[0].appendChild(script);}
load();}
documentReady();