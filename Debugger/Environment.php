<?php

namespace Obullo\Debugger;

use RuntimeException;
use Psr\Http\Message\RequestInterface as Request;

use Obullo\Session\SessionInterface as Session;

/**
 * Debugger environment tab
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Environment
{
    /**
     * Html output
     * 
     * @var string
     */
    protected $output;

    /**
     * Http Request Class
     * 
     * @var object
     */
    protected $request;

    /**
     * Constructor
     * 
     * @param object $request \Obullo\Http\Request\RequestInterface
     * @param object $session \Obullo\Http\Session\SessionInterface
     * @param string $output  html output
     */
    public function __construct(Request $request, Session $session, $output = null)
    {
        $this->output = $output;
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Build super globals
     * 
     * @return array
     */
    protected function buildSuperGlobals()
    {
        $ENVIRONMENTS = array();
        
        $ENVIRONMENTS['POST'] = $this->request->getParsedBody();
        $ENVIRONMENTS['GET'] = $this->request->getQueryParams();
        $ENVIRONMENTS['COOKIE'] = $this->request->getCookieParams();
        $ENVIRONMENTS['SESSION'] = $this->session->getAll();
        $ENVIRONMENTS['SERVER'] = $this->request->getServerParams();

        return $ENVIRONMENTS;
    }

    /**
     * Build environments
     * 
     * @return string
     */
    public function printHtml()
    {
        $ENVIRONMENTS = $this->buildSuperGlobals();

        $ENVIRONMENTS['HTTP_REQUEST']  = $this->request->getHeaders();
        $ENVIRONMENTS['HTTP_HEADERS']  = headers_list();
        $ENVIRONMENTS['HTTP_RESPONSE'] = [$this->output];

        $method = $this->request->getMethod();

        $output = '';
        foreach ($ENVIRONMENTS as $key => $value) {
            $label = (strpos($key, 'HTTP_') === 0) ? $key : '$_'.$key;
            $output.= '<a href="javascript:void(0);" onclick="fireMiniTab(this)" data_target="'.strtolower($key).'" class="fireMiniTab">'.$label.'</a>'."\n";

            $style = $this->getDefaultTab($method, $key);

            if ($key == 'HTTP_RESPONSE') {
                $style = 'style="display:block;"';
            }
            $output.= '<div id="'.strtolower($key).'"'.$style.'>'."\n";
            $output.= "<table>\n";
            $output.= "<tbody>\n";

            if (empty($value)) {
                $output.= "<tr>\n";
                $output.= "<th><pre>\"\"</pre></th>\n";
                $output.= "</tr>\n";
            } else {
                foreach ($value as $k => $v) {
                    $output.= "<tr>\n";
                    $output.= "<th><pre>$k</pre></th>\n";
                    $output.= "<td>\n";
                    if (is_array($v)) {
                        $output.= "<pre><span>".var_export($v, true)."</span></pre>\n";
                    } else {
                        $output.= "<pre><span>\"$v\"</span></pre>\n";
                    }
                    $output.= "</td>\n";
                    $output.= "</tr>\n";
                }
            }
            $output.= "</tbody>\n";
            $output.= "</table>\n";
            $output.= "</div>\n";
        }
        return $output;
    }

    /**
     * Get selected env tab style
     * 
     * @param string $method current http method
     * @param string $env    env key
     * 
     * @return string
     */
    protected function getDefaultTab($method, $env)
    {
        if ($method == 'POST' && $env == 'POST') {
            $style = 'style="display:block;"';
        } elseif ($method == 'GET' && $env == 'GET') {
            $style = 'style="display:block;"';
        } else {
            $style = 'style="display:none;"';
        }
        return $style;
    }
}