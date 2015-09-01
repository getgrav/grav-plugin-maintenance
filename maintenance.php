<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Page\Pages;

class MaintenancePlugin extends Plugin
{
    public $maintenance;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        $this->maintenance = $this->config->get('plugins.maintenance');

        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }

    /**
     * Initialize a maintenance page
     */
    public function onPageInitialized()
    {
        $user = $this->grav['user'];

        $user->authorise($this->maintenance['login_access']);

        if ($this->maintenance['active']) {
            if (!$user->authenticated) {
                /** @var $page */
                $page = null;

                /** @var Pages $pages */
                $pages = $this->grav['pages'];

                // Get the custom page route if specified
                $custom_page_route = $this->config->get('plugins.maintenance.maintenance_page_route');
                if ($custom_page_route) {
                    // Try to load user error page.
                    $page = $pages->dispatch($custom_page_route, true);
                }

                // If no page found yet, use the built-in one...
                if (!$page) {
                    $page = new Page;
                    $page->init(new \SplFileInfo(__DIR__ . "/pages/maintenance.md"));
                }

                // unset the old page, and use the new one
                unset($this->grav['page']);
                $this->grav['page'] = $page;
            }
        }
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * Set needed variables to display the maintenance page.
     */
    public function onTwigSiteVariables()
    {
        /** @var User $user */
        $user = $this->grav['user'];

        if ($this->maintenance['active'] && !$user->authenticated) {
            $this->grav['twig']->twig_vars['maintenance'] = $this->maintenance;
        }
    }
}
