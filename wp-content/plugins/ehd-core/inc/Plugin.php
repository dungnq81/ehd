<?php

namespace EHD\Plugins;

use EHD\Plugins\Themes\Admin;
use EHD\Plugins\Themes\Customizer;
use EHD\Plugins\Themes\Optimizer;

\defined('ABSPATH') || die;

class Plugin
{
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return void
     */
    public function init()
    {
        (new Customizer());
        (new Optimizer());
        if (is_admin()) {
            (new Admin());
        }
    }
}