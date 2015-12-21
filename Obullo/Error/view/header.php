<style type="text/css">
    body{ color: #777575 !important; margin:0 !important; padding:20px !important; font-family:Arial,Verdana,sans-serif !important;font-weight:normal;  }
    h1, h2, h3, h4 {
        font-weight: 500;
        line-height: 1.1;
    }
    #middle { font-size:12px;font-family:Arial,Verdana,sans-serif;font-weight:normal;padding:5px;}
    #middle h1 {font-size:20px;margin:0;}
    #middle h1 pre { margin-top: 10px;}
    #middle h2 {font-size:18px;margin:0;margin-top:10px;margin-bottom:6px;}
    #middle strong{display:inline-block;width:65px; line-height: 18px; }
    #middle span { font-size:22px; display:block; margin-top:0px !important; }

    #headerContent { padding:5px !important; }
    #headerContent h1 { font-size: 36px; color: #BF342A;margin:0;font-weight:500; line-height: 1.1}
    #headerContent strong{ display:inline-block; width:65px; }

    #exceptionContent {font-family:Arial,Verdana,Sans-serif;font-size:12px;width:99%;padding:5px;}
    #exceptionContent  h1 { font-size: 36px;margin-top:0; font-size:18px;color:#BF342A; }
    #exceptionContent  h2 {font-size:13px;color:#333;margin:0;margin-top:3px;}
    #exceptionContent .errorFile { line-height: 2.0em; }
    #exceptionContent .errorLine { font-weight: bold; color:#E53528;}
    #exceptionContent pre { color: #777575; }

    #exceptionContent pre.source { margin: 0px 0 0px 0;padding: 0; background: none; border: none;line-height: none;}
    #exceptionContent div.collapsed { display: none; }
    #exceptionContent div.arguments { }
    #exceptionContent div.arguments table { font-family: Verdana, Arial, Sans-serif;font-size: 12px; border-collapse: collapse;border-spacing: 0; background: #fff;}
    #exceptionContent div.arguments table td { text-align: left; padding: 0 4px 0 4px; border: 1px solid #ccc; }
    #exceptionContent div.arguments table td .object_name { color: blue; }
    #exceptionContent pre.source span.line { display: block; }
    #exceptionContent pre.source span.highlight { background: #F5F5F5; color:#BF342A; font-weight: bold;}
    #exceptionContent pre.source span.line span.number { color: none; }
    #exceptionContent pre.source span.line span.number { color: none; }

    a { color:#BF342A; }
    body { color:#666; }
    code,kbd { background:#EEE;border:1px solid #DDD;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:0 4px;color:#666;font-size:12px;}
    pre { color:#E53528; font-weight: normal; background:#fff;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:5px 10px;color:#666;font-size:12px;}
    pre code { border:none;padding:0; }
</style>
<script type="text/javascript">
    function ExceptionElement() {
        var elements = new Array();
        for (var i = 0; i < arguments.length; i++) {
            var element = arguments[i];
            if (typeof element == 'string')
                element = document.getElementById(element);
            if (arguments.length == 1)
                return element;
            elements.push(element);
        }
        return elements;
    }
    function ExceptionToggle(obj) {
        var el = ExceptionElement(obj);
        if (el == null) {
            return false;
        }
        el.className = (el.className != 'collapsed' ? 'collapsed' : '');
    }
    function TraceToggle(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'block' || e.style.display=='') e.style.display = 'none';
        else e.style.display = 'block';
    }
</script>
<div id="headerContent">
    <h1>
    <?php 
    $code = $e->getCode();
    switch ($code) {
    case E_PARSE:
        echo 'Syntax Error';
        break;
    case E_COMPILE_ERROR | E_CORE_ERROR:
        echo 'Fatal Error';
        break;
    case E_STRICT:
        echo 'Strict Error';
        break;
    case E_NOTICE:
        echo 'Notice';
        break;
    default:
        echo 'Error';
        break;
    }
    ?></h1>
</div>
<div style="clear:both;"></div>