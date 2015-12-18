<!DOCTYPE html>
<html>
<head>
<style type="text/css">
/* Reset CSS */

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video
{
    margin: 0;
    padding: 0;
    border: 0;
    /*font-size: 100%;*/
    /*font: inherit;*/
    vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section
{
    display: block;
}
body {
    background: white;
    line-height: 1;
}
ol, ul
{
    list-style: none;
}
blockquote, q
{
    quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after
{
    content: '';
    content: none;
}
table
{
    border-collapse: collapse;
    border-spacing: 0;
}

/* Reset CSS */

html,body {
    height:100%;
}
body
{
    font:11px 'Arial';
    background: #ddd;
    overflow-y:auto;
    overflow-x:hidden;
}
.clearfix
{
    clear: both;
}
.obulloDebugger-wrapper
{
    width:100%;
    background: #fff;
    display: block;
    height:100%;
    overflow-y:auto;
    overflow-x:hidden;
    position:relative;
}
.obulloDebugger-wrapper > .obulloDebugger-nav
{
    padding:0 5px;
    background: #eaeaea;
    border:1px solid #ccc;
    display: block;
    position: fixed;
    top:0;
    width:100%;
    z-index:99;
    border-width: 0 1px 1px 1px;
}
ul
{
    list-style-type:none;
    height:100%;
    display: block;
    height:22px;
}
ul:after
{
    content: '';
    display: block;
    clear: both;
}
li
{
    float: left;
    display: block;
}
li > a
{
    padding:0 12px;
    text-decoration:none;
    color:#5A5A5F;
    border-right:1px solid #ccc;
    display: block;
    height:22px;
    line-height:22px;
    transition:.2s;
    -webkit-transition:.2s;
    -moz-transition:.2s;
    outline:none;
}
li > a:hover
{
    background: #ddd;
}
li:nth-child(2) > a
{
    border-left:1px solid #ccc;
}
li.obulloDebugger-activeTab > a
{
    color: #E53528;
    background: #ddd;
}
li.closeBtn
{
    float: right;
    margin-right:7px;
}
li.closeBtn > a
{
    border:none;
    font-weight:bold;
    font-size:13px;
    color:#B8A4A4;
}
li.closeBtn > a:hover
{
    color:#AC8282;
}

{
    padding:12px;
    margin:25px 5px 2px 5px;
    color:#A09D9D;  /* 5A5A5F */
    height:calc(100% - 51px);
    height:-webkit-calc(100% - 51px);
    height:-moz-calc(100% - 51px);
    height:-o-calc(100% - 51px);
    height:-ms-calc(100% - 51px);
    overflow-y:auto;
    overflow-x:hidden;
}
li.favicon
{
    margin-right:10px;
}
li.favicon img
{
    margin-top:3px;
    margin-left:3px;
    display: block;
}
.p
{
    padding: 1px 0;
    background:white;
    cursor:pointer;
    position: relative;
    color:#BEBEBE;
}
.p:hover
{
/*    background:rgb(234, 234, 234);*/
    color:#000;
}
.p:hover span.date
{
    color:#000;
}
.p span.date
{
    /*font-weight:bold;*/
    padding-right:3px;
    border-right:1px solid #ccc;
    color: #e53528;
}
 img.icon
{
    width:12px;
    height:12px;
    position: absolute;
    left:5px;
}
.hiddenContainer
{
    display: none;
}
#obulloDebugger-ajax > p > span.date
{
    color:#0070FF;
}
.fireMiniTab
{
    color:#333;
    text-decoration:none;
    display: block;
    padding:8px;
    border-radius:2px;
    border:1px dotted #ddd;
    margin:5px;
}
#obulloDebugger-environment div
{
    display: none;
    margin:5px;
}
.activeMiniTab
{
    font-weight:bold;
    color:#333;
}
#obulloDebugger-environment div table
{
    width:100%;
    display: table;
    margin:0 auto;
}
#obulloDebugger-environment div table thead tr th
{
    background: rgba(236, 236, 236, 0.51);
    padding: 4px 4px;
    font-weight: bold;
    text-align:center;
}
#obulloDebugger-environment div table td,#obulloDebugger-environment div table th
{
    padding:0px 0px;
    background: rgba(236, 66, 66, 0.04);
    text-align:left;
}
#obulloDebugger-environment div table tr
{
    border:1px solid rgba(182, 182, 182, 0.18);
}
#obulloDebugger-environment > div > table > tbody > tr > th
{
    border-right: 2px solid #ddd;
    padding-left: 2px;
    background: #ECECEC;
    font-weight: normal;
    color: #000;
}
#obulloDebugger-environment > div > table > tbody > tr > td
{
    width: 85%;
    padding-left:2px;
}
pre{
  white-space: pre-wrap;       /* css-3 */
  white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
  white-space: -pre-wrap;      /* Opera 4-6 */
  white-space: -o-pre-wrap;    /* Opera 7 */
  word-wrap: break-word;       /* Internet Explorer 5.5+ */
  border:1px solid #DDD;
  border-radius:4px;
  -moz-border-radius:4px;
  -webkit-
  border-radius:4px;
  padding:5px 10px;
  color:#666;
  font-size:10px;
}
pre code{background:transparent;border:none;padding:0;}
pre span.string {color: #cc0000;}

#obulloDebugger-environment pre {
    background: none;
    border: none;
}
.title { color: #5A5A5F; margin-top: 5px; margin-bottom: 0px; }
.error, alert, emergency, warning, notice { color: #E53528; }
.info { color: blue; }

#obulloDebugger-http-log,
#obulloDebugger-console-log,
#obulloDebugger-ajax-log,
#obulloDebugger-environment {
    padding:15px 18px;
    margin-top:25px;
}
</style>

<?php 
$getDebuggerURl = function ($method = 'index') {
    return $this->c['url']->baseUrl('/debugger/debugger/'.$method);
};
?>
<script type="text/javascript">
/**
 * Obullo debbugger js.
 * 
 * @author    Rabih Abou Zaid
 * @author    Erkan Yeter
 * @author    Ersin Güvenç
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
var debuggerOff = <?php echo "'".$debuggerOff."'" ?>;
var debuggerOffMessage = '<span class="error">Debugger seems disabled. Please enable debugger from application config.</span>';
var ajax = {
    post : function(url, closure, params){
        xmlHttpRequest(url, closure, params, "POST");
    },
    get : function(url, closure, params){
        xmlHttpRequest(url, closure, params, "GET");
    }
}
function xmlHttpRequest(url, closure, params, method) {
    var xmlhttp;
    xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200){
            if( typeof closure === "function"){
                closure(xmlhttp.responseText);
            }
        }
    }
    xmlhttp.open(method, url,true);
    xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlhttp.send(params);
}
function debuggerShowTab(elem,target) {
    var containers = document.getElementsByClassName('obulloDebugger-container');
        for (var i=0; i < containers.length;i+=1){
            containers[i].style.display = 'none';
        };
    var activeTabLinks = document.getElementsByClassName('obulloDebugger-activeTab');
        for (var i=0; i < activeTabLinks.length;i+=1){
            activeTabLinks[i].classList.remove("obulloDebugger-activeTab");
        };
    var targetContainer = document.getElementById(target);
        targetContainer.style.display = "block";
        elem.className = elem.className + ' obulloDebugger-activeTab ';

        var targetAnchor = document.getElementsByClassName(target);
            for (var i = 0; i < targetAnchor.length; i++) {
                targetAnchor[i].className =  targetAnchor[i].className + " obulloDebugger-activeTab ";
            };
        var x = this.className = " obulloDebugger-activeTab ";

    <?php echo 'var cookieName = "o_debugger_active_tab";' ?>;
    setCookie(cookieName, target); // set active tab to cookie
};
function hideDebugger() {
    var obulloDebugger = document.getElementById('obulloDebugger');
    obulloDebugger.style.display = "none";
}
function fireMiniTab(elem){
    var target  = elem.getAttribute('data_target');
    var element = document.getElementById(target);
    if(elem.classList.contains('activeMiniTab') == true) {
        elem.classList.remove('activeMiniTab')
        element.style.display = ''; 
    } else {
        elem.className =  elem.className + ' activeMiniTab';
        element.style.display = 'block'; 
    }
};
function clearConsole() {
    if (debuggerOff == 0) {
        alert(debuggerOffMessage.replace(/(<([^>]+)>)/ig,""));
        return;
    }
    ajax.post(<?php echo "'".$getDebuggerURl('clear')."'" ?>, function(html){
            if (html.indexOf("<html>") == -1) {  // Show exception errors
                alert(html);
                return;
            }
            document.body.innerHTML = html;
        }
    );
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}
function refreshDebugger(msg) {
    if (msg.socket !=0 && refreshDebugger.socket == msg.socket) {  // Just one time refresh the content for same socket id
        return;
    }
    refreshDebugger.socket = msg.socket;

    var http = document.getElementById('obulloDebugger-http-log');
    var ajax = document.getElementById('obulloDebugger-ajax-log');
    var cli  = document.getElementById('obulloDebugger-console-log');

    if (typeof msg.log != "undefined" &&  msg.message == 'HTTP_REQUEST') {
        http.innerHTML = decode64(msg.log) + http.innerHTML;
    }
    if (typeof msg.log != "undefined" &&  msg.message == 'AJAX_REQUEST') {
        ajax.innerHTML = decode64(msg.log) + ajax.innerHTML;
    }
    if (typeof msg.log != "undefined" &&  msg.message == 'CONSOLE_REQUEST') {
        cli.innerHTML = decode64(msg.log) + cli.innerHTML;
    }
    if (typeof msg.env != "undefined") {
        document.getElementById("obulloDebugger-environment").innerHTML = decode64(msg.env);
    }
    if (typeof msg.css != "undefined") {

        // Send env data to debugger controller to create iframes for each layers
        // http://stackoverflow.com/questions/10418644/creating-an-iframe-with-given-html-dynamically
    
        var css = decode64(msg.css);
        
        var head = "<html><head>";
        head += css;
        head += "<style type='text/css'>";
        head += 'html, body{ background: white; margin: 0; padding: 0; }</style>';
        // console.log(head);
        createIframe(head, msg.id);
    }
}

var layers = {};
var heads  = {};
function createIframe(head, msgID) {
    var layer = document.getElementsByClassName('obullo-layer');
    for (var i in layer) {
        if (typeof layer[i] != "object") {
            continue;
        }
        if (!layer[i].getElementsByTagName('iframe').length) {
            layers[layer[i].dataset.unique] = layer[i].innerHTML;
            heads[layer[i].dataset.unique] = head;
        }
    }
    for (var i in layer) {
        
        if (typeof layer[i] != "object") {
            continue;
        }
        var tempHtml = heads[layer[i].dataset.unique];
        var iframe = document.createElement('iframe');

        iframe.frameBorder=0;
        tempHtml += '</head>';
        tempHtml += '<body>' + layers[layer[i].dataset.unique] + '</body></html>';
        iframe.id= layer[i].dataset.unique;
        iframe.width = "100%";
        iframe.height= "1px";

        document.querySelector("[data-unique='"+layer[i].dataset.unique+"']").innerHTML = '';
        layer[i].appendChild(iframe);
        iframe.contentWindow.document.write(tempHtml);

        if (typeof iframe.contentWindow == "object") {
            if (navigator.appName == "Netscape") {  // Firefox iframe unlimited loading bug fix
                setTimeout(function () {
                    if (iframe.contentWindow !== null) {
                        iframe.contentWindow.stop();
                    }
                }, 3000);
                iframe.height = iframe.contentWindow.document.body.scrollHeight + 80 + "px"; 
            } else {
                iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
            }
            
        }

    }
}

var wsUri = <?php echo "'".$websocketUrl."'"?>;           // Create webSocket connection
var websocket = new WebSocket(wsUri);

var base64ActiveSrc = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAHCAYAAADnCQYGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Qzc1NDQwMzdFOThGMTFFNEI3RUY5QTE3OEY3NDI5QjMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Qzc1NDQwMzhFOThGMTFFNEI3RUY5QTE3OEY3NDI5QjMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDNzU0NDAzNUU5OEYxMUU0QjdFRjlBMTc4Rjc0MjlCMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDNzU0NDAzNkU5OEYxMUU0QjdFRjlBMTc4Rjc0MjlCMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvXgJSIAAACwSURBVHjaYvz//z8DPvDMTPM1kOIH4i4gLgPij1Knrovi08PCQBgIAjEzEKcCMSuUjxewAF0SC6TlofzvQCwBcg0SmwnNAUxAPd1A+gUQc0J9AWODwEOQwiwgtiDCxU+BWAiIGYG4BI+6EyBDFwPxMSSXgsLrCxI7FWqQNFQNKBJmA/FrqOt4kNggcJeRiIj6Aw3TV0AsBsR/gRGFNy6YiPD2eyD+DXXdbygfLwAIMADjmCy+s+pJrgAAAABJRU5ErkJggg==";
var base64DeactiveSrc = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAHCAYAAADnCQYGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTlBQzFFMTNFOThGMTFFNEI0NEU5NDA3Qzc3OUI4REYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTlBQzFFMTRFOThGMTFFNEI0NEU5NDA3Qzc3OUI4REYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxOUFDMUUxMUU5OEYxMUU0QjQ0RTk0MDdDNzc5QjhERiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxOUFDMUUxMkU5OEYxMUU0QjQ0RTk0MDdDNzc5QjhERiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pn2CUacAAAC+SURBVHjaYvz//z8DLhATE/MaSPEDcciSJUs2Afl+QPYaIP4I5Ivi0sfEgB8IAjErEKdB+WlQviA+TSxA22OBtDwQfwdiCZArkNgwS9nRaCagvm4g/QKIOaG+gbEfsgCJLCC2IODim0i0CxAzAnEJDrUnQIYuBuJjUNeBwukLEjsVaoA6VAOMBkXEbCB+DXUdDxL7LgswwKfhiahkIMUMxD+hQjD6H1BfOrkR9R6IfwPxLCh/FpT/Hp8mgAADAAPLMt1P18UbAAAAAElFTkSuQmCC";

function connect(){
    document.getElementById("obulloDebuggerSocket").src = base64ActiveSrc;
}
function disconnect(){
    document.getElementById("obulloDebuggerSocket").src = base64DeactiveSrc;
}
function load(refresh){

    if (debuggerOff == 0) {
        document.getElementById("obulloDebugger-http-log").innerHTML = debuggerOffMessage;
        // document.getElementById("obulloDebugger-console-log").innerHTML = debuggerOffMessage;
        document.getElementById("obulloDebugger-ajax-log").innerHTML = debuggerOffMessage;
        document.getElementById("obulloDebugger-environment").innerHTML = debuggerOffMessage;
    }
    try
    {
        websocket.onopen = function(data) {        // Connection is open 
            console.log("Debugger websocket connection established.");
            connect();
        }
        websocket.onmessage = function(response) { // Received messages from server
            var msg = JSON.parse(response.data);   // Php sends Json data

            // console.log(decode64(msg.log));
            // console.log(msg.log);

            if (msg.type == "system") {
                if (msg.message == "HTTP_REQUEST") {
                    if (getCookie("o_debugger_active_tab") != 'obulloDebugger-environment') {
                        debuggerShowTab(document.getElementById("obulloDebugger-http-log"), 'obulloDebugger-http-log');
                    }
                    refreshDebugger(msg);
                } else if (msg.message == "AJAX_REQUEST") {
                    if (getCookie("o_debugger_active_tab") != 'obulloDebugger-environment') {
                        debuggerShowTab(document.getElementById("obulloDebugger-ajax-log"), 'obulloDebugger-ajax-log');

                    }
                    refreshDebugger(msg);
                } else if (msg.message == "CLI_REQUEST") {
                    refreshDebugger(msg);
                }
            }
        };
        websocket.onclose = function(data) {        // Connection is closed connect again ?
            console.log("Debugger websocket connection closed.");
            disconnect();
        }
        websocket.onerror = function(error) {
            return;
        }
    }
    catch(ex)
    { 
        console.log("Debugger exception error:" + ex.message + " at line " + ex.lineNumber);
    }
}
function reconnect() {
    if (websocket.readyState == 3) {
        ajax.get(<?php echo "'".$getDebuggerURl('ping')."'" ?>, function(html){
                if (html == 1) {
                    window.location = <?php echo "'".$getDebuggerURl()."'" ?>;
                } else {
                    disconnect();
                    console.log('Connection closed');
                }
            }
        );
    }
}
setInterval(reconnect, 2000);

var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789+/" +
               "=";
               
function encode64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
}
function decode64(input) {
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;
     var base64test = /[^A-Za-z0-9\+\/\=]/g;       // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
     if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, \'+\', \'/\',and \'=\'\n" +
              "Expect errors in decoding.");
     }
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
     do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);
        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

     } while (i < input.length);
     return unescape(output);
}
</script>
</head>
<body onload="load();">
<div class="obulloDebugger-wrapper" id="obulloDebugger">
    <nav class="obulloDebugger-nav">
        <ul>
            <li class="favicon">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAAcElEQVQ4y2NgGGjAiMx5aqrxnxhN0qdvMGIYQKxmdENY0CXWnLuJV2OIkToDAwMDw0Rmxv/5f/8zsmBTlP/3PyNOAxgYUFzKRGkgjhpABQMwojHESB0jqvABspLymnM34WkFI8FMZGYkaAi+hEYyAAAveB/cnCrFwQAAAABJRU5ErkJggg=="/>
            </li>
            <?php $activeTab = isset($cookies['o_debugger_active_tab']) ? $cookies['o_debugger_active_tab'] : 'obulloDebugger-http-log'; ?>

            <li <?php echo ($activeTab == 'obulloDebugger-http-log') ? 'class="obulloDebugger-activeTab obulloDebugger-http-log "' : '' ?> onclick="debuggerShowTab(this, 'obulloDebugger-http-log')" class="obulloDebugger-http-log">
                <a href="javascript:void(0);">HTTP</a> 
            </li>
            <li <?php echo ($activeTab == 'obulloDebugger-ajax-log') ? 'class="obulloDebugger-activeTab obulloDebugger-ajax-log "' : '' ?> onclick="debuggerShowTab(this, 'obulloDebugger-ajax-log')" class="obulloDebugger-ajax-log">
                <a href="javascript:void(0);">AJAX</a>
            </li>
            
            <li <?php echo ($activeTab == 'obulloDebugger-environment') ? 'class="obulloDebugger-activeTab obulloDebugger-environment "' : '' ?> onclick="debuggerShowTab(this, 'obulloDebugger-environment')" class="obulloDebugger-environment">
                <a href="javascript:void(0);">ENVIRONMENTS</a>
            </li>
            <li>
                <a href="javascript:void(0);" onclick="clearConsole();">CLEAR</a>
            </li>
            <li>
                <a href="javascript:void(0);"><img id="obulloDebuggerSocket" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAHCAYAAADnCQYGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Qzc1NDQwMzdFOThGMTFFNEI3RUY5QTE3OEY3NDI5QjMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Qzc1NDQwMzhFOThGMTFFNEI3RUY5QTE3OEY3NDI5QjMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDNzU0NDAzNUU5OEYxMUU0QjdFRjlBMTc4Rjc0MjlCMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDNzU0NDAzNkU5OEYxMUU0QjdFRjlBMTc4Rjc0MjlCMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvXgJSIAAACwSURBVHjaYvz//z8DPvDMTPM1kOIH4i4gLgPij1Knrovi08PCQBgIAjEzEKcCMSuUjxewAF0SC6TlofzvQCwBcg0SmwnNAUxAPd1A+gUQc0J9AWODwEOQwiwgtiDCxU+BWAiIGYG4BI+6EyBDFwPxMSSXgsLrCxI7FWqQNFQNKBJmA/FrqOt4kNggcJeRiIj6Aw3TV0AsBsR/gRGFNy6YiPD2eyD+DXXdbygfLwAIMADjmCy+s+pJrgAAAABJRU5ErkJggg=="></a>
            </li>
            <!--
            <li class="closeBtn">
                <a href="javascript:void(0);" onclick="closeWindow();">x</a>
            </li>
            -->
        </ul>
    </nav>
    <div class="obulloDebugger-container <?php echo ($activeTab != 'obulloDebugger-environment') ? 'hiddenContainer'  : '' ?>" id="obulloDebugger-environment">
        <?php echo $envHtml ?>
    </div>
    <div class="obulloDebugger-container <?php echo ($activeTab != 'obulloDebugger-ajax-log') ? 'hiddenContainer'  : '' ?>" id="obulloDebugger-ajax-log"></div>
    <div class="obulloDebugger-container <?php echo ($activeTab != 'obulloDebugger-http-log') ? 'hiddenContainer'  : '' ?>" id="obulloDebugger-http-log"></div>
</div>
</body>
</html>