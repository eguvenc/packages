<?php

namespace Obullo\Application\Middleware;

use RuntimeException;

trait MaintenanceTrait
{
    /**
     * Maintenance status : up / down
     * 
     * @var mixed
     */
    protected $maintenance;

    /**
     * Check applications
     *
     * @return void
     */
    public function check()
    {   
        $maintenance = $this->c['config']['maintenance'];  // Default loaded in config class.
        $maintenance['root']['regex'] = null;

        $domain = (isset($this->params['domain'])) ? $this->params['domain'] : null;
        
        foreach ($maintenance as $label) {
            if (! empty($label['regex']) && $label['regex'] == $domain) {  // If route domain equal to domain.php regex config
                $this->maintenance = $label['maintenance'];
            }
        }
        if ($this->checkRoot()) {
            return false;
        }
        if ($this->checkNodes()) {
            return false;
        }
        return true;
    }

    /**
     * Check root domain is down
     * 
     * @return boolean
     */
    public function checkRoot()
    {
        if ($this->c['config']['maintenance']['root']['maintenance'] == 'down') {  // First do filter for root domain
            return true;
        }
        return false;
    }

    /**
     * Check app nodes is down
     * 
     * @return boolean
     */
    public function checkNodes()
    {
        if (empty($this->maintenance)) {
            return false;
        }
        if ($this->maintenance == 'down') {
            return true;
        }
    }
}