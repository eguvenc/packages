<?php

use Obullo\Error\Utils; 

include_once OBULLO .'Error/view/header.php';

$getError = function ($message) {
    return str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
        $message
    );
};
$getTrace = function($e) {
    $debugTraces = array();
    foreach ($e->getTrace() as $key => $val) {
        if (isset($val['file']) && isset($val['line'])) {
            $debugTraces[] = $val;
        }
    }
    return $debugTraces;
};
?>
<div id="middle">
    <h1><?php echo $getError($e->getMessage()); ?></h1>
    <h2>Details</h2>
    <div><strong>Type:</strong> <?php echo get_class($e) ?></div>
    <div><strong>Code:</strong> <?php echo $e->getCode() ?></div>
    <div><strong>File:</strong> <?php echo $getError($e->getFile()) ?></div>
    <div><strong>Line:</strong> <?php echo $e->getLine() ?></div>
</div>

<?php
$traceID = md5($e->getFile().$e->getLine().$e->getCode().$e->getMessage());
?>
<div id="debug">
<div id="exceptionContent">
<?php
$debugger = include APP. 'local/debugger.php';

if ($debugger['enabled'] == false) {  // disable backtrace if websocket enabled otherwise we get memory error.
    $debugTraces = $getTrace($e);
    echo '<h1><a href="javascript:void(0);" onclick="TraceToggle(\''.$traceID.'\')">debug_backtrace ('.sizeof($debugTraces).')</a></h1>';
}
?>

<?php
if (isset($lastQuery) && ! empty($lastQuery)) {
    echo '<div class="errorFile"><pre>' . $lastQuery . '</pre></div>';
}
?>
<div class="errorFile errorLine"></div>

<div id="<?php echo $traceID ?>" style="display:none;">
<?php
if (isset($debugTraces[0]['file']) && isset($debugTraces[0]['line'])) {
    
    $html = '';
    foreach ($debugTraces as $key => $trace) {
        $prefix = uniqid() . '_';
        if (isset($trace['file'])) {
            $html = '';
            if (isset($trace['class']) && isset($trace['function'])) {
                $html.= $trace['class'] . '->' . $trace['function'];
            }
            if (!isset($trace['class']) && isset($trace['function'])) {
                $html.= $trace['function'];
            }
            if (isset($trace['args'])) {
                
                if (count($trace['args']) > 0) {

                    $html.= '(<a href="javascript:void(0);" style="color:#BF342A;" ';
                    $html.= 'onclick="ExceptionToggle(\'arg_toggle_' . $prefix . $key . '\');">';
                    $html.= 'args';
                    $html.= '</a>)';
                    $html.= '<div id="arg_toggle_' . $prefix . $key . '" class="collapsed">';
                    $html.= '<div class="arguments">';

                    $html.= '<table>';
                    foreach ($trace['args'] as $arg_key => $arg_val) {
                        $html.= '<tr>';
                        $html.= '<td>' . $arg_key . '</td>';

                        if ($trace['function'] == 'createConnection' || $trace['function'] == 'connect') { // Hide database password for security.
                            $html.= '<td>***********</td>';
                        } else {
                            $html.= '<td>' . Utils::dumpArgument($arg_val) . '</td>';
                        }
                        $html.= '</tr>';
                    }
                    $html.= '</table>';

                    $html.= '</div>';
                    $html.= '</div>';
                } else {
                    $html.= (isset($trace['function'])) ? '()' : '';
                }
            } else {
                $html.= (isset($trace['function'])) ? '()' : '';
            }
            echo '<div class="errorFile" style="line-height: 2em;">' . $html . '</div>';
        }
        ++$key;
        
        ?>
        <div class="errorFile" style="line-height: 1.8em;">
            <a href="javascript:void(0);" style="color:#BF342A;" onclick="ExceptionToggle('error_toggle_' + '<?php echo $prefix . $key ?>');"><?php echo addslashes($getError($trace['file']));
        echo ' ( ' ?><?php echo ' Line : ' . $trace['line'] . ' ) '; ?></a>
        </div>

        <?php echo Utils::debugFileSource($trace, $key, $prefix) ?>

    <?php } // end foreach  ?>

<?php }   // end if isset ?>     
</div>
</div>
</div>