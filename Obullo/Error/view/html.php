<?php

use Obullo\Error\Utils; 

include_once OBULLO .'Error/view/header.php';

$getError = function ($message) {
    return str_replace(
        array(APP, DATA, CLASSES, ROOT, OBULLO, MODULES, VENDOR), 
        array('APP/', 'DATA/', 'CLASSES/', 'ROOT/', 'OBULLO/', 'MODULES/', 'VENDOR/'),
        $message
    );
}
?>
<div id="middle">

<span><?php echo $getError($e->getMessage()); ?></span>
<h2>Details</h2>
<div><strong>Type:</strong> <?php echo get_class($e) ?></div>
<div><strong>Code:</strong> <?php echo $e->getCode() ?></div>
<div><strong>File:</strong> <?php echo $getError($e->getFile()) ?></div>
<div><strong>Line:</strong> <?php echo $e->getLine() ?></div>
</div>

<div id="debug">
    <div id="exceptionContent">
    <?php if (isset($fatalError)) :  ?>
        <h1>Fatal Error</h1>
        <h2><?php echo $getError($e->getMessage()) ?></h2>
        <div class="errorFile errorLine"><?php $getError($e->getFile()). '  Line : ' . $e->getLine() ?>
        </div>
    </div>
<?php
exit; // Shutdown error exit.
else: echo '<h1>Trace</h1>';
endif;
?>

<?php
if (isset($lastQuery) && ! empty($lastQuery)) {
    echo '<div class="errorFile"><pre>' . $lastQuery . '</pre></div>';
}
?>
<div class="errorFile errorLine"></div>
<?php
    $fullTraces  = $e->getTrace();
    $debugTraces = array();

    foreach ($fullTraces as $key => $val) {
        if (isset($val['file']) && isset($val['line'])) {
            $debugTraces[] = $val;
        }
    }
    if (isset($debugTraces[0]['file']) && isset($debugTraces[0]['line'])) {

        if (isset($debugTraces[1]['file']) && isset($debugTraces[1]['line'])) {
            
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
                            $html.= '(<a href="javascript:void(0);" style="color:#E53528;" ';
                            $html.= 'onclick="ExceptionToggle(\'arg_toggle_' . $prefix . $key . '\');">';
                            $html.= 'arg';
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
                    <a href="javascript:void(0);" style="color:#E53528;" onclick="ExceptionToggle('error_toggle_' + '<?php echo $prefix . $key ?>');"><?php echo addslashes($getError($trace['file']));
                echo ' ( ' ?><?php echo ' Line : ' . $trace['line'] . ' ) '; ?></a>
                </div>

                <?php echo Utils::debugFileSource($trace, $key, $prefix) ?>

        <?php } // end foreach  ?>

    <?php }   // end if isset ?>     
<?php }   // end if isset ?>
</div>
</div>